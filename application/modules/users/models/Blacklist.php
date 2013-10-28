<?php

class Users_Model_Blacklist extends Engine_Api_Db_Row
{

    public function getUserInfo() {
        return Engine_Api_Users::getAUserInfo($this->blacklist_blocked_id);
    }

}

