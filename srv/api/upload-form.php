<?php
class UploadForm
{
    public static function run()
    {
        $srv_abs = realpath(__dir__ . "../../");
        require_once "$srv_abs/lib/main.php";
        $root = realpath($_SERVER['DOCUMENT_ROOT']);
        $srv = diff($srv_abs, $root);
        $srv = str_replace("\\", "/", $srv);

        $html = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Upload</title>

            <script src="$srv/js/jquery.min.js"></script>
            <script src="$srv/js/md5.js"></script>

            <link rel="stylesheet" href="$srv/bootstrap/css/bootstrap.min.css" />
            <script src="$srv/bootstrap/js/bootstrap.min.js"></script>

        </head>
        <body style="padding: 10px;">

        <h1 class="alert alert-success">Upload</h1>
            <form action="$srv/api/upload" enctype="multipart/form-data" method='post'>
                <input type="file" id="fileToUpload" name="fileToUpload">
                <input type="submit" value="SUBMIT">
            </form>
        </body>
    </html>

HTML;
        echo ($html);
    }

}
