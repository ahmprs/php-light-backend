<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/fs.php";
require_once "$srv/lib/calendar.php";

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
        $fn = Posts::diff($path, '/api/posts/');
        $r = DBS::select("select * from tbl_posts where post_file_name = ? ", 's', [$fn]);
        if (count($r['records']) > 0) {
            try {
                $arr_content = null;
                $pst = $r['records'][0];
                $f = $pst['post_file_name'];
                $e = $pst['post_file_ext'];
                $p = Settings::get('posts_directory');

                $pfe = realpath("$p/$f.$e");
                if (is_file($pfe)) {
                    $ff = FS::file($pfe);
                    // $arr_content = $ff->getContentAsByteArray();
                    $arr_content = FS::file($pfe)->getContentAsByteArray();
                    // $arr_content = FS::file($pfe)->getContent();
                    return resp(1, [
                        'pst' => $pst,
                        // 'pfe' => $pfe,
                        'arr_content' => $arr_content['1'],
                    ]);
                } else {
                    return resp(0, 'missing file');
                }
            } catch (\Throwable $th) {
                return resp(0, 'unable to read file content');
            }
        }
        return resp(0, "not found: $path");
    }

    private static function diff($strA, $strB)
    {
        // swap $strA and $strB if needed
        if (strlen($strA) < strlen($strB)) {
            $t = $strA;
            $strA = $strB;
            $strB = $t;
        }
        return substr($strA, strlen($strB));
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

}
