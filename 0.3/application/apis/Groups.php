<?php

class Engine_Api_Groups {

    public static function getGroupInfo($idGroup) {
        $t = new Groups_Model_DbTable_Groups();
        $s = $t->select();
        $s->where("group_id = ?",(int)$idGroup);
        return $t->fetchAll($s)->current();
    }

    public static function isMemberOf($idUser,$idGroup) {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->where("group_member_user_id = ?",(int)$idUser)
            ->where("group_member_group_id = ?",(int)$idGroup)
        ;
        return count($t->fetchAll($s));
    }

    public static function isAdmin($idUser,$idGroup) {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->where("group_member_user_id = ?",(int)$idUser)
            ->where("group_member_group_id = ?",(int)$idGroup)
            ->where("group_member_is_owner = 1 OR group_member_is_admin = 1")
        ;
        return count($t->fetchAll($s));
    }

    public static function isOwner($idUser,$idGroup) {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->where("group_member_user_id = ?",(int)$idUser)
            ->where("group_member_group_id = ?",(int)$idGroup)
            ->where("group_member_is_owner = 1")
        ;
        return count($t->fetchAll($s));
    }

    public static function getReadersOf($idGroup) {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->where("group_member_group_id = ?",(int)$idGroup)
        ;
        return $t->fetchAll($s);
    }

    public static function getLevelOf($idUser,$idGroup) {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->where("group_member_user_id = ?",(int)$idUser)
            ->where("group_member_group_id = ?",(int)$idGroup)
        ;
        $res = $t->fetchAll($s)->current();
        if ($res) {
            if ($res->group_member_is_owner) {
                return "owner";
            }
            if ($res->group_member_is_admin) {
                return "admin";
            }
            if ($res->group_member_can_write) {
                return "writer";
            }
        }
        return null;
    }

    public static function delMember($idUser,$idGroup) {
        //se è già membro lo sego
        if (!self::isMemberOf($idUser, $idGroup)) {
            return false;
        }
        $t = new Groups_Model_DbTable_Members();
        return $t->delete(array(
            "group_member_group_id = ?" => (int)$idGroup,
            "group_member_user_id = ?" => (int)$idUser,
        ));
    }

    public static function addMember($idUser,$idGroup) {
        //controllo che non sia già membro
        if (self::isMemberOf($idUser, $idGroup)) {
            return false;
        }
        $t = new Groups_Model_DbTable_Members();
        $data = array(
            "group_member_group_id" => (int)($idGroup),
            "group_member_user_id" => (int)($idUser),
        );
        return $t->insert($data);
    }

    public static function setUserPermissions($idUser, $idGroup, array $permits) {
        $t = new Groups_Model_DbTable_Members();
        return $t->update(array(
            "group_member_is_owner" => (int)$permits[0],
            "group_member_is_admin" => (int)$permits[1],
            "group_member_can_write" => (int)$permits[2],
        ),array(
            "group_member_user_id = ?" => (int)$idUser,
            "group_member_group_id = ?" => (int)$idGroup,
        ));
    }

}