<?php

class Groups_Model_Group extends Engine_Api_Db_Row {

    public function getCountBooks() {
        $t = new Books_Model_DbTable_Stories();
        $s = $t->select();
        $s->from($t->info("name"),array(
            "story_id",
            "t" => "count(*)",
        ));
        $s->where("story_group_id = ?",$this->getIdentity());
        return $t->fetchAll($s)->current()->t;
    }

    public function getCountWriters() {
        $t = new Groups_Model_DbTable_Members();
        $s = $t->select();
        $s
            ->from($t->info("name"),array(
                "t" => "count(*)",
                "group_member_id",
            ))
            ->where("group_member_group_id = ?",$this->getIdentity());
        return $t->fetchAll($s)->current()->t;
    }

}

