<?php
class ChangePasswordForm
{
    public static function run()
    {

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Sign In</title>

        <script src="./../../../srv/js/jquery.min.js"></script>
        <script src="./../../../srv/js/md5.js"></script>

        <link rel="stylesheet" href="./../../../srv/bootstrap/css/bootstrap.min.css" />
        <script src="./../../../srv/bootstrap/js/bootstrap.min.js"></script>

    </head>
    <body style="padding: 10px;">
        <h1 class="alert alert-success">Change Password</h1>
        <input type="password" id="txt_user_pass" placeholder="NEW PASSWORD = ?">
        <br>
        <button class="btn btn-primary" onclick='changePassword();'>Change Password</button>
        <br>
        <span id='spn_login_state'></span>
        <script>
            function changePassword(){
                $.post('./../../../srv/api/sign-in/new-seed',{},(d,s)=>{
                    try {
                        var user_name = $('#txt_user_name').val();
                        var user_pass = $('#txt_user_pass').val();
                        var user_pass_hash = getMd5(user_pass);

                        $.post('./../../../srv/api/change-password',{user_name, user_pass_hash},(d,s)=>{
                            console.log(d);
                            try {
                                if(d['ok'] == 1)
                                {
                                    $('#spn_login_state').text(d['result']);
                                }
                                else
                                {
                                    $('#spn_login_state').text(d['result']);
                                }
                            } catch (err) {
                                console.log(err);
                            }
                        });

                    } catch (err) {

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
