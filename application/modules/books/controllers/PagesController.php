<?php

class Books_PagesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function addAction()
    {
        if (empty(Engine_Api_Users::getUserInfo()->user_id)) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => "users",
                'controller' => "auth",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "stories");
        $this->view->stack = Engine_Api_Stack_Viewer::getNavigation(Engine_Api_Stack_Manager::get());
        //
        $idPrevPage = (int)$this->_getParam("idparent");
        $this->view->prevPage = Engine_Api_Pages::getPageInfo($idPrevPage);
        $idStory = $this->view->prevPage->page_story_id;
//        $idStory = $this->_getParam("idstory");
        $form = new Books_Form_Page();


        $tBgs = new Books_Model_DbTable_Pagebgs();
        $options = $tBgs->getBgs();
        $value = clone ($options);
        $selectValues = array();
        foreach ($options as $option) {
//            $selectValues[$option->page_bg_file] = $option->page_bg_file;
            $selectValues[$option->page_bg_file] = "";
        }
//        $form->setValues($selectValues,$value->current()->page_bg_file);
        $form->setValues($selectValues,Engine_Api_Useroptions::get("preferredColor"));

        $tFonts = new Application_Model_DbTable_Fonts();
        $options = $tFonts->getFonts();
        $value = clone ($options);
        $selectValues = array();
        foreach ($options as $option) {
            $selectValues[$option->font_name] = $option->font_name;
        }
        $form->setFonts($selectValues,$value->current()->font_name);

        if ($idStory && $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $values["page_prev_page_id"] = $idPrevPage;
            $values["page_story_id"] = $idStory;
            $values["page_user_id"] = Engine_Api_Users::getUserInfo()->user_id;
            $tPage = new Books_Model_DbTable_Pages();
            if (!Engine_Api_Pages::isAlreadyPresent($values["page_title"],$idPrevPage)) {
                $tags = $values["page_tags"];
                unset($values["page_tags"]);
                $idPage = $tPage->addPage($values);
                //invio notifica all'autore ed ai followers dello scrittore una notifica.
                Engine_Api_Updates_Notify::sendToFollowersOf(Engine_Api_Users::getUserInfo()->user_id,$idPage["page_id"]);
                Engine_Api_Updates_Notify::sendToAuthor(Engine_Api_Users::getUserInfo()->user_id,$idPage["page_id"],$idPrevPage);
                //aggiungo tags
                //se tag sono vuoti, prendo quelli della pagina precedente!
                if (!empty($tags)) {
                    Engine_Api_Tags::addTags($tags, $idPage["page_id"],$idStory);
                } else {
                    //copio dalla pagina precedente
                    //Engine_Api_Tags::getStoryTags($idStory);
                }
                //aggiungo attivita allo stream del sito
                Engine_Api_Stream::add(Engine_Api_Users::getUserInfo()->user_id, "new_page", $idPage["page_id"]);
            }
            $this->_helper->redirector->gotoRoute(array(
                'module' => "books",
                'controller' => "stories",
                'action' => "view",
//                "id" => $idStory,
                "idpage" => $idPage["page_id"],
            ),null,true);
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        $idPage = (int)$this->_getParam("id");
        $page = Engine_Api_Pages::getPageInfo($idPage);
        if (!Engine_Api_Permissions::canEditThisPage(Engine_Api_Users::getUserInfo()->user_id, $page)) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => "users",
                'controller' => "auth",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "stories");
        //
        $idPrevPage = $page->page_prev_page_id;
        $idStory = $page->page_story_id;
        $this->view->prevPage = Engine_Api_Pages::getPageInfo($idPrevPage);
//        $idStory = $this->_getParam("idstory");
        $form = new Books_Form_Page();

        $tBgs = new Books_Model_DbTable_Pagebgs();
        $options = $tBgs->getBgs();
        $value = clone ($options);
        $selectValues = array();
        foreach ($options as $option) {
//            $selectValues[$option->page_bg_file] = $option->page_bg_file;
            $selectValues[$option->page_bg_file] = "";
        }
        $form->setValues($selectValues,$page->page_bg);

        $tFonts = new Application_Model_DbTable_Fonts();
        $options = $tFonts->getFonts();
        $value = clone ($options);
        $selectValues = array();
        foreach ($options as $option) {
            $selectValues[$option->font_name] = $option->font_name;
        }
        $form->setFonts($selectValues,$page->page_capital_font);
        $form->setContents($page->page_title, $page->page_text);
        $form->prepareForEdit();
        if ($idStory && $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $values["page_prev_page_id"] = $idPrevPage;
            $values["page_story_id"] = $idStory;
            //$values["page_user_id"] = Engine_Api_Users::getUserInfo()->user_id;
            $tPage = new Books_Model_DbTable_Pages();
            $tags = $values["page_tags"];
            unset($values["page_tags"]);
            $tPage->editPage($idPage,$values);
            //aggiungo tags
            if (!empty($tags)) {
                Engine_Api_Tags::addTags($tags,$idPage,$idStory);
            }
            //edit
            $this->_helper->redirector->gotoRoute(array(
                'module' => "books",
                'controller' => "stories",
                'action' => "view",
//                "id" => $idStory,
                "idpage" => $idPage,
            ),null,true);
        }
        $this->view->form = $form;
        $this->render("add");
    }

    public function viewAction() {
        $idPage = (int)$this->_getParam("id");
        $pageInfo = Engine_Api_Pages::getPageInfo($idPage);
        if ($pageInfo) {
            $this->_helper->redirector->gotoRoute(array(
                "module" => "books",
                'controller' => "stories",
                'action' => "view",
                "id" => $pageInfo->page_story_id,
                "idpage" => $pageInfo->page_id,
            ),null,true);
        }
        $this->_helper->redirector->gotoRoute(array(
            "module" => "books",
            'controller' => "stories",
            'action' => "view",
        ),null,true);
    }

}



