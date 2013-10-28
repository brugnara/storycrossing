<?php

class Books_Form_Page extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->addElement("Text","page_title",array(
            "label" => $ts->translate("Page title"),
            "required" => true,
            "maxlength" => 45,
            "style" => "width:200px"
        ));
        //background
        $this->addElement("Select","page_bg",array(
            "label" => $ts->translate("Page background"),
            "onchange" => "setPageBackGround(this);",
            "multiOptions" => array(),
            "id" => "select_page_bg"
        ));
        //capital letter
        $this->addElement("Select","page_capital_font",array(
            "label" => $ts->translate("Font of first char."),
            "onchange" => "setCapital(this);",
            "multiOptions" => array(),
            "id" => "select_page_capital_font"
        ));
        $this->addElement("Text","page_tags",array(
            "label" => $ts->translate("PAGE_TAGS"),
//            "required" => true,
        ));
        //resto
        $this->addElement("Textarea","page_text",array(
            "label" => $ts->translate("Page Body"),
            "required" => true,
            'rows' => 20,
            'onkeyup' => 'Engine_Api_Utility.checkCharsCount(this,100,1000,"'.sprintf($ts->translate('CHARS_COUNTER_TITLE_KO'),100,1000).'","'.sprintf($ts->translate('CHARS_COUNTER_TITLE_OK'),100,1000).'");',
            'onclick' => 'Engine_Api_Utility.checkCharsCount(this,100,1000,"'.sprintf($ts->translate('CHARS_COUNTER_TITLE_KO'),100,1000).'","'.sprintf($ts->translate('CHARS_COUNTER_TITLE_OK'),100,1000).'");'
        ));
        $this->addElement("Button","submit",array(
            "label" => $ts->translate("Add!"),
            "type" => "submit",
        ));
    }

    public function setValues($options,$value) {
        $this->page_bg->setMultiOptions($options);
        $this->page_bg->setValue($value);
    }

    public function setFonts($values,$value) {
        $this->page_capital_font->setMultiOptions($values);
        $this->page_capital_font->setValue($value);
    }

    public function setContents($title,$text) {
        $this->page_title->setValue($title);
        $this->page_text->setValue($text);
    }

    public function prepareForEdit() {
        $this->page_tags->setLabel("You can only add more tags.");
        $this->page_tags->setRequired(false);
        $this->submit->setLabel("Edit!");
    }

}

