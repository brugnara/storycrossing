<?php

class Books_Form_Story extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        //
        //backgrounds
        $this->addElement("Select","page_bg",array(
            "label" => $ts->translate("Page background"),
            "multiOptions" => array(),
            "id" => "select_page_bg",
            "onchange" => "setPageBackGround(this);",
        ));
        $this->addElement("Select","story_bg",array(
            "label" => $ts->translate("Story book cover"),
            "multiOptions" => array(),
            "id" => "select_cover_bg",
            "onchange" => "setCoverBackGround(this);",
        ));
        //
        $this->addElement("Text","story_title",array(
            "label" => $ts->translate("Story title"),
            "required" => true,
            "maxlength" => 45
        ));
        $this->addElement("Text","page_tags",array(
            "label" => $ts->translate("PAGE_TAGS"),
            "required" => true,
        ));
        $this->addElement("Select","story_language",array(
            "label" => $ts->translate("Story language"),
            "multiOptions" => array(),
        ));
        //font
        $this->addElement("Select","page_font",array(
            "label" => $ts->translate("Font of first char."),
            "multiOptions" => array(),
            "id" => "select_page_capital_font",
            "onchange" => "setCapitalBackGround(this);",
        ));
        $this->addElement("Textarea","story_body",array(
            "label" => $ts->translate("Page Body"),
            "required" => true,
            'onkeyup' => 'Engine_Api_Utility.checkCharsCount(this,100,1000,"'.sprintf($ts->translate('CHARS_COUNTER_TITLE_KO'),100,1000).'","'.sprintf($ts->translate('CHARS_COUNTER_TITLE_OK'),100,1000).'");',
            'onclick' => 'Engine_Api_Utility.checkCharsCount(this,100,1000,"'.sprintf($ts->translate('CHARS_COUNTER_TITLE_KO'),100,1000).'","'.sprintf($ts->translate('CHARS_COUNTER_TITLE_OK'),100,1000).'");'
        ));
        $this->addElement("Button","submit",array(
            "label" => $ts->translate("Save!"),
            "type" => "submit",
        ));
    }

    public function setValues($optionsPage,$valuePage,$optionsStory,$valueStory) {
        $this->page_bg->setMultiOptions($optionsPage);
        $this->page_bg->setValue($valuePage);
        $this->story_bg->setMultiOptions($optionsStory);
        $this->story_bg->setValue($valueStory);
    }

    public function setFonts($values,$value) {
        $this->page_font->setMultiOptions($values);
        $this->page_font->setValue($value);
    }

    public function setLocal($values,$value) {
        $this->story_language->setMultiOptions($values);
        $this->story_language->setValue($value);
    }

}

