<?php

class Users_Model_DbTable_Blacklists extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_blacklists';
    protected $_rowClass = "Users_Model_Blacklist";

    public function add($data) {
        return $this->insert($data);
    }

}

