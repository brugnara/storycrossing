<?php

class Engine_Api_Logintokens {

    public static function getUserInfoFromHash($token) {
        $empty = new stdClass();
        $empty->user_id = 0;
        if (empty($token)) {
            return $empty;
        }
        //controllo che il token non sia scaduto
        $tTokens = new Application_Model_DbTable_Logintokens();
        $select = $tTokens->select();
        $select
            ->where("login_token_hash = ?",$token);
        $fetch = $tTokens->fetchAll($select);
        if (count($fetch)) {
            $fetch = $fetch->current();
            if ($fetch->login_token_expires > time()) {
                $fetch->delete();
            } else {
                return Engine_Api_Users::getAUserInfo($fetch->login_token_user_id);
            }
        }
        return $empty;
    }

    public static function deleteUserToken($idUser) {
        $tTokens = new Application_Model_DbTable_Logintokens();
        $select = $tTokens->select();
        $select
            ->where("login_token_user_id = ?",$idUser);
        $fetch = $tTokens->fetchAll($select);
        if (count($fetch)) {
            $fetch->current()->delete();
        }
    }

    public static function setAndGetNewToken($idUser) {
        self::deleteUserToken($idUser);
        $token = microtime(1).rand(1,100000).rand(50,1000);
        $token = sha1(md5($token));
        $expires = time() + (3600 * 24 * 30); //scade fra 30 giorni
        $data = Array(
            "login_token_user_id" => $idUser,
            "login_token_hash" => $token,
            "login_token_expires" => @date("Y-m-d H:i:s", $expires)
        );
        $tTokens = new Application_Model_DbTable_Logintokens();
        $tTokens->insert($data);
        return array(
            "HASH" => $token,
            "EXPIRES" => (int)$expires
        );
    }

}