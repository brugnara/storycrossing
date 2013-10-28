<?php

class Groups_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // carico tutti i gruppi!
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
        //carico gruppi
        $t = new Groups_Model_DbTable_Groups();
        $this->view->groups = $t->getGroups();
    }


}

