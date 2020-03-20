<?php

// tries to extract a parameter which is sent by user
function par($key)
{
    if (!isset($_REQUEST[$key])) return null;
    else return $_REQUEST[$key];
}


function respAccessDenied(){
    resp(0, 'Access Denied.');
}

// echoes a response to client
function resp($success, $result)
{
    header('Content-type: application/json');
    $r = array(
        "ok" => $success,
        "result" => $result
    );
    $jsn = json_encode($r);
    echo $jsn;
}


// makes a form by given parameters
function makeForm($title, $action, $arrParameters, $arrTypes)
{
    $frm = "";

    $frm .= "<h2 class='heading'>Endpoint GUI</h2>";
    $frm .= "<h3>Please supply the following parameters: </h3>";
    $frm .= "<form method='post' action='$action' class='parameters'>";
    $type = "";
    foreach ($arrParameters as $key => $val) {
        $type = $arrTypes[$key];
        $frm .= "<input name ='$key' type='$type' placeholder='$val' class='inp'>";
        $frm .= "<br />";
    }

    $frm .= "<input type='submit' value='SUBMIT' class='submit'>";
    $frm .= "<input type='hidden' name='gui' value='0'>";
    $frm .= "</form>";
    $frm .= "<hr />";

    $html = <<<HTML

    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>$title</title>
            <style>
                .heading{
                    background-color: #333;
                    color:#abc;
                    padding: 10px 15px;
                    border-radius: 5px;
                }

                h3{
                    font-style: italic;
                }

                .inp{
                    padding:5px 10px;
                    width: 50%;
                }

                .submit{
                    padding:5px 10px;
                }

                .parameters{
                    padding: 10px 10px;
                    background-color: gray;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            $frm
        </body>
    </html>
HTML;


    return $html;
}
