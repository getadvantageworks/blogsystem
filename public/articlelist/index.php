<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "0", "1");

if (array_keys($_GET)[0] != "categoryId") {
    getError("パラメータが不正です。");
}

//articleIdが整数の形をしているかチェック
checkIntNull($_GET["categoryId"]);
$pdo = dbconnect();
$categoryStatement = $pdo->prepare("select categoryName from category where id = :inputCategoryId");
$categoryStatement->bindValue(":inputCategoryId", $_GET["categoryId"], PDO::PARAM_INT);
$categoryStatement->execute();
if (!($categoryName = $categoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getError("カテゴリが存在しません。");
}
$articleStatement = $pdo->prepare("select id, title, summary from article where categoryId = :inputCategoryId");
$articleStatement->bindValue(":inputCategoryId", $_GET["categoryId"], PDO::PARAM_INT);
$articleStatement->execute();
?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader(validate($categoryName["categoryName"]. "一覧")); ?>

<body>
    <?php getBodyHeader(); ?>    
    <article>
        <div class="title">
            <h2><?=validate($categoryName["categoryName"])?>の記事一覧</h2>
        </div>
        <div class="contents-body">
            <ul class="article-list">
                <div class="article-wrapper">
                    <?php
                    //1回目限定処理、記事があるか確認
                    if($article = $articleStatement->fetch(PDO::FETCH_ASSOC)){ ?>
                    <a href="<?=getUrl()?>/article/?articleId=<?=$article['id']?>">
                        <li>
                            <div class="article-body">
                                <h3><?=validate($article["title"])?></h3>
                                <p><?=cleanTag($article["summary"])?></p>
                            </div>
                        </li>
                    </a>
                    <?php }
                    else{ ?>
                    <li><p>記事がまだありません。</p></li>
                    <?php }
                    while ($article = $articleStatement->fetch(PDO::FETCH_ASSOC)) { ?>
                    <a href="<?=getUrl()?>/article/?articleId=<?=$article['id']?>">
                        <li>
                            <div class="article-body">
                                <h3><?=validate($article["title"])?></h3>
                                <p><?=cleanTag($article["summary"])?></p>
                            </div>
                        </li>
                    </a>
                    <?php } ?>
                </div>
            </ul>
            <?php getProfile(); ?>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>