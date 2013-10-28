<?php

class Engine_Api_Mailer {

    private static $_config = array(
        'auth' => 'login',
        'port' => '587',
        'ssl' => 'tls',
        'username' => 'user@storycrossing.com',
        'password' => 'ehm..a pass here?',
    );
    private static $_from = array(
        "name" => "Majordomo StoryCrossing",
        "mail" => 'majordomo@storycrossing.com',
    );
    private static $_server = 'smtp.gmail.com';

    public static function nsend($to,$subject,$message,$attachments = array()) {
        echo "<pre>";
        print_r(array($to,$subject,$message,$attachments));
//        die;
    }

    public static function send($to,$subject,$message,$attachments = array()) {
        try {
            $mail = new Zend_Mail("utf-8");
            $mail
                ->addTo($to[0],$to[1])
                ->setFrom(self::$_from["mail"], self::$_from["name"])
                ->setSubject($subject)
                ->setBodyHtml($message)
                ->setBodyText($message)
                ->setReplyTo(self::$_from["mail"], self::$_from["name"])
                ->addHeader('MIME-Version', '1.0')
                ->addHeader('Content-Transfer-Encoding', '8bit')
                ->addHeader('X-Mailer:', 'PHP/'.phpversion())
                ->send(self::getTransport());
        } catch(Exception $e) {
            //salvo su un log
            file_put_contents("mail.log", $e->getMessage()."\n",8);
        }
    }

    public static function sendBCC($to, $bcc, $subject, $message, $attachments = array()) {
        try {
            $mail = new Zend_Mail("utf-8");
            $mail
                ->addTo($to[0],$to[1])
                ->addBcc($bcc) //passare array("mail@ail.il", "iail@mai.il);
                ->setFrom(self::$_from["mail"], self::$_from["name"])
                ->setSubject($subject)
                ->setBodyHtml($message)
                ->setBodyText($message)
                ->setReplyTo(self::$_from["mail"], self::$_from["name"])
                ->addHeader('MIME-Version', '1.0')
                ->addHeader('Content-Transfer-Encoding', '8bit')
                ->addHeader('X-Mailer:', 'PHP/'.phpversion())
                ->send(self::getTransport());
        } catch(Exception $e) {
            //salvo su un log
            file_put_contents("mail.log", $e->getMessage()."\n",8);
        }
    }

    public static function getTransport() {
        return new Zend_Mail_Transport_Smtp(self::$_server, self::$_config);
    }

}
