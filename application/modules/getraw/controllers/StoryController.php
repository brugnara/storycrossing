<?php

class Getraw_StoryController extends Engine_Api_Controller
{

    public function getAction() {
        //restituisce gli id della storia.
        $idStory = (int)$this->_getParam("id");
        $idGroup = (int)$this->_getParam("idgroup");
        $tag = $this->_getParam("tag");
        $orderBy = $this->_getParam("orderby");
        $local = $this->_getParam("local");
        if (empty($orderBy)) {
            $orderBy = 'lastactivity';
        }
        //
        $tPage = new Books_Model_DbTable_Pages();
        $select = $tPage->select();
        $select
            ->from($tPage->info("name"),array(
                "page_id" => "page_id",
                "page_prev_page_id" => "page_prev_page_id",
            ))
            ->where("page_story_id = ?",$idStory);
        $fetch = $tPage->fetchAll($select);
        $pages = array();
        if (count($fetch)) {
            foreach ($fetch as $f) {
                $pages[] = array(
                    "id" => (int)$f->page_id,
                    "prev_id" => (int)$f->page_prev_page_id,
                );
            }
        }
        $tStory = new Books_Model_DbTable_Stories();
        $select = $tStory->getOrderBy($orderBy, $idStory);
        if (Engine_Api_Tags::isValid($tag)) {
            $tST = new Books_Model_DbTable_Storytags();
            $SstoryTags = $tST
                ->select()
                    ->from($tST->info("name"),array(
                        "story_tag_story_id",
                    ))
                    ->where("story_tag_tag = ?",$tag);
            $select->where("story_id IN (".(new Zend_Db_Expr($SstoryTags)).")");
        }
        if (Engine_Api_Local::isValid($local)) {
            $select->where("story_language = ?",$local);
        }
        //seleziono storie solo del gruppo richiesto! Se = 0, ritornano le storie non in gruppi
        if (!$idStory) {
            $select->where("story_group_id = ?", $idGroup);
        }
        $stories = $tStory->fetchAll($select);
        $retStories = array();
        foreach ($stories as $story) {
            $storyTags = array();
            $tags = Engine_Api_Tags::getStoryTags($story->story_id);
            foreach ($tags as $tag) {
                $storyTags[] = array(
                    "name" => $tag->story_tag_tag,
                    "counter" => $tag->story_tag_counter,
                );
            }
            $retStories[] = array(
                'id' => (int)$story->story_id,
                'user_id' => (int)$story->story_user_id,
                'user_name' => Engine_Api_Users::getAUserInfo($story->story_user_id)->user_name,
                'first_page_id' => @(int)$tPage->getStartPage($story->story_id)->page_id,
                'title' => $story->story_title,
                'votes' => (int)$story->my_votes,
                'votomedio' => (float)number_format($story->my_votomedio,2),
                'views' => (int)$story->my_views,
                'pagecount' => (int)$story->my_pagecount,
                'pagenew' => (int)$story->my_pagenew,
                'fromfollowers' => (int)$story->my_fromfollowers,
                'lastactivity' => strtotime($story->my_lastactivity),
                'tags' => $storyTags,
            );
        }
        //
        if (empty($retStories) && empty($pages)) {
            Engine_Api_Headers::_404();
        }
        $this->_return(array(
            "details" => $retStories,
            "pages" => $pages,
        ));
    }

}

