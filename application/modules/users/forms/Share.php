<?php

class Users_Form_Share extends Zend_Form
{

    public function init() {
        //
        //
        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Share!!",
        ));
    }

}

