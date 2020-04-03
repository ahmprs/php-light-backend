<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/calendar.php";

class Follow
{
    public static function doFollow()
    {
        $user_id = User::getUserId();

        if ($user_id == '') {
            return resp(0, 'please login first');
        }

        $followed_id = par('followed_id');
        if ($followed_id == '') {
            return resp(0, 'missing followed_id');
        }

        if ($followed_id == $user_id) {
            return resp(0, 'user is not allowed to follow themselves.');
        }

        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        try {

            $p = DBS::select(
                "select count(rec_id) as cnt from tbl_followers where follower_id=? and followed_id=?",
                'ii',
                [$user_id, $followed_id]
            );
            $cnt = $p['records'][0]['cnt'];
            if ($cnt > 0) {
                return resp(1, 'user is already followed');
            }

            $r = DBS::insert(
                "INSERT INTO `tbl_followers` (`rec_id`, `follower_id`, `followed_id`, `followed_gdp`) VALUES (NULL, ?, ?, ?)",
                'iid',
                [$user_id, $followed_id, $gdp]
            );

            if ($r['affected_rows_count'] == 1) {
                return resp(1, 'follow process success');
            }
            return resp(1, 'follow process succeeded');
        } catch (\Throwable $th) {
            return resp(1, 'unable to complete follow process');
        }
    }

    public static function doUnFollow()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, 'please login first');
        }

        $followed_id = par('followed_id');
        if ($followed_id == '') {
            return resp(0, 'missing followed_id');
        }

        if ($followed_id == $user_id) {
            return resp(0, 'user is not allowed to follow themselves.');
        }

        try {

            $p = DBS::select(
                "select count(rec_id) as cnt from tbl_followers where follower_id=? and followed_id=?",
                'ii',
                [$user_id, $followed_id]
            );
            $cnt = $p['records'][0]['cnt'];
            if ($cnt == 0) {
                return resp(1, 'user is already unfollowed');
            }

            $r = DBS::delete(
                "delete from tbl_followers where follower_id=? and followed_id=?",
                'ii',
                [$user_id, $followed_id]
            );
            $cnt = $r['affected_rows_count'];
            if ($cnt > 0) {
                return resp(1, 'unfollow process succeeded');
            }
            return resp(1, 'unfollow process failed');
        } catch (\Throwable $th) {
            return resp(1, 'unable to complete unfollow process');
        }
    }

    public static function getFollowers()
    {
        $user_id = User::getUserId();

        if ($user_id == '') {
            return resp(0, 'please login first');
        }

        try {
            $r = DBS::select(
                "select follower_id from tbl_followers where followed_id=?",
                'i',
                [$user_id]
            );

            return resp(1, $r['records']);
        }
        // handle exceptions
         catch (\Throwable $th) {

        }
    }

    public static function getFollowings()
    {
        $user_id = User::getUserId();

        if ($user_id == '') {
            return resp(0, 'please login first');
        }

        try {
            $r = DBS::select(
                "select followed_id from tbl_followers where follower_id=?",
                'i',
                [$user_id]
            );

            return resp(1, $r['records']);
        }
        // handle exceptions
         catch (\Throwable $th) {

        }
    }
}
