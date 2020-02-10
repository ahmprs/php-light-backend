<?php
require_once __DIR__ . '/main.php';
require_once __DIR__ . '/user.php';
require_once __DIR__ . '/dal.php';

// check if user is authorized to access this part.
$user_id = getUserId();
if ($user_id != -1) {
    resp(0, 'Access Denied');
    return;
}


$db_res = null;
$arr_log = [];


// drop database if exists
$sql = "DROP DATABASE if EXISTS db_sample;";
$db_res = runSql($sql, $getJson = false, $db_root = true);
$arr_log["Drop Database"] = $db_res;
//----------------------------------------


// create database
$sql = "CREATE DATABASE `db_sample`;";
$db_res = runSql($sql, $getJson = false, $db_root = true);
$arr_log["Create Database"] = $db_res;
//----------------------------------------


// use database
$sql = "USE DATABASE db_sample;";
$db_res = runSql($sql, $getJson = false, $db_root = false);
$arr_log["Use Database"] = $db_res;
//----------------------------------------


// create table
$sql = "CREATE TABLE `db_sample`.`tbl_users` ( `user_id` INT NOT NULL AUTO_INCREMENT , `user_name` VARCHAR(200) NOT NULL , `user_pass` VARCHAR(200) NOT NULL , `user_active` INT NOT NULL , `user_access_level` INT NOT NULL , `user_desc` VARCHAR(300) NOT NULL , PRIMARY KEY (`user_id`)) ENGINE = InnoDB;";
$db_res = runSql($sql, $getJson = false, $db_root = false);
$arr_log["Create Table tbl_users"] = $db_res;
//----------------------------------------


// add primary data
$sql = "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Jack', '123', '1', '1', '')";
$db_res = runSql($sql, $getJson = false, $db_root = false);
//----------------------------------------

$sql = "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Joe', '123', '1', '1', '')";
$db_res = runSql($sql, $getJson = false, $db_root = false);
//----------------------------------------


// create table
$sql = "CREATE TABLE `db_sample`.`tbl_settings` ( `setting_id` INT NOT NULL AUTO_INCREMENT , `key` VARCHAR(200) NOT NULL , `val` VARCHAR(200) NOT NULL, PRIMARY KEY (`setting_id`)) ENGINE = InnoDB;";
$db_res = runSql($sql, $getJson = false, $db_root = false);
$arr_log["Create Table tbl_settings"] = $db_res;
//----------------------------------------


// add primary data
$sql = "INSERT INTO `tbl_settings` (`setting_id`, `key`, `val`) VALUES (NULL, 'max_rate', '100')";
$db_res = runSql($sql, $getJson = false, $db_root = false);
//----------------------------------------



// create views


// create stored procedures


resp(1, $arr_log);
