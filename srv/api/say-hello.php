<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";

class SayHello
{
    public static function run()
    {
        SayHello::auth();

        // extract parameters
        $user_name = par('user_name');
        $user_age = par('user_age');
        $user_weight = par('user_weight');

        if ($user_name == null) {
            return resp(0, 'missing user name');
        }

        // run the logic
        $arr = [];
        $arr['user_name'] = $user_name;
        $arr['user_age'] = $user_age;
        $arr['user_weight'] = $user_weight;

        resp(1, $arr);
    }

    // check if the user access level
    // and deny service if not authorized.
    public static function auth()
    {
        // resp(0, 'Your access is banned to this feature');
        // exit(0);
    }

    // called on /form
    public static function form()
    {
        $arrParameters = [];
        $arrParameters["user_name"] = "YOUR NAME PLEASE";
        $arrParameters["user_age"] = "AGE";
        $arrParameters["user_weight"] = "WEIGHT";
        $arrTypes["user_name"] = "text";
        $arrTypes["user_age"] = "number";
        $arrTypes["user_weight"] = "number";
        $html = makeForm('Say Hello', "../say-hello", $arrParameters, $arrTypes);
        echo ($html);
    }

    // called on /help
    public static function help()
    {
        resp(1, [
            'fields' => [
                'user_name',
                'user_age',
                'user_weight',
            ],
            'hint' => 'use /form to get GUI',
        ]);
    }
}
// TEST ONLY:
// SayHello::run();
