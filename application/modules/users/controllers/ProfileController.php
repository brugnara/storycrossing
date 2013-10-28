<?php

class Users_ProfileController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function viewAction()
    {
        $form = null;
        $id = $this->_getParam("id");
        //
        $options = array();
        if ($id == Engine_Api_Users::getUserInfo()->user_id) {
            $id = null;
        }
        if ($id) {
            $options["id"] = $id;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", $options, "user_view");
        //
        if (empty($id)) {
            //visualizzo profilo utente loggato
            $id = Engine_Api_Users::getUserInfo()->user_id;
            if (empty($id)) {
                $this->_helper->redirector->gotoRoute(array(
                    'module' => "users",
                    'controller' => "auth",
                    'action' => "login",
                ),null,true);
                return;
            }
            $form = new Users_Form_Register("edit");
            //devo riempire con i colori delle pagine
            $tBgs = new Books_Model_DbTable_Pagebgs();
            $options = $tBgs->getBgs();
            $colori = array();
            foreach ($options as $option) {
//                $selectValues[$option->page_bg_file] = $option->page_bg_file;
                $colori[$option->page_bg_file] = "";
            }
            $this->view->preferred_color = $colore = Engine_Api_Useroptions::get("preferredColor");
            $form->setColors($colori, $colore);
            //
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                // we will add the category
                $values = $form->getValues();
                $values["user_id"] = Engine_Api_Users::getUserInfo()->user_id;
                Engine_Api_Users::editUserInfo($values);
                $this->view->userDetails = Engine_Api_Users::getAUserInfo(Engine_Api_Users::getUserInfo()->user_id);
                Engine_Api_Users::setLogin(array(
                    "user_id" => $values["user_id"],
                    "user_name" => $this->view->userDetails->user_name,
                    "user_locale" => $this->view->userDetails->user_locale,
                ));
                //salvo altre opzioni
                Engine_Api_Useroptions::set('email_notify', $values['user_notifications']);
                Engine_Api_Useroptions::set('preferredColor', $values['user_preferred_color']);
                //
                $this->_helper->redirector->gotoRoute(array(
                    'module' => "users",
                    'controller' => "profile",
                    'action' => "view",
                ),null,true);
                return;
                $this->view->message = "Dettagli utente aggiornati!";
            }
        } else {
            //visualizzo profilo pubblico utente interessato.
            $this->view->followable = !Engine_Api_Followers::iAmAlreadyFollowerOf($id);
            //visualizzo chi sta seguendo il tizio
            $this->view->noMyFollowers = true;
        }
        $this->view->windowOpenOptions = 'menubar=1,resizable=1,width=350,height=250';
        if (empty($this->view->userDetails)) {
            $tUsers = new Users_Model_DbTable_Users();
            $this->view->userDetails = $tUsers->find($id)->current();
        }
        if ($form) {
            //user options...
            $tmp = $this->view->userDetails;
            $userDetails = new stdClass();
            $userDetails->user_mail = $tmp->user_mail;
            $userDetails->user_locale = $tmp->user_locale;
            if (($notify = Engine_Api_Useroptions::get('email_notify')) !== null) {
                $userDetails->user_notifications = $notify;
            } else {
                $userDetails->user_notifications = 1;
            }
            $form->setValues($userDetails);
            $this->view->form = $form;
        }
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor",$id);
    }


}

