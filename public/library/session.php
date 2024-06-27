<?php
declare(strict_types = 1);

//セッションIDを過去のものとし、破棄する
function deleteSession():void
{
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 60, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    session_destroy();
}

//32バイト相当のトークンを生成
function makeToken():String
{
    $token = bin2hex(random_bytes(32));
    return validate($token);
}

//タイムアウト時間を設定する
function getTimeoutSecond():int
{
    return 60 * 20;
}