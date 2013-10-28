<?php

class Engine_Api_Stories {

    public static function getStoryInfo($id) {
        $tS = new Books_Model_DbTable_Stories();
        return $tS->fetchAll($tS->select()->where("story_id = ?",(int)$id))->current();
    }

    public static function getFirstPageOfAStory($idStory) {
        $t = new Books_Model_DbTable_Pages();
        return $t->getStartPage($idStory);
    }

    public static function getCountOfUserStories($idUser) {
        $tS = new Books_Model_DbTable_Stories();
        $s = $tS->select();
        $s
            ->from($tS->info("name"),array(
                "story_id",
                "tot" => new Zend_Db_Expr("count(*)"),
            ))
            ->where("story_user_id = ?",(int)$idUser);
        if ($fetch = $tS->fetchAll($s)) {
            return $fetch->current()->tot;
        } else {
            return 0;
        }
    }

}