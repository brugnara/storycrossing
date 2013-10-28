<?php

class Engine_Api_Stream {

    private static $_table = null;

    public static function add($idPoster,$type,$idObject = 0, $fromMobile = 0) {
        self::_getTable()->insert(array(
            "stream_user_id" => $idPoster,
            "stream_type" => $type,
            "stream_object_id" => $idObject,
            "stream_from_mobile" => (int)$fromMobile,
        ));
    }

    public static function get($last = 0) {
        $select = self::_getTable()->select();
        $select
            ->order("stream_id DESC")
            ->limit(20);
        if ($last) {
            $select
                ->where("stream_id > ?",$last);
        }
        return self::_getTable()->fetchAll($select);
    }

    private static function _getTable() {
        if (self::$_table == null) {
            self::$_table = new Stream_Model_DbTable_Stream();
        }
        return self::$_table;
    }

}