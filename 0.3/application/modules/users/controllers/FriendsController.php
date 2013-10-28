<?php

class Users_FriendsController extends Zend_Controller_Action
{

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function friendsAction() {
        $options = array();
        $id = $this->_getParam("id");
        if (empty($id)) {
            $id = Engine_Api_Users::getUserInfo()->user_id;
        } else {
            $options["id"] = $id;
        }
        $this->view->friends = Engine_Api_Followers::getFriends($id);
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", $options, "user_friends");
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($id);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$id);
    }

    public function followersAction() {
        $options = array();
        //visualizzo i follow
        $id = $this->_getParam("id");
        if (!empty($id)) {
            $options["id"] = $id;
            $this->view->myFollowers = Engine_Api_Followers::getFollowersOf($id);
            $this->view->isMe = false;
        } else {
            $this->view->myFollowers = Engine_Api_Followers::getMyFollowers();
            $id = Engine_Api_Users::getUserInfo()->user_id;
            $this->view->isMe = true;
        }
        if (empty($id)) {
            $this->_helper->redirector->gotoRoute(array(),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", $options, "user_followers");
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($id);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$id);
    }

    public function followingAction() {
        $options = array();
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        //
        $id = $this->_getParam("id");
        if (empty($id)) {
            $id = Engine_Api_Users::getUserInfo()->user_id;
        } else {
            $options["id"] = $id;
        }
        if (empty($id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", $options, "user_following");
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($id);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$id);
        $this->view->whoIAmFollowing = Engine_Api_Followers::getWhoIAmFollowing($id);
    }


}

