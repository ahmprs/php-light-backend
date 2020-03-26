<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";
require_once "$srv/settings.php";
require_once "$srv/lib/fs.php";
require_once "$srv/lib/user.php";
require_once "$srv/lib/sec.php";
require_once "$srv/lib/calendar.php";
require_once "$srv/lib/db.php";

class Upload
{
    public static function run()
    {
        $gui = par('gui');
        if ($gui == 1) {
            $arrParameters = [];

            $arrParameters["fileToUpload"] = "";
            $arrTypes["fileToUpload"] = "file";

            $enctype = "multipart/form-data";
            $html = makeForm('File upload', "./upload", $arrParameters, $arrTypes, $enctype);
            echo ($html);
            return;
        }

        $upload_max_allowed_file_size_bytes
        = Settings::get('upload_max_allowed_file_size_bytes');

        $arr_allowed_formats
        = Settings::get('upload_allowed_formats');

        $user_id = User::getUserId();
        if ($user_id == '') {
            return resp(0, [
                'err' => 'access denied',
                'hint' => 'please login first',
            ]);
        }

        $err_msg = "";
        $uploadOk = 1;

        FS::makeUserDirectory($user_id);
        $target_dir = FS::getUserDirectory($user_id);
        if (!FS::dir($target_dir)->exists()) {
            return resp(0, [
                'err' => 'unable to prepare target directory',
            ]);
        }

        $cal = new Calendar();
        $gdp = $cal->get_server_gdp_time();

        $file_uploaded_basic_name = basename($_FILES["fileToUpload"]["name"]);
        $target_file = "$target_dir\\$file_uploaded_basic_name";
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $stamp = Calendar::getStamp();
        $fnn = "$stamp.$file_extension";
        $file_new_name = "$target_dir\\$fnn";
        $file_tmp_name = $_FILES["fileToUpload"]["tmp_name"];

        // IMAGE CHECK SIZE
        // if (isset($_POST["submit"])) {
        //     $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        //     if ($check !== false) {
        //         // echo "File is an image - " . $check["mime"] . ".";
        //         $uploadOk = 1;
        //     } else {
        //         $err_msg = "File is not an image.";
        //         $uploadOk = 0;
        //     }
        // }

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
                'maximum_allowed_file_size_bytes' => $upload_max_allowed_file_size_bytes,
            ]);
        }

        // check file extension
        if (!in_array($file_extension, $arr_allowed_formats)) {
            return resp(0, [
                'err' => 'extension not allowed',
                'extension' => $file_extension,
                'allowed_formats' => $arr_allowed_formats,
            ]);
        }

        // move temp file to destination
        if (move_uploaded_file($file_tmp_name, $file_new_name)) {

            $sql = "INSERT INTO `tbl_uploads` (`file_id`, `user_id`, `file_org_name`, `file_new_name`,`file_size_bytes`, `file_target_dir`, `file_extension`) VALUES (NULL, '$user_id', '$file_uploaded_basic_name', '$fnn','$file_size_bytes', '$target_dir', '$file_extension')";
            $r = DB::connect()->insert($sql);

            resp(1, [
                'ok' => 'success',
                'user_id' => $user_id,
                'file_uploaded_basic_name' => $file_uploaded_basic_name,
                'file_tmp_name' => $file_tmp_name,
                'file_new_name' => $fnn,
                'file_extension' => $file_extension,
                'file_size_bytes' => $file_size_bytes,
                'target_dir' => $target_dir,
                'allowed_formats' => $arr_allowed_formats,
                'database_insertion' => $r->getNumberOfAffectedRows(),
            ]);
        } else {
            resp(0, [
                'err' => 'file copy to destination folder failed.',
                'file_tmp_name' => $file_tmp_name,
                'file_new_name' => $file_new_name,
            ]);
        }
    }
}

// How to use:
// call the page in url with switch gui=1
// Example:
// http://localhost/1-WebApps/php-light-backend/php-light-backend/srv/api/upload?gui=1
