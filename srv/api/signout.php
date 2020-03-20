<?php
$srv = realpath (__dir__."../../");
require_once "$srv/core/main.php";


session_start();

if (!isset($_SESSION['user_id'])) {
    resp(0, 'Already Logged out');
    return;
} else if ($_SESSION['user_id'] == "") {
    resp(0, 'Already Logged out');
    return;
}

$_SESSION['user_id'] = "";
$_SESSION['user_name'] = "";
$_SESSION['user_active'] = "";
$_SESSION['user_access_level'] = "";

resp(1, 'Logged out');
