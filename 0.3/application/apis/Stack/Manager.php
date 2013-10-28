<?php

class Engine_Api_Stack_Manager {

    private static $_stack = array();
    private static $_sessionReaded = false;
    private static $_SESSION_NAME = "stackStories";

    /**
     * clean svuota anche la sessione.
     */
    public static function clean() {
        self::$_stack = array();
        self::_elab();
    }

    public static function add(array $page) {
        self::_init();
        $pos = self::_isPresent($page);
        if ($pos !== false) {
            //significa che devo svuotare lo stack di tutte le pagine DOPO quella perchè sono tornato indietro.
            self::_switchStack($pos);
        } else {
            //se la story è diversa, svuoto lo stack
            $count = count(self::$_stack);
            if ($count) {
                $detailsLastPage = Engine_Api_Pages::getPageInfo(self::$_stack[$count-1]["params"]["idpage"]);
                $detailsCurrentPage = Engine_Api_Pages::getPageInfo($page["params"]["idpage"]);
                if($detailsCurrentPage->page_story_id != $detailsLastPage->page_story_id) {
                    self::clean();
                }
            }
            //aggiungo allo stack
            self::$_stack[] = $page;
        }
        self::_elab();
    }

    public static function del(array $page) {
        self::_init();
        $pos = self::_isPresent($page);
        if ($pos !== false) {
            $tmpStack = array();
            unset (self::$_stack[$pos]);
            foreach (self::$_stack as $stack) {
                $tmpStack[] = $stack;
            }
            self::$_stack = $tmpStack;
        }
        self::_elab();
    }

    public static function get() {
        self::_init();
        return self::$_stack;
    }

    // privates.

    private static function _init() {
        //leggo la sessione
        if (!self::$_sessionReaded) {
            self::$_stack = Engine_Api_Session::getVar(self::$_SESSION_NAME,array());
            self::$_sessionReaded = true;
        }
    }

    /**
     * USARE !== false
     *
     * @param type $page
     * @return type
     */
    private static function _isPresent($page) {
//        foreach (self::$_stack as $k => $stack) {
//            echo $stack["id"] ."-". $page["id"] ."&&". $stack["idpage"] ."-". $page["idpage"]."<br/>";
//            if ($stack["id"] == $page["id"] &&
//                $stack["idpage"] == $page["idpage"])
//                return $k;
//        }
//        return false;
        return array_search($page, self::$_stack);
    }

    private static function _switchStack($pos) {
        //dalla posizione $pos in poi elimino tutto
        self::$_stack = array_slice(self::$_stack, 0, $pos + 1);
    }

    /**
     * Mando in sessione lo stack!
     */
    private static function _elab() {
        Engine_Api_Session::setVar(self::$_SESSION_NAME,self::$_stack);
    }

}