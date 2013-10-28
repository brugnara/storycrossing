<?php

class Engine_Api_Local {

    public static function isValid($local) {
        if (empty($local)) {
            return false;
        }
        $tLocal = new Application_Model_DbTable_Languages();
        return count($tLocal->fetchAll($tLocal->select()->where("language_name = ?",$local)));
    }

    public static function getLanguages($startWithEmpty = false) {
        $tLocal = new Application_Model_DbTable_Languages();
        $obj = $tLocal->fetchAll($tLocal->select());
        if ($startWithEmpty) {
            $ret = array();
        } else {
            $ret = array("0" => "");
        }
        foreach ($obj as $o) {
            $ret[$o->language_name] = ucfirst($o->language_name);
        }
        return $ret;
    }

}