<?php

class Groups_UsersController extends Zend_Controller_Action
{

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function setAction() {
        $this->_helper->layout->setLayout('popup');
        //cambio livello ad un utente se e solo se:
        //sono admin del gruppo.
        $idAdmin = Engine_Api_Users::getUserInfo()->user_id;
        $idGroup = (int)$this->_getParam("idGroup");
        $idUser = (int)$this->_getParam("idUser");
        if (!empty($idUser) && !empty($idGroup) && !empty($idAdmin)) {
            if (Engine_Api_Groups::isAdmin($idAdmin, $idGroup)) {
                //ok, posso cambiare le opzioni
                switch ($this->_getParam("type")) {
                    case "writer" :
                        $opt = array(0,0,1);
                        break;
                    case "admin" :
                        $opt = array(0,1,0);
                        break;
                    case "regular" :
                        $opt = array(0,0,0);
                        break;
                    default:
                        return;
                }
                Engine_Api_Groups::setUserPermissions($idUser, $idGroup, $opt);
                $this->view->isOk = true;
            }
        }
    }

}

