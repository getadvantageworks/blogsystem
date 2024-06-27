<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "1", "0");

//次にpostのパラメータの名前を検証、不正ならログアウト
if (array_keys($_POST)[0] != "editCategoryId") {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//整数であるか確認
checkIntNull($_POST["editCategoryId"]);

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

//編集したいカテゴリを取得
$categoryStatement = $pdo->prepare("select categoryName, categoryExplanation from category where id = :inputId");
$categoryStatement->bindValue(":inputId", $_POST["editCategoryId"], PDO::PARAM_INT);
$categoryStatement->execute();
//カテゴリが存在しなかった場合
if (!($category = $categoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("指定したカテゴリが存在しません。");
}

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;

//カテゴリIDを保管
$_SESSION["editCategoryId"] = $_POST["editCategoryId"];
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ編集"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ編集</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>編集内容</h3>
                <div class="section-body">
                    <form name="category" action="editcategoryexecute.php" method="post">
                        <p>カテゴリ名(プレーンテキスト)</p>
                        <input type="text" name="newName" value="<?=validate($category["categoryName"])?>">
                        <p>説明(プレーンテキスト)</p>
                        <input type="text" name="newExplanation" value="<?=validate($category["categoryExplanation"])?>">
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