<?php
declare(strict_types = 1);

//httpメソッドがpostかgetでなければエラー、ログアウト
function checkMethodIsPostGet():void
{
    if($_SERVER["REQUEST_METHOD"] != "POST" && $_SERVER["REQUEST_METHOD"] != "GET"){
        session_start();
        deleteSession();
        getError("メソッドが不正です。");
    }
}

//httpメソッドがpostでなければエラー、ログアウト
function checkMethodIsPost():void
{
    if($_SERVER["REQUEST_METHOD"] != "POST"){
        session_start();
        deleteSession();
        getError("メソッドが不正です。");
    }
}

//httpメソッドがgetでなければエラー、ログアウト
function checkMethodIsGet():void
{
    if($_SERVER["REQUEST_METHOD"] != "GET"){
        session_start();
        deleteSession();
        getError("メソッドが不正です。");
    }
}