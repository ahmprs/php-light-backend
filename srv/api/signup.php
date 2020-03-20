<?php

class Signup{
    static function run(){
        $srv = realpath (__dir__."../../");

        require_once "$srv/lib/main.php";
        require_once "$srv/lib/db.php";
        
        session_start();
        
        $gui = par('gui');
        if ($gui == 1) {
            $arrParameters = [];
        
            $arrParameters["user_name"] = "USER NAME";
            $arrParameters["user_pass"] = "PASSWORD";
        
            $arrTypes["user_name"] = "text";
            $arrTypes["user_pass"] = "password";
        
            $html = makeForm('Signup', './signup.php', $arrParameters, $arrTypes);
            echo ($html);
            return;
        }
        
        
        // extract parameters
        $user_name = par('user_name');
        $user_pass = par('user_pass');
        
        if ($user_name == null) {
            resp(0, 'missing user name');
            return;
        }
        
        if ($user_pass == null) {
            resp(0, 'missing password');
            return;
        }
        
        $sql = "select user_id from tbl_users where user_name = '$user_name'";
        $r = DB::connect()->select($sql);
        $cnt = $r->getNumberOfAffectedRows();

        if ($cnt >= 1) {
            resp(0, 'Username not available');
            return;
        }
        
        $user_active_default = 1;
        $user_access_level_default = 1;
        
        $sql = "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, '$user_name', '$user_pass', '$user_active_default', '$user_access_level_default', '')";
        $r = DB::connect()->insert($sql);
        $cnt = $r->getNumberOfAffectedRows();
        
        if ($cnt != 1) {
            resp(0, 'Database Problem. Unable to signup. Sorry for inconvenience. Please try again later.');
            return;
        }
        
        $user_id = $r->getLastRecordId();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] =  $user_name;
        $_SESSION['user_active'] = $user_active_default;
        $_SESSION['user_access_level'] = $user_access_level_default;
        
        resp(1, [
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_active' => $user_active_default,
            'user_access_level' => $user_access_level_default
        ]);
    }
}

Signup::run();