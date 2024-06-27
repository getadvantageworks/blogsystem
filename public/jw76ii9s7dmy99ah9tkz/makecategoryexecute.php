<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "3", "0");

//postが空でないことを確認し、制御文字を排除
foreach ($_POST as $key => $value){
    if (checkNull($_POST[$key])) {
        getAdminError("パラメータはすべて入力してください。");
    }
    $_POST[$key] = deleteControlCode($value);
}

//次にpostのパラメータの名前を検証
if (
    array_keys($_POST)[0] != "newName" 
    || array_keys($_POST)[1] != "newExplanation" 
    || array_keys($_POST)[2] != "token"
    ) {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
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

//重複チェック
$categoryStatement = $pdo->prepare("select categoryName from category where categoryName = :inputName");
$categoryStatement->bindValue(":inputName", $_POST["newName"], PDO::PARAM_STR);
$categoryStatement->execute();
if ($categoryStatement->fetch(PDO::FETCH_ASSOC)) {
    getAdminError("指定したカテゴリはすでに存在しています。");
}

//insert文実行処理
$newCategoryStatement = $pdo->prepare("insert into category (id, categoryName, categoryExplanation) values (null, :newCategoryName, :newExplanation)");
$newCategoryStatement->bindValue(":newCategoryName", $_POST["newName"], PDO::PARAM_STR);
$newCategoryStatement->bindValue(":newExplanation", $_POST["newExplanation"], PDO::PARAM_STR);
$newCategoryStatement->execute();

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ作成完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ作成完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>カテゴリ「<?=validate($_POST["newName"])?>」を作成しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="makecategory.php">カテゴリ作成画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>