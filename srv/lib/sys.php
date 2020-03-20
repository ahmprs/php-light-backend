<?php
function readAllText($fileName)
{
    return "";
}

function writeAllText($fileName, $txtContent)
{
}


function removeFile($fileName)
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

function makeDir($path)
{
    if (!mkdir($path, 0700, true)) return 0;
    else return 1;
}

function removeDir($path)
{
    if (!rmdir($path)) return 0;
    else return 1;
}

// gets all file recursively.
function getAllFiles($dir)
{
    $result = array();
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = getAllFiles($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $result[] = $value;
            }
        }
    }
    return $result;

    // USAGE:
    // require_once __DIR__ . "/core/sys.php";
    // require_once __DIR__ . "/core/main.php";
    // $arr = getAllFiles(__DIR__);
    // resp(1, $arr);
    //-----------------------------------------

}



/*
    opendir() - Open directory handle
    readdir() - Read entry from directory handle
    glob() - Find pathnames matching a pattern
    is_dir() - Tells whether the filename is a directory
    sort() - Sort an array
*/
