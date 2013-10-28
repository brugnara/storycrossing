<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function run() {
        Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
        date_default_timezone_set("Europe/Dublin");
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => APPLICATION_PATH."/",
            'namespace'     => 'Engine',
            'resourceTypes' => array(
                'acl' => array(
                    'path'      => 'apis/',
                    'namespace' => 'Api',
                ),
            ),
        ));
        //
        Engine_Api_Session::setVar("CPUstartTime",microtime(1));
        Engine_Api_Users::updateLastSeen();
        Zend_Registry::set('config', $this->getOptions());
        //preparo translate
//        $locale = new Zend_Locale('it_IT');
//        Zend_Locale::setDefault("it_IT");
        $adapter = new Zend_Translate(array(
            'adapter' => 'csv',
            'content' => APPLICATION_PATH.'/languages/it/local.csv',
            'locale'  => 'it'
        ));
        $adapter->addTranslation(array(
            'content' => APPLICATION_PATH.'/languages/en/local.csv',
            'locale'  => 'en'
        ));
        //controllo se in session c'è un local:
        $adapter->setLocale(Engine_Api_Users::getLocale());
        //
        Zend_Registry::set('Zend_Translate', $adapter);
//        Zend_Registry::set('Zend_Locale', $locale);
        parent::run();
    }

    protected function _initAppKeysToRegistry() {
         $appkeys = new Zend_Config_Ini(APPLICATION_PATH . '/configs/appkeys.ini');
         Zend_Registry::set('keys', $appkeys);
     }

}

