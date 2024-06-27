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

//次にpostのパラメータの名前を検証、まず2つ
if (
    array_keys($_POST)[0] != "deleteCategoryId" 
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
checkIntNull($_POST["deleteCategoryId"]);

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
$checkCategoryStatement = $pdo->prepare("select id from category where id = :inputId");
$checkCategoryStatement->bindValue(":inputId", $_POST["deleteCategoryId"], PDO::PARAM_INT);
$checkCategoryStatement->execute();
if (!($checkCategoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("指定したカテゴリが存在しません。");
}

//記事で作成されているか確認
$checkArticleStatement = $pdo->prepare("select id from article where categoryId = :inputId");
$checkArticleStatement->bindValue(":inputId", $_POST["deleteCategoryId"], PDO::PARAM_INT);
$checkArticleStatement->execute();
if ($category = $checkArticleStatement->fetch(PDO::FETCH_ASSOC)) {
    getAdminError("指定したカテゴリの記事が存在します。削除を行えません。");
}

//delete文実行処理
$deleteCategoryStatement = $pdo->prepare("delete from category where id = :inputId");
$deleteCategoryStatement->bindValue(":inputId", $_POST["deleteCategoryId"], PDO::PARAM_INT);
$deleteCategoryStatement->execute();

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ削除完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ削除完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>カテゴリを削除しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="deletecategory.php">カテゴリ削除画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>