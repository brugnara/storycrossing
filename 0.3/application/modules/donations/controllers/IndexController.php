<?php

class Donations_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "donations");
        //mostro lista dei donatori e bottone per donare.
        $this->view->donations = Engine_Api_Donations::getLeaderBoard();
    }

}

