<?php

class Books_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->redirector->gotoRoute(array(
            'module' => "books",
            'controller' => "stories",
            'action' => "showall",
            'orderby' => "lastactivity",
        ),null,true);
    }


}

