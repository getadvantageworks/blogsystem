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

//カテゴリ一覧を取得
$categoryStatement = $pdo->prepare("select categoryName from category");
$categoryStatement->execute();

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ作成"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ作成</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>既存のカテゴリ</h3>
                <div class="section-body">
                    <ul>
                        <?php while($category = $categoryStatement->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力?>
                        <li><?=validate($category["categoryName"])?></li>
                        <?php } ?>
                    </ul>
                </div>
            </section>
            <section>
                <h3>これから追加するカテゴリ</h3>
                <div class="section-body">
                    <form name="category" action="makecategoryexecute.php" method="post">
                        <p>カテゴリ名(プレーンテキスト)</p>
                        <input type="text" name="newName" value="">
                        <p>説明(プレーンテキスト)</p>
                        <input type="text" name="newExplanation" value="">
                        <input type="hidden" name="token" value="<?=$csrfBlockToken?>">
                        <button type="submit">作成する</button>  
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>