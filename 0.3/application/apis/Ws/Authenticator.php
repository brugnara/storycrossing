<?php

class Engine_Api_Ws_Authenticator {

    protected $_authenticationHeaderPresent = false;
    /**
     * @var mixed
     */
    protected $_authenticatedUser = null;
    /**
     * @var mixed
     */
    protected $_serviceClass = null;

    private $_login = array(
        "username" => "username",
        "password" => "password",
    );

    public function __construct($class) {
        if (!class_exists($class)) {
            throw new Exception('invalid class: ' . $class);
        }
        $this->_serviceClass = new $class();
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function authenticate($login) {
        $this->_authenticationHeaderPresent = true;
        //
        if ($login->username == $this->_login["username"] &&
            $login->password == $this->_login["password"]) {
            $this->_authenticatedUser = array(
                "user_name" => $login->username,
                "user_id" => 91872,
            );
        } else {
            //per scoraggiare eventuali attacchi mettiamo uno sleep
            sleep(5);
        }
    }

    private function _isAuthenticationHeaderPresent() {
        return $this->_authenticationHeaderPresent;
    }

    public function __call($name, $arguments) {
        if (!$this->_isAuthenticationHeaderPresent() || is_null($this->_authenticatedUser)) {
            return "Login failed!";
        }
        if (!is_callable(array($this->_serviceClass, $name))) {
            return "Function not callable!";
        }
        $tmp = array($this->_authenticatedUser);
        $arguments = array_merge($tmp,$arguments);
        try {
            return call_user_func_array(array($this->_serviceClass, $name), $arguments);
        } catch(Exception $e) {
            return array(
                "ERROR" => array(
                    "DESC" => $e->getMessage(),
//                    "CLSS" => $e->getClass(),
                    "CODE" => $e->getCode(),
                    "LINE" => $e->getLine(),
                    "FILE" => $e->getFile(),
                    "STCK" => $e->getTraceAsString(),
                )
            );
        }

    }

}

