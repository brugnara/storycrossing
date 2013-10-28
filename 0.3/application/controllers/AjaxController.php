<?php

class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
//        $this->_helper->viewRenderer->setNoRender();
    }

    public function tagsAction() {
        $this->view->tags = Engine_Api_Tags::getTags(false,20,true);
        $max = 0;
        $tot = 0;
        foreach ($this->view->tags as $tag) {
            $tot+= $tag->tag_counter;
            if ($tag->tag_counter > $max) {
                $max = $tag->tag_counter;
            }
        }
        $this->view->maxtags = $max;
    }

    public function lastactiveusersAction() {
        $tU = new Users_Model_DbTable_Users();
        $s = $tU->select();
        //(user_last_seen + INTERVAL 15 MINUTE) > NOW()
        $time = 15;
        $s
            ->where(new Zend_Db_Expr('(user_last_seen + INTERVAL '.$time.' MINUTE) > NOW()'))
            ->order("user_name ASC")
        ;
        $this->view->users = $tU->fetchAll($s);
    }

    public function topwritersAction() {
        $showAll = $this->_getParam("showall") ? true : false;
        //migliori per pagine scritte
        //sql:
//            SELECT page_user_id, count( * ) AS tot
//            FROM `engine_pages`
//            WHERE page_date > SUBDATE( NOW( ) , '7 day' )
//            GROUP BY page_user_id
//            ORDER BY tot DESC
        $tPages = new Books_Model_DbTable_Pages();
        $selectA = $tPages->select();
        $selectA
            ->from($tPages->info("name"),array(
                "page_id",
                "page_user_id",
                "tot" => new Zend_Db_Expr("count(*)"),
            ))
            ->group("page_user_id")
            ->order("tot DESC")
            ->limit(10)
        ;
        //
        //migliori per voti medi ricevuti
        //sql:
//SELECT
//page_user_id,
//(select sum(page_vote_vote)/count(*) from engine_page_votes where page_vote_page_id = page_id) as medium_vote
//FROM `engine_pages`
//where page_id in (select page_vote_page_id from engine_page_votes where page_vote_date > SUBDATE(NOW(), '7 day'))
//group by page_user_id
        $tPagesVotes = new Books_Model_DbTable_Pagevotes();
        //subselect medium_vote
        $subSelect1 = $tPagesVotes->select();
        $subSelect1
            ->from($tPagesVotes->info("name"),array(
//                new Zend_Db_Expr("(sum(page_vote_vote)/count(*))")
                new Zend_Db_Expr("(avg(page_vote_vote))"),
                //new Zend_Db_Expr("count(*)"),
            ))
            ->where("page_vote_page_id = page_id")
            //->where("count(*) > 10") //ho almenoo 5 voti
        ;
        //subselect page_id
        $subSelect2 = $tPagesVotes->select();
        $subSelect2
            ->from($tPagesVotes->info("name"),array(
                "page_vote_page_id"
            ))
        ;
        //subselect count(*)
        $subSelectCount = $tPagesVotes->select();
        $subSelectCount
            ->from($tPagesVotes->info('name'),array(
                new Zend_Db_Expr("count(*)")
            ))
            ->where("page_vote_user_id = page_user_id")
        ;
        if (!$showAll) {
            $selectA->where("page_date > SUBDATE(NOW(), '7 day')");
            $subSelect2->where("page_vote_date > SUBDATE(NOW(), '7 day')");
        }
        $selectB = $tPages->select();
        $selectB
            ->from($tPages->info("name"),array(
                "page_id",
                "page_user_id",
                "medium_vote" => new Zend_Db_Expr("(".$subSelect1.")"),
            ))
            ->where("page_id IN (".new Zend_Db_Expr($subSelect2).")")
//            ->group("page_user_id")
        ;
        //calcolo i voti medi perché dalla query ho i voti medi di ogni pagina.
        $tmp = $tPages->fetchAll($selectB);
        $tot = array();
        $counter = array();
        $medium = array();
        foreach ($tmp as $t) {
            if (empty($counter[$t->page_user_id])) {
                $counter[$t->page_user_id] = 0;
                $tot[$t->page_user_id] = 0;
            }
            $counter[$t->page_user_id]++;
            $tot[$t->page_user_id]+= $t->medium_vote;
        }
        $votes = array();
        foreach ($tot as $k => $t) {
            if ($counter[$k] < 5)
                continue; //non calcolare se ha meno di 5 voti
            $medium[] = array(
                "user_id" => $k,
                "vote" => $t / $counter[$k],
            );
            $votes[] = $t / $counter[$k];
        }
        array_multisort($votes, SORT_DESC, $medium);
        $this->view->bestWriters = $tPages->fetchAll($selectA);
        $this->view->bestVoted = $medium;
    }

    public function mostfollowedAction() {
        //select *,(select count(*) from engine_followers where follower_writer_id = user_id) as tot from engine_users having tot > 0 order by tot desc
        $tF = new Users_Model_DbTable_Followers();
        $subS = $tF->select();
        $subS
            ->from(
                $tF->info("name"),array(
                    new Zend_Db_Expr("count(*)")
                ))
            ->where("follower_writer_id = user_id");
        $tU = new Users_Model_DbTable_Users();
        $select = $tU->select();
        $select
            ->from($tU->info("name"),array(
                "*",
                "tot" => new Zend_Db_Expr('('.$subS.')'),
            ))
            ->having("tot > 0")
            ->order("tot DESC")
            ->limit(10);
        ;
        $this->view->users = $tU->fetchAll($select);
    }

    public function showcaseAction() {
        //
        $tS = new Books_Model_DbTable_Stories();
        $orderBy = "lastactivity";
        $stories = $tS->getOrderBy($orderBy)->limit(5);
        $this->view->books = $tS->fetchAll($stories);
    }

}