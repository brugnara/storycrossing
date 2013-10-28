<?php

class Groups_EditController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction(){
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
        //mostro opzioni di edit se gruppo è di chi lo sta guardando.
        $id = (int)$this->_getParam("id");
        if (!$id || !Engine_Api_Groups::isAdmin(Engine_Api_Users::getUserInfo()->user_id, $id)) {
            //bye
        }
        $this->view->group = Engine_Api_Groups::getGroupInfo($id);
    }

    public function readersAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
        //mostro opzioni di edit se gruppo è di chi lo sta guardando.
        $id = (int)$this->_getParam("id");
        if (!$id || !Engine_Api_Groups::isAdmin(Engine_Api_Users::getUserInfo()->user_id, $id)) {
            //bye
        }
        $this->view->group = Engine_Api_Groups::getGroupInfo($id);
        //leggo utenti del gruppo.
        $this->view->readers = Engine_Api_Groups::getReadersOf($id);
    }

}

