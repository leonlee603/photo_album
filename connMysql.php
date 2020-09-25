<?php
$db_host = "localhost";
$db_username = "xxxx";
$db_password = "xxxx";
$db_name = "phpalbum";

$db_link = @new mysqli($db_host, $db_username, $db_password, $db_name);
if ($db_link->connect_errno) {
    $err_msg = "Database connection failed:</br>";
    $err_msg .= $db_link->connect_error;
    $err_msg .= " / error number:(" . $db_link->connect_errno . ")";
    exit($err_msg);
} else {
    $db_link->query("SET NAMES utf8");
}