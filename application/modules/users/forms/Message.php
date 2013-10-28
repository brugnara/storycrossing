<?php

class Users_Form_Message extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->addElement("Text",'bodyMessage',array(
            "style" => "-moz-border-radius: 3px;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    color: #555;
                    border-width: 1px;
                    width: 400px;
                    height:20px;
                    border-color: #999 #CCC #CCC #999;",
            "onclick" => "if(this.value=='".$ts->translate('Leave a message!')."'){this.value = '';}"

        ));
//        $this->addElement("Button",'submit',array(
//            'type' => 'submit',
//            'label' => 'Send!',
//        ));
    }

    public function myReset() {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->bodyMessage->setValue($ts->translate('Leave a message!'));
    }

}

