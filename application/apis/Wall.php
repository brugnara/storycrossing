<?php

class Engine_Api_Wall {

    private static $_table = null;

    public static function get($idUser,$limit = 100) {
        $s = self::_getTable()->select();
        $s
            ->where('user_wall_user_id = ?',(int)$idUser)
            ->order('user_wall_id DESC')
            ->limit($limit)
        ;
        return self::_getTable()->fetchAll($s);
    }

    public static function add(Array $values,$idUser,$idWriter = null) {
        if ($idWriter == null) {
            $idWriter = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (!$idWriter || !$idUser) {
            return false;
        }
        $_message = $values["message"];
        $_objId = empty($values["object_id"]) ? 0 : (int)$values["object_id"];
        $_type =  empty($values["type"]) ? "msg" : $values["type"];
        //inserisco notifica
        
        //add
        return self::_getTable()
                ->insert(array(
                    'user_wall_user_id' => (int)$idUser,
                    'user_wall_writer_id' => (int)$idWriter,
                    'user_wall_message' => Engine_Api_Output::clean($_message),
                    'user_wall_obj_id' => $_objId,
                    'user_wall_type' => $_type,
                ));
    }

    private static function _getTable() {
        if (self::$_table == null) {
            self::$_table = new Users_Model_DbTable_Walls();
        }
        return self::$_table;
    }

}