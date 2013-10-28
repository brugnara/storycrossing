<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function tetAction() {
        $t0 = microtime(1);
        for ($i = 0;$i<10;$i++) {
            Engine_Api_Mailer::send(array(
                "hellismyhouse@gmail.com",
                "daniele",
            ), $subject, $message);
        }
        $t1 = microtime(1);
        echo $t1-$t0;
        die;
    }

    public function chronAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        ini_set('max_execution_time', 0);
        echo "prima";
        sleep(120);
        echo "dopo";
    }

    public function indexAction() {
        //menu
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "home");
        Engine_Api_Session::setVar("story");
        //controllo browser
        $userSO = Engine_Api_Utility::getUserSO();
//        $browser = Engine_Api_Utility::getUserBrowser();
        $tStories = new Books_Model_DbTable_Stories();
        $this->view->totBooks = $tStories
        ->fetchAll($tStories
                ->select()
                ->from($tStories->info("name"),array(
                    "story_id",
                    "tot" => new Zend_Db_Expr("count(*)")
                )))->current()->tot;
        $tPages = new Books_Model_DbTable_Pages();
        $this->view->totPages = $tPages
        ->fetchAll($tPages
                ->select()
                ->from($tPages->info("name"),array(
                    "page_id",
                    "tot" => new Zend_Db_Expr("count(*)")
                )))->current()->tot;
        $tUsers = new Users_Model_DbTable_Users();
        $this->view->totUsers = $tUsers
        ->fetchAll($tUsers
                ->select()
                ->from($tUsers->info("name"),array(
                    "user_id",
                    "tot" => new Zend_Db_Expr("count(*)")
                )))->current()->tot;
        $totPageRead;
        if (Engine_Api_Users::isLogged()) {
            //ok
        } else {
            //prendo una storia
            $tS = new Books_Model_DbTable_Stories();
            $orderBy = "votes";
            $stories = $tS->getOrderBy($orderBy)->limit(10);
            $books = $tS->fetchAll($stories);
            $story = $books[rand(0, 9)];
            $books = null;
            $tP = new Books_Model_DbTable_Pages();
            $page = $tP->getStartPage($story->story_id);
            $queries = $tP->getChildPages($page->page_id);
            //pulizia delle variabili in sessione
            Engine_Api_Session::setVar("sx_reply_1");
            Engine_Api_Session::setVar("sx_reply_id_1");
            Engine_Api_Session::setVar("sx_reply_2");
            Engine_Api_Session::setVar("sx_reply_id_2");
            Engine_Api_Session::setVar("dx_reply_1");
            Engine_Api_Session::setVar("dx_reply_id_1");
            Engine_Api_Session::setVar("dx_reply_2");
            Engine_Api_Session::setVar("dx_reply_id_2");
            //salvo in sessione alcune variabili della storia più votata e una fra le nuove
            Engine_Api_Session::setVar("sx_title", $story->story_title);
            Engine_Api_Session::setVar("sx_id", $story->story_id);
            Engine_Api_Session::setVar("sx_text", $page->page_text);
            if (count($queries) > 0) {
                Engine_Api_Session::setVar("sx_reply_1", $queries[0]->page_title);
                Engine_Api_Session::setVar("sx_reply_id_1", $queries[0]->page_id);
            }
            if (count($queries) > 1) {
                Engine_Api_Session::setVar("sx_reply_2", $queries[1]->page_title);
                Engine_Api_Session::setVar("sx_reply_id_2", $queries[1]->page_id);
            }
            //
            //prendo una storia
            $tS = new Books_Model_DbTable_Stories();
            $orderBy = "lastactivity";
            $stories = $tS->getOrderBy($orderBy)->limit(10);
            $books = $tS->fetchAll($stories);
            $story = $books[rand(0, 9)];
            $books = null;
            $tP = new Books_Model_DbTable_Pages();
            $page = $tP->getStartPage($story->story_id);
            $queries = $tP->getChildPages($page->page_id);//
            Engine_Api_Session::setVar("dx_id", $story->story_id);
            Engine_Api_Session::setVar("dx_title", $story->story_title);
            Engine_Api_Session::setVar("dx_text", $page->page_text);
            if (count($queries) > 0) {
                Engine_Api_Session::setVar("dx_reply_1", $queries[0]->page_title);
                Engine_Api_Session::setVar("dx_reply_id_1", $queries[0]->page_id);
            }
            if (count($queries) > 1) {
                Engine_Api_Session::setVar("dx_reply_2", $queries[1]->page_title);
                Engine_Api_Session::setVar("dx_reply_id_2", $queries[1]->page_id);
            }
            //cambio layout
            $this->_helper->_layout->setLayout('homepage');
        }


        return;
        //loggato?
        if (Engine_Api_Users::isLogged()) {
            //redirect to menu/index
            $controller = "menu";
            $action = "index";
        } else {
            //redirect to user/login
            $controller = "users";
            $action = "login";
        }
        $this->_helper->redirector->gotoRoute(array(
            'controller' => $controller,
            'action' => $action,
        ),null,true);
    }




}



