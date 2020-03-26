<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";

class User
{

    public static function getUserInfo()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = '';
        }

        if (!isset($_SESSION['user_name'])) {
            $_SESSION['user_name'] = '';
        }

        if (!isset($_SESSION['user_active'])) {
            $_SESSION['user_active'] = '';
        }

        if (!isset($_SESSION['user_access_level'])) {
            $_SESSION['user_access_level'] = '';
        }

        return [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $_SESSION['user_name'],
            'user_active' => $_SESSION['user_active'],
            'user_access_level' => $_SESSION['user_access_level'],
        ];
    }

    public function auth($access_level)
    {
        $ui = User::getUserInfo();

        if ($ui['user_id'] == null) {
            return 0;
        }

        if ($ui['user_id'] == '') {
            return 0;
        }

        if ($ui['user_access_level'] == null) {
            return 0;
        }

        if ($ui['user_access_level'] == '') {
            return 0;
        }

        $ual = (int) $ui['user_access_level'];

        if ($ual >= $access_level) {
            return 1;
        } else {
            return 0;
        }

    }

    public static function getUserId()
    {
        $ui = User::getUserInfo();
        if ($ui['user_id'] == null) {
            return '';
        } else {
            return $ui['user_id'];
        }
    }

    public static function getUserName()
    {
        $ui = User::getUserInfo();
        if ($ui['user_name'] == null) {
            return '';
        } else {
            return $ui['user_name'];
        }

    }

    public static function getUserAccessLevel()
    {
        $ui = User::getUserInfo();
        if ($ui['user_access_level'] == null) {
            return '';
        } else {
            return $ui['user_access_level'];
        }

    }

    public static function getUserActive()
    {
        $ui = User::getUserInfo();
        if ($ui['user_active'] == null) {
            return '';
        } else {
            return $ui['user_active'];
        }

    }
}

// TESTS:
// resp (1, User::getUserInfo());
// resp (1, User::auth(1));
// resp(1, User::getUserId());
// resp (1, User::getUserName());
// resp (1, User::getUserActive());
// resp (1, User::getUserAccessLevel());
