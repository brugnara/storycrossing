<?php

class Books_Model_DbTable_Pagebgs extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_page_bgs';

    public function getBgs() {
        $select = $this->select()->order("page_bg_file ASC");
        return $this->fetchAll($select);
    }

}

