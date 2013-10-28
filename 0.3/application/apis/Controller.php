<?php

class Engine_Api_Controller extends Zend_Controller_Action
{

    public function init()
    {
        //se non arrivo da ssh, butto un errore.
        if (empty($_SERVER['HTTP_X_FORWARDED_HOST']) && $_SERVER['HTTP_HOST'] != 'localhost') {
//            Engine_Api_Headers::_404();
        }
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }


    protected function _return($ret) {
        header("Content-type: application/json");
        echo json_encode($ret);
        die;
    }

}

