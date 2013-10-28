<?php

class Engine_Api_Menu_Manager {

    public static function getNavigation($name,array $options = array(),$activeItem = null) {
        //set last seen
        Engine_Api_Users::updateLastSeen();
        //
        $pages = self::getMenuItems($name,$options,$activeItem);
        $navigation = new Zend_Navigation();
        $navigation->addPages($pages);
        return $navigation;
    }

    public static function getMenuItems($name,array $options = array(),$activeItem = null) {

        $tMenuItems = new Application_Model_DbTable_Menuitems();
        $items = $tMenuItems->getItems($name,Engine_Api_Users::isLogged());
        $pages = array();
        $translator = Zend_Registry::get("Zend_Translate");
        if (count($items)) {
            foreach ($items as $item) {
                $page = (array) json_decode($item->menu_item_params);
                if ($item->menu_item_name == $activeItem) {
                    $page["class"] = "selected";
                }
                foreach ($options as $k => $option) {
                    $page["params"][$k] = $option;
                }
//                print_r($page);
                $page["label"] = $translator->translate($item->menu_item_label);
                if ($item->menu_item_name == "updates") {
                    //conto gli aggiornamenti dell'utente corrente e li mostro
                    $tUserUpdates = new Users_Model_DbTable_Userupdates();
                    $tot = $tUserUpdates->getCountUserUpdates();
                    if ($tot) {
                        $page["class"] = "newUpdates";
                    }
                    $page["label"].= " ($tot)";
                }
                $page['reset_params'] = true;
                $page['active'] = true;
                $pages[] = $page;
            }
        }
//        die;
        return $pages;
    }

}