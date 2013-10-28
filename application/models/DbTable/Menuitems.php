<?php

class Application_Model_DbTable_Menuitems extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_menu_items';

    public function getItems($menu,$isLogged,$module = "core") {
        $select = $this->select();
        $select
            ->where("menu_item_enabled = 1")
            ->where("menu_item_module = ?",$module)
            ->where("menu_item_menu = ?",$menu)
            ->where("(menu_item_logged = 2 OR menu_item_logged = ?)",(int)$isLogged)
            ->order("menu_item_order ASC")
        ;
        return $this->fetchAll($select);
    }


}

