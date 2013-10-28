<?php

class Groups_Form_Add extends Zend_Form
{

    public function init() {
        $this->addElement("Text","name",array(
            "label" => "Group name",
            "required" => true,
        ));
        $this->addElement("Text","desc",array(
            "label" => "Group description",
            "required" => true,
        ));
        $this->addElement("Select","type",array(
            "label" => "Group type",
            "multiOptions" => array(
                "1" => "Open",
                "2" => "Read only",
            )
        ));
        $this->addElement("Button","sbmt",array(
            "type" => "submit",
            "label" => "Create!",
        ));
    }


}

