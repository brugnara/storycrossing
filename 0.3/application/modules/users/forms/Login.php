<?php

class Users_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->addElement("Text","user_mail",array(
            "label" => "Email"
        ));
        $this->addElement("Password","user_pass",array(
            "label" => "Password"
        ));

//        $this->addElement("Captcha","captcha",array(
//            "label" => "Please enter the letters displayed below:",
//            "required" => true,
//            "captcha" => array(
//                "captcha" => "Figlet",
//                "wordLen" => 6,
//                "timeout" => 300,
//            )
//        ));

        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Login!!",
        ));
    }


}

