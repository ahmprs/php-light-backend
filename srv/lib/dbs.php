<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/settings.php";
require_once "$srv/lib/main.php";

class DBS
{
    private static function connect()
    {
        $stg = Settings::getSettings();
        $mysqli = new mysqli(
            $stg['database_server_name'],
            $stg['database_username'],
            $stg['database_password'],
            $stg['database_name']
        );
        $mysqli->query("set character_set_client='utf8'");
        $mysqli->query("set collation_connection='utf8_general_ci'");
        $mysqli->query("set character_set_results='utf8'");
        return $mysqli;
    }

    public static function select($sql, $types = '', $params = [])
    {
        // connect to database using given credentials in settings.php
        $mysqli = DBS::connect();

        /* check connection */
        if (mysqli_connect_errno()) {
            return [
                'err' => mysqli_connect_error(),
                'records' => [],
                'types' => $types,
                'params' => $params,
                'sql' => $sql,
            ];
        }

        /* create a prepared statement */
        if ($stmt = $mysqli->prepare($sql)) {

            /* bind parameters for markers */
            $arr_parameters = [];
            for ($i = 0; $i < count($params); $i++) {
                $arr_parameters[$i] = &$params[$i];
            }

            $arr_types = array($types);

            if (strlen($types) != count($arr_parameters)) {
                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'number of types and parameters are not equal',
                    'records' => [],
                    'types-length' => strlen($types),
                    'params-count' => count($arr_parameters),
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            call_user_func_array(
                array($stmt, 'bind_param'),
                array_merge($arr_types, $arr_parameters));

            /* execute query */
            if (!$stmt->execute()) {

                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'statement execution failed',
                    'msg' => $stmt->error,
                    'records' => [],
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }
            $stmt->store_result();

            // get column names
            $metadata = $stmt->result_metadata();
            $fields = $metadata->fetch_fields();

            $results = [];
            $ref_results = [];
            foreach ($fields as $field) {
                $results[$field->name] = null;
                $ref_results[] = &$results[$field->name];
            }

            call_user_func_array(array($stmt, 'bind_result'), $ref_results);

            $data = [];
            while ($stmt->fetch()) {
                $data[] = $results;
            }

            $stmt->free_result();
            $stmt->close();
            $mysqli->close();

            return [
                'err' => '',
                'records' => $data,
            ];
        } else {
            return [
                'err' => 'statement parameter preparation failed.',
                'msg' => $stmt->error,
                'hint' => 'check sql',
                'records' => [],
                'types' => $types,
                'parameters' => $params,
                'sql' => $sql,
            ];
        }
    }

    public static function insert($sql, $types = '', $params = [])
    {
        // connect to database using given credentials in settings.php
        $mysqli = DBS::connect();

        /* check connection */
        if (mysqli_connect_errno()) {
            return [
                'err' => mysqli_connect_error(),
                'types' => $types,
                'params' => $params,
                'sql' => $sql,
            ];
        }

        /* create a prepared statement */
        if ($stmt = $mysqli->prepare($sql)) {

            /* bind parameters for markers */
            $arr_parameters = [];
            for ($i = 0; $i < count($params); $i++) {
                $arr_parameters[$i] = &$params[$i];
            }

            $arr_types = array($types);

            if (strlen($types) != count($arr_parameters)) {
                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'number of types and parameters are not equal',
                    'types-length' => strlen($types),
                    'params-count' => count($arr_parameters),
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            call_user_func_array(
                array($stmt, 'bind_param'),
                array_merge($arr_types, $arr_parameters));

            /* execute query */
            if (!$stmt->execute()) {

                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'statement execution failed',
                    'msg' => $stmt->error,
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            $res = [
                'err' => '',
                'record_id' => $mysqli->insert_id,
                'affected_rows_count' => $mysqli->affected_rows,
            ];
            $stmt->close();
            $mysqli->close();
            return $res;
        } else {
            return [
                'err' => 'statement parameter preparation failed.',
                'msg' => $stmt->error,
                'hint' => 'check sql',
                'types' => $types,
                'parameters' => $params,
                'sql' => $sql,
            ];
        }
    }

    public static function update($sql, $types = '', $params = [])
    {
        // connect to database using given credentials in settings.php
        $mysqli = DBS::connect();

        /* check connection */
        if (mysqli_connect_errno()) {
            return [
                'err' => mysqli_connect_error(),
                'types' => $types,
                'params' => $params,
                'sql' => $sql,
            ];
        }

        /* create a prepared statement */
        if ($stmt = $mysqli->prepare($sql)) {

            /* bind parameters for markers */
            $arr_parameters = [];
            for ($i = 0; $i < count($params); $i++) {
                $arr_parameters[$i] = &$params[$i];
            }

            $arr_types = array($types);

            if (strlen($types) != count($arr_parameters)) {
                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'number of types and parameters are not equal',
                    'types-length' => strlen($types),
                    'params-count' => count($arr_parameters),
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            call_user_func_array(
                array($stmt, 'bind_param'),
                array_merge($arr_types, $arr_parameters));

            /* execute query */
            if (!$stmt->execute()) {

                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'statement execution failed',
                    'msg' => $stmt->error,
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            $res = [
                'err' => '',
                'affected_rows_count' => $mysqli->affected_rows,
            ];
            $stmt->close();
            $mysqli->close();
            return $res;
        } else {
            return [
                'err' => 'statement parameter preparation failed.',
                'msg' => $stmt->error,
                'hint' => 'check sql',
                'types' => $types,
                'parameters' => $params,
                'sql' => $sql,
            ];
        }
    }

    public static function delete($sql, $types = '', $params = [])
    {
        // connect to database using given credentials in settings.php
        $mysqli = DBS::connect();

        /* check connection */
        if (mysqli_connect_errno()) {
            return [
                'err' => mysqli_connect_error(),
                'types' => $types,
                'params' => $params,
                'sql' => $sql,
            ];
        }

        /* create a prepared statement */
        if ($stmt = $mysqli->prepare($sql)) {

            /* bind parameters for markers */
            $arr_parameters = [];
            for ($i = 0; $i < count($params); $i++) {
                $arr_parameters[$i] = &$params[$i];
            }

            $arr_types = array($types);

            if (strlen($types) != count($arr_parameters)) {
                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'number of types and parameters are not equal',
                    'types-length' => strlen($types),
                    'params-count' => count($arr_parameters),
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            call_user_func_array(
                array($stmt, 'bind_param'),
                array_merge($arr_types, $arr_parameters));

            /* execute query */
            if (!$stmt->execute()) {

                $stmt->close();
                $mysqli->close();
                return [
                    'err' => 'statement execution failed',
                    'msg' => $stmt->error,
                    'types' => $types,
                    'params' => $params,
                    'sql' => $sql,
                ];
            }

            $res = [
                'err' => '',
                'affected_rows_count' => $mysqli->affected_rows,
            ];
            $stmt->close();
            $mysqli->close();
            return $res;
        } else {
            return [
                'err' => 'statement parameter preparation failed.',
                'msg' => $stmt->error,
                'hint' => 'check sql',
                'types' => $types,
                'parameters' => $params,
                'sql' => $sql,
            ];
        }
    }
}
