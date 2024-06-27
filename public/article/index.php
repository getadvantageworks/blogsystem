<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "0", "1");

if (array_keys($_GET)[0] != "articleId") {
    getError("パラメータが不正です。");
}

//articleIdが整数の形をしているかチェック
checkIntNull($_GET["articleId"]);
$pdo = dbconnect();
//記事取得
$articleStatement = $pdo->prepare("select * from article where id = :inputArticleId");
$articleStatement->bindValue(":inputArticleId", $_GET["articleId"], PDO::PARAM_INT);
$articleStatement->execute();
if (!($articleRow = $articleStatement->fetch(PDO::FETCH_ASSOC))) {
    //データを取得できないエラー
    getError("指定された記事はありません。");
}
//サブタイトル管理変数
$count = 1;

//カテゴリ取得
$categoryStatement = $pdo->prepare("select categoryName from category where id = :inputCategoryId");
$categoryStatement->bindValue(":inputCategoryId", $articleRow["categoryId"], PDO::PARAM_INT);
$categoryStatement->execute();
if (!($categoryName = $categoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getError("カテゴリが存在しません。");
}

//日付をDateTime型にしておく
$release = sqlToDate($articleRow["release_date"]);
$update = sqlToDate($articleRow["update_date"]);
?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader(validate($articleRow["title"])); ?>

<body>
    <?php getBodyHeader(); ?>    
    <article>
        <div class="title">
            <h2><?=validate($articleRow["title"])?></h2>
            <p>カテゴリ:<?=validate($categoryName["categoryName"])?></p>
            <p>公開日:<time datetime="<?=validate($release->format("Y-m-d"))?>"><?=validate($release->format("Y年m月d日"))?></time></p>
            <p>更新日:<time datetime="<?=validate($update->format("Y-m-d"))?>"><?=validate($update->format("Y年m月d日"))?></time></p>
        </div>
        
        <div class="contents-body">
            <section>
                <h3>概要</h3>
                <div class="section-body">
                    <?=cleanTag($articleRow["summary"])?>
                </div>
            </section>
            
            <?php
            //サブタイトルが空になるまで繰り返し、連想配列の中身はsubTitle+回数
            while (!checkNull($articleRow["subTitle" . $count])){ 
            ?>
            <section>
                <h3 class="section-title"><?=validate($articleRow["subTitle" . $count])?></h3>
                <div class="section-body">
                    <?=cleanTag($articleRow["subBody" . $count])?>
                </div>
            </section>
            <?php
            //次のサブタイトルへ
            $count++;
            } ?>
            <?php getProfile(); ?>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>