<?php

class Users_Model_User extends Engine_Api_Db_Row
{

    private $_details = null;

    public function getDetails() {
        if ($this->_details == null) {
            $this->_details = new stdClass();
            $this->_details->countStories = Engine_Api_Stories::getCountOfUserStories($this->getIdentity());
            $this->_details->countPages = Engine_Api_Pages::getCountOfUserPages($this->getIdentity());
            $this->_details->countFollowers = Engine_Api_Followers::getCountOfFollowers($this->getIdentity());
            $this->_details->countFollowing = Engine_Api_Followers::getCountOfFollowing($this->getIdentity());
            $this->_details->countVotes = Engine_Api_Users::getCountOfVotes($this->getIdentity());
            $this->_details->countPagesRead = Engine_Api_Users::getCountOfPagesRead($this->getIdentity());
        }
        return $this->_details;
    }

}

