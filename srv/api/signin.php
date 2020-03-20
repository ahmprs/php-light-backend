<?php
class SignIn{
    static function run(){
        $srv = realpath (__dir__."../../");

        require_once "$srv/lib/main.php";
        require_once "$srv/lib/db.php";
        
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['user_id'] != "") {
                resp(0, 'Already Logged in');
                return;
            }
        }
        
        $gui = par('gui');
        if ($gui == 1) {
            $arrParameters = [];
        
            $arrParameters["user_name"] = "USER NAME";
            $arrParameters["user_pass"] = "PASSWORD";
        
            $arrTypes["user_name"] = "text";
            $arrTypes["user_pass"] = "password";
        
            $html = makeForm('Signup', './signin.php', $arrParameters, $arrTypes);
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
        
        
        $sql = "select user_id, user_name, user_active, user_access_level from tbl_users where user_name = '$user_name' and user_pass = '$user_pass' ";
        $r = DB::connect()->select($sql);
        $cnt = $r->getNumberOfAffectedRows();
        $users =  $r->getRecords();
        if($cnt == 1){
            $u = $users[0];
            $_SESSION['user_id'] = $u['user_id'];
            $_SESSION['user_name'] = $u['user_name'];
            $_SESSION['user_active'] = $u['user_active'];
            $_SESSION['user_access_level'] = $u['user_access_level'];
            resp(1,['logged_in'=>true, 'msg'=>'welcome '.$u['user_name'], $u]);
        }
        else{
            respAccessDenied();
        }
    }    
}


SignIn::run();