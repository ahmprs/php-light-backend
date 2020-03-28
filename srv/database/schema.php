<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/dbs.php";
require_once "$srv/settings.php";

class Schema
{
    public static function run()
    {
        $crd_ignore_db = [
            'database_server_name' => Settings::get('database_server_name'),
            'database_username' => Settings::get('database_username'),
            'database_password' => Settings::get('database_password'),
            'database_name' => '',
        ];
        //-------------------------------------------------

        $arrResult = [];
        $arr = Schema::execSqlArr([
            "DROP DATABASE if EXISTS db_test;",
            "CREATE DATABASE `db_test`;",
        ], $crd_ignore_db);
        array_push($arrResult, $arr);

        $arr = Schema::execSqlArr([
            "
                CREATE TABLE `db_test`.`tbl_users`(
                    `user_id` INT NOT NULL AUTO_INCREMENT,
                    `user_name` VARCHAR(200) NOT NULL UNIQUE,
                    `user_pass_hash` VARCHAR(200) NOT NULL,
                    `user_active` INT NOT NULL,
                    `user_access_level` INT NOT NULL,
                    `user_desc` VARCHAR(300) NOT NULL,
                    PRIMARY KEY(`user_id`)
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------

            "
                CREATE TABLE `db_test`.`tbl_settings`(
                    `setting_id` INT NOT NULL AUTO_INCREMENT,
                    `key` VARCHAR(200) NOT NULL,
                    `val` VARCHAR(200) NOT NULL,
                    PRIMARY KEY(`setting_id`)
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------

            "
            CREATE TABLE `db_test`.`tbl_uploads`(
                `file_id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT NOT NULL,
                `file_org_name` VARCHAR(200) NOT NULL,
                `file_new_name` VARCHAR(200) NOT NULL,
                `file_size_bytes` BIGINT NOT NULL,
                `file_target_dir` VARCHAR(200) NOT NULL,
                `file_extension` VARCHAR(10) NOT NULL,
                PRIMARY KEY(`file_id`),
                FOREIGN KEY(user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
        ]);
        array_push($arrResult, $arr);

        return $arrResult;
    }

    public static function execSql($sql, $credentials = null)
    {
        $r = DBS::runSql($sql, $credentials);
        return $r;
    }

    public static function execSqlArr($arrSql, $credentials = null)
    {
        $arrResult = [];
        $n = count($arrSql);
        for ($i = 0; $i < $n; $i++) {
            array_push($arrResult, [
                'sql' => $arrSql[$i],
                'sql_execution_result' => Schema::execSql($arrSql[$i], $credentials),
            ]);
        }
        return $arrResult;
    }
}

// Schema::run();
