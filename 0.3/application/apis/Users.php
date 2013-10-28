<?php

class Engine_Api_Users {

    private static $_nameSpace = "user";

    public static function isLogged() {
        return Engine_Api_Session::getVar(self::$_nameSpace);
        $auth = Engine_Api_Multiplelogins_Auth::getInstance();
        return $auth->hasIdentity();
    }

    public static function setLogout() {
        Engine_Api_Session::setVar(self::$_nameSpace);
    }

    public static function setLogin(Array $user) {
        unset($user["user_pass"]);
        Engine_Api_Session::setVar(self::$_nameSpace, $user);
    }

    public static function getLocale() {
        $tmp = self::getUserInfo();
        if ($tmp->user_id) {
            return $tmp->user_locale;
        } else {
            return "it_IT";
        }
    }

    public static function setNewLogin(Engine_Api_Multiplelogins_Auth $auth) {
        //
    }

    public static function getUserInfo() {
        $ret = new stdClass();
        $userInfo = Engine_Api_Session::getVar(self::$_nameSpace);
        if (!empty($userInfo)) {
            foreach ($userInfo as $k => $var) {
                $ret->$k = $var;
            }
        } else {
            $ret->user_id = 0;
        }
        return $ret;
    }

    public static function getAUserInfo($id) {
        $tUsers = new Users_Model_DbTable_Users();
        return $tUsers->fetchAll($tUsers->select()->where("user_id = ?",(int)$id))->current();
    }

    public static function updateLastSeen() {
        $tUsers = new Users_Model_DbTable_Users();
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if ($idUser == 0) {
            return;
        }
        $tUsers->update(array(
            "user_last_seen" => new Zend_Db_Expr('NOW()')
        ),array(
            "user_id = ?" => $idUser
        ));
    }

    public static function editUserInfo($values) {
        $tUsers = new Users_Model_DbTable_Users();
        $tUsers->update(array(
            //"user_mail" => $values["user_mail"],
            "user_locale" => $values["user_locale"]
        ),array(
            "user_id = ?" => $values["user_id"]
        ));
    }

    public static function getCountOfVotes($idUser) {
        $tV = new Books_Model_DbTable_Pagevotes();
        $s = $tV->select();
        $s
            ->from($tV->info("name"),array(
                "page_vote_id",
                "tot" => new Zend_Db_Expr("count(*)")
            ))
            ->where("page_vote_user_id = ?",(int)$idUser);
        if ($fetch = $tV->fetchAll($s)) {
            return $fetch->current()->tot;
        } else {
            return 0;
        }
    }

    public static function getCountOfPagesRead($idUser) {
        $t = new Books_Model_DbTable_Pageviews();
        $s = $t->select();
        $s
            ->from($t->info('name'),array(
                "page_view_id",
                "tot" => new Zend_Db_Expr("count(*)")
            ))
            ->where("page_view_user_id = ?",(int)$idUser);
        if ($fetch = $t->fetchAll($s)) {
            return $fetch->current()->tot;
        } else {
            return 0;
        }
    }

}