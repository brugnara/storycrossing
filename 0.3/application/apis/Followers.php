<?php

class Engine_Api_Followers {

    public static function addFollower($idUser,$idWriter) {
        $tFollowers = new Users_Model_DbTable_Followers();
        return $tFollowers->addFollower($idUser, $idWriter);
    }

    public static function delFollower($idUser,$idWriter) {
        $tFollowers = new Users_Model_DbTable_Followers();
        return $tFollowers->delFollower($idUser, $idWriter);
    }

    public static function iAmAlreadyFollowerOf($idWriter) {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $tFollowers = new Users_Model_DbTable_Followers();
        return $tFollowers->isFollowing($idUser, $idWriter);
    }

    public static function getMyFollowers() {
        $tF = new Users_Model_DbTable_Followers();
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        return $tF->getFollowersOf($idUser);
    }

    public static function getFollowersOf($id) {
        $tF = new Users_Model_DbTable_Followers();
        return $tF->getFollowersOf($id);
    }

    public static function getCountOfFollowers($idUser) {
        $tF = new Users_Model_DbTable_Followers();
        $s = $tF->select();
        $s
            ->from($tF->info("name"),array(
                "follower_id",
                "tot" => new Zend_Db_Expr("count(*)")
            ))
            ->where("follower_writer_id = ?",(int)$idUser)
            ;
        if ($fetch = $tF->fetchAll($s)) {
            return $fetch->current()->tot;
        } else {
            return 0;
        }
    }

    public static function getCountOfFollowing($idUser) {
        $tF = new Users_Model_DbTable_Followers();
        $s = $tF->select();
        $s
            ->from($tF->info("name"),array(
                "follower_id",
                "tot" => new Zend_Db_Expr("count(*)")
            ))
            ->where("follower_user_id = ?",(int)$idUser)
            ;
        if ($fetch = $tF->fetchAll($s)) {
            return $fetch->current()->tot;
        } else {
            return 0;
        }
    }

    public static function getWhoIAmFollowing($idUser = null) {
        $tF = new Users_Model_DbTable_Followers();
        if ($idUser == null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        return $tF->getFollowedBy($idUser);
    }

    public static function isFriendOf($idUser,$idWriter) {
        //TODO
    }

    public static function getFriends($idUser = null) {
        if ($idUser == null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        $whoIMFollowing = Engine_Api_Followers::getWhoIAmFollowing($idUser);
        $myFollowers = array();
        foreach (Engine_Api_Followers::getFollowersOf($idUser) as $follower) {
            $myFollowers[] = $follower->follower_user_id;
        }
        $friends = array();
        foreach ($whoIMFollowing as $w) {
            if (in_array($w->follower_writer_id,$myFollowers)) {
                $friends[] = $w;
            }
        }
        return $friends;
    }

}
