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

//記事一覧を取得、一覧用とプルダウン用
$articleStatementForList = $pdo->prepare("select title from article");
$articleStatementForList->execute();
$articleStatementForPulldown = $pdo->prepare("select id, title from article");
$articleStatementForPulldown->execute();

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("記事削除"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>記事削除</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>既存の記事</h3>
                <div class="section-body">
                    <ul>
                        <?php while($article = $articleStatementForList->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力?>
                        <li><?=validate($article["title"])?></li>
                        <?php } ?>
                    </ul>
                </div>
            </section>
            <section>
                <h3>削除する記事</h3>
                <div class="section-body">
                    <form name="article" action="deletearticleexecute.php" method="post">
                        <select name="deleteArticleId">
                            <?php while($article = $articleStatementForPulldown->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力?>
                            <option value="<?=$article["id"]?>"><?=validate($article["title"])?></option>
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