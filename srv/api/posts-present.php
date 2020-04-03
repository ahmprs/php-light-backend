<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";

class PostsPresent
{
    public static function run($file_name, $extension, $content)
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
