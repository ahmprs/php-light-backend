<?php
class Settings{
    static function getSettings(){
        return [
            'database_server_name'=>'localhost',
            'database_username'=>'root',
            'database_password'=>'',
            'database_name'=>'db_test',

            'super_user'=>'admin',
            'super_password'=>'123'
        ]; 
    }

    static function get($key){
        $arr = Settings::getSettings();
        return $arr[$key];
    }
}

?>