<?php

class Getrawc2_PageController extends Engine_Api_Controller
{

    public function voteAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            Engine_Api_Headers::_404("User_id vuoto");
        }
        $idPage = (int)$this->_getParam("idpage");
        $voto = (int)$this->_getParam("vote");
        $commento = $this->_getParam("comment");
        $commento = html_entity_decode($commento,ENT_COMPAT,'UTF-8');
        $commento = preg_replace(array(
            "#<p>#",
            "#</p>#",
        ), "", $commento);
        $commento = Engine_Api_Output::clean($commento);
        if ($voto === 1 || $voto === 0) {
            //aggiungo voto.
            $tPageVotes = new Books_Model_DbTable_Pagevotes();
            if (!$tPageVotes->hasAlreadyVoted($idPage,$userInfo->user_id) && !Engine_Api_Pages::isOwnerOf($idPage,$userInfo->user_id)) {
                if ($tPageVotes->add($idPage,(int)$voto,$commento,$userInfo->user_id)) {
                    Engine_Api_Stream::add($userInfo->user_id, "new_vote", $idPage, 1);
                    $this->_return(array(
                        "status" => "OK",
                    ));
                }
            }
        }
        $this->_return(array(
            "status" => "KO",
        ));
    }

    public function addAction() {
        if ($this->_getParam("dbg")) {
//            print_r($_POST);
//            die;
        }
        $hash = $this->_getParam("hash");
        $idParent = (int)$this->_getParam("parent");
        $idGroup = (int)$this->_getParam("group");
        $title = $this->_getParam("title");
        $text = $this->_getParam("text");
        $title = html_entity_decode($title,ENT_COMPAT,'UTF-8');
        $text = html_entity_decode($text,ENT_COMPAT,'UTF-8');
        //echo $title."\n".$text;
        //die;
        $tags = $this->_getParam("tags");
        $text = preg_replace(array(
            "#<p>#",
            "#</p>#",
        ), array(), $text);
        $title = preg_replace(array(
            "#<p>#",
            "#</p>#",
        ), array(), $title);

        $mobile = (int)$this->_getParam("mobile");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        if (!$userInfo->user_id) {
            //Engine_Api_Headers::_404("User_id vuoto");
            echo "user_id ko";
            die;
        }
        $userId = $userInfo->user_id;
        if (!$idParent) {
            //se idparent == 0, sto inserendo una nuova storia!
            //salvo la storia ed il testo inserito, finisce come prima pagina della storia.
            $tStories = new Books_Model_DbTable_Stories();
            $story = array(
                "story_title" => $title,
                "story_bg" => "#329000",
                "story_user_id" => $userId,
                "story_language" => "Italian", //$this->_getParam("story_language"),
            );
            if ($idGroup) {
                $story["story_group_id"] = $idGroup;
            }
            $idStory = $tStories->addStory($story, $userId);
            //aggiungo attivita allo stream del sito
            //Non serve, viene fatto sotto!
            //Engine_Api_Stream::add($userId, "new_story", $idStory["story_id"]);
            //
            $pageInfo = new stdClass();
            $pageInfo->page_story_id = $idStory["story_id"];
        } else {
            //else aggiungo pagina!
            $pageInfo = Engine_Api_Pages::getPageInfo($idParent);
        }
        //Recupero page_bg dalle opzioni dell'utente
        $prefColor = Engine_Api_Useroptions::get("preferredColor", $userId);
        if (empty($prefColor)) {
            $tBgs = new Books_Model_DbTable_Pagebgs();
            $options = $tBgs->getBgs();
            //prendo primo colore della lista e che si inculino tutti
            $prefColor = $options->current()->page_bg_file;
        }
        //inserimento
        $values = array(
            'page_prev_page_id' => $idParent,
            'page_story_id' => $pageInfo->page_story_id,
            'page_user_id' => $userInfo->user_id,
            'page_title' => $title,
            'page_text' => $text,
            'page_bg' => $prefColor,
            'page_from_mobile' => $mobile,
            'page_capital_font' => 'pianta', //da far scegliere il font preferito all'utente nel profilo!
        );
        $tPage = new Books_Model_DbTable_Pages();
        if (!Engine_Api_Pages::isAlreadyPresent($values["page_title"],$idParent)) {
            $idPage = $tPage->addPage($values,$hash);
            //invio notifica all'autore ed ai followers dello scrittore una notifica.
            Engine_Api_Updates_Notify::sendToFollowersOf($userInfo->user_id,$idPage["page_id"]);
            Engine_Api_Updates_Notify::sendToAuthor(Engine_Api_Users::getUserInfo()->user_id, $idPage["page_id"], $idParent);
            if ($tags) {
                Engine_Api_Tags::addTags($tags, $idPage["page_id"],$pageInfo->page_story_id);
            }
            //aggiungo attivita allo stream del sito
            Engine_Api_Stream::add($userInfo->user_id, "new_page", $idPage["page_id"], 1);
        }
        if ($idPage) {
            $this->_return(array(
                "id_page" => (int)$idPage["page_id"],
            ));
        }
        /* inizialmente mandavo alla pagina di visualizzazione, ma non va bene.
        if ($idPage) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => "getraw",
                'controller' => "page",
                'action' => "get",
                'token' => $hash,
                'id' => $idPage["page_id"],
            ),null,true);
        }*/
        //Engine_Api_Headers::_404("Inserimento fallito");
        echo "altro errore";
        die(2);
    }

    public function getAction() {
        $hash = $this->_getParam("hash");
        $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($hash);
        $tPage = new Books_Model_DbTable_Pages();
        $idPage = (int)$this->_getParam("id");
        $page = $tPage->getPage($idPage);
        if (empty($page->page_id)) {
            Engine_Api_Headers::_404();
        }
        $pageQueries = $tPage->getChildPages($page->page_id);
        $bookmarked = Engine_Api_Bookmarks::isBookmarked($page->page_id,$userInfo->user_id);
        //vota pagina
        $tVotes = new Books_Model_DbTable_Pagevotes();
        $alreadyVoted = $tVotes->hasAlreadyVoted($page->page_id,$userInfo->user_id);
        $votes = array(
            "medium" => (float)$tVotes->getMediumPageVotes($page->page_id),
        );
        $isOwner = $page->page_user_id == $userInfo->user_id;
        //salvo le informazioni dell'utente che ha visto la pagina
        Engine_Api_Pages::addView($page->page_id,$userInfo->user_id);
        $tUU = new Users_Model_DbTable_Userupdates();
        $tUU->setAsReadUpdate($page->page_id,"new_page",$userInfo->user_id);
        //
        $queries = array();
        foreach ($pageQueries as $pageQuery) {
            $queries[] = array(
                "id" => (int)$pageQuery->page_id,
                "title" => $pageQuery->page_title,
//                "text" => $pageQuery->page_text,
                "story_id" => (int)$pageQuery->page_story_id,
                "user_id" => (int)$pageQuery->page_user_id,
                "user_name" => Engine_Api_Users::getAUserInfo($pageQuery->page_user_id)->user_name,
                "prev_page_id" => (int)$pageQuery->page_prev_page_id,
                "page_date" => strtotime($pageQuery->page_date),
                "page_bg" => $pageQuery->page_bg,
                "page_capital_font" => $pageQuery->page_capital_font,
            );
        }
        $tFollowers = new Users_Model_DbTable_Followers();
        $imFollowing = $tFollowers->isFollowing($userInfo->user_id, $page->page_user_id);
        $this->_return(array(
            "PAGE" => array(
                "id" => (int)$page->page_id,
                "title" => $page->page_title,
                "text" => $page->page_text,
                "story_id" => (int)$page->page_story_id,
                "user_id" => (int)$page->page_user_id,
                "user_name" => Engine_Api_Users::getAUserInfo((int)$page->page_user_id)->user_name,
                "prev_page_id" => (int)$page->page_prev_page_id,
                "page_date" => strtotime($page->page_date),
                "page_bg" => $page->page_bg,
                "page_capital_font" => $page->page_capital_font,
                //
                "votedbyyou" => (bool)$alreadyVoted,
                "isowner" => (bool)$isOwner,
                "following" => (bool)$imFollowing,
            ),
            "QUERIES" => $queries,
            "BOOKMARKED" => (bool)$bookmarked,
            "ALREADYVOTED" => (bool)$alreadyVoted,
            "VOTES" => $votes
        ));
    }

}

