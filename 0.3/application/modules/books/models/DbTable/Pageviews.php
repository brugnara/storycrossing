<?php

class Books_Model_DbTable_Pageviews extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_page_views';

    public function getReads($idPage) {
        $select = $this->select();
        $select
            ->from($this->info("name"),array(
                "tot" => "count(*)"
            ))
            ->where("page_view_page_id = ?",(int)$idPage);
        return (int)$this->fetchAll($select)->current()->tot;
    }

}

