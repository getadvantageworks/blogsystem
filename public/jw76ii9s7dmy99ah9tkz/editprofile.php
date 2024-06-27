<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "0", "0");

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

//編集したいプロフィールを取得、今のところid=1固定
$profileStatement = $pdo->prepare("select body from profile where id = 1");
$profileStatement->execute();
//プロフィールが存在しなかった場合
if (!($profile = $profileStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("プロフィールが存在しません。");
}

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("プロフィール編集"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>プロフィール編集</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>編集内容</h3>
                <div class="section-body">
                    <form name="profile" action="editprofileexecute.php" method="post">
                        <p>プロフィール(HTML)</p>
                        <textarea name="newBody" rows="5" cols="50"><?=cleanTag($profile["body"])?></textarea>
                        <input type="hidden" name="token" value="<?=$csrfBlockToken?>">
                        <button type="submit">編集する</button>  
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>