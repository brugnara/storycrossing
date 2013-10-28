<?php

class Books_Model_DbTable_Pages extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_pages';
    protected $_rowClass = "Books_Model_Page";

    public function addPage($page,$token = null) {
        if ($token !== null) {
            $userInfo = Engine_Api_Logintokens::getUserInfoFromHash($token);
            if (!$userInfo->user_id) {
                return -1;
            }
        } else {
            if (!Engine_Api_Users::isLogged()) {
                return -1;
            }
        }
        $page["page_title"] = Engine_Api_Output::clean($page["page_title"]);
        $page["page_text"] = Engine_Api_Output::clean($page["page_text"]);
        return $this->insert($page);
    }

    public function editPage($id,$values) {
        $id = (int)$id;
        if (!$id) {
            return false;
        }
        $this->update($values,array(
            "page_id = ?" => $id,
        ));
    }

    public function getPage($id) {
        $select = $this->select();
        $select
            ->where("page_id = ?",$id)
        ;
        return $this->fetchAll($select)->current();
    }

    public function getStartPage($storyId) {
        $select = $this->select();
        $select
            ->where("page_story_id = ?",$storyId)
            ->where("page_prev_page_id = 0")
        ;
        return $this->fetchAll($select)->current();
    }

    public function getChildPages($pageId) {
        $select = $this->select();
        //non mostro le pagine degli utenti nella mia blacklist con lock_type = hide.
        //mentre quelli in normal, li accodo come ultimi.
        $blockedNormal = (array) Engine_Api_Blacklists::getMyBlacklist("normal",true);
        $blockedHide = (array) Engine_Api_Blacklists::getMyBlacklist("hide",true);
        //select percentage
        //SELECT *,(select count(*) from engine_page_views where page_view_page_id = page_id) views
// FROM `engine_pages` where page_prev_page_id = 2 order by views DESC
        $tViews = new Books_Model_DbTable_Pageviews();
        $selectPercentage = $tViews->select();
        $selectPercentage
            ->from($tViews->info("name"),array(new Zend_Db_Expr("count(*)")))
            ->where('page_view_page_id = page_id')
        ;
        //selectvotes
        $tVotes = new Books_Model_DbTable_Pagevotes();
        $selectVotes = $tVotes->select();
        $selectVotes
            ->from($tVotes->info("name"),array(
//                new Zend_Db_Expr("(SUM( page_vote_vote ) / COUNT( * ))"),
                new Zend_Db_Expr("(avg(page_vote_vote))"),
            ))
            ->where("page_vote_page_id = page_id")
        ;
        $tVotes = new Books_Model_DbTable_Pagevotes();
        $selectVotesUp = $tVotes->select();
        $selectVotesUp
            ->from($tVotes->info("name"),array(
//                new Zend_Db_Expr("(SUM( page_vote_vote ) / COUNT( * ))"),
                new Zend_Db_Expr("(count(*))"),
            ))
            ->where("page_vote_page_id = page_id")
            ->where("page_vote_vote = 1")
        ;
        $tVotes = new Books_Model_DbTable_Pagevotes();
        $selectVotesDown = $tVotes->select();
        $selectVotesDown
            ->from($tVotes->info("name"),array(
//                new Zend_Db_Expr("(SUM( page_vote_vote ) / COUNT( * ))"),
                new Zend_Db_Expr("(count(*))"),
            ))
            ->where("page_vote_page_id = page_id")
            ->where("page_vote_vote = 0")
        ;
        //select
        $select
            ->from($this->info("name"),array(
                "*",
                "views" => "(".new Zend_Db_Expr($selectPercentage).")",
                "votes" => "(".new Zend_Db_Expr($selectVotes).")",
                "votesUp" => "(".new Zend_Db_Expr($selectVotesUp).")",
                "votesDown" => "(".new Zend_Db_Expr($selectVotesDown).")",
            ))
            ->where("page_prev_page_id = ?",$pageId)
//            ->order("views DESC")
        ;
        $selectA = clone $select;
        if (!empty($blockedNormal)) {
            $selectA
                ->where("page_user_id IN (".join(", ",$blockedNormal).")");
            $select
                ->where("page_user_id NOT IN (".join(", ",$blockedNormal).")");
        }
        if (!empty($blockedHide)) {
            $selectA
                ->where("page_user_id NOT IN (".join(", ",$blockedHide).")");
            $select
                ->where("page_user_id NOT IN (".join(", ",$blockedHide).")");
        }
        $union = $this->select();
        $union
            ->union(array($select,$selectA))
            ->order("votes DESC")
            ->order("views DESC")
        ;
        return $this->fetchAll($union);
    }

    public function getMostVotedPages($userId,$limit = 0) {
        $select = $this->select();
        //
        return $this->fetchAll($select->limit($limit));
    }

}

