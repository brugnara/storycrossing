<?php

class Engine_Api_Stack_Viewer {

    public static function getNavigation($stack) {
        $navigation = new Zend_Navigation();
        $navigation->addPages($stack);
        return $navigation;
    }

}