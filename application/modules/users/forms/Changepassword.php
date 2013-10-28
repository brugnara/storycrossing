<?php

class Users_Form_Changepassword extends Zend_Form
{

    public function init()
    {
//        $this->addElement("Text","user_name",array(
//            "type" => "password",
//            "label" => "Type your username..."
//        ));
//        $this->addElement("Text","user_mail",array(
//            "label" => "...or E-Mail",
//        ));
        $this->addElement("Password","old_password",array(
            "type" => "password",
            "label" => "Old password",
            "required" => true,
        ));
        $this->addElement("Password","user_pass",array(
            "type" => "password",
            "label" => "New password",
            "required" => true,
        ));
        $this->addElement("Password","user_pass2",array(
            "type" => "password",
            "label" => "Repeat password",
            "required" => true,
        ));
        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Ok!"
        ));
    }


}

