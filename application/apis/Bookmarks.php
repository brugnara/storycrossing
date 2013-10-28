<?php

class Engine_Api_Bookmarks {

    public static function getBookmarks($idUser) {
        $tBm = new Users_Model_DbTable_Bookmarks();
        $select = $tBm->select();
        $select
            ->where('bookmark_user_id = ?',$idUser)
            ->order('bookmark_id DESC')
        ;
        return $tBm->fetchAll($select);
    }

    public static function add($idPage,$idUser,$name) {
        $tBm = new Users_Model_DbTable_Bookmarks();
        return $tBm->insert(array(
            "bookmark_user_id" => $idUser,
            "bookmark_page_id" => $idPage,
            "bookmark_name" => $name,
        ));
    }

    public static function isBookmarked($idPage,$idUser = null) {
        if ($idUser === null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (empty($idUser)) {
            return false;
        }
        $tBm = new Users_Model_DbTable_Bookmarks();
        $select = $tBm->select();
        $select
            ->where("bookmark_user_id = ?",$idUser)
            ->where("bookmark_page_id = ?",$idPage)
        ;
        return count($tBm->fetchAll($select));
    }

    public static function getPageFromBookmark($id) {
        $tBm = new Users_Model_DbTable_Bookmarks();
        $select = $tBm->select();
        $select
            ->where("bookmark_id = ?",$id)
        ;
        $idPage = $tBm->fetchAll($select)->current()->bookmark_page_id;
        //
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP->select();
        $select
            ->where("page_id = ?",$idPage)
        ;
        return $tP->fetchAll($select)->current();
    }

}