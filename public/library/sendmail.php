<?php
declare(strict_types = 1);

class SMTPMailSender
{
    private $mailer = null;

    //コンストラクタ
    public function __construct()
    {
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->CharSet = "utf-8";
        $mailer->isSMTP();
        $mailer->Host = getMailHost();
        $mailer->SMTPAuth = true;
        $mailer->Username = getMailAddress();
        $mailer->Password = getMailPassword();
        $mailer->SMTPSecure = "ssl";
        $mailer->Port = 465;
        $this->mailer = $mailer;
    }

    public function send(string $to, array $fromHeader, string $subject, string $body):bool
    {
        $result = false;
        // Fromヘッダーが正しいかチェックして送信者に設定
        if (count($fromHeader) == 0 || count($fromHeader) > 2) {
            return $result;
        }
        $fromEmail = $fromHeader[0];
        if (is_null($fromEmail) || strlen($fromEmail) === 0) {
            return $result;
        }
        $fromName = null;
        if (count($fromHeader) == 2) {
            $fromName = $fromHeader[1];
        }
        // 送信者 引数$fromHeaderの2つめの送信者名は、nullでなく空文字でもない場合のみメールに入る
        if (is_null($fromName)) {
            $this->mailer->setFrom($fromEmail);
        } else {
            $this->mailer->setFrom($fromEmail, $fromName);
        }

        // 宛先メールアドレスをチェックして設定
        if (strlen($to) === 0) {
            return $result;
        }
        $this->mailer->addAddress($to);

        // メールのタイトル設定(空でも送信されます)
        $this->mailer->Subject = $subject;
        // メールの本分のチェックと設定
        // メール本文が空だとPHPMailerが"Message body empty"のエラーを出すので、送信前に判定
        if (strlen($body) === 0) {
            return $result;
        }
        $this->mailer->Body = $body;

        // メインの送信処理
        try {
            $result = $this->mailer->send();
        } catch (Exception $e) {
            echo "error";
            var_dump($e);
        }
        return $result;
    }
}