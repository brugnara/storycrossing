<?php

class Books_Model_DbTable_Storytags extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_story_tags';

    public function getStoriesHavingTag($tag) {
        $select = $this->select();
        $select
            ->where("story_tag_tag LIKE ?",'%'.$tag.'%')
        ;
        return $this->fetchAll($select);
    }

}

