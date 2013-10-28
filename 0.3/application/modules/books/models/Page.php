<?php

class Books_Model_Page extends Engine_Api_Db_Row
{

    private $_userInfo = null;
    private $_group = null;

    public function getUserInfo() {
        if ($this->_userInfo == null) {
            $tUsers = new Users_Model_DbTable_Users();
            $this->_userInfo = $tUsers->find($this->page_user_id)->current();
        }
        return $this->_userInfo;
    }

    public function isWriterInMyBlacklist() {
        return Engine_Api_Blacklists::getLockType($this->getUserInfo()->user_id) != Engine_Api_Blacklists::NO_LOCK;
    }

    public function getDate() {
        return date_format(date_create($this->page_date), "d-m-Y H:i");
    }

    public function getGroup() {
        if ($this->_group == null) {
            $t = new Books_Model_DbTable_Stories();
            $s = $t->select();
            $s->where("story_id = ?",$this->page_story_id);
            $groupId = $t->fetchAll($s)->current()->story_group_id;
            $t = new Groups_Model_DbTable_Groups();
            $s = $t->select();
            $s->where("group_id = ?",$groupId);
            $this->_group = $t->fetchAll($s)->current();
        }
        return $this->_group;
    }

    public function getDateAdvancedFormat() {
        return Engine_Api_Date_Utility::advancedDateFormat($this->page_date);
    }

    public function getCapitalized($advanced = false) {
        $pattern = '#^[a-z]#i';
        $text = ucfirst($this->page_text);
        if ($advanced) {
            if (preg_match($pattern,$text,$matches)) {
                $char = $matches[0];
                $text = preg_replace('#^[A-Z]#',$newChar,$text);
            }
        }
        $this->page_text = $text;
    }

    public function getPercentage() {
        $counter = Engine_Api_Pages::getCountOfViewsOnQueries($this->page_prev_page_id);
        if (!$counter) {
            return 0;
        }
        if ($this->views) {
            $zeros = 1;
        } else {
            $zeros = 0;
        }
        return number_format($this->views * 100 / $counter,$zeros);
    }

}

