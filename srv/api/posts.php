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
        $fn = Posts::diff($path, '/posts/');
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

                    return Posts::present($f, $e, $content);

                    // return resp(1, [
                    //     'pst' => $pst,
                    //     // 'pfe' => $pfe,
                    //     'arr_content' => $content,
                    // ]);
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

    private static function present($file_name, $extension, $content)
    {
        $srv_abs = realpath(__dir__ . "../../");
        require_once "$srv_abs/lib/main.php";
        $root = realpath($_SERVER['DOCUMENT_ROOT']);
        $srv = diff($srv_abs, $root);
        $srv = str_replace("\\", "/", $srv);

        $url_doc = "$srv/storage/posts/$file_name.$extension";

        // present text posts
        if (in_array($extension, ['txt'])) {
            // present test
            $content = "<p>$content</p>";
        }

        // present doc or docx
        else if (in_array($extension, ['docx'])) {
            // users can download the document with the following link
            $content = "<a href='$url_doc'>Download Link </a>";

            // TODO:
            // present content...
        } else if (in_array($extension, ['pdff'])) {
            // NOTE:
            // head over to the following url to get more
            // https://pspdfkit.com/blog/2018/render-pdfs-in-the-browser-with-pdf-js/

            // $content = "
            // <iframe src='$srv/viewer/web/viewer.html?file=$url_doc'
            // style='border: none; width:100%' />
            // ";

            $content = "
            <embed id='emb_pdf' src='$srv/viewer/web/viewer.html?file=$url_doc' type='text/html'
            style='border: none; width:100%;' >
            ";

        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Present Post</title>

        <script src="$srv/js/jquery.min.js"></script>
        <script src="$srv/js/md5.js"></script>
        <link rel="stylesheet" href="$srv/bootstrap/css/bootstrap.min.css" />
        <script src="$srv/bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body style="padding: 10px;" onload="init();">
        <h1 class="alert alert-success">USER POST</h1>
        $content
        <script>
            function init(){
                var emb_pdf = document.getElementById('emb_pdf');
                if(emb_pdf==null) return;
                emb_pdf.style.height = (window.innerHeight-150)+'px';
                // console.log(emb_pdf);
                // debugger;
            }

        </script>
    </body>
    </html>

HTML;
        echo ($html);
    }
}
