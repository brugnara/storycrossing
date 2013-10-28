<?php

class Engine_Api_Blacklists {

    const NO_LOCK = "nolock";

    public static function getMyBlacklist($lock_type = "normal", $asIdList = false) {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $tBl = new Users_Model_DbTable_Blacklists();
        $select = $tBl->select();
        $select
            ->where("blacklist_user_id = ?",$idUser);
        if ($lock_type != false)
            $select
                ->where("blacklist_lock_type = ?",$lock_type);
        $blacklist = $tBl->fetchAll($select);
        if ($asIdList) {
            $tmp = array();
            foreach ($blacklist as $element) {
                $tmp[] = $element->blacklist_blocked_id;
            }
            return $tmp;
        }
        return $blacklist;
    }

    public static function isInBlackList($idUser) {
        //
    }

    public static function getLockType($idWriter) {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $tBl = new Users_Model_DbTable_Blacklists();
        $select = $tBl->select();
        $select
            ->where("blacklist_user_id = ?",$idUser)
            ->where("blacklist_blocked_id = ?",(int)$idWriter)
        ;
        $fetch = $tBl->fetchAll($select);
        if (count($fetch)) {
            return $fetch->current()->blacklist_lock_type;
        } else {
            return Engine_Api_Blacklists::NO_LOCK;
        }
    }

    public static function delLock($idWriter) {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $tBl = new Users_Model_DbTable_Blacklists();
        $select = $tBl->select();
        $select
            ->where("blacklist_user_id = ?",$idUser)
            ->where("blacklist_blocked_id = ?",(int)$idWriter)
        ;
        $fetch = $tBl->fetchAll($select);
        if (count($fetch)) {
            return $fetch->current()->delete();
        }
        return null;
    }

}