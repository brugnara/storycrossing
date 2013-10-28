<?php

class Users_FollowersController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('popup');
    }

    public function indexAction()
    {
        // ?
    }

    public function addAction() {
        $idWriter = $this->_getParam("idwriter");
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if ($idUser != $idWriter && $idUser) {
            Engine_Api_Followers::addFollower($idUser,$idWriter);
        }
    }

    public function delAction() {
        $idWriter = $this->_getParam("idwriter");
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if ($idUser != $idWriter && $idUser) {
            Engine_Api_Followers::delFollower($idUser,$idWriter);
        }
    }


}

