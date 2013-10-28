<?php

class Users_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $this->_helper->redirector->gotoRoute(array(
            'controller' => "profile",
            'action' => "view",
        ));
    }

}

