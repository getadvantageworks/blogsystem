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

//存在チェック
$checkCategoryStatement = $pdo->prepare("select categoryName from category where id = :inputId");
$checkCategoryStatement->bindValue(":inputId", $_SESSION["editCategoryId"], PDO::PARAM_INT);
$checkCategoryStatement->execute();
if (!($checkCategoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("指定したカテゴリは存在しません。");
}

//重複チェック、取り扱う記事の上書きは許可
$checkCategoryStatement = $pdo->prepare("select categoryName from category where categoryName = :inputName and not id = :inputId");
$checkCategoryStatement->bindValue(":inputName", $_POST["newName"], PDO::PARAM_STR);
$checkCategoryStatement->bindValue(":inputId", $_SESSION["editCategoryId"], PDO::PARAM_INT);
$checkCategoryStatement->execute();
if ($checkCategoryStatement->fetch(PDO::FETCH_ASSOC)) {
    getAdminError("指定したカテゴリはすでに存在しています。");
}

//update文実行処理
$updateCategoryStatement = $pdo->prepare("update category set categoryName = :newName, categoryExplanation = :newExplanation where id = :inputId");
$updateCategoryStatement->bindValue(":newName", $_POST["newName"], PDO::PARAM_STR);
$updateCategoryStatement->bindValue(":newExplanation", $_POST["newExplanation"], PDO::PARAM_STR);
$updateCategoryStatement->bindValue(":inputId", $_SESSION["editCategoryId"], PDO::PARAM_INT);
$updateCategoryStatement->execute();

//セッションのカテゴリIDをリセット
$_SESSION["editCategoryId"] = "";

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ編集完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ編集完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>カテゴリ「<?=validate($_POST["newName"])?>」を編集しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="editcategoryselect.php">カテゴリ編集選択画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>