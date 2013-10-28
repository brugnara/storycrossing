<?php

class Books_AjaxController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function getvotesAction() {
        //idpage
        $idPage = (int)$this->_getParam("id");
        $tPageVotes = new Books_Model_DbTable_Pagevotes();
        $votes = $tPageVotes->getPageVotes($idPage);
        $ret = array();
        foreach ($votes as $vote) {
            $ret[] = array(
                "user" => Engine_Api_Users::getAUserInfo($vote->page_vote_user_id)->user_name,
                "vote" => $vote->page_vote_vote,
                "comment" => $vote->page_vote_comment,
            );
        }
        echo json_encode($ret);
    }

}