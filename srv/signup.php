<?php

require_once __DIR__ . "/core/main.php";
require_once __DIR__ . "/core/dal.php";

session_start();

$gui = par('gui');
if ($gui == 1) {
    $arrParameters = [];

    $arrParameters["user_name"] = "USER NAME";
    $arrParameters["user_pass"] = "PASSWORD";

    $arrTypes["user_name"] = "text";
    $arrTypes["user_pass"] = "password";

    $html = makeForm('Signup', './signup.php', $arrParameters, $arrTypes);
    echo ($html);
    return;
}


// extract parameters
$user_name = par('user_name');
$user_pass = par('user_pass');

if ($user_name == null) {
    resp(0, 'missing user name');
    return;
}

if ($user_pass == null) {
    resp(0, 'missing password');
    return;
}

$sql = "select * from tbl_users where user_name = '$user_name'";
$db_res = select($sql, $getJson = false);
if ($db_res['recCnt'] >= 1) {
    resp(0, 'Username not available');
    return;
}


$user_active_default = 1;
$user_access_level_default = 1;

$sql = "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, '$user_name', '$user_pass', '$user_active_default', '$user_access_level_default', '')";
$db_res = insert($sql, $getJson = false);
if ($db_res['cnState'] != 1) {
    resp(0, 'Database Connection Problem');
    return;
}

if ($db_res['recCnt'] != 1) {
    resp(0, 'Unable to Signup');
    return;
}

$user_id = $db_res['last_rec_id'];
$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] =  $user_name;
$_SESSION['user_active'] = $user_active_default;
$_SESSION['user_access_level'] = $user_access_level_default;

resp(1, [
    'user_id' => $user_id,
    'user_name' => $user_name,
    'user_active' => $user_active_default,
    'user_access_level' => $user_access_level_default
]);
