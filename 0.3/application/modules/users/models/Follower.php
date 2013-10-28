<?php

class Users_Model_Follower extends Engine_Api_Db_Row
{

    public function getInfo() {
        //ritorno le info del follower.
        $tFollowers = new Users_Model_DbTable_Followers();
        $select = $tFollowers->select();
        $select
            ->where("follower_id = ?",$this->getIdentity())
        ;
        $idFollower = $tFollowers->fetchAll($select)->current()->follower_user_id;
        //
        $tUsers = new Users_Model_DbTable_Users();
        $select = $tUsers->select();
        $select
            ->where("user_id = ?",$idFollower)
        ;
        return $tUsers->fetchAll($select)->current();
    }

    public function getFollowedInfo() {
        //ritorno le info del followed.
        $tFollowers = new Users_Model_DbTable_Followers();
        $select = $tFollowers->select();
        $select
            ->where("follower_id = ?",$this->getIdentity())
        ;
        $idFollower = $tFollowers->fetchAll($select)->current()->follower_writer_id;
        //
        $tUsers = new Users_Model_DbTable_Users();
        $select = $tUsers->select();
        $select
            ->where("user_id = ?",$idFollower)
        ;
        return $tUsers->fetchAll($select)->current();
    }

}

