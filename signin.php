<?php
  /*
    Signs in the user
  */
  include("common.php");

  if (!isset($_POST["session"])) {
    die_and_error("session parameter is missing");
  }
  if (!isset($_POST["password"])) {
    die_and_error("password parameter is missing");
  }

  $session = $_POST["session"];
  $password = $_POST["password"];
  $uwnetid = $_SERVER["REMOTE_USER"];

  $sql_query = "SELECT identifier, password, start, end FROM sessions WHERE identifier = '$session'";
  $result_set = $database->query($sql_query);
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
  if ($data["password"] != $password) {
    die_and_error("Password is incorrect.");
  }

  $now = date('Y-m-d H:i:s');
  $start = $data["start"];
  $end = $data["end"];
  if (!($start <= $now && $now <= $end)) {
    die_and_error("Attendance for $session is closed.");
  }
  
  $sql_query = "SELECT id FROM records WHERE netid = '$uwnetid' AND session = '$session'";
  $result_set = $database->query($sql_query);
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
  
  $sql_query = "INSERT INTO records (netid, session, timestamp) VALUES ('$uwnetid', '$session', '$now')";
  $database->query($sql_query);
  header('HTTP/1.1 204 No Content');
?>
