<?php

require_once __DIR__ . "/db_info.php";


function runSql($sql, $getJson, $db_root)
{
    $cnState = 0;
    $recCnt = 0;

    // Create connection

    if ($db_root) {
        $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], "");
    } else {
        $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);
    }

    // Check connection
    if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        $cnState = 0;
    } else {
        $cnState = 1;

        // IMPORTANT:
        // Support for utf-8:
        // ---------------------------------------------------------
        $conn->query("set character_set_client='utf8'");
        $conn->query("set collation_connection='utf8_general_ci'");
        $conn->query("set character_set_results='utf8'");

        // mysql_set_charset('utf8', $conn);
        // mysqli_set_charset($link, "utf8");
        // $q = mysql_set_charset('utf8');
        // var_dump($q);
        // ---------------------------------------------------------

        if ($conn->query($sql) === TRUE) {
            $recCnt = 1;
        } else {
            $recCnt = 0;
        }

        $conn->close();
    }

    $data = array(
        'cnState' => $cnState,
        'recCnt' => $recCnt
    );

    if ($getJson) {
        $str_json = json_encode($data);
        return $str_json;
    } else return $data;
}


function select($sql, $getJson)
{
    $cnState = 0;
    $recCnt = 0;
    $tbl = array();

    // Create connection
    $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);

    // Check connection
    if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        $cnState = 0;
    } else {
        $cnState = 1;

        // IMPORTANT:
        // Support for utf-8:
        // ---------------------------------------------------------
        // $conn->query("set character_set_client='utf8'");
        // $conn->query("set collation_connection='utf8_general_ci'");
        // $conn->query("set character_set_results='utf8'");
        // ---------------------------------------------------------
        //---
        $conn->query("set character_set_client='utf8'");
        $conn->query("set collation_connection='utf8_general_ci'");
        $conn->query("set character_set_results='utf8'");
        //---

        $result = $conn->query($sql);
        $recCnt = $result->num_rows;

        if ($recCnt > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($tbl, $row);
            }
        }
        $conn->close();
    }

    $data = array(
        'cnState' => $cnState,
        'recCnt' => $recCnt,
        'tbl' => $tbl
    );

    if ($getJson) {
        $str_json = json_encode($data);
        return $str_json;
    } else return $data;
}

function insert($sql, $getJson)
{
    $cnState = 0;
    $recCnt = 0;
    $last_rec_id = 0;

    // Create connection
    $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);

    // Check connection
    if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        $cnState = 0;
    } else {
        $cnState = 1;

        // IMPORTANT:
        // Support for utf-8:
        // ---------------------------------------------------------
        $conn->query("set character_set_client='utf8'");
        $conn->query("set collation_connection='utf8_general_ci'");
        $conn->query("set character_set_results='utf8'");

        // mysql_set_charset('utf8', $conn);
        // mysqli_set_charset($link, "utf8");
        // $q = mysql_set_charset('utf8');
        // var_dump($q);
        // ---------------------------------------------------------

        if ($conn->query($sql) === TRUE) {
            $recCnt = 1;
            $last_rec_id = $conn->insert_id;
        } else {
            $recCnt = 0;
        }

        $conn->close();
    }

    $data = array(
        'cnState' => $cnState,
        'recCnt' => $recCnt,
        'last_rec_id' => $last_rec_id
    );

    if ($getJson) {
        $str_json = json_encode($data);
        return $str_json;
    } else return $data;
}


// update
function update($sql, $getJson)
{
    $cnState = 0;
    $recCnt = 0;
    $tbl = array();

    // Create connection
    $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);

    // Check connection
    if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        $cnState = 0;
    } else {
        $cnState = 1;

        // IMPORTANT:
        // Support for utf-8:
        // ---------------------------------------------------------
        $conn->query("set character_set_client='utf8'");
        $conn->query("set collation_connection='utf8_general_ci'");
        $conn->query("set character_set_results='utf8'");

        // mysql_set_charset('utf8', $conn);
        // mysqli_set_charset($link, "utf8");
        // $q = mysql_set_charset('utf8');
        // var_dump($q);
        // ---------------------------------------------------------

        if ($conn->query($sql) === TRUE) {
            $recCnt = 1;
        } else {
            $recCnt = 0;
        }

        $conn->close();
    }

    $data = array(
        'cnState' => $cnState,
        'recCnt' => $recCnt
    );

    if ($getJson) {
        $str_json = json_encode($data);
        return $str_json;
    } else return $data;
}


// delete
function delete($sql, $getJson)
{
    $cnState = 0;
    $recCnt = 0;
    $tbl = array();

    // Create connection
    $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);

    // Check connection
    if ($conn->connect_error) {
        // die("Connection failed: " . $conn->connect_error);
        $cnState = 0;
    } else {
        $cnState = 1;
        if ($conn->query($sql) === TRUE) {
            $recCnt = 1;
        } else {
            $recCnt = 0;
        }

        $conn->close();
    }

    $data = array(
        'cnState' => $cnState,
        'recCnt' => $recCnt
    );

    if ($getJson) {
        $str_json = json_encode($data);
        return $str_json;
    } else return $data;
}



// // storedProc
// function storedProc($sql)
// {
//     $cnState = 0;
//     $tbl = array();

//     // Create connection
//     $conn = new mysqli($GLOBALS['db_servername'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_dbname']);

//     // Check connection
//     if ($conn->connect_error) {
//         // die("Connection failed: " . $conn->connect_error);
//         $cnState = 0;
//     } else {
//         $cnState = 1;

//         // IMPORTANT:
//         // Support for utf-8:
//         // ---------------------------------------------------------
//         // $conn->query("set character_set_client='utf8'");
//         // $conn->query("set collation_connection='utf8_general_ci'");
//         // $conn->query("set character_set_results='utf8'");
//         // ---------------------------------------------------------
//         //---
//         $conn->query("set character_set_client='utf8'");
//         $conn->query("set collation_connection='utf8_general_ci'");
//         $conn->query("set character_set_results='utf8'");
//         //---

//         $result = $conn->query($sql);
//         $conn->close();
//     }

//     $data = array(
//         'cnState' => $cnState,
//         'result' => $result,
//         'err' => mysqli_connect_error()
//     );

//     $str_json = json_encode($data);
//     return $str_json;
// }
