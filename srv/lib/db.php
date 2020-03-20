<?php

$srv = realpath (__dir__."../../");
require_once "$srv/settings.php";


class DB{
    private $arrSettings = null;
    private $sqlStr = '';
    private $err = '';
    private $lastRecId = -1;
    private $recCnt = -1;
    private $tbl = [];
    private $cn = null;
    private $queryExecutedSuccessfully = false;

    static function connect($ignoreDatabaseName = false){
        $db = new DB();
        $arrSettings = Settings::getSettings();

        if ($ignoreDatabaseName){
            $db->cn = new mysqli(
                $arrSettings['database_server_name'], 
                $arrSettings['database_username'], 
                $arrSettings['database_password'], 
                ''
            );
        }
        
        else{
            $db->cn = new mysqli(
                $arrSettings['database_server_name'], 
                $arrSettings['database_username'], 
                $arrSettings['database_password'], 
                $arrSettings['database_name']
            );
        }

        $db->err = $db->cn->connect_error;

        if($db->err) {
            $db->cn = null;
            return $db;
        }

        $db->cn->query("set character_set_client='utf8'");
        $db->cn->query("set collation_connection='utf8_general_ci'");
        $db->cn->query("set character_set_results='utf8'");

        return $db;
    }

    function select($sql){
        if ($this->cn === null) return $this;

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

    function insert($sql){
        if ($this->cn === null) return $this;

        if ($this->cn->query($sql) === TRUE) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = 1;
            $this->lastRecId = $this->cn->insert_id;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    function update($sql){
        if ($this->cn === null) return $this;

        if ($this->cn->query($sql) === TRUE) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }
    
    
    function runSql($sql){
        if ($this->cn === null) return $this;

        if ($this->cn->query($sql) === TRUE) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }
    
    function delete($sql){
        if ($this->cn === null) return $this;

        if ($this->cn->query($sql) === TRUE) {
            $this->queryExecutedSuccessfully = true;
            $this->recCnt = $this->cn->affected_rows;
        } else {
            $this->recCnt = 0;
        }
        $this->cn->close();
        return $this;
    }

    function getRecords(){
        return $this->tbl;
    }

    function getLastRecordId(){
        return $this->lastRecId;
    }

    function getNumberOfAffectedRows(){
        return $this->recCnt;
    }

    function isSuccessful(){
        return $this->queryExecutedSuccessfully;
    }

}

?>