<?php

class Application_Model_DbTable_Fonts extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_fonts';

    public function getFonts() {
        return $this->fetchAll($this->select());
    }

}

