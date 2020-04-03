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

        // TEST
        // return resp(1, [$u, $p, $c]);

        if ($u != Settings::get('super_user')) {
            return respAccessDenied();
        }

        if ($p != Settings::get('super_password')) {
            return respAccessDenied();
        }

        if ($c != 'yes') {
            return respAccessDenied();
        }

        // TEST:
        // return resp(1, 'AAA');

        $arr = Schema::run();
        $brr = Seed::run();
        resp(1, ['Schema' => $arr, 'Seed' => $brr]);
    }

    public static function getAllSqlStatements()
    {
        // Schema::

        $arr = Schema::getAllSqls();
        $out = '';
        $n = count($arr);
        $sql = null;
        for ($i = 0; $i < $n; $i++) {
            $sql = $arr[$i];
            $out .= "<h4>$sql</h4>";
        }
        $out = "<div style='padding: 10px;'>$out</div>";
        echo ($out);
    }
}
