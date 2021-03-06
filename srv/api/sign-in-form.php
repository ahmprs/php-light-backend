<?php
class SignInForm
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
            <title>Sign In</title>

            <script src="$srv/js/jquery.min.js"></script>
            <script src="$srv/js/md5.js"></script>

            <link rel="stylesheet" href="$srv/bootstrap/css/bootstrap.min.css" />
            <script src="$srv/bootstrap/js/bootstrap.min.js"></script>

        </head>
        <body style="padding: 10px;">
            <h1 class="alert alert-success">Sign In</h1>
            <input type="text" id="txt_user_name" placeholder="USER NAME = ?">
            <br>
            <input type="password" id="txt_user_pass" placeholder="USER PASSWORD = ?">
            <br>
            <button class="btn btn-primary" onclick='signIn();'>Sign In</button>
            <br>
            <span id='spn_login_state'></span>
            <br />
            <a id="lnk_sign_out" href="$srv/api/sign-out" class="hide">SIGN OUT</a>

            <script>
                function signIn(){
                    $.post('$srv/api/sign-in/new-seed',{},(d,s)=>{
                        try {
                            var seed = d['result'];
                            var user_name = $('#txt_user_name').val();
                            var user_pass = $('#txt_user_pass').val();

                            var user_pass_hash = getMd5(user_pass);
                            var otp = getMd5(user_pass_hash + seed);

                            $.post('$srv/api/sign-in',{user_name, otp},(d,s)=>{
                                console.log(d);
                                try {
                                    if(d['result']['logged_in'])
                                    {
                                        $('#spn_login_state').text('Logged In. '+ d['result']['msg']);
                                        $('#lnk_sign_out').addClass('hide');
                                    }
                                    else
                                    {
                                        $('#spn_login_state').text(d['result']);
                                        $('#lnk_sign_out').removeClass('hide');
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
