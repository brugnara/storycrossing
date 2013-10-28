<?php

class Users_Form_Blacklist extends Zend_Form
{

    public function init()
    {
        $this->addElement("Select","lock_type",array(
            "multiOptions" => array(
                "normal" => "Normal",
                "hide" => "Hide forever",
            ),
            "required" => true,
            "value" => "normal",
            "label" => "Select lock level",
        ));
        $this->addElement("Button","submit",array(
            "type" => "submit",
            "value" => "Lock!",
        ));
    }


}

