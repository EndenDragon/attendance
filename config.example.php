<?php
$database = new PDO("mysql:dbname=database_name;host=netid.ovid.u.washington.edu;port=12345;charset=utf8", "user", "password"); // pdo db connection

$course_title = "CSE 154 Section AI"; // global header

$admins = array("netid", "netid2"); // array of netids to access admin page
?>
