<?php
    $srv = realpath (__dir__."../../");
    require_once "$srv/lib/main.php";

    resp(1, 'this is a test');

    // require_once "$srv/lib/db.php";
    // require_once "$srv/database/schema.php";
    // require_once "$srv/database/seed.php";
    
    // $id = DB::connect()
    // ->insert("INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_access_level`, `user_active`, `user_desc`) VALUES (NULL, 'Pit', '123', '1', '1', 'desc')")
    // ->getLastRecordId();

    // $arr_rec = DB::connect()
    // ->select('select * from tbl_users')
    // ->getRecords();

    // resp(1, $arr_rec);
    // $arr = Schema::Run();
    // $brr = Seed::Run();
    // resp(1, [$arr, $brr]);
?>