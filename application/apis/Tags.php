<?php

class Engine_Api_Tags {

    public static function addTags($string,$idPage,$idStory) {
        $tags = self::_prepareTags($string);
        if (!$tags) {
            return false;
        }
        self::_addTags($tags);
        self::_addTagsToPage($tags,$idPage);
        self::_addTagsToStory($tags,$idStory);
    }

    public static function getTags($startFromZero = true,$limit = 0,$asObject = false) {
        $tTags = new Application_Model_DbTable_Tags();
        $select = $tTags->select();
        if ($limit) {
            $select->limit($limit)->order("tag_counter DESC");
        } else {
            $select->order("tag_name ASC");
        }
        $obj = $tTags->fetchAll($select);
        if ($startFromZero) {
            $ret = array("0" => "");
        } else {
            $ret = array();
        }
        if ($asObject) {
            return $obj;
        }
        foreach ($obj as $o) {
            $ret[$o->tag_name] = ucfirst($o->tag_name);
        }
        return $ret;
    }

    public static function getStoryTags($idStory,$asArray = false) {
        $tST = new Books_Model_DbTable_Storytags();
        $select = $tST->select();
        $select->where("story_tag_story_id = ?",(int)$idStory);
        if ($asArray) {
            $ret = array();
            foreach ($tST->fetchAll($select) as $item) {
                $ret[] = $item->story_tag_tag;
            }
            return $ret;
        } else {
            return $tST->fetchAll($select);
        }
    }

    public static function isValid($tag) {
        if (empty($tag)) {
            return false;
        }
        $tTags = new Application_Model_DbTable_Tags();
        return count($tTags->fetchAll($tTags->select()->where("tag_name = ?",$tag)));
    }

    private static function _addTagsToPage($tags,$idPage) {
        $tPT = new Books_Model_DbTable_Pagetags();
        foreach ($tags as $tag) {
            $tPT->insert(array(
                "page_tag_tag" => $tag,
                "page_tag_page_id" => $idPage
            ));
        }
    }

    private static function _addTagsToStory($tags,$idStory) {
        //aggiorno i contatori di quante volte sono stati utilizzati i tag nelle pagine di una storia.
        $storyTags = self::getStoryTags($idStory,true);
        $tST = new Books_Model_DbTable_Storytags();
        foreach ($tags as $tag) {
            if (!in_array($tag, $storyTags)) {
                $tST->insert(array(
                    "story_tag_tag" => $tag,
                    "story_tag_story_id" => $idStory
                ));
            } else {
                $tST->update(array(
                    "story_tag_counter" => new Zend_Db_Expr("story_tag_counter + 1")
                ),array(
                    "story_tag_story_id = ?" => $idStory,
                    "story_tag_tag = ?" => $tag,
                ));
            }
        }
    }

    private static function _addTags($tags) {
        $tTags = new Application_Model_DbTable_Tags();
        foreach ($tags as $tag) {
            $select = $tTags->select()->where("tag_name = ?",$tag);
            $fetch = $tTags->fetchAll($select);
            if (!count($fetch)) {
                $tTags->insert(array(
                    "tag_name" => $tag,
                ));
            } else {
                $tTags->update(array(
                    "tag_counter" => new Zend_Db_Expr("tag_counter + 1"),
                ),array(
                    "tag_id = ?" => $fetch->current()->tag_id
                ));
            }
        }
    }

    private static function _prepareTags($tags) {
        $tags = preg_replace('#,| #','|',$tags);
        $tmp = explode("|",$tags);
//        print_r($tmp);
        if (!is_array($tmp)) {
            return array();
        }
        $ret = array();
        foreach ($tmp as $t) {
            $t = strtolower(trim(Engine_Api_Output::clean($t)));
            $t = substr($t,0,20);
            if (preg_match('#^[a-z]+$#i',$t)) {
                //non ammetto duplicati
                if (!in_array($t,$ret)) {
                    $ret[] = $t;
                }
            }
            //una storia ha massimo 10 tags.
            if (count($ret) == 10) {
                break;
            }
        }
        return $ret;

    }

}