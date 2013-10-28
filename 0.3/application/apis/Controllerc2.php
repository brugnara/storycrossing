<?php

class Engine_Api_Controllerc2 extends Zend_Controller_Action
{

    public function init() {
        //se non arrivo da ssh, butto un errore.
        if (empty($_SERVER['HTTP_X_FORWARDED_HOST']) && $_SERVER['HTTP_HOST'] != 'localhost') {
//            Engine_Api_Headers::_404();
        }
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function _toC2Array($ret) {
        $max = 0;
        $max2 = 100000;
        foreach ($ret as $r) {
            if ($max < count($r)) {
                $max = count($r);
            }
            if (!is_array($r)) {
                continue;
            }
            foreach ($r as $rr) {
                if ($max2 < count($rr)) {
                    $max2 = count($rr);
                }
            }
        }
        return array(
            "c2array" => true,
            "size" => array(
                count($ret),
                $max,
                $max2
            ),
            "data" => $ret
        );
    }

    protected function _return($ret) {
        header("Content-type: application/json");
        header("Access-Control-Allow-Origin:*");
        // json va preparato per construct 2
        echo json_encode($this->_toC2Array($ret));
        die;
    }

}

