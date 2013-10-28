<?php

class Search_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if (empty(Engine_Api_Users::getUserInfo()->user_id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "search");
        $form = new Search_Form_Search();
        $this->view->form = $form;
    }


}

