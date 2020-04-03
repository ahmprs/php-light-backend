<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/fs.php";
require_once "$srv/lib/calendar.php";

require_once "$srv/api/posts-present.php";

class Posts
{
    public static function all()
    {
        Posts::adminCheck();
        $r = DBS::select('select * from tbl_posts');
        resp(1, [
            'db_result' => $r,
        ]);
    }

    public static function expired()
    {
        Posts::adminCheck();
        $c = new Calendar();
        $gdp = $c->get_server_gdp();
        $r = DBS::select('select * from tbl_posts where post_expire_gdp < ?', 'i', [$gdp]);
        resp(1, [
            'db_result' => $r,
            'gdp' => $gdp,
        ]);
    }

    public static function me()
    {
        Posts::accessCheck();
        $user_id = User::getUserId();
        $c = new Calendar();
        $gdp = $c->get_server_gdp();
        $r = DBS::select('select * from tbl_posts where post_owner_id = ?', 'i', [$user_id]);
        resp(1, [
            'db_result' => $r,
        ]);
    }

    public static function meExpired()
    {
        Posts::accessCheck();
        $user_id = User::getUserId();
        $c = new Calendar();
        $gdp = $c->get_server_gdp();
        $r = DBS::select('select * from tbl_posts where post_owner_id = ? and post_expire_gdp < ?', 'ii', [$user_id, $gdp]);
        resp(1, [
            'db_result' => $r,
            'gdp' => $gdp,
        ]);
    }

    public static function getPost($path)
    {
        $fn = diff($path, '/posts/');
        $r = DBS::select("select * from tbl_posts where post_file_name = ? ", 's', [$fn]);
        if (count($r['records']) > 0) {
            try {
                $content = null;
                $pst = $r['records'][0];

                $f = $pst['post_file_name'];
                $e = $pst['post_file_ext'];
                $p = Settings::get('posts_directory');

                $pfe = realpath("$p/$f.$e");
                if (is_file($pfe)) {

                    $ff = FS::file($pfe);

                    if (in_array($e, ['txt'])) {
                        $content = FS::file($pfe)->getContent();
                    } else {
                        // $content = FS::file($pfe)->getContentAsByteArray()[1];
                    }
                    return PostsPresent::run($f, $e, $content);
                    // return resp(1, [
                    //     'pst' => $pst,
                    //     // 'pfe' => $pfe,
                    //     'arr_content' => $content,
                    // ]);
                } else {
                    return resp(0, 'missing file');
                }
            } catch (\Throwable $th) {
                return resp(0, [
                    'err' => 'unable to read file content',
                    // 'rec' => $r,
                    'ex' => $th,
                ]);
            }
        }
        return resp(0, "not found: $path");
    }

    private static function accessCheck()
    {
        if (User::getUserId() == '') {
            resp(0, [
                'err' => 'not signed in yet',
                'hint' => 'sign in first',
            ]);
            exit(0);
        }
    }

    private static function adminCheck()
    {
        $acc = User::getUserAccessLevel();

        if ($acc != 100) {
            resp(0, [
                'err' => 'admin access denied',
                'hint' => 'sign in as admin please',
            ]);
            exit(0);
        }
    }

    public static function likePost($like_val)
    {
        $r = null;
        try {
            $post_id = par('post_id');
            if ($post_id == '') {
                return resp(0, 'missing post_id');
            }
            $sql = "
            INSERT INTO `tbl_likes` (`rec_id`, `post_id`, `like_val`) VALUES (NULL, ?, ?);
            ";
            $r = DBS::insert($sql, 'ii', [$post_id, $like_val]);
            if ($r['affected_rows_count'] == 1) {
                return resp(1, 'like succeeded');
            }

            return resp(0, 'like failed');

        } catch (\Throwable $th) {
            return resp(0, [
                'err' => 'unable to like the given post',
                'r' => $r,
            ]);
        }
    }

    public static function likeCount($like_val)
    {
        $r = null;
        try {
            $post_id = par('post_id');
            if ($post_id == '') {
                return resp(0, 'missing post_id');
            }
            $sql = "
            SELECT COUNT(rec_id) as like_cnt from tbl_likes where post_id=? and like_val=?;
            ";
            $r = DBS::select($sql, 'ii', [$post_id, $like_val]);
            return resp(1, $r);
        } catch (\Throwable $th) {
            return resp(0, [
                'err' => 'unable to like the given post',
                'r' => $r,
            ]);
        }
    }
}
