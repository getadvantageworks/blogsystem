<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPost();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "24", "0");

//制御文字を排除
foreach ($_POST as $key => $value){
    $_POST[$key] = deleteControlCode($value);
}

//次にpostのパラメータの名前を検証
//3つめまで
if (
    array_keys($_POST)[0] != "title" 
    || array_keys($_POST)[1] != "categoryId" 
    || array_keys($_POST)[2] != "summary"
    ) {
    deleteSession();
    getAdminError("不正なパラメータが送られました。");
}

//空白チェック
if (checkNull($_POST["title"]) || checkNull($_POST["summary"])) {
    getAdminError("タイトルと概要を記入してください。");
}

//categoryIdが整数の形をしているかチェック
checkIntNull($_POST["categoryId"]);

//サブタイトル部分の名前はループで検証
for($i = 1; $i <= 10; $i++){
    if (
        array_keys($_POST)[2 * $i + 1] != "subTitle" . strval($i) 
        || array_keys($_POST)[2 * $i + 2] != "subBody" . strval($i)
        ) {
        deleteSession();
        getAdminError("不正なパラメータが送られました。");
    }
}

//サブ1だけは必須とする
if (checkNull($_POST["subTitle1"]) || checkNull($_POST["subBody1"])) {
    getAdminError("サブ1は必須です。");
}

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

//csrfトークン検証
//存在しない場合(おそらくリロード)はトップへ
if (checkNull($_SESSION["token"])) {
    header("Location: top.php");
    exit();
}

//不一致ならログアウト
if ($_SESSION["token"] != $_POST["token"]) {
    deleteSession();
    getAdminError("不正処理です。ログアウトします。");
}

$pdo = dbconnect();

//重複チェック
$articleStatement = $pdo->prepare("select id from article where title = :inputTitle");
$articleStatement->bindValue(":inputTitle", $_POST["title"], PDO::PARAM_STR);
$articleStatement->execute();
if ($articleStatement->fetch(PDO::FETCH_ASSOC)) {
    getAdminError("そのタイトルはすでに存在しています。");
}

//カテゴリ存在チェック
$categoryStatement = $pdo->prepare("select id from category where id = :inputId");
$categoryStatement->bindValue(":inputId", $_POST["categoryId"], PDO::PARAM_INT);
$categoryStatement->execute();
if (!($categoryStatement->fetch(PDO::FETCH_ASSOC))) {
    getAdminError("そのカテゴリは存在しません。");
}

//insert文実行処理
$newArticleStatement = $pdo->prepare("insert into article 
(title, categoryId, summary, subTitle1, subBody1, subTitle2, subBody2,
subTitle3, subBody3, subTitle4, subBody4, subTitle5, subBody5, subTitle6, 
subBody6, subTitle7, subBody7, subTitle8, subBody8, subTitle9, subBody9,
subTitle10, subBody10) 
values (:title, :categoryId, :summary, :subTitle1, :subBody1, 
:subTitle2, :subBody2, :subTitle3, :subBody3, :subTitle4, :subBody4, :subTitle5, :subBody5, 
:subTitle6, :subBody6, :subTitle7, :subBody7, :subTitle8, :subBody8, :subTitle9, :subBody9,
:subTitle10, :subBody10)");
$newArticleStatement->bindValue(":title", $_POST["title"], PDO::PARAM_STR);
$newArticleStatement->bindValue(":categoryId", $_POST["categoryId"], PDO::PARAM_INT);
$newArticleStatement->bindValue(":summary", $_POST["summary"], PDO::PARAM_STR);
for($i = 1; $i <= 10; $i++){
    $newArticleStatement->bindValue(":subTitle" . strval($i), $_POST["subTitle" . strval($i)], PDO::PARAM_STR);
    $newArticleStatement->bindValue(":subBody" . strval($i), $_POST["subBody" . strval($i)], PDO::PARAM_STR);
}
$newArticleStatement->execute();

//トークンを無効化
$_SESSION["token"] = "";

?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("記事投稿完了"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>記事投稿完了</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <p>記事「<?=validate($_POST["title"])?>」を投稿しました。</p>
                    <p><a href="top.php">管理画面トップ</a></p>
                    <p><a href="makearticle.php">投稿画面</a></p>
                </div>
            </section>
        </div>
        
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>