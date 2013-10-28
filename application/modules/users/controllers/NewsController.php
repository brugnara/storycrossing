<?php

class Users_NewsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "updates");
        //se ci sono updates, li mostro!
        $tU = new Users_Model_DbTable_Userupdates();
        $this->view->pageUpdates = $tU->getUserUpdates("");
    }

    public function setallasreadAction() {
        $tUpdates = new Users_Model_DbTable_Userupdates();
        $tUpdates->setAllAsReadUpdates();
        //finally
        $this->_helper->redirector->gotoRoute(array(
            'module' => "users",
            'controller' => "news",
        ),null,true);
    }

    public function bulkAction() {
        switch ($this->_getParam("bulkAction")) {
            case "setread" :
                $this->_setAsRead();
                break;
            case "delete" :
                $this->_delete();
                break;
        }
        $this->_helper->redirector->gotoRoute(array(
            'module' => "users",
            'controller' => "news",
        ),null,true);
    }

    private function _setAsRead() {
        //in post dovrei avere dei dati.
        $boxs = $this->_getParam("box");
        if (is_array($boxs)) {
            $tUpdates = new Users_Model_DbTable_Userupdates();
            foreach ($boxs as $box) {
                $tUpdates->setAsReadUpdate($box,"new_page");
            }
        }
    }

    private function _delete() {
        //in post dovrei avere dei dati.
        $boxs = $this->_getParam("box");
        if (is_array($boxs)) {
            $tUpdates = new Users_Model_DbTable_Userupdates();
            foreach ($boxs as $box) {
                $tUpdates->delUpdate($box,"new_page");
            }
        }
    }

}

