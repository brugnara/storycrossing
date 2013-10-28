<?php

class Search_Form_Search extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->setAttrib("id", "formSearch");
        $this->addElement("Select","what",array(
            "multiOptions" => array(
                "story" => $ts->translate("..story"),
                "page" => $ts->translate("..page"),
                "user" => $ts->translate("..user"),
                "tag" => "..tag",
            ),
            "label" => $ts->translate("You are searching a.."),
            "value" => "story",
            "id" => "searchWhat",
            "onchange" => "Engine_Api_Search.page('searchResult');",
        ));
        $this->addElement("Text","search",array(
            "required" => true,
            "onkeyup" => "if (this.value.length < 1){return false;}Engine_Api_Search.page('searchResult');",
            "label" => $ts->translate("Type what you are searching..."),
        ));
        $this->addElement("Checkbox","inTitle",array(
            "label" => $ts->translate("Search in title / username"),
            "checked" => true,
            "onchange" => "Engine_Api_Search.page('searchResult');",
        ));
        $this->addElement("Checkbox","inText",array(
            "label" => $ts->translate("Search in text / email"),
            "onchange" => "Engine_Api_Search.page('searchResult');",
        ));
//        $this->addElement("Button","submit",array(
//            "type" => "submit",
//            "value" => "Search!",
//        ));
    }

}

