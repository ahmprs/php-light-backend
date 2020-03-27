<?php
class SignUpForm
{
    public static function run()
    {
        {
            $html = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Sign Up</title>

            <script src="./../../../srv/js/jquery.min.js"></script>
            <script src="./../../../srv/js/md5.js"></script>

            <link rel="stylesheet" href="./../../../srv/bootstrap/css/bootstrap.min.css" />
            <script src="./../../../srv/bootstrap/js/bootstrap.min.js"></script>

        </head>
        <body style="padding: 10px;">
            <h1 class="alert alert-success">Sign Up</h1>
            <input type="text" id="txt_user_name" placeholder="USER NAME = ?">
            <br>
            <input type="password" id="txt_user_pass" placeholder="USER PASSWORD = ?">
            <br>
            <button onclick='signUp();'>Sign Up</button>
            <br>
            <span id='spn_login_state'></span>
            <script>
                function signUp(){
                    var user_name = $('#txt_user_name').val();
                    var user_pass = $('#txt_user_pass').val();
                    var user_pass_hash = getMd5(user_pass);
                    $.post('./../../../srv/api/sign-up',{user_name, user_pass_hash},(d,s)=>{
                        console.log(d);
                        try {
                            if(d['ok']==1){
                                $('#spn_login_state').text ('Welcome ' + d['result']['user_name']);
                            }
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
}
