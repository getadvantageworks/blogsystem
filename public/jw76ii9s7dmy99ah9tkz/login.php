<?php
declare(strict_types = 1);
$rootDirectry = mb_strstr(dirname(__FILE__), "getadvantageworks.site", true);
include_once $rootDirectry . "getadvantageworks.site/library/include.php";

//メソッドチェック
checkMethodIsPostGet();

//パラメータ数チェック
checkPostGet($_POST, $_GET, "[02]", "0");

session_start();

//次にpostが2つの場合はpostのパラメータの名前を検証、不正ならログアウト
if (count($_POST) == 2) {
    if (array_keys($_POST)[0] != "inputname" || array_keys($_POST)[1] != "inputpassword") {
        deleteSession();
        getAdminError("不正なパラメータが送られました。");
    }
}

//message初期値
$message = "入力してください";
//postが2つ、つまり入力が2つあるとき
if (count($_POST) == 2) {
    $pdo = dbconnect();
    $Statement = $pdo->prepare("select * from user where mailaddress = :inputname");
    $Statement->bindValue(":inputname", $_POST["inputname"], PDO::PARAM_STR);
    $Statement->execute();
    if (!($dbpassword = $Statement->fetch(PDO::FETCH_ASSOC))) {
        //DB上にデータがない、つまりIDが存在しないとき
        $message = "ログインに失敗しました。";
    } elseif (password_verify($_POST["inputpassword"], $dbpassword["password"])) {
        //ログイン成功処理
        //セッションIDを再度生成
        session_regenerate_id(true);
        //ログインステータスを記述
        $_SESSION["loginstatus"] = 1;
        //タイムアウト制御のためのunixタイムスタンプ
        $_SESSION["time"] = time();
        //ログインアカウント名
        $_SESSION["name"] = $dbpassword["username"];
        //管理画面トップへ移動
        header("Location: top.php");
        exit();
    } else {
        //パスワードが間違っていたとき
        $message = "ログインに失敗しました。";
    }
}

//このページにログイン成功以外で辿り着いたら強制ログアウト
deleteSession();
?>
<!DOCTYPE html>
<html lang="ja">
<?php getHtmlHeader("ログイン"); ?>

<body>
    <?php getAdminBodyHeader(); ?>
    <article>
            
        <div class="title">
            <h2>ログイン</h2>
        </div>
        <h3><?=validate($message)?></h3>
        <form name="password" action="login.php" method="post">
            <p>ユーザーID</p><input type="text" name="inputname" value="">
            <p>パスワード</p><input type="password" name="inputpassword">
            <button type="submit">ログイン</button>  
        </form>
    </article>
    <?php getBodyFooter(); ?>
</body>
</html>
