<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/main.php";

class ReadNumber
{
    public static function nar($num)
    {
        $Pr = array('', 'هزار', 'ميليون', 'ميليارد', 'تريليون');
        $arr = array();
        $ts = ReadNumber::getTriple($num);
        $n = count($ts);
        $and_char = ' و ';

        for ($i = 0; $i < $n; $i++) {
            $n3 = ReadNumber::nar3($ts[$i]);
            if (strcmp($n3['str'], '') == 0) {
                continue;
            }

            if (count($arr) > 0) {
                $arr[] = $and_char;
            }

            $arr[] = $n3['str'];
            $m = count($n3['arr']);
            $t = '';
            for ($j = 0; $j < $m; $j++) {
                $t .= trim($n3['arr'][$j]);
            }
            if (strcmp($t, '') != 0) {
                $prfx = $Pr[$n - $i - 1];
                if (strcmp(trim($prfx), '') == 0) {
                    continue;
                }

                $arr[] = $prfx;
            }
        }

        $n = count($arr);
        $resp = '';
        $prt = '';
        for ($i = 0; $i < $n; $i++) {
            $prt = $arr[$i];
            if (strcmp($prt, '') == 0) {
                continue;
            }

            $resp .= $prt . ' ';
        }

        return $resp;
    }
// ====================================

    public static function getDigits3($num)
    {
        $n = $num;
        $d3 = floor($n / 100);
        $n = $n % 100;
        $d2 = floor($n / 10);
        $d1 = $n % 10;
        return array($d1, $d2, $d3);
    }

    public static function getTriple($num)
    {
        $num_str = '' . $num;
        if (strlen($num_str) > 15) {
            return;
        }

        while (strlen($num_str) < 15) {
            $num_str = '0' . $num_str;
        }

        $c1 = (int) substr($num_str, 12, 3);
        $c2 = (int) substr($num_str, 9, 3);
        $c3 = (int) substr($num_str, 6, 3);
        $c4 = (int) substr($num_str, 3, 3);
        $c5 = (int) substr($num_str, 0, 3);
        return array($c5, $c4, $c3, $c2, $c1);
    }

    public static function nar3($num)
    {

        $A1 = array('', 'يک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه');
        $A2 = array('', '', 'بيست', 'سي', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود');
        $A3 = array('', 'صد', 'دويست', 'سيصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد');
        $Ae = array('ده', 'يازده', 'دوازده', 'سيزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده');

        $d = ReadNumber::getDigits3($num);
        $and_char = ' و ';

        $arr = null;
        if ($d[1] != 1) {
            $arr = array(
                $A3[$d[2]],
                $A2[$d[1]],
                $A1[$d[0]],
            );
        } else {
            $arr = array(
                $A3[$d[2]],
                $Ae[$d[0]],
            );
        }

        $n = count($arr);
        $prt = '';
        $arr2 = array();

        for ($i = 0; $i < $n; $i++) {
            $prt = $arr[$i];
            if (strcmp($prt, '') == 0) {
                continue;
            }

            $arr2[] = $prt;
        }

        $resp = '';
        $n = count($arr2);
        $prt = '';
        for ($i = 0; $i < $n; $i++) {
            $prt = $arr2[$i];
            if ($i > 0) {
                $resp .= $and_char;
            }

            $resp .= $prt;
        }
        return array("str" => $resp, "arr" => $arr);
    }
}

// TESTS:
// resp(1, ReadNumber::nar(123456789));
