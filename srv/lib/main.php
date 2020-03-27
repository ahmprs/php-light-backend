<?php

// tries to extract a parameter which is sent by user
function par($key)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return null;

    // if (!isset($_REQUEST[$key])) {
    //     return null;
    // } else {
    //     return $_REQUEST[$key];
    // }
}

function parSec($key)
{
    $v = par($key);
    // TODO: check input string
    // and reject it if some threats are
    // detected
    return $v;
}

function respAccessDenied()
{
    resp(0, 'Access Denied.');
}

// echoes a response to client
function resp($success, $result)
{
    header('Content-type: application/json');
    $r = array(
        "ok" => $success,
        "result" => $result,
    );
    $jsn = json_encode($r);
    echo $jsn;
}

function diff($strA, $strB)
{
    // swap $strA and $strB if needed
    if (strlen($strA) < strlen($strB)) {
        $t = $strA;
        $strA = $strB;
        $strB = $t;
    }
    return substr($strA, strlen($strB));
}

// makes a form by given parameters
function makeForm($title, $action, $arrParameters, $arrTypes, $enctype = '')
{
    $srv = realpath(__dir__ . "../../");

    $enc = '';

    if ($enctype != '') {
        $enc = "enctype='$enctype' ";
    }

    $frm = "";

    $frm .= "<h2 class='heading'>Endpoint GUI</h2>";
    $frm .= "<h3>Please supply the following parameters: </h3>";
    $frm .= "<form method='post' action='$action' class='parameters' $enc >";
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
