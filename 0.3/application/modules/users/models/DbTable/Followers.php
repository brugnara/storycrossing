<?php

class Users_Model_DbTable_Followers extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_followers';
    protected $_rowClass = 'Users_Model_Follower';

    public function addFollower($idUser,$idWriter) {
        if (empty($idUser)) {
            return $this;
        }
        if ($this->isFollowing($idUser,$idWriter)) {
            return $this;
        }
        return $this->insert(array(
            "follower_user_id" => $idUser,
            "follower_writer_id" => $idWriter,
        ));
    }

    public function delFollower($idUser,$idWriter) {
        if (!$this->isFollowing($idUser,$idWriter)) {
            return $this;
        }
        return $this->_get($idUser, $idWriter)->current()->delete();
    }

    public function getFollowersOf($idUser) {
        $select = $this->select();
        $select
            ->where("follower_writer_id = ?",$idUser)
        ;
        return $this->fetchAll($select);
    }

    public function getFollowedBy($idUser) {
        $select = $this->select();
        $select
            ->where("follower_user_id = ?",(int)$idUser)
        ;
        return $this->fetchAll($select);
    }

    public function getDetailedFollowersOf($idWriter) {
        $select = $this->select();
        $select
            ->from($this->info("name"),array("*"))
            ->setIntegrityCheck(false)
            ->where("follower_writer_id = ?",$idWriter)
            ->join(array(
                "u" => "engine_users"
            ),"u.user_id = follower_user_id")
        ;
        return $this->fetchAll($select);
    }

    public function isFollowing($idUser,$idWriter) {
        return count($this->_get($idUser,$idWriter));
    }

    private function _get($idUser,$idWriter) {
        $select = $this->select();
        $select
            ->where("follower_user_id = ?",$idUser)
            ->where("follower_writer_id = ?",$idWriter)
        ;
        return $this->fetchAll($select);
    }



}

