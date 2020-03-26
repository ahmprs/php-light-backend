<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/calendar.php";
require_once "$srv/lib/user.php";

class DateTimeInfo
{
    public static function run()
    {
        DateTimeInfo::auth();
        $r = Calendar::all();
        resp(1, $r);
    }

    public static function jal()
    {
        DateTimeInfo::auth();
        $c = new Calendar();
        $r = $c->get_server_jal_date_as_str();
        resp(1, $r);
    }

    public static function greg()
    {
        DateTimeInfo::auth();
        $c = new Calendar();
        $r = $c->get_server_greg_date_as_str();
        resp(1, $r);
    }

    public static function gdp()
    {
        DateTimeInfo::auth();
        $c = new Calendar();
        $r = $c->get_server_gdp_time();
        resp(1, $r);
    }

    public static function stamp()
    {
        DateTimeInfo::auth();
        $c = new Calendar();
        $r = $c->getStamp();
        resp(1, $r);
    }

    public static function auth()
    {
        $id = User::getUserId();
        if ($id == '') {
            resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
            exit(0);
        }
    }
}
