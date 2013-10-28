<?php

class Groups_Model_DbTable_Members extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_group_members';
    protected $_rowClass = 'Groups_Model_Member';

    public function addMember($idGroup,$idUser,array $permits) {
        //
        $id = $this->insert(array(
            "group_member_user_id" => (int)$idUser,
            "group_member_group_id" => (int)$idGroup,
            "group_member_is_owner" => (int)$permits[0],
            "group_member_is_admin" => (int)$permits[1],
            "group_member_can_write" => (int)$permits[2],
        ));
        return $id;
    }

}

