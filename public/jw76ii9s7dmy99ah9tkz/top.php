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
$_SESSION["time"] = time();
?>

<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("管理画面トップ"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>管理画面トップ</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>ようこそ、<?=validate($_SESSION["name"])?>さん</h3>
                <ul>
                    <li><a href="makearticle.php">記事を投稿する</a></li>
                    <li><a href="makepointtag.php">ポイントタグを作成する</a></li>
                    <li><a href="makequotetag.php">引用タグを作成する</a></li>
                    <li><a href="makecodetag.php">codeタグを作成する</a></li>
                    <li><a href="editarticleselect.php">記事を編集する</a></li>
                    <li><a href="deletearticle.php">記事を削除する</a></li>
                    <li><a href="makecategory.php">カテゴリを新設する</a></li>
                    <li><a href="editcategoryselect.php">カテゴリを編集する</a></li>
                    <li><a href="deletecategory.php">カテゴリを削除する</a></li>
                    <li><a href="categorylist.php">カテゴリ一覧</a></li>
                    <li><a href="editprofile.php">プロフィールを編集する</a></li>
                    <li><a href="changepassword.php">パスワードを変更する</a></li>
                    <li><a href="phpinfo.php">PHP情報</a></li>
                    <li><a href="makehash.php">ハッシュ値作成</a></li>
                    <li><a href="login.php">ログアウト</a></li>
                </ul>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>
