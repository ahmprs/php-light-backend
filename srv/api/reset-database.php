<?php
$srv = realpath(__dir__ . "../../");

require_once "$srv/lib/main.php";
require_once "$srv/settings.php";
require_once "$srv/database/schema.php";
require_once "$srv/database/seed.php";

class ResetDatabase
{
    public static function run()
    {
        $u = par('user_name');
        $p = par('user_pass');
        $c = par('confirm');

        if ($u != Settings::get('super_user')) {
            return respAccessDenied();
        }

        if ($p != Settings::get('super_password')) {
            return respAccessDenied();
        }

        if ($c != 'yes') {
            return respAccessDenied();
        }

        $arr = Schema::run();
        $brr = Seed::run();
        resp(1, ['Schema' => $arr, 'Seed' => $brr]);
    }

    public static function form()
    {
        $arrParameters = [];

        $arrParameters["user_name"] = "USER NAME";
        $arrParameters["user_pass"] = "PASSWORD";
        $arrParameters["confirm"] = "WARNING: ALL DATA WILL BE LOST. ARE YOU SUER?";

        $arrTypes["user_name"] = "text";
        $arrTypes["user_pass"] = "password";
        $arrTypes["confirm"] = "text";

        $html = makeForm('Signup', './', $arrParameters, $arrTypes);
        echo ($html);
    }
}
// TEST ONLY:
// ResetDatabase::run();
