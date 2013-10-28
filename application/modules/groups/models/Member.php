<?php

class Groups_Model_Member extends Engine_Api_Db_Row {

    private $_info = null;

    public function getInfo() {
        if ($this->_info == null)
            $this->_info = Engine_Api_Users::getAUserInfo($this->group_member_user_id);
        return $this->_info;
    }

}

