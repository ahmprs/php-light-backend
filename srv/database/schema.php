<?php
$srv = realpath(__dir__ . "../../");
require_once "$srv/lib/dbs.php";
require_once "$srv/settings.php";

class Schema
{
    private static function getSqlArrDropCreateDatabase()
    {
        return [
            // database drop and create:
            "DROP DATABASE if EXISTS db_test;",
            "CREATE DATABASE `db_test`;",
        ];
    }

    private static function getSqlArrCreateTables()
    {
        return [
            // create tables
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
                    `user_id` INT NOT NULL,
                    `key` VARCHAR(200) NOT NULL,
                    `val` VARCHAR(2048) NOT NULL,
                    PRIMARY KEY(`setting_id`),
                    FOREIGN KEY(user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
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
            "
                CREATE TABLE `db_test`.`tbl_posts`(
                    `post_id` INT NOT NULL AUTO_INCREMENT,
                    `post_title` VARCHAR(200) NOT NULL,
                    `post_owner_id` INT NOT NULL,
                    `post_audit_id` INT NOT NULL,
                    `post_parent_id` INT NOT NULL,
                    `post_text` VARCHAR(1024) NOT NULL,
                    `post_access_level` INT NOT NULL,
                    `post_file_name` VARCHAR(200) NOT NULL,
                    `post_file_ext` VARCHAR(16) NOT NULL,
                    `post_create_gdp` DECIMAL(12,5) zerofill NULL,
                    `post_expire_gdp` DECIMAL(12,5) zerofill NOT NULL,
                    `post_desc` VARCHAR(1024) NOT NULL,
                    PRIMARY KEY(`post_id`),
                    FOREIGN KEY(post_owner_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_survey`(
                    `survey_id` INT NOT NULL AUTO_INCREMENT,
                    `survey_owner_id` INT NOT NULL,
                    `survey_audit_id` INT,
                    `survey_post_id` INT,
                    `survey_title` VARCHAR(200) NOT NULL,
                    `survey_text` VARCHAR(1024) NOT NULL,
                    `survey_create_gdp` DECIMAL(12,5) zerofill,
                    `survey_expire_gdp` DECIMAL(12,5) zerofill,
                    `survey_desc` VARCHAR(1024) NOT NULL,
                    PRIMARY KEY(`survey_id`),
                    FOREIGN KEY(survey_owner_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_survey_choices`(
                    `choice_id` INT NOT NULL AUTO_INCREMENT,
                    `survey_id` INT NOT NULL,
                    `choice_text` VARCHAR(1024) NOT NULL,
                    `choice_count` INT NOT NULL,
                    PRIMARY KEY(`choice_id`),
                    FOREIGN KEY(survey_id) REFERENCES tbl_survey(survey_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_comments`(
                    `comment_id` INT NOT NULL AUTO_INCREMENT,
                    `post_id` INT NOT NULL,
                    `comment_owner_id` INT,
                    `comment_gdp` DECIMAL(12,5) zerofill NOT NULL,
                    `comment_text` VARCHAR(1024) NOT NULL,
                    `comment_extra` VARCHAR(1024) NOT NULL,
                    PRIMARY KEY(`comment_id`),
                    FOREIGN KEY(comment_owner_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(post_id) REFERENCES tbl_posts(post_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_followers`(
                    `rec_id` INT NOT NULL AUTO_INCREMENT,
                    `follower_id` INT NOT NULL,
                    `followed_id` INT NOT NULL,
                    `followed_gdp` FLOAT NOT NULL,
                    PRIMARY KEY(`rec_id`),
                    FOREIGN KEY(follower_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY(followed_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_likes`(
                    `rec_id` INT NOT NULL AUTO_INCREMENT,
                    `post_id` INT NOT NULL,
                    `like_val` INT NOT NULL,
                    PRIMARY KEY(`rec_id`),
                    FOREIGN KEY(post_id) REFERENCES tbl_posts(post_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_chat_sessions`(
                    `chat_session_id` INT NOT NULL AUTO_INCREMENT,
                    `chat_session_title` VARCHAR(512) NOT NULL,
                    `chat_session_start_gdp` DECIMAL(12,5) zerofill NOT NULL,
                    `chat_session_dismissed_gdp` DECIMAL(12,5) zerofill,
                    `chat_starter_user_id` INT NOT NULL,
                    PRIMARY KEY(`chat_session_id`)
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_chat_members`(
                    `rec_id` INT NOT NULL AUTO_INCREMENT,
                    `chat_session_id` INT NOT NULL,
                    `chat_member_user_id` INT NOT NULL,
                    `chat_member_inviter_id` INT NOT NULL,
                    `chat_member_invitation_gdp` DECIMAL(12,5) zerofill NOT NULL,
                    `chat_member_blocked` INT NOT NULL,
                    PRIMARY KEY(`rec_id`),
                    FOREIGN KEY(chat_session_id) REFERENCES tbl_chat_sessions(chat_session_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
            "
                CREATE TABLE `db_test`.`tbl_chat_messages`(
                    `message_id` INT NOT NULL AUTO_INCREMENT,
                    `chat_session_id` INT NOT NULL,
                    `message_sender_id` INT NOT NULL,
                    `message_gdp` DECIMAL(12,5) zerofill NOT NULL,
                    `message_text` VARCHAR(1024) NOT NULL,
                    `message_signals` VARCHAR(1024) NOT NULL,
                    PRIMARY KEY(`message_id`),
                    FOREIGN KEY(chat_session_id) REFERENCES tbl_chat_sessions(chat_session_id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB;
            ",
            //---------------------------------------------
        ];
    }

    public static function getAllSqls()
    {
        return array_merge(
            Schema::getSqlArrDropCreateDatabase(),
            Schema::getSqlArrCreateTables()
        );
    }

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
        $arr = Schema::execSqlArr(Schema::getSqlArrDropCreateDatabase(), $crd_ignore_db);
        array_push($arrResult, $arr);
        $arr = Schema::execSqlArr(Schema::getSqlArrCreateTables());
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
