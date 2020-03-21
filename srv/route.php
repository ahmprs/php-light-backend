<?php
$srv = realpath(__dir__);
require_once "$srv/lib/main.php";
require_once "$srv/api/say-hello.php";
require_once "$srv/api/reset-database.php";
require_once "$srv/api/sign-in.php";
require_once "$srv/api/sign-out.php";
require_once "$srv/api/sign-up.php";
require_once "$srv/api/upload.php";

class Route
{

    public static function run()
    {
        $path = Route::getPath();

        switch ($path) {

            case '/api/say-hello':return SayHello::run();
            case '/api/reset-database':return ResetDatabase::run();
            case '/api/sign-in':return SignIn::run();
            case '/api/sign-out':return SignOut::run();
            case '/api/sign-up':return SignUp::run();
            case '/api/upload':return Upload::run();

            default:return resp(0, "wrong path: $path");
        }
    }

    private static function getPath()
    {
        $srv_url = Route::getSrvUrl();
        $req_uri = $_SERVER['REQUEST_URI'];
        $path = Route::diff("$srv_url", $req_uri);
        $path = strtolower($path);
        $path = Route::stripParameters($path);
        return $path;
    }

    private static function stripParameters($path)
    {
        $indx = strpos($path, '?');
        if (!$indx) {
            return $path;
        } else {
            return substr($path, 0, $indx);
        }

    }

    private static function diff($strA, $strB)
    {
        // swap $strA and $strB if needed
        if (strlen($strA) < strlen($strB)) {
            $t = $strA;
            $strA = $strB;
            $strB = $t;
        }
        return substr($strA, strlen($strB));
    }

    private static function getSrvUrl()
    {
        // C:\xampp\htdocs
        $server_root_path = realpath($_SERVER['DOCUMENT_ROOT']);
        // C:\xampp\htdocs\1-WebApps\php-light-backend\php-light-backend\srv
        $srv_path = realpath(__dir__);
        $srv_url = Route::diff($srv_path, $server_root_path);
        $srv_url = str_replace('\\', '/', $srv_url);
        return $srv_url;
    }
}

Route::run();
