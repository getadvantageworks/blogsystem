<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPostGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "[01]", "0");

//返答文字列を初期化
$ans = "";

if (count($_POST) == 1) {
    if (array_keys($_POST)[0] != "codeBody") {
        getAdminError("不正なパラメータが送られました。");
    }
    //入力文字列が存在する場合、返答文字列を作成
    //固定部分
    $ans = "<div class=\"sourcecode\"><h4 class=\"sourcecode-title\">Code</h4><div class=\"sourcecode-body\"><pre><code>";
    //入力部分
    $ans = $ans . $_POST["codeBody"];
    //固定部分
    $ans = $ans . "</code></pre></div></div>";
    //エスケープ
    $ans = validate($ans);
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
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("codeタグ作成"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>codeタグ作成</h2>
        </div>
        <div class="contents-body">
            <section>
                <h3>codeにしたい文章、タグ不要</h3>
                <div class="section-body">
                    <form name="code" action="makecodetag.php" method="post">
                        <p>内容</p>
                        <textarea name="codeBody" rows="5" cols="50"><?=$ans?></textarea>
                        <button type="submit">作成する</button>  
                    </form>
                </div>
            </section>
        </div>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>

