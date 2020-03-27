<?php
$srv = realpath(__dir__);
require_once "$srv/api/test.php";

require_once "$srv/lib/main.php";
require_once "$srv/api/say-hello.php";
require_once "$srv/api/reset-database.php";
require_once "$srv/api/sign-in-form.php";
require_once "$srv/api/sign-in.php";
require_once "$srv/api/sign-out.php";
require_once "$srv/api/sign-up.php";
require_once "$srv/api/sign-up-form.php";
require_once "$srv/api/upload.php";
require_once "$srv/api/upload-form.php";
require_once "$srv/api/date-time-info.php";
require_once "$srv/api/change-password.php";
require_once "$srv/api/change-password-form.php";

class Route
{
    public static function run()
    {
        $path = Route::getPath();

        switch ($path) {

            case '/api/test':return Test::run();

            case '/api/say-hello':return SayHello::run();
            case '/api/say-hello/form':return SayHello::form();
            case '/api/say-hello/help':return SayHello::help();

            case '/api/reset-database':return ResetDatabase::run();
            case '/api/reset-database/form':return ResetDatabase::form();

            case '/api/sign-in':return SignIn::run();
            case '/api/sign-in/form':return SignInForm::run();
            case '/api/sign-in/new-seed':return SignIn::newSeed();
            case '/api/sign-in/last-seed':return SignIn::lastSeed();
            case '/api/sign-in/state':return SignIn::getLoginState();
            case '/api/change-password':return changePassword::run();
            case '/api/change-password/form':return ChangePasswordForm::run();

            case '/api/sign-up':return SignUp::run();
            case '/api/sign-up/form':return SignUpForm::run();
            case '/api/sign-out':return SignOut::run();

            case '/api/upload':return Upload::run();
            case '/api/upload/form':return UploadForm::run();

            case '/api/date-time-info':return DateTimeInfo::run();
            case '/api/date-time-info/jal':return DateTimeInfo::jal();
            case '/api/date-time-info/greg':return DateTimeInfo::greg();
            case '/api/date-time-info/gdp':return DateTimeInfo::gdp();
            case '/api/date-time-info/stamp':return DateTimeInfo::stamp();

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
        $server_root_path = realpath($_SERVER['DOCUMENT_ROOT']);
        $srv_path = realpath(__dir__);
        $srv_url = Route::diff($srv_path, $server_root_path);
        $srv_url = str_replace('\\', '/', $srv_url);
        return $srv_url;
    }
}

Route::run();
