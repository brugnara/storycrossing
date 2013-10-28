<?php

class Application_Form_Query extends Zend_Form
{

    public function init()
    {
        $this->addElement("Text","text",array(
            "label" => "Text",
            "required" => true,
        ));
        $this->addElement("Button","submit",array(
            "type" => "submit",
            "label" => "Save!",
        ));
    }


}

