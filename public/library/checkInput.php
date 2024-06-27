<?php
declare(strict_types = 1);
//入力が空文字、もしくは非整数であるかチェック、不正ならエラー画面、ログアウト
function checkIntNull($str): void
{
    if(checkNull($str) || !checkInt($str)){
        session_start();
        deleteSession();
        getError("URLが不正です。");
    }
}

//空文字、nullならtrue
function checkNull($input): bool
{
    return is_null($input) || $input === "";
}

//整数の形をしていたらtrue
function checkInt(string $input):bool
{
    if((preg_match("/\A[1-9][0-9]*\z/u", $input) == 1)){
        return true;
    }else{
        return false;
    }
}

//制御文字排除とHTMLエスケープ
function validate(string $str): string
{
    return deleteControlCode(validateHTML($str));
}

//HTMLエスケープ
function validateHTML(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

//制御文字排除
function deleteControlCode(string $str): string
{
    return preg_replace("/[[:cntrl:]]/u", "", $str);
}

//HTMLタグ制御、制御文字排除
function cleanTag(string $inputHTML): string
{
    $config = HTMLPurifier_Config::createDefault();
    $config->set("Core.Encoding", "UTF-8");
    $config->set("Core.Language", "ja");
    $config->set("HTML.AllowedElements", array("div", "span", "p", "a", "h4", "pre", "code"));
    $config->set("HTML.AllowedAttributes", array("class", "href"));

    $purifier = new HTMLPurifier($config);
    return $purifier->purify($inputHTML);
}

//validateをかけるかを設定したい
//postとgetが正しい数で設定されているかチェック、pとgは正規表現
function checkPostGet(array $post,array $get, String $p, String $g):void
{
    //postとgetの正規表現一致を判定
    $pMatch = preg_match("/\A" . $p . "\z/u", strval(count($post)));
    $gMatch = preg_match("/\A" . $g . "\z/u", strval(count($get)));
    //どちらかが一致しなければエラー、ログアウト
    if($pMatch != 1 || $gMatch != 1){
        session_start();
        deleteSession();
        getError("不正なパラメータです。");
    }
    
}