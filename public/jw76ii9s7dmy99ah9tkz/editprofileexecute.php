<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "2", "0");

//postが空でないことを確認し、制御文字を排除
foreach ($_POST as $key => $value){
    if (checkNull($_POST[$key])) {
        getAdminError("パラメータはすべて入力してください。");
    }
    $_POST[$key] = deleteControlCode($value);
}

//次にpostのパラメータの名前を検証
if (array_keys($_POST)[0] != "newBody" || array_keys($_POST)[1] != "token") {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//postが空でないことを確認し、無害化
foreach ($_POST as $key => $value){
    if (checkNull($_POST[$key])) {
        getAdminError("パラメータはすべて入力してください。");
    }
    $_POST[$key] = cleanTag($value);
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

//update文実行処理
$updateProfileStatement = $pdo->prepare("update profile set body = :newBody where id = 1");
$updateProfileStatement->bindValue(":newBody", $_POST["newBody"], PDO::PARAM_STR);
$updateProfileStatement->execute();

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("プロフィール編集完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>プロフィール編集完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>プロフィールを編集しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="editprofile.php">プロフィール編集画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>