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
$categoryStatement = $pdo->prepare("select id, categoryName from category");
$categoryStatement->execute();

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("記事投稿"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>記事投稿</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <form name="article" action="makearticleexecute.php" method="post">
                        <p>タイトル(プレーンテキスト)</p>
                        <input type="text" name="title" value="">
                        <p>カテゴリ</p>
                        <select name="categoryId">
                            <?php while($category = $categoryStatement->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力?>
                            <option value="<?=$category["id"]?>"><?=validate($category["categoryName"])?></option>
                            <?php } ?>
                        </select>
                        <p>概要(HTML)</p>
                        <textarea name="summary" rows="5" cols="50"></textarea>
                        <?php for($i = 1; $i <= 10; $i++){ ?>
                        <p>サブタイトル<?=$i?>(プレーンテキスト)</p>
                        <input type="text" name="subTitle<?=$i?>" value="">
                        <p>サブ内容<?=$i?>(HTML)</p>
                        <textarea name="subBody<?=$i?>" rows="5" cols="50"></textarea>
                        <?php } ?>
                        <input type="hidden" name="token" value="<?=$csrfBlockToken?>">
                        <button type="submit">投稿する</button>  
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>