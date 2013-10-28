<?php

class Engine_Api_Pages {

    public static function isAlreadyPresent($title,$idPrevPage) {
        $tPages = new Books_Model_DbTable_Pages();
        $select = $tPages->select();
        $select
            ->where("page_prev_page_id = ?",$idPrevPage)
            ->where("page_title = ?",$title)
        ;
        return count($tPages->fetchAll($select));
    }

    public static function getPageInfo($id) {
        $id = (int)$id;
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP->select();
        $select
            ->where("page_id = ?",$id)
        ;
        //
        return $tP->fetchAll($select)->current();
    }

    public static function isOwnerOf($idPage,$idUser = null) {
        if (!empty($idPage)){
            $info = self::getPageInfo($idPage);
            if ($idUser === null) {
                $idUser = Engine_Api_Users::getUserInfo()->user_id;
            }
            return $info->page_user_id == $idUser;
        }
        return false;
    }

    public static function addView($idPage,$idUser = null) {
        if ($idUser == null) {
            $idUser = Engine_Api_Users::getUserInfo()->user_id;
        }
        if (!$idUser) {
            return;
        }
        //controllo che NON sia l'autore.
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP
            ->select()
                ->where("page_id = ?",$idPage)
                ->where("page_user_id = ?",$idUser);
        if (count($tP->fetchAll($select))) {
            return;
        }
        //controllo se c'è già il record.
        $tPagesViews = new Books_Model_DbTable_Pageviews();
        $select = $tPagesViews->select();
        $select
            ->where("page_view_user_id = ?",$idUser)
            ->where("page_view_page_id = ?",(int)$idPage)
        ;
        if (!count($tPagesViews->fetchAll($select))) {
            $tPagesViews->insert(array(
                "page_view_user_id" => $idUser,
                "page_view_page_id" => (int)$idPage,
            ));
        }
    }

    public static function getCountOfQueries($idPage) {
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP->select();
        $select->from($tP->info("name"),array(
            "page_id",
            "tot" => new Zend_Db_Expr("count(*)"),
        ))->where("page_prev_page_id = ?",(int)$idPage);
        return $tP->fetchAll($select)->current()->tot;

    }

    public static function getCountOfViewsOnQueries($id) {
        $id = (int)$id;
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP->select();
        $select
            ->from($tP->info("name"),array("page_id"))
            ->where("page_prev_page_id = ?",$id)
        ;
        $ar = array();
        foreach ($tP->fetchAll($select) as $item) {
            $ar[] = $item->page_id;
        }
        if (count($ar)) {
            $tV = new Books_Model_DbTable_Pageviews();
            $select = $tV->select();
            $select
                ->from($tV->info("name"),array(
                    "tot" => new Zend_Db_Expr("count(*)")
                ))
                ->where("page_view_page_id IN (".join(", ",$ar).")");
            return $tV->fetchAll($select)->current()->tot;
        }
        //
        return 0;
    }

    public static function getCountOfUserPages($idUser) {
        $tP = new Books_Model_DbTable_Pages();
        $s = $tP->select();
        $s
            ->from($tP->info("name"),array(
                "page_id",
                "tot" => new Zend_Db_Expr("count(*)"),
            ))
            ->where("page_user_id = ?",(int)$idUser)
            ;
        $fetch = $tP->fetchAll($s);
        if (count($fetch)) {
            $tot = $fetch->current()->tot;
        } else $tot = 0;
        return $tot;
    }

}