<?php

class Groups_ManageController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function joinAction() {
        $this->_helper->layout->setLayout('popup');
        //mi arriva id gruppo
        $id = (int)$this->_getParam("id");
        if (empty($id)) {
            //diocan!
        }
        if (Engine_Api_Groups::addMember(Engine_Api_Users::getUserInfo()->user_id,$id)) {
            $this->view->isOk = true;
        }
    }

    public function leaveAction() {
        $this->_helper->layout->setLayout('popup');
        $id = (int)$this->_getParam("id");
        if (empty($id)) {
            //diocan!
        }
        if (Engine_Api_Groups::delMember(Engine_Api_Users::getUserInfo()->user_id,$id)) {
            $this->view->isOk = true;
        }
    }

    public function addAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
        //aggiunge un gruppo! form eccetera necessario
        $form = new Groups_Form_Add();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            $values = $form->getValues();
            //aggiungo gruppo!
            $t = new Groups_Model_DbTable_Groups();
            $idGroup = $t->addGroup($values);
            if ($idGroup) {
                //aggiungo membership!
                $tM = new Groups_Model_DbTable_Members();
                $tM->addMember($idGroup,Engine_Api_Users::getUserInfo()->user_id,array(1,0,0)); //is owner, is admin, can edit
                //redirect alla vista del gruppo
                $this->_helper->redirector->gotoRoute(array(
                    'module' => "groups",
                    'controller' => "view",
                    'action' => "one",
                    'id' => $idGroup,
                ),null,true);
            }
        }
        $this->view->form = $form;
    }

}

