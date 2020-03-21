<?php

$srv = realpath (__dir__."../../");
require_once "$srv/lib/main.php";

class Sec{
    
    // $format: 'h' hexadecimal
    // $format: '1' just numbers
    // $format: '1a' or 'a1' (order doesn't care) numbers and lower case characters
    // $format: '1aA' (orders don't matter) numbers, lower case and upper case characters
    // $format: '~' ~!@#$%^&*_
    static function getNthOrderHash($str, $cnt, $order=3, $format='h'/*h1aA~*/)
    {
        $h = $str;
        for ($i = 0; $i < $order; $i++) {
            $h = Sec::getStrHash($h, $cnt, $format);
        }
        return $h;
    }

    static function getStrHash($str, $cnt, $format)
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
            $res .= Sec::getChar($b, $format);
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
    
    static function getChar($indx, $format)
    {   
        $f = " $format";
        $str = '';
        if (strpos($f, "h")) $str .= "0123456789abcdef";
        if (strpos($f, "1")) $str .= "0123456789";
        if (strpos($f, "a")) $str .= "abcdefghijklmnopqrstuvwxyz";
        if (strpos($f, "A")) $str .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if (strpos($f, "~")) $str .= "~!@#$%^&*_";
        
        $indx = $indx % strlen($str);
        return $str{$indx};
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


// TESTS:

// resp (1, Sec::getNthOrderHash("hello", 64, 5, 'a1'));
// xc94dej2j49cxspebevkp4901uj63k5k1sly36zcpol81y3yzwt05operezwdoh4

// resp (1, Sec::getHash("md5", "hello", 10));
// 5d41402abc4b2a76b9719d911017c592

// resp (1, Sec::getHash("sha1", "hello", 10));
// aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d

// resp (1, Sec::getHash("gost", "hello", 10));
// a7eb5d08ddf2363f1ea0317a803fcef81d33863c8b2f9f6d7d14951d229f4567

// resp (1, Sec::getHash("sha256", "hello", 10));
// 2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824

// resp (1, Sec::getHash("sha512", "hello", 10));
// 9b71d224bd62f3785d96d46ad3ea3d73319bfbc2890caadae2dff72519673ca72323c3d99ba5c11d7c7acc6e14b8c5da0c4663475c2e5c3adef46f73bcdec043
