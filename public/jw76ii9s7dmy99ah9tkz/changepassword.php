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
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("パスワード変更"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>パスワード変更</h2>
        </div>
        <div class="contents-body">
            <section>
                <div class="section-body">
                    <form name="password" action="changepasswordexecute.php" method="post">
                        <p>現在のパスワード</p>
                        <input type="password" name="nowPassword" value="">
                        <p>変更するパスワード</p>
                        <input type="password" name="newPassword1" value="">
                        <p>変更するパスワード(確認用)</p>
                        <input type="password" name="newPassword2" value="">
                        <button type="submit">変更する</button>
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>