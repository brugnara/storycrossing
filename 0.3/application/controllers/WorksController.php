<?php

class WorksController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getmostreadedbooksAction()
    {
        $limit = (int)$this->_getParam("limit");
        $idUser = (int)$this->_getParam("userid");
    }

    public function getmostvotedpagesAction()
    {
        $limit = (int)$this->_getParam("limit");
        $idUser = (int)$this->_getParam("userid");
        //select votes
        $tVotes = new Application_Model_DbTable_Pagevotes();
        $selectVotes = $tVotes->select();
        $selectVotes
            ->from($tVotes->info("name"),array(
//                "sum(page_vote_vote) / count(*)",
                "avg(page_vote_vote)",
            ))
            ->where("page_vote_page_id = page_id")
        ;
        $selectVoteCount = $tVotes->select();
        $selectVoteCount
            ->from($tVotes->info("name"),array(
                "count(*)",
            ))
            ->where("page_vote_page_id = page_id")
        ;
        $tViews = new Application_Model_DbTable_Pageviews();
        $selectViews = $tViews->select();
        $selectViews
            ->from($tViews->info("name"),array(
                "count(*)",
            ))
            ->where("page_view_page_id = page_id")
        ;
        //select pages
        $tPages = new Application_Model_DbTable_Pages();
        $select = $tPages->select();
        $select
            ->from($tPages->info("name"),array(
                "*",
                "vote" => "(".new Zend_Db_Expr($selectVotes).")",
                "votecount" => "(".new Zend_Db_Expr($selectVoteCount).")",
                "views" => "(".new Zend_Db_Expr($selectViews).")",
            ))
            ->where("page_user_id = ?",$idUser)
//            ->having("vote <> ''")
        ;
        //
        $select->limit($limit);
        $selectA = clone($select);
        $selectB = clone($select);
        $ret = array();
        foreach ($tPages->fetchAll($selectA->order("vote DESC")) as $item) {
            if ($item->vote) {
                $ret[] = array(
                    "id" => $item->page_id,
                    "title" => $item->page_title,
                    "vote" => $item->vote,
                    "votecount" => $item->votecount,
                );
            }
        }
        //ordereby views
        $byViews = array();
        foreach ($tPages->fetchAll($selectB->order("views DESC")) as $item) {
            if ($item->views) {
                $byViews[] = array(
                    "id" => $item->page_id,
                    "title" => $item->page_title,
                    "views" => $item->views,
                );
            }
        }
        echo json_encode(array(
            $ret,
            $byViews,
        ));
//        echo "<pre>";
//        print_r($ret);
    }


}

