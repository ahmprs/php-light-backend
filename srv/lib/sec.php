<?php

class Sec{
    static function getNthOrderHash($str, $cnt, $order)
    {
        $h = $str;
        for ($i = 0; $i < $order; $i++) {
            $h = Sec::getStrHash($h, $cnt);
        }
        return $h;
    }

    static function getStrHash($str, $cnt)
    {
        $s = Sec::getSeed($str);
        $p = array(2, 3, 5, 7, 11, 13, 17, 19, 23, 29);
        $res = "";
        $b = 0;
    
        for ($i = 0; $i < $cnt; $i++) {
            $s = $s * $p[$i % 10];
            $s++;
            $s = $s % 65536;
            $b = $s % 72;
            // if(($i>0) && ($i%4==0)) $res.='-';
            $res .= Sec::getChar($b);
        }
        return $res;
    }
    
    static function getHex($b)
    {
        $hx = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $low = $b & 0x0f;
        $high = $b & 0xf0;
        $high = $high >> 4;
        return $hx[$high] . "" . $hx[$low] . "";
    }
    
    static function getChar($indx)
    {
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~!@#$%^&*_";
        return $str{
            $indx};
    }
    
    
    static function getSeed($str)
    {
        $arr = Sec::getArrFromStr($str);
        $n = count($arr);
        $s = 0;
    
        for ($i = 0; $i < $n; $i++) {
            $s += $arr[$i] * $i;
            $s = ($s % 65536);
        }
        return $s;
    }
    
    static function getArrFromStr($str)
    {
        $arr = array();
        for ($i = 0; $i < strlen($str); $i++) {
            $charcode = ord($str[$i]);
            $arr[] = $charcode;
        }
        return $arr;
    }

    // from: https://stackoverflow.com/questions/3422759/php-aes-encrypt-decrypt
    static function encrypt($plaintext, $password)
    {
        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);

        return $iv . $hash . $ciphertext;
    }


    // from: https://stackoverflow.com/questions/3422759/php-aes-encrypt-decrypt
    static function decrypt($ivHashCiphertext, $password)
    {
        $method = "AES-256-CBC";
        $iv = substr($ivHashCiphertext, 0, 16);
        $hash = substr($ivHashCiphertext, 16, 32);
        $ciphertext = substr($ivHashCiphertext, 48);
        $key = hash('sha256', $password, true);

        if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) return null;

        return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    static function getHash($alg, $inpStr)
    {
        return hash($alg, $inpStr);
    }
}


    // ALGORITHM     SIZE
    //-------------+-----
    // md2           32 
    // md4           32 
    // md5           32 
    // sha1          40 
    // sha256        64 
    // sha384        96 
    // sha512       128 
    // ripemd128     32 
    // ripemd160     40 
    // ripemd256     64 
    // ripemd320     80 
    // whirlpool    128 
    // tiger128,3    32 
    // tiger160,3    40 
    // tiger192,3    48 
    // tiger128,4    32 
    // tiger160,4    40 
    // tiger192,4    48 
    // snefru        64 
    // gost          64 
    // adler32        8 
    // crc32          8 
    // crc32b         8 
    // haval128,3    32 
    // haval160,3    40 
    // haval192,3    48 
    // haval224,3    56 
    // haval256,3    64 
    // haval128,4    32 
    // haval160,4    40 
    // haval192,4    48 
    // haval224,4    56 
    // haval256,4    64 
    // haval128,5    32 
    // haval160,5    40 
    // haval192,5    48 
    // haval224,5    56 
    // haval256,5    64 



