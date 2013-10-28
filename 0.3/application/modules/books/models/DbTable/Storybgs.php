<?php

class Books_Model_DbTable_Storybgs extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_story_bgs';

    public function getBgs() {
        $select = $this->select();
        return $this->fetchAll($select);
    }

}

