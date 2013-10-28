<?php

class Getraw_UserController extends Engine_Api_Controller {

    public function bookmarksAction() {
        //
    }

    public function infoAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //per il momento faccio tornare le info solo dell'utente loggato e non degli altri!
        $this->_return($userInfo->getDetails());
    }

    public function followAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //controllo action
        $action = $this->_getParam("act");
        $idWriter = (int)$this->_getParam("id_writer");
        if (($action != "f" && $action != "u") || empty($idWriter)) {
            Engine_Api_Headers::_404("Qualcosa è andato wrong");
        }
        //ho tutto il necessario.
        if ($action == "f") {
            Engine_Api_Followers::addFollower($userInfo->user_id, $idWriter);
            $status = 1;
        }
        if ($action == "u") {
            $status = 2;
            Engine_Api_Followers::delFollower($userInfo->user_id, $idWriter);
        }
        return $this->_return(array(
            "status" => $status,
        ));
    }

    public function followersAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //
//        $myFollowers = Engine_Api_Followers::getWhoIAmFollowing($userInfo->user_id);
        $myFollowers = Engine_Api_Followers::getFollowersOf($userInfo->user_id);
        //
        $users = array();
        foreach ($myFollowers as $follower) :
            $tmp = $follower->getInfo();
            $users[] = array(
                "id" => $tmp->user_id,
                "name" => $tmp->user_name,
            );
        endforeach;
        $this->_return(array(
            "followers" => $users,
        ));
    }

    public function followingAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //
        $users = array();
        $myFollowings = Engine_Api_Followers::getWhoIAmFollowing($userInfo->user_id);
        foreach ($myFollowings as $follower) :
            $tmp = $follower->getFollowedInfo();
            $users[] = array(
                "id" => $tmp->user_id,
                "name" => $tmp->user_name,
            );
        endforeach;
        $this->_return(array(
            "following" => $users,
        ));
    }

    public function friendsAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //
        $friends = array();
        $arFriends = Engine_Api_Followers::getFriends($userInfo->user_id);
        foreach ($arFriends as $follower) :
            $tmp = $follower->getFollowedInfo();
            $friends[] = array(
                "id" => $tmp->user_id,
                "name" => $tmp->user_name,
            );
        endforeach;
        $this->_return(array(
            "friends" => $friends,
        ));
    }

    public function wallAction() {
        $hash = $this->_getParam("hash");
        $userInfos = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfos->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        $walls = Engine_Api_Wall::get($userInfos->user_id);
        $posts = array();
        foreach ($walls as $wall) {
            $userInfo = $wall->getPosterInfo();
            switch ($wall->user_wall_type) :
                case 'msg' :
                    $footer = Engine_Api_Date_Utility::advancedDateFormat($wall->user_wall_data);
                    $from = $userInfo->user_name;
                    $type = "MSG";
                    $objId = 0;
                    $message = $wall->user_wall_message;
                    break;
                case 'vote' :
                    $footer = Engine_Api_Date_Utility::advancedDateFormat($wall->user_wall_data);
                    $from = $userInfo->user_name;
                    $type = "VOTE";
                    $objId = $wall->user_wall_obj_id;
                    $message = $wall->user_wall_message;
                    break;
                default:
                    $objId = 0;
                    $type = "";
                    $message = "";
                    $from = "";
                    $footer = "";
            endswitch;
            $posts[] = array(
                "from" => $from,
                "message" => $message,
                "footer" => $footer,
                "type" => $type,
                "objid" => (int) $objId,
            );
        }
        //
        $this->_return(array(
            "wall" => ($posts),
        ));
    }

    public function getupdatesAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //Restituisco gli updates non letti!
        $t = new Users_Model_DbTable_Userupdates();
        $updates = $t->getUserUpdates("", $userInfo->user_id, true);
        //
        $this->_return(array(
            "count" => (int) count($updates),
        ));
    }

    public function updatesAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        //restituisco tutte le news
        $tU = new Users_Model_DbTable_Userupdates();
        $pageUpdates = $tU->getUserUpdates("", $userInfo->user_id);
        //
        $count = 0;
        $updates = array();
        foreach ($pageUpdates as $update) {
            if ($update->getDetails() == null) {
                continue;
            }
//            if ($update->getDetails()->object_type == "new_page") {
//                $idpage = $update->getDetails()->object->page_id;
//            }
            $object = $update->getDetails()->object;
            switch (true) {
                case ($object instanceof Books_Model_Page) :
                    $userInfo = $object->getUserInfo();
                    break;
                case ($object instanceof Books_Model_Story) :
                    $userInfo = $object->getWriter();
                    break;
                default:
                    continue;
            }
            $read = $update->user_update_read;
            if (!$read) {
                $count++;
            }
            if ($update->getDetails()->object_type == "new_page") :
                $title = $update->getDetails()->object->page_title;
                $type = "PAGE";
                //$this->translate('wrote by')
                $data = Engine_Api_Date_Utility::advancedDateFormat($update->user_update_data, 1);
            endif; //new_page
            if ($update->getDetails()->object_type == "new_story") :
                $title = $object->story_title;
                $type = "STORY";
                $data = Engine_Api_Date_Utility::advancedDateFormat($update->user_update_data, 1);
            endif; //new_story
            $updates[] = array(
                "username" => $userInfo->user_name,
                "title" => $title,
                "date" => $data,
                "type" => $type,
                "objid" => $update->getDetails()->object_id,
                "userid" => $userInfo->user_id,
                "read" => (bool)$read,
            );
        }
        $this->_return(array(
            "news" => $updates,
            "count" => (int)$count,
        ));
    }

}

