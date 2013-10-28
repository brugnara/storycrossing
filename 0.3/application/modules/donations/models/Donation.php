<?php

class Donations_Model_Donation extends Engine_Api_Db_Row
{

    public function getUserInfo() {
        return Engine_Api_Users::getAUserInfo($this->donation_user_id);
    }

}

