<?php

class Engine_Api_Search {

    public static function clean($value) {
        $patterns = array(
            '#_#',
            '#-#',
            '#%#',
        );
        $return = preg_replace($patterns, "", $value);
        return $return;
    }

    public static function user($values) {
        $tU = new Users_Model_DbTable_Users();
        $select = $tU->select();
        $what = '%'.$values["search"].'%';
        $params = array(
            "text" => "user_name",
            "id" => "user_id",
            "user_id",
        );
        $select->from($tU->info("name"),$params);
        if ($values["inTitle"]) {
            $select->where("user_name LIKE ?",$what);
        }
        if ($values["inText"]) {
            $select->where("user_mail LIKE ?",$what);
        }
        $select->order("user_id DESC")->limit(50);
        return $tU->fetchAll($select);
    }

    public static function story($values) {
        $tS = new Books_Model_DbTable_Stories();
        $select = $tS->select();
        $what = '%'.$values["search"].'%';
        $params = array(
            "text" => "story_title",
            "id" => "story_id",
            "story_id" => "story_id"
        );
        $select->from($tS->info("name"),$params);
        if ($values["inTitle"]) {
            $select->where("story_title LIKE ?",$what);
        }
        $select->order("story_id DESC")->limit(50);
        return $tS->fetchAll($select);
    }

    public static function tag($values) {
        $tS = new Books_Model_DbTable_Stories();
        $select = $tS->select();
        $tag = $values["search"];
        $params = array(
            "text" => "story_title",
            "id" => "story_id",
            "story_id" => "story_id",
        );
        $select->from($tS->info("name"),$params);
        //
        $tStoryTags = new Books_Model_DbTable_Storytags();
        $stories = $tStoryTags->getStoriesHavingTag($tag);
        $ids = array();
        foreach ($stories as $story) {
            $ids[] = $story->story_tag_story_id;
        }
        if (count($ids)) {
            $select->where("story_id IN (".join(",",$ids).")");
        } else {
            $select->where("1 = 0");
        }
        //
        $select->order("story_id DESC")->limit(50);
        return $tS->fetchAll($select);
    }

    public static function page($values) {
        $tP = new Books_Model_DbTable_Pages();
        $select = $tP->select();
        $what = '%'.$values["search"].'%';
        $params = array(
            "text" => "page_title",
            "id" => "page_id",
            "page_id" => "page_id"
        );
        $select->from($tP->info("name"),$params);
        if ($values["inTitle"]) {
            $select->where("page_title LIKE ?",$what);
        }
        if ($values["inText"]) {
            $select->where("page_text LIKE ?",$what);
        }
        $select->order("page_id DESC")->limit(50);
        return $tP->fetchAll($select);
    }

}