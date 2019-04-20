<?php
  /*
    Signs in the user
  */
  include_once("common.php");

  if (!isset($_POST["session"])) {
    die_and_error("session parameter is missing");
  }
  if (!isset($_POST["password"])) {
    die_and_error("password parameter is missing");
  }

  $session = $_POST["session"];
  $password = $_POST["password"];
  $uwnetid = get_remote_user();

  $sql_query = "SELECT identifier, password, start, end FROM sessions WHERE identifier = :session";
  $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sql_query->execute(array(":session" => $session));
  $result_set = $sql_query->fetchAll();
  $data = [];
  foreach ($result_set as $row) {
    $id_row = [
      "identifier" => $row["identifier"],
      "password" => $row["password"],
      "start" => $row["start"],
      "end" => $row["end"],
    ];
    $data[]= $id_row;
  }

  if (sizeof($data) == 0) {
    die_and_error("Session does not exist.");
  }

  $data = $data[0];

  if (substr($data["identifier"], 0, 6) == "debug_") {
    $expected = $data["password"];
    $expected_ascii = implode(",", unpack("C*", $expected));
    $got = $password;
    $got_ascii = implode(",", unpack("C*", $got));

    $msg = "Expected password to be (" . $expected . ") [" . $expected_ascii . "]. Got (" . $got . ") [" . $got_ascii . "].";

    if ($expected != $got) {
      $msg = $msg . " Password is different/incorrect and is rejected by the server.";
    } else {
      $msg = $msg . " Password is same/correct and is accepted by the server.";
    }

    $msg = $msg . " NetID[" . $uwnetid . "]";

    file_put_contents('debug.log', $msg . "\n", FILE_APPEND);
    die_and_error($msg);
  }


  if ($data["password"] != $password) {
    die_and_error("Password is incorrect.");
  }

  $now = date('Y-m-d H:i:s');
  $start = $data["start"];
  $end = $data["end"];
  if (!($start <= $now && $now <= $end)) {
    die_and_error("Attendance for $session is closed.");
  }

  $sql_query = "SELECT id FROM records WHERE netid = :uwnetid AND session = :session";
  $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sql_query->execute(array(":uwnetid" => $uwnetid, ":session" => $session));
  $result_set = $sql_query->fetchAll();
  $data = [];
  foreach ($result_set as $row) {
      $id_row = [
        "id" => $row["id"],
      ];
      $data[]= $id_row;
  }

  if (sizeof($data) > 0) {
      die_and_error("You have already signed in.");
  }

  $sql_query = "INSERT INTO records (netid, session, timestamp) VALUES (:uwnetid, :session, :now)";
  $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sql_query->execute(array(":uwnetid"=>$uwnetid, ":session"=>$session, ":now"=>$now));
  $result_set = $sql_query->fetchAll();
  header('HTTP/1.1 204 No Content');
?>
