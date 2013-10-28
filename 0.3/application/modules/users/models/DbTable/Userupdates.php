<?php

class Users_Model_DbTable_Userupdates extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_user_updates';
    protected $_rowClass = 'Users_Model_Userupdate';

    public function getUserUpdates($type = "",$idUser = null,$onlyNew = false) {
        //posso guardare gli update del solo utente loggato!
        if ($idUser == null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (!$idUser) {
            return null;
        }
        $select = $this->select();
        $select
            ->where("user_update_user_id = ?",$idUser)
        ;
        if ($onlyNew) {
            $select
                ->where("user_update_read = 0");
        }
        if ($type != "") {
            $select
                ->where("user_update_type = ?",$type);
        } else {
            $select
                ->order("user_update_type");
        }
        $select
            ->order("user_update_data DESC");
        return ($this->fetchAll($select));
    }

    public function getNewUserUpdates($idUser = null) {
        return $this->getUserUpdates("", $idUser, true);
    }

    public function getCountUserUpdates() {
        return count($this->getNewUserUpdates());
    }

    public function add($idUser,$type,$idObject) {
        return $this->insert(array(
            "user_update_user_id" => $idUser,
            "user_update_type" => $type,
            "user_update_object_id" => $idObject,
        ));
    }

    public function setAllAsReadUpdates() {
        foreach ($this->getNewUserUpdates() as $update) {
            $this->update(array(
                "user_update_read" => 1,
            ),array(
                "user_update_id = ?" => $update->user_update_id,
            ));
        }
    }

    public function delUpdate($idObject,$type) {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $select = $this->select();
        $select
            ->where("user_update_user_id = ?",$idUser)
            ->where("user_update_object_id = ?",(int)$idObject)
            ->where("user_update_type = ?",$type)
        ;
        $fetch = $this->fetchAll($select);
        if (count($fetch)) {
            $fetch->current()->delete();
        }
        return $this;
    }

    public function setAsReadUpdate($idObject,$type,$idUser = null) {
        if ($idUser === null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (!$idUser)
            return $this;
        $this->update(array(
            "user_update_read" => 1,
        ),array(
            "user_update_user_id = ?" => $idUser,
            "user_update_object_id = ?" => (int)$idObject,
            "user_update_type = ?" => $type,
        ));
        return $this;
    }

}

