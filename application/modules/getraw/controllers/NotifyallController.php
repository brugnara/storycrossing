<?php

class Getraw_NotifyallController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {
        // action body
    }

    public function s3cr3tm4il3rAction() {
        //aumento tempo di esecuzione
        ini_set('max_execution_time', 0);
        //scorro tutti gli utenti.
        $tU = new Users_Model_DbTable_Users();
        $select = $tU->select();
        $select
            ->where("user_active = 1")
        ;
        $fetch = $tU->fetchAll($select);
        //conto storie di questa ultima settimana

        //
        foreach ($fetch as $f) {
            $email = Engine_Api_Useroptions::get('email_notify',$f->user_id);
            if ($email === 0) {
                //se è null o 1, the show must go on
                continue;
            }
            $tU = new Users_Model_DbTable_Userupdates();
            $userPageUpdates = count($tU->getUserUpdates("new_page"));
            $msg = "";
            if ($userPageUpdates) {
                $msg.= sprintf("Hi %s, you have %s unreaded news!\n",$f->user_name,$userPageUpdates);
            }
            $msg.= sprintf("This week, %s pages has been written, %s books started.",$countPages,$countBooks);
            echo $msg."<hr>";
        }
    }

}

