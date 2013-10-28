<?php

class Search_AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
    }

    public function searchAction() {
        if (empty(Engine_Api_Users::getUserInfo()->user_id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $form = new Search_Form_Search();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $values["search"] = Engine_Api_Search::clean($values["search"]);
            switch ($values["what"]) {
                case "story":
                    $this->view->results = Engine_Api_Search::story($values);
                    $this->view->searched = "stories";
                    break;
                case "page":
                    $this->view->results = Engine_Api_Search::page($values);
                    $this->view->searched = "pages";
                    break;
                case "user":
                    $this->view->results = Engine_Api_Search::user($values);
                    $this->view->searched = "users";
                    break;
                case "tag":
                    $this->view->results = Engine_Api_Search::tag($values);
                    $this->view->searched = "tag";
                    break;
                default:
                    //error!
            }
        }
        $this->view->form = $form;
    }

}

