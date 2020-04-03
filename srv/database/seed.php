<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/dbs.php";
require_once "$srv/settings.php";

class Seed
{
    public static function run()
    {
        $arrResult = [];

        $arr = Seed::execSqlArr([
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass_hash`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Jack', '202cb962ac59075b964b07152d234b70', '1', '100', '')",
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass_hash`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Joe', '202cb962ac59075b964b07152d234b70', '1', '1', '')",
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass_hash`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Jim', '202cb962ac59075b964b07152d234b70', '1', '1', '')",
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass_hash`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Cat', '202cb962ac59075b964b07152d234b70', '1', '1', '')",
        ]);
        array_push($arrResult, $arr);

        return $arrResult;
    }

    public static function execSql($sql, $credentials = null)
    {
        $r = DBS::runSql($sql, $credentials);
        return $r;
    }

    public static function execSqlArr($arrSql, $credentials = null)
    {
        $arrResult = [];
        $n = count($arrSql);
        for ($i = 0; $i < $n; $i++) {
            array_push($arrResult, Schema::execSql($arrSql[$i], $credentials));
        }
        return $arrResult;
    }
}
