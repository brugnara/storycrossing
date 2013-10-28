<?php

class Users_Model_Userupdate extends Engine_Api_Db_Row
{

    private $_details = null;

    public function getDetails() {
        if (empty($this->_details)) {
            //dato il type e l'object id, torno indietro il fetch.
            $this->_details = new stdClass();
            $this->_details->object_type = $this->user_update_type;
            //
            switch ($this->user_update_type) {
                case "new_page" :
                    $details = Engine_Api_Pages::getPageInfo($this->user_update_object_id);
                    $this->_details->object_id = $details->page_id;
                    $this->_details->object = $details;
                    //se è vuoto, significa che la pagina è stata cancellata e non ha senso avere la notifica di una pagina non più presente.
                    if (empty($this->_details->object)) {
                        $tU = new Users_Model_DbTable_Userupdates();
                        $tU->delUpdate($this->user_update_object_id,"new_page");
                    }
                    break;
                case "new_story" :
                    $details = Engine_Api_Stories::getStoryInfo($this->user_update_object_id);
                    $this->_details->object_id = $details->story_id;
                    $this->_details->object = $details;
                    //se è vuoto, significa che la pagina è stata cancellata e non ha senso avere la notifica di una pagina non più presente.
                    if (empty($this->_details->object)) {
                        $tU = new Users_Model_DbTable_Userupdates();
                        $tU->delUpdate($this->user_update_object_id,"new_page");
                    }
                    break;
                //more...
                default:
                    return null;
            }
        }
        return $this->_details;
    }

}

