<?php

$srv = realpath (__dir__."../../");
require_once "$srv/settings.php";
require_once "$srv/lib/main.php";

class File {
    public $fileName = '';

    function getContent()
    {
        return file_get_contents($this->fileName);
    }

    function getLines()
    {
        $content = $this->getContent($this->fileName);
        $lines = explode("\n", $content);
        return $lines;
    }
    
    function create()
    {
        $fp = fopen($this->fileName, 'w');
        fclose($fp);
    }

    function setContent($txtContent)
    {
        $fp = fopen($this->fileName, 'w');
        fwrite($fp, $txtContent);
        fclose($fp);
    }
    
    function exists(){
        return file_exists($this->fileName);
    }

    function remove()
    {
        if (file_exists($this->fileName)) {
            $res = unlink($this->fileName);
            if ($res == true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
 }

class Dir{
    public $path = '';

    function make()
    {
        if (!mkdir($this->path, 0700, true)) return 0;
        else return 1;
    }

    function exists(){
        return (is_dir($this->path));
    }

    public function remove($dir=null) {
        if($dir == null) $dir = $this->path;
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->remove("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }    
    // gets all file recursively.
    function getAllFiles($p=null)
    {
        if($p == null) $p = $this->path;
        $result = array();
        $cdir = scandir($p);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($p . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->getAllFiles($p . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }
}

class FS{
    static function file($fileName){
        $f = new File();
        $f->fileName = $fileName;
        return $f;
    }

    static function dir($path){
        $d = new Dir();
        $d->path = $path;
        return $d;
    }

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
        $path = FS::getUsersDirectory();
        $path = "$path/user_$user_id/";
        if (is_dir($path)) return 1;
        else return FS::dir($path)-> make();
    }

    static function getUserDirectory($user_id){
        $path = FS::getUsersDirectory();
        $path = "$path/user_$user_id/";
        if(is_dir($path))return realpath($path);
        else return '';
    }
}

// TESTS:

// resp(1, FS::getStorageRootDirectory());
// resp(1, FS::getUploadsDirectory());
// resp(1, FS::getUsersDirectory());
// resp(1, FS::makeUserDirectory(11));
// resp(1, FS::getUserDirectory(11));

// $u = FS::getUserDirectory(11);
// $f = "$u/test.txt";
// FS::file($f)->setContent("123\n456");
// resp(1, FS::file($f)->getContent());
// resp(1, FS::file($f)->getLines());
// resp(1, FS::file($f)->create());
// resp(1, FS::file($f)->exists());
// FS::file($f)->remove();

// $u = FS::getUserDirectory(11);
// resp(1, FS::dir($u)->exists());
// resp(1, FS::dir("$u/2020-03-21")->make());

// $r = FS::getStorageRootDirectory();
// resp(1, $r);
// resp(1, FS::dir($r)->getAllFiles());

// $u = FS::getUserDirectory(11);
// FS::dir($u)->remove();
