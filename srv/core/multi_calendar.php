<?php
// global values
$JAL_ORG_GDP = 226894;
$JAL_MAX_GDP = 3652058;



if (isset($_POST['func']))
    if ($_POST['func'] == 'get_server_greg_date_time_as_json')
        echo get_server_greg_date_time_as_json();

if (isset($_POST['func']))
    if ($_POST['func'] == 'get_server_jal_date_time_as_str')
        echo get_server_jal_date_time_as_str();


// TODO: ADD TIME INFO
// TODO: ADD get_server_greg_date_time_as_str() FUNCTION
function get_server_jal_date_time_as_str()
{
    $date = get_server_date_time();
    $gdp = getGdp($date['year'], $date['month'], $date['day_of_month']);
    $jal_date_str = getJalDateStrFrom($gdp);
    return $jal_date_str;
}

function get_server_time()
{
    $date = get_server_date_time();

    $time_str = "";
    $time_str .= str_pad($date['hour'], 2, "0", STR_PAD_LEFT) . ':';
    $time_str .= str_pad($date['min'], 2, "0", STR_PAD_LEFT) . ':';
    $time_str .= str_pad($date['sec'], 2, "0", STR_PAD_LEFT);

    return $time_str;
}


function get_server_jal_date_as_str()
{
    $date = get_server_date_time();
    $gdp = getGdp($date['year'], $date['month'], $date['day_of_month']);
    $jal_date_str = getJalDateStrFrom($gdp);
    return $jal_date_str;
}

function get_server_greg_date_as_str()
{
    $date = get_server_date_time();
    $greg_date_str = "";
    $greg_date_str .= str_pad($date['year'], 4, "0", STR_PAD_LEFT) . '/';          // padLeft($jal_year.toString(), 4, '0') + "/";
    $greg_date_str .= str_pad($date['month'], 2, "0", STR_PAD_LEFT) . '/';         // padLeft($jal_month.toString(), 2, '0') + "/";
    $greg_date_str .= str_pad($date['day_of_month'], 2, "0", STR_PAD_LEFT);        //padLeft($jal_day_of_month.toString(), 2, '0');
    return $greg_date_str;
}


function get_server_greg_date_time_as_str()
{
    $date = get_server_date_time();
    $greg_date_str = "";
    $greg_date_str .= str_pad($date['year'], 4, "0", STR_PAD_LEFT) . '/';          // padLeft($jal_year.toString(), 4, '0') + "/";
    $greg_date_str .= str_pad($date['month'], 2, "0", STR_PAD_LEFT) . '/';         // padLeft($jal_month.toString(), 2, '0') + "/";
    $greg_date_str .= str_pad($date['day_of_month'], 2, "0", STR_PAD_LEFT);        //padLeft($jal_day_of_month.toString(), 2, '0');

    $greg_date_str .= 'T';
    $greg_date_str .= str_pad($date['hour'], 2, "0", STR_PAD_LEFT) . ':';
    $greg_date_str .= str_pad($date['min'], 2, "0", STR_PAD_LEFT) . ':';
    $greg_date_str .= str_pad($date['sec'], 2, "0", STR_PAD_LEFT);

    return $greg_date_str;
}


function get_server_gdp()
{
    $date = get_server_date_time();
    $gdp = getGdp($date['year'], $date['month'], $date['day_of_month']);
    return $gdp;
}


function get_server_gdpp()
{
    $gdp = get_server_gdp();
    $date = get_server_date_time();
    $h = (float) $date['hour'];
    $m = (float) $date['min'];
    $s = (float) $date['sec'];
    $t = ($h * 3600 + $m * 60 + $s) / 68400;
    $gdpp = (float) $gdp + $t;
    return $gdpp;
}


function get_server_greg_date_time_as_json()
{
    $arr = get_server_date_time();
    $json = json_encode($arr);
    return $json;
}

function get_server_date_time()
{
    date_default_timezone_set('Iran');

    $info = getdate();

    $year = $info['year'];
    $month = $info['mon'];
    $day_of_month = $info['mday'];

    $hour = $info['hours'];
    $min = $info['minutes'];
    $sec = $info['seconds'];

    $arr = array(
        'year' => $year,
        'month' => $month,
        'day_of_month' => $day_of_month,
        'hour' => $hour,
        'min' => $min,
        'sec' => $sec
    );
    return $arr;
}

?>



<?php
function getJalDateStrFrom($gdp)
{
    $jal_year = 0;
    $jal_month = 0;
    $jal_day_of_month = 0;
    $jal_day_of_week = 0;
    $rem = 0;
    $jal_year = getJalYear($gdp, $rem);
    $is_leap = IsJalLeapYear($jal_year);

    $rem2 = 0;
    $jal_month = getJalMonth($is_leap, $rem, $rem2);
    $jal_day_of_month = $rem2 + 1;

    $res = "";
    $res .= str_pad($jal_year, 4, "0", STR_PAD_LEFT) . '/';          // padLeft($jal_year.toString(), 4, '0') + "/";
    $res .= str_pad($jal_month, 2, "0", STR_PAD_LEFT) . '/';         // padLeft($jal_month.toString(), 2, '0') + "/";
    $res .= str_pad($jal_day_of_month, 2, "0", STR_PAD_LEFT);  //padLeft($jal_day_of_month.toString(), 2, '0');
    return $res;
}

function getJalYearFrom($gdp)
{
    $jal_year = 0;
    $rem = 0;
    $jal_year = getJalYear($gdp, $rem);
    return $jal_year;
}

function getJalMonthFrom($gdp)
{
    $rem = 0;
    $rem2 = 0;
    $jal_year = getJalYear($gdp, $rem);
    $is_leap = IsJalLeapYear($jal_year);
    $jal_month = getJalMonth($is_leap, $rem, $rem2);
    return $jal_month;
}

function getJalDaysInMonth($gdp)
{
    $rem = 0;
    $jal_year = getJalYear($gdp, $rem);
    $year = getJalYearFrom($gdp);
    $month = getJalMonthFrom($gdp);
    $is_leap = IsJalLeapYear($jal_year);
    $M = array();
    if ($is_leap) $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30);
    else $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
    return $M[$month - 1];
}

function getJalDayOfMonthFrom($gdp)
{
    $rem = 0;
    $rem2 = 0;
    $jal_year = getJalYear($gdp, $rem);
    $is_leap = IsJalLeapYear($jal_year);
    $rem = 0;
    $jal_month = getJalMonth($is_leap, $rem, $rem2);
    $jal_day_of_month = $rem + 1;
    return $jal_day_of_month;
}

function getJalDayOfWeekFrom($gdp)
{
    $jal_day_of_week = floor($gdp + 2) % 7;
    return $jal_day_of_week;
}

function getJalFirstDayOfMonthFrom($gdp)
{
    $g = $gdp;
    $d = getJalDayOfMonthFrom($gdp);
    $g = $g - $d + 1;
    $d = getJalDayOfWeekFrom($g);
    return $d;
}

function getGdp($Year, $Month, $DayOfMonth)
{
    $c = 0;
    $y = 1;
    $d = 0;
    $M = array();

    while (($c + 1) * 100 < $Year) {
        $c++;
        if ($c % 4 == 0) $d += 36525;
        else $d += 36524;
    }

    while ($c * 100 + $y < $Year) {
        if (IsGregLeapYear($c * 100 + $y)) $d += 366;
        else $d += 365;
        $y++;
    }
    $M = array();
    if (IsGregLeapYear($Year)) $M = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    else $M = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    for ($m = 1; $m < $Month; $m++) {
        $d += $M[$m - 1];
    }
    $d += $DayOfMonth;
    return $d - 1;
}


function IsGregLeapYear($AYear)
{
    if ($AYear <= 0) return false;
    if ($AYear % 4 != 0) return false;
    if ($AYear % 100 == 0) {
        if ($AYear % 400 == 0) return true;
        else return false;
    }
    return true;
}


function getJalYear($GregDaysPassed, &$rem)
{
    if ($GregDaysPassed < $GLOBALS['JAL_ORG_GDP']) return -1;
    if ($GregDaysPassed > $GLOBALS['JAL_MAX_GDP']) return -1;

    $d = 0;
    $y = 0;

    $Rem = 0;
    $n = GetJalLeapSequence($GregDaysPassed, $Rem);
    if ($n == 0) $y = 0;
    else $y = $n * 33 - 8;

    $y_days = 0;
    while (true) {
        $y++;
        if (IsJalLeapYear($y)) $y_days = 366;
        else $y_days = 365;

        if ($d + $y_days > $Rem) break;
        $d += $y_days;
    }
    $rem = $Rem - $d;
    return $y;
}


function getJalMonth($IsLeapYear, $YearDaysPassed, &$rem)
{
    if ($YearDaysPassed > 366) return -1;
    if ($YearDaysPassed < 0) return -1;

    $M = array();
    if ($IsLeapYear) $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30);
    else $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    $d = 0;
    $m = 1;
    while ($d + $M[$m - 1] <= $YearDaysPassed) {
        $d += $M[$m - 1];
        $m++;
    }
    $rem = $YearDaysPassed - $d;
    return $m;
}


function GetJalLeapSequence($GregDaysPassed, &$rem)
{
    $JalDaysPassed = $GregDaysPassed - $GLOBALS['JAL_ORG_GDP'];
    $n = floor(($JalDaysPassed + 2922) / 12053);

    if ($n == 0) {
        $rem = $JalDaysPassed;
        return $n;
    } else if ($n == 1) {
        $rem = $JalDaysPassed - 9131;
        return $n;
    } else {
        $rem = $JalDaysPassed - (9131 + ($n - 1) * 12053);
        return $n;
    }
}


function IsJalLeapYear($AJalaliYear)
{
    for ($i = 0; $i < 7; $i++) if (($AJalaliYear - ($i * 4 - 7)) % 33 == 0) return true;
    if (($AJalaliYear - (7 * 4 - 6)) % 33 == 0) return true;
    return false;
}


function jalDateToGdp($Year, $Month, $DayOfMonth)
{

    if (IsValidJalDate($Year, $Month, $DayOfMonth) == false) return null;

    $n = floor(($Year + 7) / 33);
    $y = 0;
    $d = 0;
    $m = 1;
    $M = array();

    if ($n == 0) {
        $d = 0;
        $y = 1;
    } else {
        $d = 9131 + ($n - 1) * 12053;
        $y = $n * 33 - 7;
    }

    while ($y < $Year) {
        if (IsJalLeapYear($y)) $d += 366;
        else $d += 365;
        $y++;
    }

    if (IsJalLeapYear($y)) $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 30);
    else $M = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

    while ($m < $Month) {
        $d += $M[$m - 1];
        $m++;
    }
    $d += $DayOfMonth;
    $d += $GLOBALS['JAL_ORG_GDP'];

    return $d - 1;
}

function jalDateStrToGdp($dateStr)
{
    $year = 0;
    $month = 0;
    $day = 0;

    $is_valid = getDateParts($dateStr, $year, $month, $day);
    if ($is_valid == false) {
        return null;
    } else {
        $gdp = jalDateToGdp($year, $month, $day);
        return $gdp;
    }
}


function IsValidJalDate($JalYear, $JalMonth, $JalDayOfMonth)
{
    if ($JalYear < 0) return false;
    if ($JalYear > 9999) return false;
    if ($JalMonth < 0) return false;
    if ($JalMonth > 12) return false;
    if ($JalDayOfMonth < 0) return false;
    if ($JalDayOfMonth > 31) return false;
    if ($JalMonth > 6 && $JalDayOfMonth > 30) return false;
    $is_leap = IsJalLeapYear($JalYear);
    if ($is_leap && $JalMonth == 12 && $JalDayOfMonth > 29) return false;
    return true;
}

function getDateParts($dateStr, &$year, &$month, &$day)
{
    try {
        $parts = explode('/', trim($dateStr));
        if (count($parts) != 3) return null;
        $year = (int) ($parts[0]);
        $month = (int) ($parts[1]);
        $day = (int) ($parts[2]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// function padLeft($str, $totlalLength, $ch) {
//     $len = count($str);
//     $n = $totlalLength - $len;
//     $res = $str;
//     for ($i = 0; $i < $n; $i++) {
//         $res = $ch . $res;
//     }
//     return $res;
// }
//----------------------------
?> 