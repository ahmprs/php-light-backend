<?php
class MakePostForm
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
        <title>Make Post</title>

        <script src="$srv/js/jquery.min.js"></script>
        <script src="$srv/js/md5.js"></script>

        <link rel="stylesheet" href="$srv/bootstrap/css/bootstrap.min.css" />
        <script src="$srv/bootstrap/js/bootstrap.min.js"></script>
        <style>
            input[type=text], input[type=number], textarea{
                width:60%;
                padding:5px;
                margin-bottom: 5px;
            }
        </style>
    </head>
    <body style="padding: 10px;">
        <h1 class="alert alert-success">Make Post</h1>

        <h2>MAKE A SIMPLE TEXT POST</h2>
        <input type="text" id="txt_post_title" placeholder="POST TITLE = ?">
        <br>
        <textarea type="text" id="txt_post_text" placeholder="POST TEXT = ?"></textarea>
        <br>
        <input type="number" id="txt_expires" placeholder="EXPIRES AFTER (DAYS) = ?" value='7'>
        <button class="btn btn-primary" onclick='sendPost();'>SEND</button>
        <hr />

        <h2>MAKE A FILE POST</h2>
        <form action="$srv/api/make-post-file" enctype="multipart/form-data" method='post'>
            <input type="text" name="post_title" placeholder='POST TITLE = ?'>
            <br>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <br>
            <input type="number" name="post_expire_days" placeholder="EXPIRES AFTER (DAYS) = ?" value='7'>
            <input class="btn btn-primary" type="submit" value="SEND">
        </form>

        <script>
            function sendPost(){
                var post_title = $('#txt_post_title').val();
                var post_text = $('#txt_post_text').val();
                var post_expire_days = parseInt($('#txt_expires').val());

                if(post_title == '') {
                    alert('missing post title');
                    return;
                }
                if(post_text == '') {
                    alert('missing post text');
                    return;
                }
                if(post_expire_days <= 0) {
                    alert('invalid expire duration');
                    return;
                }

                $.post('$srv/api/make-post-text',{post_title, post_text, post_expire_days},(d,s)=>{
                    try {
                        console.log(d);
                    } catch (err) {
                        console.log(err);
                    }
                });
            }
            </script>
        </body>
    </html>

HTML;
        echo ($html);

    }
}
