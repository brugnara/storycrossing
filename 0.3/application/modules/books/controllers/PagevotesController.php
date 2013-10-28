<?php

class Books_PagevotesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('popup');
//        $this->_helper->layout->disableLayout();
    }

    public function voteAction() {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $idPage = (int) $this->_getParam("idpage");
        $voto = $this->_getParam("vote");
        if (($voto != "up" && $voto != "down") || !$idUser) {
            $this->view->error = 1;
        } else {
            //
            $tPageVotes = new Books_Model_DbTable_Pagevotes();
            if (!$tPageVotes->hasAlreadyVoted($idPage) && !Engine_Api_Pages::isOwnerOf($idPage)) {
                $tPageVotes->add($idPage,$voto == "up" ? 1 : 0,"");
                Engine_Api_Stream::add($idUser, "new_vote", $idPage);
                Engine_Api_Wall::add(array(
                    'message' => "",
                    'type' => 'vote',
                    'object_id' => $idPage,
                ), Engine_Api_Pages::getPageInfo($idPage)->page_user_id);
            }
            $this->view->voted = true;
        }
    }

    public function voteOLDAction()
    {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        $idPage = (int) $this->_getParam("idpage");
        if ($idUser != 0) {
            $form = new Books_Form_Vote();

            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                //
                //inserisco voto.
                $tPageVotes = new Books_Model_DbTable_Pagevotes();
                if (!$tPageVotes->hasAlreadyVoted($idPage) && !Engine_Api_Pages::isOwnerOf($idPage)) {
                    $tPageVotes->add($idPage,(int)$values["vote"],$values["comment"]);
                    Engine_Api_Stream::add($idUser, "new_vote", $idPage);
                    Engine_Api_Wall::add(array(
                        'message' => $values["comment"],
                        'type' => 'vote',
                        'object_id' => $idPage,
                    ), Engine_Api_Pages::getPageInfo($idPage)->page_user_id);
                }
                $this->view->voted = true;
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->error = true;
        }
    }

}

