<?php

class Users_AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function loginfacebookAction() {
        // Create our Application instance (replace this with your appId and secret).
        $facebook = new Engine_Api_Facebook_Main(array(
            'appId'  => '249962915089037',
            'secret' => '28953e33ea1e47a54d15a8400ef1a13f',
        ));

        // Get User ID
        $this->view->user = $facebook->getUser();

        // We may or may not have this data based on whether the user is logged in.
        //
        // If we have a $user id here, it means we know the user is logged into
        // Facebook, but we don't know if the access token is valid. An access
        // token is invalid if the user logged out of Facebook.

        if ($this->view->user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $this->view->user_profile = $facebook->api('/me');
                //se ho i vari dettagli, posso fare login.
                if (!empty($this->view->user_profile["email"])) {
                    $mail = $this->view->user_profile["email"];
                    //c'è nel db un utente con questa mail?
                    $t = new Users_Model_DbTable_Users();
                    if ($t->isEmailPresent($mail)) {
                        //aggiorno nome utente [name] nel db. ad ogni login così non ho problemi se cambia su faccebook
                        $t->update(array(
                            "user_name" => $this->view->user_profile["name"],
                            "user_fb_id" => $this->view->user_profile["id"],
                            "user_fb_profile" => $this->view->user_profile["link"],
                        ),array(
                            "user_mail = ?" => $mail,
                        ));
                        //faccio login.
                    } else {
                        //aggiungo al db l'email con nome utente.
                        $pwd = substr(md5(rand(10, 2030)), 1, 10);
                        $data = array(
                            "user_name" => $this->view->user_profile["name"],
                            "user_pass" => $pwd,
                            "user_mail" => $mail,
                            "user_fb_id" => $this->view->user_profile["id"],
                            "user_fb_profile" => $this->view->user_profile["link"],
                            "user_locale" => "en_EN",
                        );
                        $idUser = $t->addUser($data);
                        if ($idUser) {
                            //registrazione ok!
                            Engine_Api_Stream::add($idUser, "new_user");
                            Engine_Api_Mailer::send(array(
                                $mail,
                                $this->view->user_profile["name"],
                            ), "Registrazione effettuata!", "Grazie per esserti registrato a StoryCrossing. Per accedere dal tuo cellulare, usa la tua email e questa password: ".$pwd);
                        } else {
                            //FUCK!
                            $this->_helper->redirector->gotoRoute(array(
                                'module' => "users",
                                'controller' => "auth",
                                'action' => 'login'
                            ),null,true);
                            return;
                        }
                    }
                    $values = array(
                        "user_mail" => $mail,
                        "user_fb_id" => $this->view->user_profile["id"],
                    );
                    $t->isLoginOk($values, false);
                    Engine_Api_Users::setLogin($values);
                    $this->_helper->redirector->gotoRoute(array(
                        'module' => "users",
                        'controller' => "wall",
                    ),null,true);
                }
                //
            } catch (Engine_Api_Facebook_Exception $e) {
                error_log($e);
                file_put_contents("log.log", print_r($e->getMessage(),1));
                $this->view->user = null;
                /*
                $this->_helper->redirector->gotoRoute(array(
                    'module' => "users",
                    'controller' => "auth",
                    'action' => 'login'
                ),null,true);*/
            }
        }

        // Login or logout url will be needed depending on current user state.
        if ($this->view->user) {
            $this->view->logoutUrl = $facebook->getLogoutUrl();
        } else {
            $this->view->loginUrl = $facebook->getLoginUrl(array('scope' => "email"));
            header("location:".$this->view->loginUrl);
        }

        // This call will always work since we are fetching public data.
        //$this->view->naitik = $facebook->api('/naitik');
    }

    public function loginAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "login");
        // action body
        $form = new Users_Form_Login();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $tUsers = new Users_Model_DbTable_Users();
            if ($tUsers->isLoginOk($values)) {
                Engine_Api_Users::setLogin($values);
                $this->_helper->redirector->gotoRoute(array(
                    'module' => "users",
                    'controller' => "wall",
                ),null,true);
            } else {
                $this->view->error = "E-Mail or Password wrong!";
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction() {
        Engine_Api_Users::setLogout();
        $this->_helper->redirector->gotoRoute(array(
            'module' => "",
            'action' => "",
        ),null,true);
    }

    public function registerAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "login");
        $form = new Users_Form_Register();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // we will add the category
            $values = $form->getValues();
            if ($values["user_pass2"] == $values["user_pass"]) {
                unset($values["user_pass2"]);
                unset($values["captcha"]);
                $tUsers = new Users_Model_DbTable_Users();
                $idUser = $tUsers->addUser($values);
                if ($idUser) {
                    //loggo utente
                    Engine_Api_Users::setLogin(array(
                        "user_id" => $idUser,
                        "user_name" => $values["user_name"],
                        "user_locale" => $values["user_locale"],
                    ));
                    //aggiungo attivita allo stream del sito
                    Engine_Api_Stream::add($idUser, "new_user");
                    $this->_helper->redirector->gotoRoute(array(
                        'action' => "index",
                    ),null,true);
                } else {
                    $this->view->error = "Utente gia' presente.";
                }
            } else {
                $this->view->error = "Le password non coincidono.";
            }
        }
        $this->view->form = $form;
    }

    public function resetpasswordAction() {
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "login");
        $hash = $this->_getParam("token");
        $errors = "";
        if (!$hash) {
            $form = new Users_Form_Resetpassword();
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $tUsers = new Users_Model_DbTable_Users();
                $select = $tUsers->select();
                $select
                    ->where("user_mail = ?",$values["user_mail"]);
                $fetch = $tUsers->fetchAll($select);
                if (count($fetch)) {
                    $fetch = $fetch->current();
                    $hash = sha1($fetch->user_pass.$this->_PWD_KEY);
                    $message = "Click to reset your password: <a href='http://storycrossing.com/".$this->view->url(array(
                        "module" => "users",
                        "controller" => "auth",
                        "action" => "resetpassword",
                        "token" => $hash,
                        "mail" => $fetch->user_mail,
                    ),null,true)."'>RESET</a>";
//                    die($message);
                    Engine_Api_Mailer::send(
                        array($values["user_mail"],null),
                        "storycrossing.com - Reset password",
                        $message);
                    $this->view->message = "Check your email!";
                } else {
                    $errors = true;
                }
            }
            $this->view->form = $form;
        } else {
            //resetto la password dell'utente.
            $mail = $this->_getParam("mail");
            $tUsers = new Users_Model_DbTable_Users();
            $select = $tUsers->select();
            $select
                ->where("user_mail = ?",$mail);
            $fetch = $tUsers->fetchAll($select);
            if (count($fetch)) {
                $fetch = $fetch->current();
                if (sha1($fetch->user_pass.$this->_PWD_KEY) == $hash) {
                    //ok, posso resettare la password.
                    $newPass = substr(md5(microtime(1)), 1, 9);
                    $tUsers->update(array(
                        "user_pass" => sha1($newPass),
                    ),array(
                        "user_id = ?" => $fetch->user_id
                    ));
                    Engine_Api_Logintokens::deleteUserToken($fetch->user_id);
                    $this->view->isOk = true;
                    $this->view->newPassword = $newPass;
                } else {
                    $errors = true;
                }
            } else {
                $errors = true;
            }
        }
        if ($errors) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "index",
                'action' => "index",
            ),null,true);
        }
    }

    public function changepasswordAction() {
        $idUser = Engine_Api_Users::getUserInfo()->user_id;
        if (!$idUser) {
            $this->_helper->redirector->gotoRoute(array(
                'controller' => "index",
                'action' => "index",
            ),null,true);
            return;
        }
        $this->view->navigation = Engine_Api_Menu_Manager::getNavigation("index_menu", array(), "userprofile");
        $form = new Users_Form_Changepassword();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            if ($values["user_pass"] == $values["user_pass2"]) {
                $tUsers = new Users_Model_DbTable_Users();
                $select = $tUsers->select();
                $select
                    ->where("user_id = ?",$idUser);
                $select
                    ->where("user_pass = ?",sha1($values["old_password"]));
                $fetch = $tUsers->fetchAll($select);
                if (count($fetch)) {
                    $idUser = $fetch->current()->user_id;
                    $tUsers->update(array(
                        "user_pass" => sha1($values["user_pass"]),
                    ),array(
                        "user_id = ?" => $idUser,
                    ));
                    Engine_Api_Logintokens::deleteUserToken($idUser);
                    $this->view->isOk = true;
                }
                /** if (!empty($values["user_mail"]) || !empty($values["user_name"])) {
                    $tUsers = new Application_Model_DbTable_Users();
                    $select = $tUsers->select();
                    if ($values["user_mail"]) {
                        $select
                            ->where("user_mail = ?",$values["user_mail"]);
                    }
                    if ($values["user_name"]) {
                        $select
                            ->where("user_name = ?",$values["user_name"]);
                    }
                    $select
                        ->where("user_pass = ?",sha1($values["old_password"]));
                    $fetch = $tUsers->fetchAll($select);
                    if (count($fetch)) {
                        $idUser = $fetch->current()->user_id;
                        $tUsers->update(array(
                            "user_pass" => sha1($values["user_pass"]),
                        ),array(
                            "user_id = ?" => $idUser,
                        ));
                        $this->view->isOk = true;
                    } else {
                        $this->view->error = "Username/Email or password wrong!";
                    }
                } else {
                    $this->view->error = "Please type username or email!";
                } */
            } else {
                $this->view->error = "Password doesn't match!";
            }
        }
        $this->view->form = $form;
    }


}

