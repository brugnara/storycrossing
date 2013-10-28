<?php

class Engine_Api_Core {

    public function __construct() {
        echo "ciao";
        die;
    }

    public static function _() {
        $self = new self();
    }

}