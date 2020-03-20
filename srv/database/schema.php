<?php
$srv = realpath (__dir__."../../");
require_once "$srv/lib/db.php";


class Schema{

    static function Run(){
        $arrResult=[];

        $arr = Schema::execSqlArr([
            "DROP DATABASE if EXISTS db_test;",
            "CREATE DATABASE `db_test`;",
        ], $ignoreDatabaseName = true);
        array_push($arrResult, $arr);

        $arr = Schema::execSqlArr([
            "USE DATABASE db_test;",
            "CREATE TABLE `db_test`.`tbl_users` ( `user_id` INT NOT NULL AUTO_INCREMENT , `user_name` VARCHAR(200) NOT NULL , `user_pass` VARCHAR(200) NOT NULL , `user_active` INT NOT NULL , `user_access_level` INT NOT NULL , `user_desc` VARCHAR(300) NOT NULL , PRIMARY KEY (`user_id`)) ENGINE = InnoDB;",
            "CREATE TABLE `db_test`.`tbl_settings` ( `setting_id` INT NOT NULL AUTO_INCREMENT , `key` VARCHAR(200) NOT NULL , `val` VARCHAR(200) NOT NULL, PRIMARY KEY (`setting_id`)) ENGINE = InnoDB;",
        ]);
        array_push($arrResult, $arr);

        return $arrResult;
    }
    
    static function execSql($sql, $ignoreDatabaseName = false){
        $r = DB::connect($ignoreDatabaseName)->runSql($sql);
        $affected_rows = $r->getNumberOfAffectedRows();
        $isSuccessful = $r->isSuccessful();

        return ['sql'=>$sql, 'affected_rows'=> $affected_rows, 'is_successful'=>$isSuccessful];
    }

    static function execSqlArr($arrSql, $ignoreDatabaseName = false){
        $arrResult=[];
        $n = count($arrSql);
        for($i=0; $i<$n; $i++){
            array_push($arrResult, Schema::execSql($arrSql[$i], $ignoreDatabaseName));
        }
        return $arrResult;
    }
}