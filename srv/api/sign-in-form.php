<?php
class SignInForm
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
            <title>Sign In</title>

            <script src="./../../../srv/js/jquery.min.js"></script>
            <script src="./../../../srv/js/md5.js"></script>

            <link rel="stylesheet" href="./../../../srv/bootstrap/css/bootstrap.min.css" />
            <script src="./../../../srv/bootstrap/js/bootstrap.min.js"></script>

        </head>
        <body style="padding: 10px;">
            <h1 class="alert alert-success">Sign In</h1>
            <input type="text" id="txt_user_name" placeholder="USER NAME = ?">
            <br>
            <input type="password" id="txt_user_pass" placeholder="USER PASSWORD = ?">
            <br>
            <button onclick='signIn();'>Sign In</button>
            <br>
            <span id='spn_login_state'></span>
            <script>
                function signIn(){
                    $.post('./../../../srv/api/sign-in/new-seed',{},(d,s)=>{
                        try {
                            var seed = d['result'];
                            var user_name = $('#txt_user_name').val();
                            var user_pass = $('#txt_user_pass').val();

                            var user_pass_hash = getMd5(user_pass);
                            var otp = getMd5(user_pass_hash + seed);

                            $.post('./../../../srv/api/sign-in',{user_name, otp},(d,s)=>{
                                console.log(d);
                                try {
                                    if(d['result']['logged_in'])
                                    {
                                        $('#spn_login_state').text('Logged In. '+ d['result']['msg']);
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
}
