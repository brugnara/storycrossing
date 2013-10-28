<?php

class Users_BookmarksController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $this->view->submenu = Engine_Api_Menu_Manager::getNavigation("user_profile_menu", array(), "user_bookmarks");
        //mostro i bookmarks dell'utente corrente.
        //forse un amico potrebbe vedere i bookmarks di un altro amico?
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if (empty($idUser)) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => "users",
                'controller' => "auth",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->bookmarks = Engine_Api_Bookmarks::getBookmarks($idUser);
        $this->view->userDetails = Engine_Api_Users::getAUserInfo($idUser);
        $this->view->user_preferred_color = Engine_Api_Useroptions::get("preferredColor");
        if ($this->_getParam("deleted")) {
            $this->view->deleted = true;
        }
    }

    public function showAction() {
        $idBookmark = $this->_getParam("id");
        //prendo come parametro la sola idpage e faccio redirect al controller stories
        $infoBookmark = Engine_Api_Bookmarks::getPageFromBookmark($idBookmark);
        $this->_helper->redirector->gotoRoute(array(
            "module" => "books",
            "controller" => "stories",
            "action" => "view",
            "id" => $infoBookmark->page_story_id,
            "idpage" => $infoBookmark->page_id,
        ),null,true);
    }

    public function addAction() {
        $this->_helper->layout->setLayout('popup');
        $idPage = $this->_getParam("idpage");
        $idStory = $this->_getParam("idstory");
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if ($idPage && $idUser) {
            $title = Engine_Api_Pages::getPageInfo($idPage)->page_title;
//            $tPages = new Application_Model_DbTable_Pages();
//            $title = $tPages->find($idPage)->current()->page_title;
            Engine_Api_Bookmarks::add($idPage,$idUser,$title);
            $this->view->isOk = true;
        } else {
            $this->view->isOk = false;
        }
        //
        $options = Array(
            'module' => "books",
            'controller' => "stories",
            'action' => "view",
        );
        if ($idStory) {
            $options["id"]= $idStory;
            $options["idpage"] = $idPage;
        }
//        $this->_helper->redirector->gotoRoute($options,null,true);
    }

    public function delAction() {
        $this->_helper->layout->setLayout('popup');
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $idBook = $this->_getParam("id");
        if ($idUser && $idBook) {
            $tB = new Users_Model_DbTable_Bookmarks();
            $select = $tB->select();
            $select
                ->where("bookmark_id = ?",$idBook)
                ->where("bookmark_user_id = ?",$idUser)
            ;
            $fetch = $tB->fetchAll($select);
            if (count($fetch)) {
                $fetch->current()->delete();
            }
            $this->view->isOk = true;
        } else {
            $this->view->isOk = false;
        }
//        $this->_helper->redirector->gotoRoute(array(
//            "controller" => "bookmarks",
//            "action" => "view",
//            "deleted" => "1"
//        ),null,true);
    }

}

