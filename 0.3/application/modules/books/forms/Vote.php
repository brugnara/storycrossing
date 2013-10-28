<?php

class Books_Form_Vote extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->addElement("Select","vote",array(
            "label" => $ts->translate("Vote!"),
            "multiOptions" => array(
                "1" => 1,
                "2" => 2,
                "3" => 3,
                "4" => 4,
                "5" => 5,
            ),
            "value" => "1",
        ));
        $this->addElement("Text","comment",array(
            "label" => $ts->translate("Short comment")
        ));
        $this->addElement("Button","submit",array(
            "type" => "submit",
            'label' => $ts->translate('Add!')
        ));
    }


}

