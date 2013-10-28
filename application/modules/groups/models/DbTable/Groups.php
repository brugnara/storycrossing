<?php

class Groups_Model_DbTable_Groups extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_groups';
    protected $_rowClass = 'Groups_Model_Group';

    public function getGroups($id = 0) {
        $select = $this->select();
        $select->order("group_id DESC");
        if ($id) {
            $select->where("group_id = ?", (int)$id);
        }
        return $this->fetchAll($select);
    }

    public function addGroup($values) {
        $data = array(
            "group_name" => $values["name"],
            "group_desc" => $values["desc"],
            "group_type" => (int)$values["type"],
        );
        return $this->insert($data);
    }

}

