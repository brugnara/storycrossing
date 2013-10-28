<?php

class Engine_Api_Permissions {

    public static function canEditThisPage($idUser, Books_Model_Page $page) {
        if ((!$idUser || Engine_Api_Pages::getCountOfQueries($page->page_id)) && !self::hasPermissionTo("edit_all", $idUser)) {
            return false;
        }
        return ($idUser == $page->page_user_id) || (self::hasPermissionTo("edit_all", $idUser));
    }

    public static function hasPermissionTo($do,$userId = null) {
        $t = new Users_Model_DbTable_Permissions();
        return $t->hasPermissionTo($do, $userId);
    }

}