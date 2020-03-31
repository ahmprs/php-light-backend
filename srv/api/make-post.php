<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/dbs.php";
require_once "$srv/lib/fs.php";
require_once "$srv/lib/calendar.php";
require_once "$srv/settings.php";

class MakePost
{
    public static function makePostText()
    {
        MakePost::accessCheck();
        $post_title = par('post_title');
        $post_text = par('post_text');
        $post_expire_days = (int) (par('post_expire_days'));

        $c = new Calendar();
        $gdp = $c->get_server_gdp();
        $post_create_gdp = $gdp;
        $post_expire_gdp = $gdp + $post_expire_days;

        $stamp = Calendar::getStamp();
        $post_owner_id = User::getUserId();
        $posts = Settings::get('posts_directory');

        $post_owner_id_str = "$post_owner_id";
        $post_owner_id_str = str_pad($post_owner_id_str, 5, "0", STR_PAD_LEFT);
        $post_file_name = "$posts/$stamp-$post_owner_id_str.txt";

        $f = new File();
        $f->fileName = $post_file_name;
        $f->setContent($post_text);

        $sql = "
        INSERT INTO `tbl_posts`(
            `post_id`,
            `post_title`,
            `post_owner_id`,
            `post_audit_id`,
            `post_parent_id`,
            `post_text`,
            `post_access_level`,
            `post_file_name`,
            `post_file_ext`,
            `post_create_gdp`,
            `post_expire_gdp`,
            `post_desc`
            )
            VALUES(NULL,?,?,'0','0',?,'0',?,'txt',?,?,'');
            ";

        $r = DBS::insert($sql, 'sissii', [
            $post_title,
            $post_owner_id,
            $post_text,
            pathinfo(realpath($post_file_name), PATHINFO_FILENAME),
            $post_create_gdp,
            $post_expire_gdp,
        ]);
        return resp(1, [$post_title, $post_text, $post_expire_gdp, $post_file_name, $sql, $r]);
    }

    public static function makePostFile()
    {
        MakePost::accessCheck();

        $post_title = par('post_title');
        $post_expire_days = (int) (par('post_expire_days'));

        $c = new Calendar();
        $gdp = $c->get_server_gdp();
        $post_create_gdp = $gdp;
        $post_expire_gdp = $gdp + $post_expire_days;

        $stamp = Calendar::getStamp();
        $post_owner_id = User::getUserId();
        $posts = Settings::get('posts_directory');

        $post_owner_id_str = "$post_owner_id";
        $post_owner_id_str = str_pad($post_owner_id_str, 5, "0", STR_PAD_LEFT);

        //----
        $upload_max_allowed_file_size_bytes
        = Settings::get('upload_max_allowed_file_size_bytes');

        $arr_allowed_formats
        = Settings::get('upload_allowed_formats');

        $target_dir = Settings::get('posts_directory');

        $file_uploaded_basic_name = basename($_FILES["fileToUpload"]["name"]);
        $target_file = "$target_dir\\$file_uploaded_basic_name";
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $post_file_name = "$posts/$stamp-$post_owner_id_str.$file_extension";
        $file_new_name = $post_file_name;
        $file_tmp_name = $_FILES["fileToUpload"]["tmp_name"];

        // checks if file already exists
        if (file_exists($file_new_name)) {
            return resp(0, [
                'err' => 'file already exists',
                'hint' => 'try again in a few seconds',
            ]);
        }

        // check file size
        $file_size_bytes = $_FILES["fileToUpload"]["size"];
        if ($file_size_bytes > $upload_max_allowed_file_size_bytes) {
            return resp(0, [
                'err' => 'file is too large',
                'file_size_bytes' => $file_size_bytes,
                'ttt' => $_FILES["fileToUpload"]["name"],
            ]);
        }

        // check file extension
        if (!in_array($file_extension, $arr_allowed_formats)) {
            return resp(0, [
                'err' => 'extension not allowed',
                'extension' => $file_extension,
                'allowed_formats' => $arr_allowed_formats,
                'file_uploaded_basic_name' => $file_uploaded_basic_name,
            ]);
        }

        // move temp file to destination
        if ($file_extension == 'pdf') {
            $file_new_name .= 'f';
            $file_extension .= 'f';
            $post_file_name .= "f";
        }

        if (move_uploaded_file($file_tmp_name, $file_new_name)) {
            // insert record here
            $sql = "
            INSERT INTO `tbl_posts`(
                `post_id`,
                `post_title`,
                `post_owner_id`,
                `post_audit_id`,
                `post_parent_id`,
                `post_text`,
                `post_access_level`,
                `post_file_name`,
                `post_file_ext`,
                `post_create_gdp`,
                `post_expire_gdp`,
                `post_desc`
                )
                VALUES(NULL, ?, ?, '0', '0', '', '0', ?, ?, ?, ?, '');
                ";

            $r = DBS::insert($sql, 'sissii', [
                $post_title,
                $post_owner_id,
                pathinfo(realpath($post_file_name), PATHINFO_FILENAME),
                $file_extension,
                $post_create_gdp,
                $post_expire_gdp,
            ]);
            return resp(1, [
                'post_title' => $post_title,
                'post_text' => $post_text,
                'post_expire_gdp' => $post_expire_gdp,
                'post_file_name' => $post_file_name,
                'post_file_name' => $sql,
                'db_result' => $r,
            ]);
        } else {
            resp(0, [
                'err' => 'file copy to destination folder failed.',
                'file_tmp_name' => $file_tmp_name,
                'file_new_name' => $file_new_name,
            ]);
        }
        //----
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

}
