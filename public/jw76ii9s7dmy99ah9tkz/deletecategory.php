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

//カテゴリ一覧を取得、一覧用とプルダウン用
$categoryStatementForList = $pdo->prepare("select categoryName from category");
$categoryStatementForList->execute();
$categoryStatementForPulldown = $pdo->prepare("select id, categoryName from category");
$categoryStatementForPulldown->execute();

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("カテゴリ削除"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>カテゴリ削除</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>既存のカテゴリ</h3>
                <div class="section-body">
                    <ul>
                        <?php while ($category = $categoryStatementForList->fetch(PDO::FETCH_ASSOC)) { //カテゴリ一覧を出力?>
                        <li><?=validate($category["categoryName"])?></li>
                        <?php } ?>
                    </ul>
                </div>
            </section>
            <section>
                <h3>削除するカテゴリ</h3>
                <div class="section-body">
                    <form name="category" action="deletecategoryexecute.php" method="post">
                        <select name="deleteCategoryId">
                            <?php while ($category = $categoryStatementForPulldown->fetch(PDO::FETCH_ASSOC)) { //カテゴリ一覧を出力?>
                            <option value="<?=$category["id"]?>"><?=validate($category["categoryName"])?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="token" value="<?=$csrfBlockToken?>">
                        <p>削除は取り消せません。よろしければ「check」と入力してください。</p>
                        <input type="text" name="check" value="">
                        <button type="submit">削除する</button>  
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>