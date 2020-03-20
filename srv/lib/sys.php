<?php

$srv = realpath (__dir__."../../");
require_once "$srv/settings.php";
// require_once "$srv/lib/main.php";


class Sys{

    static function getStorageRootDirectory(){
        return realpath(Settings::get('storage_root_directory'));
    }

    static function getUploadsDirectory(){
        return realpath(Settings::get('uploads_directory'));
    }
    
    static function getUsersDirectory(){
        return realpath(Settings::get('users_directory'));
    }

    static function makeUserDirectory($user_id){
        $path = Sys::getUsersDirectory();
        $path = "$path/user_$user_id/";
        return Sys::makeDir($path);
    }

    static function getUserDirectory($user_id){
        $path = Sys::getUsersDirectory();
        $path = "$path/user_$user_id/";
        if(is_dir($path))return realpath($path);
        else return '';
    }

    static function readAllText($fileName)
    {
        return file_get_contents($fileName);
    }

    static function readAllLines($fileName)
    {
        $content = Sys::readAllText($fileName);
        $lines = explode("\n", $content);
        return $lines;
    }
    
    static function writeAllText($fileName, $txtContent)
    {
        $fp = fopen($fileName, 'w');
        fwrite($fp, $txtContent);
        fclose($fp);
    }
    
    
    static function removeFile($fileName)
    {
        if (file_exists($fileName)) {
            $res = unlink($fileName);
            if ($res == true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    
    static function makeDir($path)
    {
        if (!mkdir($path, 0700, true)) return 0;
        else return 1;
    }
    
    static function removeDir($path)
    {
        if (!rmdir($path)) return 0;
        else return 1;
    }
    
    // gets all file recursively.
    static function getAllFiles($dir)
    {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = Sys::getAllFiles($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
}