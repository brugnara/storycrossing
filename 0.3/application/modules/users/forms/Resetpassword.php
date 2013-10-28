<?php

class Users_Form_Resetpassword extends Zend_Form
{

    public function init()
    {
        $this->addElement("Text","user_mail",array(
            "required" => true,
            "label" => "Insert email",
            'validators' => array(
                array('regex',false,'/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/'),
            )
        ));
        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Reset password",
        ));
    }


}

