<?php

class Books_ReporttoadminsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('popup');
    }

    public function indexAction()
    {

    }

    public function pageAction() {
        $form = new Books_Form_Report();
        $idPage = (int) $this->_getParam("id");
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            //invio mail
            $msg = "Un utente ha segnalato la pagina: <a target='_blank' href='http://storycrossing.com/books/stories/view/idpage/{$idPage}'>CLICCA PER APRIRE</a>\n".
                    "Descrizione del problema: <b>".$values["obj"]."</b>\n".
                    "\n".
                    "Messaggio:\n".
                    "<div style='border:2px solid #555'>{$values["body"]}</div>\n".
                    "";
            $msg = nl2br($msg);
//            echo $msg;
            Engine_Api_Mailer::send(array('supremi@storycrossing.com','Segnala pagina scorretta'), "Segnalazione di una pagina su StoryCrossing",$msg);
            $this->view->isOk = true;
            //
        }
        $this->view->form = $form;
    }

}

