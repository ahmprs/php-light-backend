<?php



class Settings{
    static function getSettings(){
        $srv = realpath (__dir__);
        return [
            'database_server_name'=>'localhost',
            'database_username'=>'root',
            'database_password'=>'',
            'database_name'=>'db_test',

            'super_user'=>'admin',
            'super_password'=>'123',

            'storage_root_directory'=>"$srv/Storage",
            'uploads_directory'=>"$srv/Storage/uploads",
            'users_directory'=>"$srv/Storage/users",

        ]; 
    }

    static function get($key){
        $arr = Settings::getSettings();
        return $arr[$key];
    }
}

?>