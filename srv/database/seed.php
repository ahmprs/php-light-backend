<?php
$srv = realpath (__dir__."../../");
require_once "$srv/lib/db.php";

class Seed{

    static function Run(){
        $arrResult=[];

        $arr = Seed::execSqlArr([
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Jack', '123', '1', '1', '')",
            "INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_pass`, `user_active`, `user_access_level`, `user_desc`) VALUES (NULL, 'Joe', '123', '1', '1', '')",
            "INSERT INTO `tbl_settings` (`setting_id`, `key`, `val`) VALUES (NULL, 'max_rate', '100')",
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
            array_push($arrResult, Seed::execSql($arrSql[$i], $ignoreDatabaseName));
        }
        return $arrResult;
    }
}
?>