<?php

class Engine_Api_Ws_Soap {

    /** Prova getPage
     *
     * @param String $idPage
     */
    public function getPage($userInfo,$idPage) {
        $tmp = array();
        $info = Engine_Api_Pages::getPageInfo($idPage);
        if (!empty($info)) {
            $tmp["pagetitle"] = $info->page_title;
            $tmp["pagetext"] = $info->page_text;
            $tmp["pageprevPageId"] = $info->page_prev_page_id;
            $tmp["pagedate"] = $info->page_date;
            $tmp["pagestoryId"] = $info->page_story_id;
            $tmp["pageUserId"] = $info->page_user_id;
            $tmp["pageBg"] = $info->page_bg;
            $tmp["pageCapitalFont"] = $info->page_capital_font;
            $tmp["error"] = "NO_ERRORS";
        } else {
            $tmp["error"] = "PAGE_NOT_FOUND";
        }
        $tmp["userInfo"] = $userInfo;
        return $tmp;
    }

    public function getPageQueries($userInfo,$idPage) {
        //
    }

}