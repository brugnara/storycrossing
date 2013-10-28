<?php

class Engine_Api_Config {

    private static $_config = null;

    public static function getConfig($varName = null) {
        self::init();
        return self::_read($varName);
    }

    public static function setConfig($varName,$varValue) {
        //
    }

    public static function init() {
        if (self::$_config != null) {
            return;
        }
        //leggo da db i valori di configurazione.
        $tConfigs = new Application_Module_DbTable_Configs();
        $select = $tConfigs->select();
        $select
            ->where("enabled = 1")
        ;
        $fetch = $tConfigs->fetchAll($select);
        if (count($fetch)) {
            foreach ($fetch as $key => $row) {
                self::_write($row->config_name,$row->config_value);
            }
        }
    }

    private static function _read($varName) {
        if (!empty(self::$_config[$varName])) {
            return self::$_config[$varName];
        } else {
            return null;
        }
    }

    private static function _write($varName,$varValue) {
        self::$_config[$varName] = $varValue;
    }

}