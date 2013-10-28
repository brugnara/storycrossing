<?php

class Engine_Api_Updates_Notify {

    public static function sendToFollowersOf($idWriter,$idNewPage) {
        //recupero gli id dei seguaci e relativa opzione per ricevere o meno la notifica via mail.
        $tF = new Users_Model_DbTable_Followers();
        $writerInfo = Engine_Api_Users::getAUserInfo($idWriter);
        $followers = $tF->getDetailedFollowersOf($idWriter);
        $tUpdates = new Users_Model_DbTable_Userupdates();
        $ts = Zend_Registry::get("Zend_Translate");
        $oldLocale = $ts->getLocale();
        //
        $mailBody = "";
        $prevLang = $destinatari = array();
        $page = Engine_Api_Pages::getPageInfo($idNewPage);
        $pageText = $page->page_text;
        if (strlen($pageText) > 100) {
            $pageText = substr($pageText, 0, 100) . "[...]";
        }
        //
        foreach ($followers as $follower) {
            $tUpdates->add($follower->user_id,"new_page",$idNewPage);
            //if ($follower->user_wants_mail)
            $notify = Engine_Api_Useroptions::get('email_notify', $follower->user_id);
            //se un utente non cambia l'impostazione nel suo profilo, NON c'è nella tabella config.
            if ($notify === null || $notify) {
                $ts->setLocale($follower->user_locale);
                if (!in_array($follower->user_locale, $prevLang)) {
                    //nomeautore, 100chars, idpagina
                    $mailBody.= "<br/>".sprintf($ts->translate('NOTIFY_SEND_TO_FOLLOWERS_OF_TEXT'),
                            $writerInfo->user_name,
                            $pageText,
                            $idNewPage)."<hr/>";
                    $prevLang[] = $follower->user_locale;
                }
                $destinatari[] = $follower->user_mail;
//                Engine_Api_Mailer::send(
//                    array(
//                        $follower->user_mail,
//                        $follower->user_name,
//                ),
            }
        }
        //$mailBody.= "<hr/><a href='mailto:unsubscribe@storycrossing.com'>";
        if (count($destinatari)) {
            Engine_Api_Mailer::sendBCC(
                array(
                    "no-reply@storycrossing.com",
                    "Notify System - StoryCrossing"
                ),
                $destinatari,
                $ts->translate("New page from a writer you follow!"),
                //sprintf($ts->translate('NOTIFY_SEND_TO_FOLLOWERS_OF_TEXT'),$writerInfo->user_name,$idNewPage)
                $mailBody
            );
        }
        $ts->setLocale($oldLocale);
//        echo "<pre>";
//        print_r($followers);
//        die;
    }

    public static function sendToAuthor($idWriter,$idNewPage, $idPrevPage) {
        //se sono io lo scrittore, non mi serve ricevere la notifica...
        if ($idWriter == Engine_Api_Users::getUserInfo()->user_id) {
            return;
        }
        $tUpdates = new Users_Model_DbTable_Userupdates();
        $tUpdates->add($idWriter,"new_page",$idNewPage);
        //info prev page
        if (!$idPrevPage)
            return;
        $prevPage = Engine_Api_Pages::getPageInfo($idPrevPage);
        //$page = Engine_Api_Pages::getPageInfo($idNewPage);
        $pageText = $prevPage->page_text;
        if (strlen($pageText) > 100) {
            $pageText = substr($pageText, 0, 100) . "[...]";
        }
        //mail
        $userInfo = Engine_Api_Users::getAUserInfo($prevPage->page_user_id);
        $notify = Engine_Api_Useroptions::get('email_notify', $userInfo->user_id);
        //se un utente non cambia l'impostazione nel suo profilo, NON c'è nella tabella config.
        if ($notify === null || $notify) {
            $ts = Zend_Registry::get("Zend_Translate");
            $oldLocale = $ts->getLocale();
            $ts->setLocale($userInfo->user_locale);
            //if ($follower->user_wants_mail)
            Engine_Api_Mailer::send(
                    array(
                        $userInfo->user_mail,
                        $userInfo->user_name,
                    ),
                    $ts->translate("Someone continued your story"),
                    //titolo, chihaproseguito, 100chars, idpage
                    sprintf($ts->translate('NOTIFY_SEND_TO_AUTHOR_OF_STORY'),
                            $prevPage->page_title,
                            Engine_Api_Users::getUserInfo()->user_name,
                            $pageText,
                            $idNewPage)
            );
            $ts->setLocale($oldLocale);
        }
    }

}