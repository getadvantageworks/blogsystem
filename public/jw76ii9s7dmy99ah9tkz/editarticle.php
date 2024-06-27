<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "1", "0");

//次にpostのパラメータの名前を検証、不正ならログアウト
if (array_keys($_POST)[0] != "editArticleId") {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//整数であるか確認
checkIntNull($_POST["editArticleId"]);

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

//編集記事を取得
$articleStatement = $pdo->prepare("select * from article where id = :inputId");
$articleStatement->bindValue(":inputId", $_POST["editArticleId"], PDO::PARAM_STR);
$articleStatement->execute();
if (!($articleData = $articleStatement->fetch(PDO::FETCH_ASSOC))) {
    //DB上にデータがない、つまりIDが存在しないとき
    deleteSession();
    getAdminError("IDが存在しません。ログアウトします。");
}

//カテゴリ一覧を取得
$categoryStatement = $pdo->prepare("select id, categoryName from category");
$categoryStatement->execute();

//csrfトークン
$csrfBlockToken = makeToken();
$_SESSION["token"] = $csrfBlockToken;

//記事IDを保管
$_SESSION["editArticleId"] = $_POST["editArticleId"];
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("記事編集"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>記事編集</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <form name="article" action="editarticleexecute.php" method="post">
                        <p>タイトル(プレーンテキスト)</p>
                        <input type="text" name="title" value="<?=validate($articleData["title"])?>">
                        <p>カテゴリ</p>
                        <select name="categoryId">
                            <?php while($category = $categoryStatement->fetch(PDO::FETCH_ASSOC)){ //カテゴリ一覧を出力、初期値は元の値?>
                            <option value="<?=$category["id"]?>"
                            <?php if ($articleData["categoryId"] == $category["id"]) { ?> selected<?php } ?>><?=validate($category["categoryName"])?>
                            </option>
                            <?php } ?>
                        </select>
                        <p>概要(HTML)</p>
                        <textarea name="summary" rows="5" cols="50"><?=cleanTag($articleData["summary"])?></textarea>
                        <?php for($i = 1; $i <= 10; $i++){ ?>
                        <p>サブタイトル<?=$i?>(プレーンテキスト)</p>
                        <input type="text" name="subTitle<?=$i?>" value="<?=cleanTag($articleData["subTitle" . strval($i)])?>">
                        <p>サブ内容<?=$i?>(HTML)</p>
                        <textarea name="subBody<?=$i?>" rows="5" cols="50"><?=cleanTag($articleData["subBody" . strval($i)])?></textarea>
                        <?php } ?>
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