<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPostGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "[01]", "0");

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

$message = "";
if (count($_POST) == 1) {
    $message = $_POST["input"] . "のハッシュ値は「" . password_hash($_POST["input"], PASSWORD_DEFAULT) . "」です";
}
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("ハッシュ値表示"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>ハッシュ値表示</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <?php if ($message != "") { ?>
                    <p><?=validate($message)?></p>
                    <p>リロードすると同じ文字列からハッシュ値を作れます。</p>
                    <?php } ?>
                    <form name="hash" action="makehash.php" method="post">
                        <input type="text" name="input" value="">
                        <button type="submit">ハッシュ値を表示する</button>
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>