<?php

class Users_SharerController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->disableLayout();
    }

    public function shareAction() {
        //idobj: è l'ID
        //what: è il tipo di elemento che sto condividendo. (page, story, group)
        //with: followers, friends, group
        switch ($this->_getParam("what")) {
            case "page":
            case "story":
            case "group":
                break;
            default:
                return false;
        }
        //mostro opzioni di condivisione.
        $form = new Users_Form_Share();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            //posso salvare gli updates.
        }
        $this->view->what = $this->_getParam("what");
        $this->view->form = $form;
    }

}