<?php
    // Test cases here
    $srv = realpath (__dir__."../../");
    require_once "$srv/lib/main.php";
    resp(1, 'this is a test');
?>