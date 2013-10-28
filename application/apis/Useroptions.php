<?php

class Engine_Api_Useroptions {

    private static $_table = null;

    public static function get($name,$idUser = null) {
        $s = self::_()->select();
        if ($idUser == null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        } else {
            $idUser = (int)$idUser;
        }
        $s
            ->where("user_option_user_id = ?",$idUser)
            ->where("user_option_name = ?",self::_trim($name))
        ;
        if (count($fetch = self::_()->fetchAll($s))) {
            return $fetch->current()->user_option_value;
        } else {
            return null;
        }
    }

    public static function set($name,$value = null) {
        $name = self::_trim($name);
        if (self::get($name) == null) {
            self::_()->insert(array(
                'user_option_user_id' => Engine_Api_Users::getUserInfo()->user_id,
                'user_option_name' => $name,
                'user_option_value' => $value,
            ));
        } else {
            self::_()->update(array(
                'user_option_value' => $value,
            ),array(
                'user_option_user_id = ?' => Engine_Api_Users::getUserInfo()->user_id,
                'user_option_name = ?' => $name
            ));
        }
    }

    /**
     * ciao
     *
     * @return Users_Model_DbTable_Options
     */
    private static function _() {
        if (self::$_table == null) {
            self::$_table = new Users_Model_DbTable_Options();
        }
        return self::$_table;
    }

    private static function _trim($v) {
        $ret = trim($v);
        return $ret;
    }

}