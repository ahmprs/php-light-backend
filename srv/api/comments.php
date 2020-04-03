<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/calendar.php";

class Comments
{
    public static function addComment()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'login first',
            ]);
        }

        $post_id = par('post_id');
        if ($post_id == '') {
            return resp(0, 'missing post_id');
        }

        $comment_text = par('comment_text');
        if ($comment_text == '') {
            return resp(0, 'missing comment_text');
        }

        $c = new Calendar();
        $gdp = $c->get_server_gdp_time();

        try {
            $r = DBS::insert(
                "INSERT INTO `tbl_comments` (`comment_id`, `post_id`, `comment_owner_id`, `comment_gdp`, `comment_text`, `comment_extra`) VALUES (NULL, ?, ?, ?, ?, '')",
                'iids',
                [
                    $post_id,
                    $user_id,
                    $gdp,
                    $comment_text,
                ]
            );
            if ($r['affected_rows_count'] == 1) {
                return resp(1, 'comment added');
            }
            return resp(0, 'comment adding failed');
        }
        //
         catch (\Throwable $th) {
            return resp(0, 'unable to complete the process of adding comment');
        }
    }

    public static function removeComment()
    {
        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'login first',
            ]);
        }

        $comment_id = par('comment_id');
        if ($comment_id == '') {
            return resp(0, 'missing comment_id');
        }

        try {
            $r = DBS::select(
                "select comment_owner_id from `tbl_comments` where comment_id=?",
                'i',
                [
                    $comment_id,
                ]
            );

            if (count($r['records']) == 0) {
                return resp(0, 'invalid comment_id');
            }

            $comment_owner_id = $r['records'][0]['comment_owner_id'];
            if ($comment_owner_id != $user_id) {
                return resp(0, 'only comment owner can remove the comment');
            }
        } 
        //
        catch (\Throwable $th) {
            return resp(0, 'unable to complete the process of adding comment');
        }

        try {
            $p = DBS::delete(
                "delete from tbl_comments where comment_id=?",
                'i',
                [$comment_id]
            );

            if ($p['affected_rows_count'] == 1) {
                return resp(1, 'comment removed');
            } else {
                return resp(1, 'comment removing failed');
            }
        }
        //
         catch (\Throwable $th) {
            return resp(1, 'unable to remove comment');
        }
    }
}
