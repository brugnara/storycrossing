<?php

class Stream_AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function getAction()
    {
        $lastId = (int)$this->_getParam("last");
        $this->view->stream = Engine_Api_Stream::get($lastId);
    }


}

