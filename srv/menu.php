<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links</title>
    <style>
        a {
            font-size: 32px;
            text-decoration: none;
            display: block;
        }

        a:hover {
            background-color: #def;
        }
    </style>
</head>

<body>
    <h1>Server Endpoints Menu</h1>
    <a href="./signin.php?gui=1" target="_blank">sign in</a>
    <a href="./signout.php?gui=1" target="_blank">sign out</a>
    <a href="./signup.php?gui=1" target="_blank">sign up</a>
    <a href="./say-hello.php?gui=1" target="_blank">say hello</a>

    <?php
    require_once __DIR__ . "/core/user.php";
    if (getUserId() == -1) {
        echo ('<hr />');
        echo ('<a href="./core/ddl.php" target="_blank">Reset Database</a>');
        echo ('<hr />');
    }
    ?>
</body>

</html>