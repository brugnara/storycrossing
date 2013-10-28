<?php

class MenuController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if (!Engine_Api_Users::isLogged()) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "index",
            ),null,true);
        }
        //test
        echo "<pre>";
//        print_r(get_browser(null, true));
        print_r($_SERVER['HTTP_USER_AGENT']);
//        print_r($_SERVER);
        echo "</pre>";
        //tolgo dalla sessione la storia.
        Engine_Api_Session::setVar("story");
    }


}

