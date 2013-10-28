<?php

class Users_Model_Wall extends Engine_Api_Db_Row
{
    private $_d = null;

    public function getPosterInfo() {
        return Engine_Api_Users::getAUserInfo($this->user_wall_writer_id);
    }

}

