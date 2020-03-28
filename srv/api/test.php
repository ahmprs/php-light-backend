<?php
// Test cases here
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/dbs.php";

class Test
{
    public static function run()
    {
        // $sql = "SELECT * FROM tbl_users";
        // $r = DBS::select($sql);
        // resp(1, $r['records']);

        // $sql = "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass_hash`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, ?, ?, ?, ?, ?)";
        // $r = DBS::insert($sql, 'ssdds', ['a', 2, 3, 4, 5]);
        // resp(1, $r);

        // $sql = "UPDATE `tbl_users` SET `user_name`=?, `user_pass_hash`=?, `user_active`=?, `user_access_level`=?, `user_desc`=? WHERE `user_id`=?";
        // $r = DBS::update($sql, 'ssddsd', ['Jade', 'asqw', 1, 1, '', 3]);
        // resp(1, $r);

        $sql = "DELETE FROM `tbl_users` WHERE `user_id` = ?";
        $r = DBS::delete($sql, 'd', [5]);
        resp(1, $r);

    }
}
