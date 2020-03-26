<?php
// Test cases here
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";

class Test
{
    public static function run()
    {
        echo (makePage());
        // resp(1, makePage());
    }
}
