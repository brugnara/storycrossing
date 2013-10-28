<?php

class Engine_Api_Donations {

    public static function getLeaderBoard() {
        $tD = new Donations_Model_DbTable_Donations();
        $s = $tD->select();
        $s
            ->from($tD->info("name"),array(
                '*',
                'donation_tot' => new Zend_Db_Expr('sum(donation_gross)'),
            ))
            ->group('donation_user_id')
            ->order("donation_tot DESC");
        return $tD->fetchAll($s);
    }

}