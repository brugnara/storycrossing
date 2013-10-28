<?php

class Books_Model_Story extends Engine_Api_Db_Row
{
    private $_userInfo = null;

    public function getWriter() {
        if ($this->_userInfo == null) {
            $tUsers = new Users_Model_DbTable_Users();
            $this->_userInfo =  $tUsers->find($this->story_user_id)->current();
        }
        return $this->_userInfo;
        return "a";
    }

    public function getTags($limit = null) {
        $tST = new Books_Model_DbTable_Storytags();
        $select = $tST->select()->where("story_tag_story_id = ?",$this->getIdentity());
        if ($limit) {
            //se chiedo un limit, vorrò solo i X più popolari
            $select->order("story_tag_counter DESC")->limit($limit);
        }
        return $tST->fetchAll($select);
    }

    public function getCountTags($limit = null) {
        $tST = new Books_Model_DbTable_Storytags();
        $select = $tST
            ->select()
                ->from($tST->info("name"),array(
                    "tot" => new Zend_Db_Expr("sum(story_tag_counter)"),
                    "most" => new Zend_Db_Expr("max(story_tag_counter)"),
                    "less" => new Zend_Db_Expr("min(story_tag_counter)"),
                ))
                ->where("story_tag_story_id = ?",$this->getIdentity());
        if ($limit) {
            $select->order("story_tag_counter DESC")->limit($limit);
        }
        return $tST->fetchAll($select)->current();
    }

    public function getCounterPages() {
        $tPages = new Books_Model_DbTable_Pages();
        $idStory = $this->getIdentity();
        $select = $tPages->select();
        $select
            ->from($tPages->info("name"),array(
                "page_id" => "page_id",
                "pages" => "count(*)",
                ))
            ->where("page_story_id = ?",$idStory)
        ;
        return $tPages->fetchAll($select)->current()->pages;
    }

    public function getUnreadedPages() {
        $tUpdates = new Users_Model_DbTable_Userupdates();
        $tPages = new Books_Model_DbTable_Pages();
        $idStory = $this->getIdentity();
        $updates = $tUpdates->getUserUpdates("new_page");
        $select = $tPages->select();
        $select
            ->from($tPages->info("name"),array(
                "page_id" => "page_id",
                "pages" => "count(*)",
                ))
            ->where("page_story_id = ?",$idStory)
        ;
        if (count($updates)) {
            $tmp = array();
            foreach ($updates as $update) {
                $tmp[] = $update->user_update_object_id;
            }
            $select->where("page_id IN (".join(", ",$tmp).")");
        } else {
            return 0;
        }
        return $tPages->fetchAll($select)->current()->pages;
    }

}

