<?php
class Captcha{
    static function Run(){
        session_start();
        $num_str = "" . rand(12345, 98765);
        
        $w = 60;
        $h = 20;
        
        $IMG = imagecreate($w, $h);
        
        $background = imagecolorallocate($IMG, 200, 200, 200);
        $text_color = imagecolorallocate($IMG, 25, 66, 6);
        
        imagesetthickness($IMG, 1);
        
        for ($i = 0; $i < 20; $i++) {
            $line_color = imagecolorallocate($IMG, rand(0, 255), rand(0, 255), rand(0, 255));
            imageline($IMG, rand(0, $w), rand(0, $h), rand(0, $w), rand(0, $h), $line_color);
        }
        
        header("Content-type: image/png");
        
        $p = 10000;
        $num = 0;
        for ($i = 0; $i < 5; $i++) {
        
            $line_color = imagecolorallocate($IMG, rand(0, 255), rand(0, 255), rand(0, 255));
            imageline($IMG, rand(0, $w), rand(0, $h), rand(0, $w), rand(0, $h), $line_color);
        
            $n = rand(1, 9);
            $num += $n * $p;
            $p /= 10;
            $text_color = imagecolorallocate($IMG, rand(10, 100), rand(20, 100), rand(30, 100));
            imagestring($IMG, 5, 5 + $i * 10, 2, $n, $text_color);
        }
        
        
        imagepng($IMG);
        $_SESSION['captcha'] = $num;
        
        
        imagecolordeallocate($IMG, $line_color);
        imagecolordeallocate($IMG, $text_color);
        imagecolordeallocate($IMG, $background);
        imagedestroy($IMG);
        exit;
    }
}

Captcha::Run();