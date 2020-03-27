<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/db.php";
require_once "$srv/lib/sec.php";
require_once "$srv/lib/user.php";

class SignIn
{
    public static function run()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (User::getUserId() != "") {
            resp(0, 'Already Logged in');
            return;
        }

        $user_name = par('user_name');
        if ($user_name == null) {
            resp(0, 'missing user name');
            return;
        }

        $otp_client = par('otp');
        if ($otp_client == null) {
            resp(0, 'missing otp');
            return;
        }

        $r = DB::connect()->select("
            SELECT
                user_id,
                user_name,
                user_pass_hash,
                user_active,
                user_access_level
            FROM
                tbl_users
            WHERE
                user_name = '$user_name'
            LIMIT 1;
        ");
        $rec = $r->getRecords();

        if (count($rec) != 1) {
            resp(0, 'user name is not correct');
            return;
        }

        $u = $rec[0];
        $h = $u['user_pass_hash'];
        $seed = $_SESSION['sign_in_seed'];
        $otp_server = Sec::getHash('md5', $h . $seed);

        if ($otp_client != $otp_server) {
            respAccessDenied();
            return;
        }

        // user is authenticated successfully
        $_SESSION['user_id'] = $u['user_id'];
        $_SESSION['user_name'] = $u['user_name'];
        $_SESSION['user_active'] = $u['user_active'];
        $_SESSION['user_access_level'] = $u['user_access_level'];

        resp(1, ['logged_in' => true, 'msg' => 'welcome ' . $u['user_name']]);
    }

    public static function newSeed()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $r = rand(1000000000, 11000000000);
        $seed = Sec::getHash("md5", "$r");
        $_SESSION['sign_in_seed'] = $seed;
        resp(1, $seed);
    }

    public static function lastSeed()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $seed = '';
        if (isset($_SESSION['sign_in_seed'])) {
            $seed = $_SESSION['sign_in_seed'];
        }
        resp(1, $seed);
    }

    public static function getLoginState()
    {
        $id = User::getUserId();
        if ($id == '') {
            resp(0, 'signed out');
        } else {
            resp(1, 'signed in');
        }
    }
}

// TEST ONLY:
// SignIn::run();
