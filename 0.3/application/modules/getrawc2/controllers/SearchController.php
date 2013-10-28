<?php

class Getrawc2_SearchController extends Engine_Api_Controller
{

    public function getfiltersAction() {
        $this->_return(Array(
            "tags" => Engine_Api_Tags::getTags(false),
            "locals" => Engine_Api_Local::getLanguages(true),
        ));
    }

}

