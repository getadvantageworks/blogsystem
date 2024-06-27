<?php
declare(strict_types = 1);
//DB接続
function dbconnect()//戻り値はpdoもしくはvoid
{
    try{
        $pdoStatement = "mysql:host=".getDBHost()."; dbname=".getDBName();"; charset=utf8mb4";
        $pdo = new PDO($pdoStatement, getDBUser(), getDBPassword());
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }catch(PDOException $e){
        getError("データベースに接続できません。");
        exit();
    }
    return $pdo;
}


//SQLの日付をphpのdatetimeへ
function sqlToDate(string $sqldate): DateTime
{
    preg_match("/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/u", $sqldate, $ymd);
    $year = intval(mb_substr($ymd[0], 0, 4));
    $month = intval(mb_substr($ymd[0], 5, 2));
    $date = intval(mb_substr($ymd[0], 8, 2));
    return (new DateTime())->setDate($year, $month, $date);
}