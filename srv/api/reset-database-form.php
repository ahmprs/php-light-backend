<?php
class ResetDatabaseForm
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
        <title>Reset Database</title>

        <script src="$srv/js/jquery.min.js"></script>
        <script src="$srv/js/md5.js"></script>

        <link rel="stylesheet" href="$srv/bootstrap/css/bootstrap.min.css" />
        <script src="$srv/bootstrap/js/bootstrap.min.js"></script>

    </head>
    <body style="padding: 10px;">
        <h1 class="alert alert-success">Reset Database</h1>
        <input type="text" id="txt_user_name" placeholder="USER NAME = ?" value='admin'>
        <br>
        <input type="password" id="txt_user_pass" placeholder="USER PASSWORD = ?" value='123'>
        <br>
        <input type="text" id="txt_user_confirm" placeholder="CONFIRM BY TYPING: yes" value='yes'>
        <br>
        <button class="btn btn-primary" onclick='resetDatabase();'>RESET DATABASE</button>
        <br>
        <textarea id="txt_log" cols="30" rows="25" style="width:100%"></textarea>
        <script>
            function resetDatabase(){
                var user_name = $('#txt_user_name').val();
                var user_pass = $('#txt_user_pass').val();
                var confirm = $('#txt_user_confirm').val();

                $.post('$srv/api/reset-database',{user_name, user_pass, confirm},(d,s)=>{
                    try {
                        console.log(d);
                        $('#txt_log').val(JSON.stringify(d, null, 2));

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
