<?php

class Books_Model_DbTable_Stories extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_stories';
    protected $_rowClass = "Books_Model_Story";

    public function addStory($values, $idUser = null) {
        if (!Engine_Api_Users::isLogged() && $idUser == null) {
            return -1;
        }
        $values["story_title"] = Engine_Api_Output::clean($values["story_title"]);
        return $this->insert($values);
    }

    public function getStories($returnSelect = false) {
        $select = $this->select();
        $select
            ->where("story_active = 1")
        ;
        //le ordino mettendo in fondo quelle di utenti che ho in blacklist
        if ($returnSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    public function getStory($idStory) {
        $select = $this->select();
        $select
            ->where("story_id = ?",$idStory)
        ;
        $fetch = $this->fetchAll($select);
        if (count($fetch)) {
            return $fetch->current();
        } else {
            return null;
        }
    }

    /**
     * @return select
     */
    public function getOrderBy($order,$storyId = 0,$idGroup = 0) {
        $direction = "DESC";
        //count voti nella storia
//SELECT count( * )
//FROM `engine_pages` AS ep
//INNER JOIN engine_page_votes epv ON epv.page_vote_page_id = ep.page_id
//WHERE ep.page_story_id =3
        $table = new Books_Model_DbTable_Pages();
        $Svotes = $table->select();
        $Svotes
            ->from($table->info("name"),array(
                "tot" => "count(*)"
            ))
            ->setIntegrityCheck(false)
            ->join("engine_page_votes","page_vote_page_id = page_id","")
            ->where("page_story_id = story_id")
        ;
        //count visite storia
//SELECT count( * )
//FROM `engine_pages` AS ep
//INNER JOIN engine_page_view epv ON epv.page_view_page_id = ep.page_id
//WHERE ep.page_story_id =3
        $table = new Books_Model_DbTable_Pages();
        $Sviews = $table->select();
        $Sviews
            ->from($table->info("name"),array(
                "tot" => "count(*)"
            ))
            ->setIntegrityCheck(false)
            ->join("engine_page_views","page_view_page_id = page_id","")
            ->where("page_story_id = story_id")
        ;
        //count pagine storia
//SELECT count( * )
//FROM `engine_pages`
//WHERE page_story_id =3
        $table = new Books_Model_DbTable_Pages();
        $Spagecount = $table->select();
        $Spagecount
            ->from($table->info("name"),array(
                "count(*)"
            ))
            ->where("page_story_id = story_id")
        ;
        //count pagine nuove
//SELECT count( * )
//FROM `engine_pages`
//WHERE page_story_id =3
//AND page_id NOT
//IN (
//  SELECT page_view_page_id
//  FROM engine_page_views
//  WHERE page_view_user_id =2
//)
        $table = new Books_Model_DbTable_Pages();
        $table2 = new Books_Model_DbTable_Pageviews();
        $extra2 = $table2->select();
        $extra2
            ->from($table2->info("name"),array(
                "page_view_page_id"
            ))
            ->where("page_view_user_id = ?",  Engine_Api_Users::getUserInfo()->user_id);
        $Spagenew = $table->select();
        $Spagenew
            ->from($table->info("name"),array(
                "tot" => new Zend_Db_Expr("count(*)")
            ))
//            ->setIntegrityCheck(false)
//            ->join("engine_page_views","page_view_page_id = page_id","")
            ->where("page_story_id = story_id")
            ->where("page_id NOT IN (".(new Zend_Db_Expr($extra2)).")")
            ->where("page_user_id <> ?",Engine_Api_Users::getUserInfo()->user_id)
        ;
//        echo $Spagenew->__toString();
//        die;
        //count following writers
        $table2 = new Users_Model_DbTable_Followers();
        $extra2 = $table2->select();
        $extra2
            ->from($table2->info("name"),array(
                "follower_writer_id"
            ))
            ->where("follower_user_id = ?",  Engine_Api_Users::getUserInfo()->user_id);
        $table = new Books_Model_DbTable_Pages();
        $Sfollowers = $table->select();
        $Sfollowers
            ->from($table->info("name"),array(
                "count(*)"
            ))
            ->where("page_story_id = story_id")
            ->where("page_user_id IN (".(new Zend_Db_Expr($extra2)).")")
        ;
//        //last activity
        $table = new Books_Model_DbTable_Pages();
        $SlastActivity = $table->select();
        $SlastActivity
            ->from($table->info("name"),array(
                "max(page_date)"
            ))
            ->where("page_story_id = story_id")
        ;
        //select sum(page_vote_vote) / count(*) from engine_page_votes where page_vote_page_id IN (select page_id from engine_pages where page_story_id = 28)
        $Spages = $table->select();
        $Spages
            ->from($table->info("name"),array(
                "page_id"
            ))
            ->where("page_story_id = story_id");
        //
        $tVotes = new Books_Model_DbTable_Pagevotes();
        $SvotoMedio = $tVotes->select();
        $SvotoMedio
            ->from($tVotes->info("name"),array(
//                new Zend_Db_Expr("sum(page_vote_vote) / count(*)")
                new Zend_Db_Expr("avg(page_vote_vote)")
            ))
            ->where("page_vote_page_id IN (".new Zend_Db_Expr($Spages).")")
        ;
        //
        $select = $this->select();
        $select
            ->from($this->info("name"),array(
                "*",
                "my_votes" => "(".(new Zend_Db_Expr($Svotes)).")",
                "my_views" => "(".(new Zend_Db_Expr($Sviews)).")",
                "my_pagecount" => "(".(new Zend_Db_Expr($Spagecount)).")",
                "my_pagenew" => new Zend_Db_Expr("(".$Spagenew.")"),
                "my_fromfollowers" => "(".(new Zend_Db_Expr($Sfollowers)).")",
                "my_lastactivity" => "(".(new Zend_Db_Expr($SlastActivity)).")",
                "my_votomedio" => "(".(new Zend_Db_Expr($SvotoMedio)).")",
//                "my_newfromfollowers" => "(".(new Zend_Db_Expr($SnewFromFollowers)).")",
//                "orderBy" => "(".(new Zend_Db_Expr($extra)).")",
            ))
//            ->order("orderBy ".$direction)
        ;
        if ($idGroup) {
            $select->where("story_group_id = ?",(int)$idGroup);
        }
        if ($storyId) {
            $select->where("story_id = ?",(int)$storyId);
        } else {
            switch ($order) {
                case "votes" :
                    $select->order("my_votes DESC");
                    break;
                case "votomedio" :
                    $select->order("my_votomedio DESC");
                    break;
                case "views" :
                    $select->order("my_views DESC");
                    break;
                case "pagecount" :
                    $select->order("my_pagecount DESC");
                    break;
                case "pagenew" :
                    $select->order("my_pagenew DESC");
                    break;
                case "lastactivity" :
                    $select->order("my_lastactivity DESC");
                    break;
            }
        }
//        echo $extra->__toString();
//        echo "<hr>";
//        echo $select->__toString();
//        die;
        return $select;
    }

}

