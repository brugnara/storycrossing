<?php

class Engine_Api_Db_Row extends Zend_Db_Table_Row_Abstract {

    protected $_identity;

    public function __construct(array $config = array()) {
        parent::__construct($config);
        //prepare identity
        $primary = $this->getTable()->info(Zend_Db_Table_Abstract::PRIMARY);
//        if (count($primary) !== 1) {
//            throw new Zend_Exception('Non riesco a gestire più di una chiave primaria. Ce ne sono '.join(", ",$primary));
//        }
        $prop = array_shift($primary);
        if (!isset($this->$prop)) {
            throw new Zend_Exception('MY - Primary key not defined: '.$prop);
        } else {
            $this->_identity = $this->$prop;
        }
    }

    public function getIdentity() {
        return (int) $this->_identity;
    }

}