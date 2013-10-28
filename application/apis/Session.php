<?php

class Engine_Api_Session {

    private static $_sessionNameSpace = "storycrossing.com";
    private static $_ = null;

    public static function getVar($varName,$defaultReturnValue = null) {
        //controllo che ci sia una sessione e quindi che l'utente sia loggato.
        if (!empty(self::_()->$varName)) {
            return self::_()->$varName;
        } else {
            return $defaultReturnValue;
        }
    }

    public static function setVar($varName,$value = null) {
        self::_()->$varName = $value;
    }

    public static function getSessionNameSpace() {
        return self::$_sessionNameSpace;
    }

    private static function _() {
        if (self::$_ == null) {
            self::$_ = new Zend_Session_Namespace(self::getSessionNameSpace());
        }
        return self::$_;
    }

}