<?php

$srv = realpath (__dir__."../../");


require_once "$srv/lib/main.php";
require_once "$srv/settings.php";
require_once "$srv/database/schema.php";
require_once "$srv/database/seed.php";


$gui = par('gui');
if ($gui == 1) {
    $arrParameters = [];

    $arrParameters["user_name"] = "USER NAME";
    $arrParameters["user_pass"] = "PASSWORD";

    $arrTypes["user_name"] = "text";
    $arrTypes["user_pass"] = "password";

    $html = makeForm('Signup', './reset-database.php', $arrParameters, $arrTypes);
    echo ($html);
    return;
}

$u = par('user_name');
$p = par('user_pass');


if ($u != Settings::get('super_user')) return respAccessDenied();
if ($p != Settings::get('super_password')) return respAccessDenied();


$arr = Schema::Run();
$brr = Seed::Run();
resp(1, ['Schema'=>$arr, 'Seed'=>$brr]);
?>