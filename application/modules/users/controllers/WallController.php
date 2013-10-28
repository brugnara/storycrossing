<?php

class Users_WallController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function addmessageAction() {
        $this->_helper->layout->setLayout('popup');
        $idDest = $this->_getParam('id');
        $form = new Users_Form_Message();
        //
        $this->view->isOk = false;
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            Engine_Api_Wall::add(array(
                'message' => $values["bodyMessage"],
            ),$idDest);
            $this->view->isOk = true;
        }
        //
        $this->view->form = $form;
    }

    public function indexAction()
    {
        $idUser = $this->_getParam("id");
        $options = array();
        if (!empty($idUser)) {
            $options["id"] = $idUser;
            $this->view->isMe = false;
        } else {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
            $this->view->isMe = true;
        }
        //
        $form = new Users_Form_Message();
        $idDest = $this->_getParam('id');
        if (empty($idDest)) {
            $idDest = Engine_Api_Users::getUserInfo()->user_id;
        }
        if ($idDest && $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            Engine_Api_Wall::add(array(
                'message' => $values["bodyMessage"],
            ),$idDest);
        }
        //
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", $options, "user_works");
        //leggo la wall utente.
        $this->view->wall = Engine_Api_Wall::get($idUser);
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($idUser);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$idUser);
        //add message
        $form->myReset();
        if (Engine_Api_Users::isLogged()) {
            $this->view->form = $form;
        } else {
            $this->view->form = "";
        }
    }


}

