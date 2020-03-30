<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/user.php";

class ChangePassword
{
    public static function run()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {

            return resp(0, [
                'err' => 'not signed in yet',
                'hint' => 'please sign in first',
            ]);

        }

        $user_pass_hash = par('user_pass_hash');
        if ($user_pass_hash == '') {
            return resp(0, [
                'err' => 'missing hash of password',
                'hint' => 'make md5 hash of password',
            ]);
        }

        $sql = "UPDATE `tbl_users` SET `user_pass_hash` = '$user_pass_hash' WHERE `tbl_users`.`user_id` = $user_id;";
        $cn = DB::connect();
        $cn->update($sql);
        $n = $cn->getNumberOfAffectedRows();

        // TEST
        // return resp(1, [
        //     'cn' => $cn,
        //     'n' => $n,
        //     'sql' => $sql,
        //     'err' => $cn->err,
        // ]);

        if ($n == 1) {
            return resp(1, 'password changed successfully');
        }
        return resp(0, [
            'err' => 'password change failed!',
        ]);
    }}
