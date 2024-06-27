<?php
declare(strict_types = 1);
//ライブラリ読込み用php
//外部ライブラリ
require_once "PHPMailer-master/src/PHPMailer.php";
require_once "PHPMailer-master/src/SMTP.php";
require_once "PHPMailer-master/src/Exception.php";

require_once "htmlpurifier-4.15.0/library/HTMLPurifier.auto.php";

//自作ライブラリ
require_once "getTag.php";
require_once "db.php";
require_once "checkInput.php";
require_once "myurl.php";
require_once "session.php";
require_once "sendmail.php";
require_once "httpmethod.php";

//DBパスワードなど
require_once dirname(__FILE__, 3)."/pass/pass.php";

//テストDB
//require_once "testdb.php";