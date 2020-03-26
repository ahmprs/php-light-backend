<?php

$srv = realpath(__dir__ . "../../");
require_once "$srv/settings.php";
require_once "$srv/lib/main.php";

class DB
{
    private $arrSettings = null;
    private $sqlStr = '';
    private $err = '';
    private $lastRecId = -1;
    private $recCnt = -1;
    private $tbl = [];
    private $cn = null;
    private $queryExecutedSuccessfully = false;

    public static function connect($ignoreDatabaseName = false)
    {
        $db = new DB();
        $arrSettings = Settings::getSettings();

        if ($ignoreDatabaseName) {
            $db->cn = new mysqli(
                $arrSettings['database_server_name'],
                $arrSettings['database_username'],
                $arrSettings['database_password'],
                ''
            );
        } else {
            $db->cn = new mysqli(
                $arrSettings['database_server_name'],
                $arrSettings['database_username'],
                $arrSettings['database_password'],
                $arrSettings['database_name']
            );
        }

        $db->err = $db->cn->connect_error;

        if ($db->err) {
            $db->cn = null;
            return $db;
        }

        $db->cn->query("set character_set_client='utf8'");
        $db->cn->query("set collation_connection='utf8_general_ci'");
        $db->cn->query("set character_set_results='utf8'");

        return $db;
    }

    public function select($sql)
    {
        if ($this->cn === null) {
            return $this;
        }

        $result = $this->cn->query($sql);
        $this->recCnt = $result->num_rows;
        $this->tbl = [];

        if ($this->recCnt > 0) {
            $this->queryExecutedSuccessfully = true;
            while ($row = $result->fetch_assoc()) {
                array_push($this->tbl, $row);
            }
        }
        $this->cn->close();
        return $this;
    }

    public function insert($sql)
    {
        if ($this->cn === null) {
            return $this;
        }

        if ($this->cn->query($sql) === true) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = 1;
            $this->lastRecId = $this->cn->insert_id;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    public function update($sql)
    {
        if ($this->cn === null) {
            return $this;
        }

        if ($this->cn->query($sql) === true) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    public function runSql($sql)
    {

        if ($this->cn === null) {
            return $this;
        }

        if ($this->cn->query($sql) === true) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    public function delete($sql)
    {
        if ($this->cn === null) {
            return $this;
        }

        if ($this->cn->query($sql) === true) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    public function getRecords()
    {
        return $this->tbl;
    }

    public function getLastRecordId()
    {
        return $this->lastRecId;
    }

    public function getNumberOfAffectedRows()
    {
        return $this->recCnt;
    }

    public function isSuccessful()
    {
        return $this->queryExecutedSuccessfully;
    }

}
