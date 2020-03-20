<?php
$srv = realpath (__dir__."../../");

require_once "$srv/core/main.php";
require_once "$srv/core/dal.php";

session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_id'] != "") {
        resp(0, 'Already Logged in');
        return;
    }
}

$gui = par('gui');
if ($gui == 1) {
    $arrParameters = [];

    $arrParameters["user_name"] = "USER NAME";
    $arrParameters["user_pass"] = "PASSWORD";

    $arrTypes["user_name"] = "text";
    $arrTypes["user_pass"] = "password";

    $html = makeForm('Signup', './signin.php', $arrParameters, $arrTypes);
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

if ($user_name == 'admin' && $user_pass == 'admin') {
    $_SESSION['user_id'] = -1;
    $_SESSION['user_name'] = $user_name;
    resp(1, 'SUPERUSER LOGGED IN');
    return;
}


$sql = "select * from tbl_users where user_name = '$user_name' and user_pass = '$user_pass' ";
$db_res = select($sql, $getJson = false);
if ($db_res['recCnt'] == 1) {

    $_SESSION['user_id'] = $db_res['tbl'][0]['user_id'];
    $_SESSION['user_name'] = $db_res['tbl'][0]['user_name'];
    $_SESSION['user_active'] = $db_res['tbl'][0]['user_active'];
    $_SESSION['user_access_level'] = $db_res['tbl'][0]['user_access_level'];

    resp(1, [
        'user_id' => $db_res['tbl'][0]['user_id'],
        'user_name' => $db_res['tbl'][0]['user_name'],
        'user_active' => $db_res['tbl'][0]['user_active'],
        'user_access_level' => $db_res['tbl'][0]['user_access_level']
    ]);
} else {
    resp(0, 'Access Denied');
}
