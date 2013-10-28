<?php

class Users_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_users';
    protected $_rowClass = 'Users_Model_User';

    public function addUser($data) {
        if ($this->isRegistered($data["user_name"])){
            return false;
        }
        if ($this->isEmailPresent($data["user_mail"])) {
            return false;
        }
        $data["user_pass"] = sha1($data["user_pass"]);
        $data2 = array(
            "user_name" => $data["user_name"],
            "user_pass" => $data["user_pass"],
            "user_mail" => $data["user_mail"],
            "user_locale" => $data["user_locale"],
        );
        return $this->insert($data2);
    }

    public function isRegistered($user) {
        $select = $this->select();
        $select->where("user_name = ?",$user);
        return count($this->fetchAll($select));
    }

    public function isEmailPresent($email) {
        $select = $this->select();
        $select->where("user_mail = ?",$email);
        return count($this->fetchAll($select));
    }

    public function isLoginOk(&$data, $pass = true) {
        $select = $this->select();
        $select
            ->where("user_mail = ?",$data["user_mail"]);
        if ($pass)
            $select->where("user_pass = ?",sha1($data["user_pass"]));
        $fetch = $this->fetchAll($select);
        if (count($fetch)) {
            $data["user_id"] = $fetch->current()->user_id;
            $data["user_name"] = $fetch->current()->user_name;
            $data["user_locale"] = $fetch->current()->user_locale;
            return true;
        }
        return false;
    }

}

