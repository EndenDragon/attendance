<?php
  /*
    Add or edit sessions
  */
  include_once("common.php");
  require_administrator();

  if (!isset($_POST["name"])) {
    die_and_error("name parameter is missing");
  }

  if (!isset($_POST["password"])) {
    die_and_error("password parameter is missing");
  }

  if (!isset($_POST["startdate"])) {
    die_and_error("startdate parameter is missing");
  }

  if (!isset($_POST["starttime"])) {
    die_and_error("starttime parameter is missing");
  }

  if (!isset($_POST["enddate"])) {
    die_and_error("enddate parameter is missing");
  }

  if (!isset($_POST["endtime"])) {
    die_and_error("endtime parameter is missing");
  }

  $name = $_POST["name"];
  $password = $_POST["password"];
  $startdate = $_POST["startdate"];
  $starttime = $_POST["starttime"];
  $enddate = $_POST["enddate"];
  $endtime = $_POST["endtime"];
  $start = DateTime::createFromFormat("j F, Y H:i", "$startdate $starttime")->format('Y-m-d H:i:s');
  $end = DateTime::createFromFormat("j F, Y H:i", "$enddate $endtime")->format('Y-m-d H:i:s');

  $sql_query = "SELECT identifier FROM sessions WHERE identifier = :name";
  $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sql_query->execute(array(":name" => $name));
  $result_set = $sql_query->fetchAll();
  $data = [];

  foreach ($result_set as $row) {
    $id_row = [
      "identifier" => $row["identifier"]
    ];
    $data[]= $id_row;
  }
  if (sizeof($data) > 0) {
    $sql_query = "UPDATE sessions SET password=:password, start=:start, end=:end WHERE identifier = :name";
    $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sql_query->execute(array(":password"=>$password, ":start"=>$start, ":end"=>$end, ":name"=>$name));
    $result_set = $sql_query->fetchAll();
  } else {
    $sql_query = "INSERT INTO sessions (identifier, password, start, end) VALUES (:name, :password, :start, :end)";
    $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sql_query->execute(array(":password"=>$password, ":start"=>$start, ":end"=>$end, ":name"=>$name));
    $result_set = $sql_query->fetchAll();
  }
  header('HTTP/1.1 204 No Content');
?>
