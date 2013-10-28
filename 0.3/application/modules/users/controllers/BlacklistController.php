<?php

class Users_BlacklistController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", array(), "user_blacklist");
        $id = Engine_Api_Users::getUserInfo()->user_id;
        if (empty($id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($id);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$id);
        //visualizzo la mia blacklist
        $this->view->myBlacklist = Engine_Api_Blacklists::getMyBlacklist(false);
    }

    public function addAction() {
        $this->_helper->layout->setLayout('popup');
        $idLock = $this->_getParam("id");
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if ($idLock == $idUser || empty($idUser)) {
            $this->view->error = true;
            return false;
        }
        $lockInfo = Engine_Api_Users::getAUserInfo($idLock);
        $this->view->lockInfo = $lockInfo;
        $form = new Users_Form_Blacklist();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            switch ($values["lock_type"]) {
                case "normal":
                case "hide":
                    $lockType = $values["lock_type"];
                    break;
                default:
                    $lockType = "normal";
            }
            $data = array(
                "blacklist_user_id" => $idUser,
                "blacklist_blocked_id" => $idLock,
                "blacklist_lock_type" => $lockType,
            );
            $tBlacklist = new Application_Model_DbTable_Blacklists();
            $tBlacklist->add($data);
            $this->view->isOk = true;
        }
        $this->view->form = $form;
    }

    public function delAction() {
        $this->_helper->layout->setLayout('popup');
        if (empty(Engine_Api_Users::getUserInfo()->user_id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $idLock = $this->_getParam("id");
        Engine_Api_Blacklists::delLock($idLock);
    }


}

