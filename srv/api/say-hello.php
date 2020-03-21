<?php
    $srv = realpath (__dir__."../../");
    require_once "$srv/lib/main.php";
    
    class SayHello{
        static function run(){
            
            $gui = par('gui');
            if ($gui == 1) {
                $arrParameters = [];
            
                $arrParameters["user_name"] = "YOUR NAME PLEASE";
                $arrParameters["user_age"] = "AGE";
                $arrParameters["user_weight"] = "WEIGHT";
            
                $arrTypes["user_name"] = "text";
                $arrTypes["user_age"] = "number";
                $arrTypes["user_weight"] = "number";
            
                $html = makeForm('Say Hello', "./say-hello", $arrParameters, $arrTypes);
                echo ($html);
                return;
            }
            
            
            // extract parameters
            $user_name = par('user_name');
            $user_age = par('user_age');
            $user_weight = par('user_weight');
            
            if ($user_name == null) return resp(0, 'missing user name');
            
            
            // run the logic
            $arr = [];
            $arr['user_name'] = $user_name;
            $arr['user_age'] = $user_age;
            $arr['user_weight'] = $user_weight;
            
            resp(1, $arr);
        }        
    }
    // TEST ONLY:    
    // SayHello::run();