<?php
$database = new PDO("mysql:dbname=database_name;host=netid.ovid.u.washington.edu;port=12345;charset=utf8", "user", "password"); // pdo db connection

$canvas_access_token = "make a new access token from https://canvas.uw.edu/profile/settings"; // canvas access token

$canvas_course_id = "1199398"; // course id for canvas

$course_title = "CSE 154 Section AI"; // global header

$admins = array("netid", "netid2"); // array of netids to access admin page
?>
