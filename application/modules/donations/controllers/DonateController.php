<?php

class Donations_DonateController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->userInfo = Engine_Api_Users::getUserInfo();
        $this->view->dati = array(
            'urlpaypal' => '',
            'emailpaypal' => '',
            'urlipn' => '',
            'urlreturn' => '',
            'msgback' => '',
            'locale' => '',
            'currency' => '',
            'immagine' => '',
        );
    }


}

