<?php

class Settings
{
    public static function getSettings()
    {
        $srv = realpath(__dir__);
        return [
            'database_server_name' => 'localhost',
            'database_username' => 'root',
            'database_password' => '',
            'database_name' => 'db_test',

            'super_user' => 'admin',
            'super_password' => '123',

            'storage_root_directory' => "$srv/Storage",
            'uploads_directory' => "$srv/Storage/uploads",
            'users_directory' => "$srv/Storage/users",

            // 5 MB
            'upload_max_allowed_file_size_bytes' => 5000000,
            'upload_allowed_formats' => ['txt', 'pdf', 'doc', 'docx', 'jpg', 'png', 'gif'],

        ];
    }

    public static function get($key)
    {
        $arr = Settings::getSettings();
        return $arr[$key];
    }
}
