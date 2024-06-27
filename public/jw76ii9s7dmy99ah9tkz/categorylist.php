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
$categoryStatement = $pdo->prepare("select * from category");
$categoryStatement->execute();
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ一覧"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ一覧</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <table>
                        <tr>
                            <th>カテゴリ名</th>
                            <th>説明</th>
                        </tr>
                        <?php while($category = $categoryStatement->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力?>
                        <tr><td><?=validate($category["categoryName"])?></td><td><?=validate($category["categoryExplanation"])?></td></tr>
                        <?php } ?>
                    </table>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>