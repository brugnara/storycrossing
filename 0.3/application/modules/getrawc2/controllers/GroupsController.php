<?php

class Getrawc2_GroupsController extends Engine_Api_Controller {

    public function getAction() {
        // what/all || id
        $id = (int)$this->_getParam("what");
        $t = new Groups_Model_DbTable_Groups();
        $ret = array();
        foreach ($t->getGroups($id) as $group) {
            $ret[] = array(
                "id" => (int)$group->group_id,
                "name" => $group->group_name,
                "desc" => $group->group_desc,
                "type" => ($group->group_type == 1 ? "aperto" : "sola lettura"),
                "date" => $group->group_date,
                "countbooks" => $group->getCountBooks(),
                "countusers" => $group->getCountWriters(),
            );
        }
        $this->_return(array("groups" => $ret));
    }

}