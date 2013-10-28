<?php

class Engine_Api_Multiplelogins_Auth_Identity_Google extends Engine_Api_Multiplelogins_Auth_Identity_Generic
{
   protected $_api;

   public function __construct($token)
   {
        $this->_api = new Engine_Api_Multiplelogins_Resource_Google($token);
        $this->_name = 'google';
        $this->_id = $this->_api->getId();
   }

   /**
    *
    * @return Engine_Api_Multiplelogins_Resource_Google
    */
    public function getApi()
    {
        return $this->_api;
    }
}
