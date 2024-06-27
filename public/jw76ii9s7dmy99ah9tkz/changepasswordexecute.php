<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "3", "0");

//postが空でないことを確認し、制御文字を排除
foreach ($_POST as $key => $value) {
    if (checkNull($_POST[$key])){
        getAdminError("パラメータはすべて入力してください。");
    }
    $_POST[$key] = deleteControlCode($value);
}

//次にpostのパラメータの名前を検証
if (
    array_keys($_POST)[0] != "nowPassword" 
    || array_keys($_POST)[1] != "newPassword1" 
    || array_keys($_POST)[2] != "newPassword2"
    ) {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//確認用パスワードが一致しているかを確認
if ($_POST["newPassword1"] != $_POST["newPassword2"]) {
    getAdminError("パスワードが一致していません。");
}

session_start();

//loginstatusが存在しない場合、終了
if (!isset($_SESSION["loginstatus"])) {
    deleteSession();
    header("Location: login.php");
    exit();
}

//ログインステータスが1でない、または指定時間経過でログイン画面へ
if ($_SESSION["loginstatus"] != 1 || time() - $_SESSION["time"] > getTimeoutSecond()) {
    deleteSession();
    header("Location: login.php");
    exit();
}
//タイムアウト時間更新
$_SESSION["time"] = time();



$pdo = dbconnect();
$readStatement = $pdo->prepare("select password, mailaddress from user where username = :inputname");
$readStatement->bindValue(":inputname", $_SESSION["name"], PDO::PARAM_STR);
$readStatement->execute();
if (!($dbData = $readStatement->fetch(PDO::FETCH_ASSOC))) {
    //DB上にデータがない、つまりIDが存在しないとき
    deleteSession();
    getAdminError("IDが存在しません。ログアウトします。");
}elseif (password_verify($_POST["nowPassword"], $dbData["password"])) {
    //現在のパスワードが一致したとき
    $changeStatement = $pdo->prepare("update user set password = :newPassword where username = :name");
    $changeStatement->bindValue(":newPassword", password_hash($_POST["newPassword1"], PASSWORD_DEFAULT), PDO::PARAM_STR);
    $changeStatement->bindValue(":name", $_SESSION["name"], PDO::PARAM_STR);
    $changeStatement->execute();

    //パスワード変更をメールで通知
    $mailSender = new SMTPMailSender();
    $mailTo = $dbData["mailaddress"];
    $fromHeader = ["info@getadvantageworks.site", "自動通知"];
    $mailSubject = "パスワード変更通知";
    $mailBody = "ブログのパスワードが変更されました。";

    try{
        $mailSender->send($mailTo, $fromHeader, $mailSubject, $mailBody);
    }catch(Exception $e){
        getAdminError("メールの送信に失敗しました。");
    }

}else{
    //現在のパスワードが一致しなかったとき
    getAdminError("パスワードが違います。");
}
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("パスワード変更完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>パスワード変更完了</h2>
        </div>
        <div class="contents-body">
            <p>パスワードを変更しました。</p>
            <p><a href="top.php">トップへ</a></p>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>