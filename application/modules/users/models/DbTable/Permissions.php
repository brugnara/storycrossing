<?php

class Users_Model_DbTable_Permissions extends Zend_Db_Table_Abstract
{

    protected $_name = 'engine_user_permissions';

    public function hasPermissionTo($do,$userId = null) {
        $s = $this->select();
        $s
            ->where("user_permission_user_id = ?",(int)$userId)
            ->where("user_permission_permission_name = ?",$do);
        $fetch = $this->fetchAll($s);
        return count($fetch) > 0;
    }

}

