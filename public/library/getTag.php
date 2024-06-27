<?php
declare(strict_types = 1);
//HTMLheadを表示する、引数にタイトルを取る
function getHtmlHeader(string $title): void
{
    $rootDirectry = mb_strstr(dirname(__FILE__), "htdocs", true);
include_once $rootDirectry . "htdocs/library/include.php";
    echo '<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . validate($title) ."-". getTitle() . '</title>
    <link rel="stylesheet" href="'.getUrl().'/reset.css">
    <link rel="stylesheet" href="'.getUrl().'/style.css">
    <link rel="stylesheet" href="'.getUrl().'/style600.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    </head>';
}

//bodyheaderを表示する
function getBodyHeader(): void
{
    echo '<header>
    <h1><a href="'.getUrl().'">'.getTitle().'</a></h1>
    </header>';
}

//管理用bodyheaderを表示する
function getAdminBodyHeader(): void
{
    echo '<header>
    <h1><a href="'.getUrl().'/jw76ii9s7dmy99ah9tkz/top.php">'.getTitle().'管理画面</a></h1>
    </header>';
}

//bodyheaderを表示する
function getBodyFooter(): void
{
    //著作権表示用のドメインを切り出す、ひとまず://から.まで
    //preg_matchの仕様から$domainは配列になる
    preg_match("/:\/\/.*\./u", getUrl(), $domain);
    //://切り捨て
    $domain[0] = mb_substr($domain[0], 3, strlen($domain[0]) - 4);
    echo '<footer>
    <p>©'.date("Y").' '.$domain[0].'</p>
    </footer>';
}

//プロフィール欄を表示する
function getProfile(): void
{
    $pdo = dbconnect();
    $profileStatement = $pdo->prepare("select body from profile where id = 1");
    $profileStatement->execute();
    if (!($profile = $profileStatement->fetch(PDO::FETCH_ASSOC))) {
        //データを取得できないエラー
        getError("プロフィールがありません。");
    }
    echo '<div class="profile">
    <h3>プロフィール</h3>
    <div class="profile-body">'
     . cleanTag($profile["body"]) . 
    '</div>
    </div>';
}

//エラーを表示する、引数にエラーメッセージを取る
function getError(string $errorMessage): void
{
    echo '<!DOCTYPE html>
    <html lang="ja">';
    getHtmlHeader("エラー");
    echo  '<body>';
    getBodyHeader();
    echo '<article>
            <div class="title">
                <h2>エラー</h2>
                <div class="contents-body">
                    <p>'. validate($errorMessage) . '</p>
                </div>
            </div>
        </article>';
    getBodyFooter();
    echo '</body>
    </html>';
    exit();
}

//管理者用エラーを表示する、引数にエラーメッセージを取る
function getAdminError(string $errorMessage): void
{
    echo '<!DOCTYPE html>
    <html lang="ja">';
    getHtmlHeader("エラー");
    echo  '<body>';
    getAdminBodyHeader();
    echo '<article>
            <div class="title">
                <h2>エラー</h2>
                <div class="contents-body">
                    <p>'. validate($errorMessage) . '</p>
                </div>
            </div>
        </article>';
    getBodyFooter();
    echo '</body>
    </html>';
    exit();
}