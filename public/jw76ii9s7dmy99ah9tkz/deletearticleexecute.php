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
    array_keys($_POST)[0] != "deleteArticleId" 
    || array_keys($_POST)[1] != "token" 
    || array_keys($_POST)[2] != "check"
    ) {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//確認のcheckが入力されているかを確認
if (validate($_POST["check"]) != "check") {
    getAdminError("checkが入力されていません。");
}

//整数であるか確認
checkIntNull($_POST["deleteArticleId"]);

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

//csrfトークン検証
//存在しない場合(おそらくリロード)はトップへ
if (checkNull($_SESSION["token"])) {
    header("Location: top.php");
    exit();
}

//不一致ならログアウト
if ($_SESSION["token"] != $_POST["token"]) {
    deleteSession();
    getAdminError("不正処理です。ログアウトします。");
}

$pdo = dbconnect();

//実在確認
$checkArticleStatement = $pdo->prepare("select id from article where id = :inputId");
$checkArticleStatement->bindValue(":inputId", $_POST["deleteArticleId"], PDO::PARAM_INT);
$checkArticleStatement->execute();
if (!($checkArticleStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("指定した記事が存在しません。");
}

//delete文実行処理
$deleteArticleStatement = $pdo->prepare("delete from article where id = :inputId");
$deleteArticleStatement->bindValue(":inputId", $_POST["deleteArticleId"], PDO::PARAM_INT);
$deleteArticleStatement->execute();

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("記事削除完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>記事削除完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>記事を削除しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="deletearticle.php">記事削除画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>