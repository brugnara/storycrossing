<?php

class Getrawc2_AuthController extends Engine_Api_Controller
{

    public function loginAction() {
        //controllo i parametri di input
        $data = array();
        $seq = $this->_getParam("seq");
        if (empty($seq)) {
            Engine_Api_Headers::_404();
        }

        $data["user_mail"] = $this->_getParam("usermail");
        $data["user_pass"] = $this->_getParam("p455w0rd");
        $tUser = new Users_Model_DbTable_Users();
        if (!empty($data["user_mail"]) && !empty($data["user_pass"]) && $tUser->isLoginOk($data)) {
            //cancello l'attuale token
            Engine_Api_Logintokens::deleteUserToken($data["user_id"]);
            //genero nuovo token
            $token = Engine_Api_Logintokens::setAndGetNewToken($data["user_id"]);
        } else {
            sleep(5);
            Engine_Api_Headers::_404();
        }
        $this->_return($token);
    }

    public function getuserinfoAction() {
        $id = (int)$this->_getParam("id");
        if ($id) {
            $user = Engine_Api_Users::getAUserInfo($id);
            $this->_return(array(
                "name" => $user->user_name,
            ));
        }
        Engine_Api_Headers::_404();
    }

    public function registerAction() {
        //registrazione!
        //ho bisogno di, username, password, email.
        $u = $this->_getParam("name");
        $p = $this->_getParam("pass");
        $m = $this->_getParam("mail");
        // /^[a-z0-9\._-]+$/i
        // /^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/
        if (!preg_match('/^[a-z0-9\._-]+$/i', $u)) {
            //username non valido.
            $this->_return(array(
                "error" => 1,
            ));
        }
        if (!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $m)) {
            //email non valida!
            $this->_return(array(
                "error" => 2,
            ));
        }
        //tutto ok!
        $values = array(
            "user_name" => $u,
            "user_pass" => $p,
            "user_mail" => $m,
            "user_locale" => "it_IT",
        );
        //provo registrazione
        $tUsers = new Users_Model_DbTable_Users();
        $idUser = $tUsers->addUser($values);
        if ($idUser) {
            //OK
            Engine_Api_Mailer::send(array(
                $m,
                $u,
            ), "StoryCrossing Registrazione Mobile", "Grazie per esserti registrato a StoryCrossing. Promemoria password: ".$p);
            //aggiungo attivita allo stream del sito
            Engine_Api_Stream::add($idUser, "new_user");
            $this->_return(array(
                "status" => "ok",
            ));
        } else {
            //KO
            $this->_return(array(
                "error" => 3,
            ));
        }
    }

}

