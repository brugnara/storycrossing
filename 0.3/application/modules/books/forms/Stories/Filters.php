<?php

class Books_Form_Stories_Filters extends Zend_Form
{

    public function init()
    {
        $ts = Zend_Registry::get("Zend_Translate");
        $this->setMethod("get");
        $url = new Zend_View_Helper_Url();
//        $this->setAction($url->url(array(
//            "action" => "index2"
//        )));
        $this->setAttrib("id", "formOrderBy");
        $this->addElement("Select","orderby",array(
            "label" => $ts->translate("Order by"),
            "multiOptions" => array(
                "",
                "votes" => $ts->translate("Votes"),
                "votomedio" => $ts->translate("Medium vote"),
                "views" => $ts->translate("Views"),
                "pagecount" => $ts->translate("Page count"),
                "pagenew" => $ts->translate("New pages"),
                "lastactivity" => $ts->translate("Last page date"),
            ),
            "onchange" => "window.location.href='".$url->url(array(
                "controller" => "stories",
                "action" => "showall",
                "orderby" => null,
            ))."/orderby/'+getSelectValue(this);return false;",
        ));
        $this->addElement("Select","tags",array(
            "label" => "Tag",
            "multiOptions" => array(),
            "onchange" => "window.location.href='".$url->url(array(
                "controller" => "stories",
                "action" => "showall",
                "tags" => null,
            ))."/tags/'+getSelectValue(this);return false;",
        ));
        $this->addElement("Select","local",array(
            "label" => $ts->translate("Language"),
            "multiOptions" => array(),
            "onchange" => "window.location.href='".$url->url(array(
                "controller" => "stories",
                "action" => "showall",
                "local" => null,
            ))."/local/'+getSelectValue(this);return false;",
        ));
    }

    public function setTags($values) {
        $this->tags->setMultiOptions($values);
    }

    public function setLocal($values) {
        $this->local->setMultiOptions($values);
    }

}

