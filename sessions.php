<?php
  /*
    Get list of signed in members
  */
  include("common.php");
  require_administrator();

  if (!isset($_GET["session"])) {
    die_and_error("session parameter is missing");
  }

  $session = $_GET["session"];
  $sql_query = "SELECT id, netid, session, timestamp FROM records WHERE session = :session ORDER BY netid";
  $sql_query = $database->prepare($sql_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sql_query->execute(array(":session" => $session));
  $result_set = $sql_query->fetchAll();
  $json_data = [];

  foreach ($result_set as $row) {
    $json_row = [
      "id" => $row["id"],
      "netid" => $row["netid"],
      "session" => $row["session"],
      "timestamp" => $row["timestamp"],
    ];
    $json_data[]= $json_row;
  }
  header("Content-type:application/json");
  echo json_encode($json_data, JSON_PRETTY_PRINT);
?>
