<?php

class Books_StoriesController extends Zend_Controller_Action
{

    public function init()
    {
//        $response = $this->getResponse();
//        $response->insert('menu', $this->view->render('menu/index.phtml'));
    }

    public function indexAction() {
        $this->_helper->redirector->gotoRoute(array(
            'module' => "books",
            'controller' => "stories",
            'action' => "showall",
            'orderby' => "lastactivity",
        ),null,true);
    }

    public function showallAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "books");
        $this->view->isLogged = !empty(Engine_Api_Users::getUserInfo()->user_id);
        $tStories = new Books_Model_DbTable_Stories();
        //form filtri
        $form = new Books_Form_Stories_Filters();
        $form->setTags(Engine_Api_Tags::getTags());
        $form->setLocal(Engine_Api_Local::getLanguages());
        if ($this->getRequest()->isGet() && $form->isValid($this->getRequest()->getParams())) {
            $values = $form->getValues();
            $orderBy = $values["orderby"];
        } else {}
        if (empty($orderBy)) {
            $orderBy = "lastactivity";
        }
        $idGroup = $this->_getParam("idgroup");
        if ($idGroup) {
            $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "groups");
            $stories = $tStories->getOrderBy($orderBy,0,$idGroup);
            $this->view->groupInfo = Engine_Api_Groups::getGroupInfo($idGroup);
        } else {
            $stories = $tStories->getOrderBy($orderBy);
        }
        //controllo se l'utente mi sta chiedendo un tag specifico.
        $searchTag = $this->_getParam("tags");
        if (Engine_Api_Tags::isValid($searchTag)) {
            $tST = new Books_Model_DbTable_Storytags();
            $SstoryTags = $tST
                ->select()
                    ->from($tST->info("name"),array(
                        "story_tag_story_id",
                    ))
                    ->where("story_tag_tag = ?",$searchTag);
            $stories->where("story_id IN (".(new Zend_Db_Expr($SstoryTags)).")");
        }
        //controllo se l'utente mi sta chiedendo un language specifico.
        $searchLocal = $this->_getParam("local");
        if (Engine_Api_Local::isValid($searchLocal)) {
            $stories->where("story_language = ?",$searchLocal);
        }
        //
        $this->view->form = $form;
        //paginator
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($stories);
        $paginator = new Zend_Paginator($adapter);
        $paginator
            ->setCurrentPageNumber($this->_getParam("page"))
            ->setItemCountPerPage(12);
        $this->view->stories = $paginator;
        //pulisco lo stack.
        Engine_Api_Stack_Manager::clean();
    }

    public function coverAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "stories");
        //
        $this->view->isLogged = !empty(Engine_Api_Users::getUserInfo()->user_id);
        //se in sessione non ho un id_storia, mostro tutte le storie.
        $storyId = $this->_getParam("id");
        $tStories = new Books_Model_DbTable_Stories();
        $showCover = $this->_getParam("showCover");
        if ($showCover) {
//            $this->view->story = $tStories->getStory($storyId);
            $this->view->story = $tStories->fetchAll($tStories->getOrderBy("votes",$storyId))->current();
            $this->view->author = Engine_Api_Users::getAUserInfo($this->view->story->story_user_id);
//            $this->view->firstPage = Engine_Api_Pages::
        }
    }

    public function viewAction() {
        $this->view->isLogged = !empty(Engine_Api_Users::getUserInfo()->user_id);
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        //se in sessione non ho un id_storia, mostro tutte le storie.
        $idPage = (int)$this->_getParam("idpage");
        if ($idPage) {
            $storyId = Engine_Api_Pages::getPageInfo($idPage)->page_story_id;
        } else {
            $storyId = (int)$this->_getParam("id");
        }
        $tPage = new Books_Model_DbTable_Pages();
        if ($storyId) {
            if ($idPage) {
                //mostro la pagina.
                $this->view->page = $tPage->getPage($idPage);
                //qua so che ho una pagina precedente e la carico.
                $this->view->prevPage = $tPage->getPage($this->view->page->page_prev_page_id);
            } else {
                //mostro l'inizio della stoira!
                $this->view->page = $tPage->getStartPage($storyId);
            }
            //salvo che l'utente ha visto questa pagina.
            Engine_Api_Pages::addView($this->view->page->page_id);
            //l'utente può modificare questa pagina?
            $this->view->canEdit = Engine_Api_Permissions::canEditThisPage($idUser, $this->view->page);
            //preparo la pagina per lo stack
            $page = array(
                "module" => "books", //default
                "controller" => "stories",
                "action" => "view",
                //params
                "params" => array(
                    "idpage" => $this->view->page->page_id,
                ),
                //extra
                "active" => true,
                "reset_params" => true,
//                "label" => Engine_Api_Output::charsetFix($this->view->page->page_title),
//                "label" => iconv("UTF-8","ISO-8859-1//TRANSLIT//IGNORE", $this->view->page->page_title),
                "label" => $this->view->page->page_title,
            );
            $this->view->bookmarked = Engine_Api_Bookmarks::isBookmarked($this->view->page->page_id);
            $this->view->page_queries = $tPage->getChildPages($this->view->page->page_id);
            $this->view->windowOpenOptions = 'menubar=1,resizable=1,width=350,height=250';
            //capitalize della prima lettera
            if (preg_match("#^[a-z]#i",$this->view->page->page_text,$match)) {
                $this->view->page->page_text = preg_replace("#^[a-z]#i", "", $this->view->page->page_text);
                $this->view->pageFirstLetter = strtolower($match[0]).".png";
                $this->view->fontFamily = $this->view->page->page_capital_font;
            }
            //capitalize della pagina precedente
            if (!empty($this->view->prevPage)) {
                if (preg_match("#^[a-z]#i",$this->view->prevPage->page_text,$match)) {
                    $this->view->prevPage->page_text = preg_replace("#^[a-z]#i", "", $this->view->prevPage->page_text);
                    $this->view->prevPageFirstLetter = strtolower($match[0]).".png";
                    $this->view->prevPageFontFamily = $this->view->prevPage->page_capital_font;
                }
            }
            //alimento lo stack
            Engine_Api_Stack_Manager::add($page);
            //
            if ($this->view->page->page_user_id != Engine_Api_Users::getUserInfo()->user_id) {
                $this->view->followable = true;
            } else {
                $this->view->followable = false;
            }
            $this->view->following = Engine_Api_Followers::iAmAlreadyFollowerOf($this->view->page->page_user_id);
            $this->view->writerAlreadyBlocked = Engine_Api_Blacklists::NO_LOCK != Engine_Api_Blacklists::getLockType($this->view->page->page_user_id);
            //vota pagina
            $tVotes = new Books_Model_DbTable_Pagevotes();
            $this->view->alreadyVoted = $tVotes->hasAlreadyVoted($this->view->page->page_id);
            $this->view->votes = array(
                //"medium" => $tVotes->getMediumPageVotes($this->view->page->page_id),
                "up" => $tVotes->getVotes($this->view->page->page_id),
                "down" => $tVotes->getVotes($this->view->page->page_id, false),
            );
            //letture pagina.
            $tViews = new Books_Model_DbTable_Pageviews();
            $this->view->totReads = $tViews->getReads($this->view->page->page_id);
            //se la pagina è negli aggiornamenti dell'utente, elimino quel record così da decrementare gli updates.
            $tUU = new Users_Model_DbTable_Userupdates();
            $tUU->setAsReadUpdate($this->view->page->page_id,"new_page");
            //determino se l'utente corrente è il proprietario della pagina corrente
            $this->view->isOwner = $idUser == $this->view->page->page_user_id;
        } else {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "stories",
                'action' => "index",
            ),null,true);
            $this->view->followable = false;
        }
        $this->view->stack = Engine_Api_Stack_Viewer::getNavigation(Engine_Api_Stack_Manager::get());
        //menu
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "stories");
    }

    public function addAction()
    {
        if (empty(Engine_Api_Users::getUserInfo()->user_id)) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "users",
                'action' => "login",
            ),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "stories");
        //mostro form per la creazione della storia
        $form = new Books_Form_Story();
        //page
        $tBgs = new Books_Model_DbTable_Pagebgs();
        $options = $tBgs->getBgs();
        $valuePage = clone ($options);
        $selectValuesPage = array();
        foreach ($options as $option) {
//            $selectValuesPage[$option->page_bg_file] = $option->page_bg_file;
            $selectValuesPage[$option->page_bg_file] = "";
        }
        //story
        $tBgs = new Books_Model_DbTable_Storybgs();
        $options = $tBgs->getBgs();
        $valueStory = clone ($options);
        $selectValuesStory = array();
        foreach ($options as $option) {
//            $selectValuesStory[$option->story_bg_file] = $option->story_bg_file;
            $selectValuesStory[$option->story_bg_file] = "";
        }
        $form->setValues($selectValuesPage,Engine_Api_Useroptions::get("preferredColor"),$selectValuesStory,$valueStory->current()->story_bg_file);
        //fonts
        $tFonts = new Application_Model_DbTable_Fonts();
        $options = $tFonts->getFonts();
        $value = clone ($options);
        $selectValues = array();
        foreach ($options as $option) {
            $selectValues[$option->font_name] = $option->font_name;
        }
        $form->setFonts($selectValues,$value->current()->font_name);
        //setLocals
        $form->setLocal(Engine_Api_Local::getLanguages(true), "Italian");
        //
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $userId = Engine_Api_Users::getUserInfo()->user_id;
            //salvo la storia ed il testo inserito, finisce come prima pagina della storia.
            $tStories = new Books_Model_DbTable_Stories();
            $story = array(
                "story_title" => $values["story_title"],
                "story_bg" => $values["story_bg"],
                "story_user_id" => $userId,
                "story_language" => $values["story_language"],
            );
            if ($idg = $this->_getParam("idgroup")) {
                $story["story_group_id"] = (int)$idg;
            }
            $idStory = $tStories->addStory($story);
            //aggiungo attivita allo stream del sito
            Engine_Api_Stream::add(Engine_Api_Users::getUserInfo()->user_id, "new_story", $idStory["story_id"]);
            //
            $tPages = new Books_Model_DbTable_Pages();
            $page = array(
                "page_story_id" => $idStory["story_id"],
                "page_user_id" => $userId,
                "page_prev_page_id" => "",
                "page_title" => $values["story_title"],
                "page_text" => $values["story_body"],
                "page_bg" => $values["page_bg"],
                "page_capital_font" => $values["page_font"],
            );
            $idPage = $tPages->addPage($page);
            //aggiungo tags
            if (!empty($values["page_tags"])) {
                Engine_Api_Tags::addTags($values["page_tags"], $idPage["page_id"],$idStory["story_id"]);
                $values["page_tags"] = null;
            }
            $this->_helper->redirector->gotoRoute(array(
                'module' => "books",
                'controller' => "stories",
                'action' => "view",
                "id" => $idStory["story_id"]
            ),null,true);
        }

        $this->view->form = $form;
    }

}




