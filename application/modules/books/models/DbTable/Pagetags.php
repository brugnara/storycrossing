<?php

class Books_Model_DbTable_Pagetags extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_page_tags';

    public function getPagesHavingTag($tag) {
        $select = $this->select();
        $select
            ->where("page_tag_tag LIKE ?",'%'.$tag.'%')
        ;
        return $this->fetchAll($select);
    }

}

