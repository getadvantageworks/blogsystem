<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";
//メソッドチェック
checkMethodIsGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "0", "0");

$pdo = dbconnect();
$categoryStatement = $pdo->prepare("select * from category order by categoryName");
$categoryStatement->execute();
$newArticleStatement = $pdo->prepare("select id, title, summary from article order by update_date desc limit 6");
$newArticleStatement->execute();

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("トップ"); ?>

<body>
    <?php getBodyHeader(); ?>    
    <article>
        <div class="title">
            <h2>ようこそ</h2>
        </div>
        <div class="main-image">
            <p>ここにそれっぽい画像を入れることができます。</p>
        </div>
        
        <div class="contents-body">
            <section>
                <div class="site-summary">
                    <h3>当サイト概要</h3>
                    <div class="section-body">
                        <p>ここに説明文を書けます。複数個所から参照されるわけではないので、index.phpにそのまま書いています。</p>
                    </div>
                    
                </div>
            </section>
            <section>
                <div class="category-list">
                    <h3>カテゴリ一覧</h3>
                    <div class="section-body">
                        <ul>
                            <div class="category-wrapper">
                            
                                <?php while ($categoryData = $categoryStatement->fetch(PDO::FETCH_ASSOC)) { ?>
                                <a href="/articlelist/?categoryId=<?=$categoryData["id"]?>">
                                    <li>
                                        <div class="category-body">
                                            <h3><?=validate($categoryData["categoryName"])?></h3>
                                            <p><?=validate($categoryData["categoryExplanation"])?></p>
                                        </div>
                                    </li>
                                </a>
                                <?php } ?>
                            
                            </div>
                        </ul>
                    </div>
                      
                </div>
            </section>
            <section>
                <div class="new-article">
                    <h3>新着記事</h3>
                    <div class="section-body">
                        <ul class="article-list">
                            <div class="article-wrapper">
                                <?php while($articleData = $newArticleStatement->fetch(PDO::FETCH_ASSOC)) {?>
                                <a href="/article/?articleId=<?=$articleData["id"]?>">
                                    <li>
                                        <div class="article-body">
                                            <h3><?=validate($articleData["title"])?></h3>
                                            <p><?=cleanTag($articleData["summary"])?></p>
                                        </div>
                                    </li>
                                </a>
                                <?php } ?>
                            </div>
                        </ul>
                    </div>
                    
                </div>
            </section>
            <?php getProfile(); ?>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>