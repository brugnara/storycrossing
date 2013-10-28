<?php

class Application_Model_DbTable_Queries extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_page_queries';
    protected $_rowClass = 'Application_Model_Query';

    public function getQueries($idPage) {
        $select = $this->select();
        $select
            ->where("page_query_page_id = ?",$idPage)
        ;
        return $this->fetchAll($select);
    }

}

