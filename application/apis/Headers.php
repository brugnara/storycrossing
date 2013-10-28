<?php

class Engine_Api_Headers {

    public static function _404($errorDesc = "") {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        die(print_r($errorDesc,1));
    }

    public static function _403() {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden'");
        die;
    }

}