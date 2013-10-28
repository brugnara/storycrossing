<?php

class Books_Model_DbTable_Pagevotes extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_page_votes';
//    protected $_rowClass = 'Books_Model_Pagevote';

    public function getPageVotes($idPage) {
        return $this->fetchAll($this->select()->where("page_vote_page_id = ?",$idPage)->order("page_vote_id DESC")->limit(20));
    }

    public function getVotes($idPage, $up = true) {
        if (empty($idPage)) {
            return 0;
        }
        $select = $this->select();
        $select
            ->from($this->info("name"),array(
                "total" => "count(*)",
            ))
            ->where("page_vote_page_id = ?",$idPage)
            ->where("page_vote_vote = ?",$up ? 1 : 0)
        ;
        $fetch = $this->fetchAll($select);
        if (count($fetch)) {
            $fetch = $fetch->current();
            return $fetch->total;
        }
        return 0;
    }

    public function getMediumPageVotes($idPage) {
        if (empty($idPage)) {
            return 0;
        }
        $select = $this->select();
        $select
            ->from($this->info("name"),array(
                "total" => "count(*)",
                "somma" => "SUM(page_vote_vote)",
            ))
            ->where("page_vote_page_id = ?",$idPage)
        ;
        $fetch = $this->fetchAll($select);
        if (count($fetch)) {
            $fetch = $fetch->current();
            if ($fetch->total) {
                return number_format($fetch->somma / $fetch->total,1);
            }
        }
        return 0;
    }

    public function hasAlreadyVoted($idPage,$idUser = null) {
        if ($idUser === null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (empty($idPage) || empty($idUser)) {
            return false;
        }
        $select = $this->select();
        $select
            ->where("page_vote_user_id = ?",$idUser)
            ->where("page_vote_page_id = ?",$idPage)
        ;
        $fetch = $this->fetchAll($select);
        return count($fetch);
    }

    public function add($idPage,$vote,$comment = "",$idUser = null) {
        if ($idUser === null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (empty($idUser) || empty($idPage)) {
            return 0;
        }
        if (empty($comment)) {
            $comment = "Not commented.";
        }
        $vote = (int)$vote;
        if ($vote < 1) {
            $vote = 1;
        }
        if ($vote > 5) {
            $vote = 5;
        }
        return $this->insert(array(
            "page_vote_page_id" => $idPage,
            "page_vote_user_id" => $idUser,
            "page_vote_vote" => $vote,
            "page_vote_comment" => $comment,
        ));
    }

}

