<?php
  error_reporting(E_ALL);
  date_default_timezone_set('America/Los_Angeles');

  include("config.php");

  function die_and_error($message) {
      header("HTTP/2 400 Bad Request");
      header("Content-type:application/json");
      echo json_encode(array(
              "error" => $message
          ), JSON_PRETTY_PRINT);
      die();
  }

  function require_administrator() {
    global $admins;
    if (!in_array(get_remote_user(), $admins)) {
      die_and_error("Admin required");
    }
  }

  function get_remote_user() {
    $remote_user = $_SERVER["REMOTE_USER"];
    if (strpos($remote_user, "@") !== false) {
      return explode("@", $remote_user)[0];
    }
    return $remote_user;
  }
?>
