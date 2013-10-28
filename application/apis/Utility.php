<?php

class Engine_Api_Utility {

    public static function getUserSO() {
        //$_SERVER['HTTP_USER_AGENT'];
//        echo $_SERVER['HTTP_USER_AGENT'];
//        if (preg_match('#\((.*)\)#U',$_SERVER['HTTP_USER_AGENT'],$match)) {
//            list($browser) = explode(";",$match[1]);
//            echo "<pre>";
//            print_r($browser);
//            echo "</pre>";
//        }
    }

    public static function getUserBrowser() {
        //$_SERVER['HTTP_USER_AGENT'];
//        $tmp = $_SERVER['HTTP_USER_AGENT'];
//        $tmp = substr($tmp, strrpos($tmp, ")"));
////        if (preg_match('#\)(.*)$#U',$_SERVER['HTTP_USER_AGENT'],$match)) {
//        if (preg_match('#(\) ([a-z]+)/(.+ )([a-z]+)/(.+))$#Umi',$tmp,$match)) {
////            list($browser) = explode(";",$match[1]);
//            echo "<pre>";
//            print_r($match);
//            echo "</pre>";
//        }
    }

    public static function isValidEmail($e) {
        //
    }

    public static function getSiteInfo() {
        $tmp = Zend_Registry::get('config');
        $siteOptions = new stdClass();
        foreach ($tmp['siteinfo'] as $k => $t) {
            $siteOptions->$k = $t;
        }
        return $siteOptions;
    }

}