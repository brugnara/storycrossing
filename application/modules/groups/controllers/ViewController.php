<?php

class Groups_ViewController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function oneAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
        $idGroup = (int)$this->_getParam("id");
        if (!$idGroup) {
            //redirect!
        }
        //carico tutte le storie facenti parte di questo gruppo!
        $this->_helper->redirector->gotoRoute(array(
            'module' => "books",
            'controller' => "stories",
            'action' => 'showall',
            'idgroup' => $idGroup,
        ),null,true);
    }

}

