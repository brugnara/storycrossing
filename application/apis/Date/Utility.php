<?php

class Engine_Api_Date_Utility {

    public static function init() {
        date_default_timezone_set("Europe/Dublin");
    }

    public static function advancedDateFormat($data,$toLower = false) {
        self::init();
        $ts = Zend_Registry::get('Zend_Translate');
        $dateStrings = array(
            "0:19" => $ts->translate("Seconds ago"),
            "20:59" => $ts->translate("Less than a minute ago"),
            "60:599" => $ts->translate("A few minutes ago"),
            "600:1199" => $ts->translate("Ten minutes ago"),
            "1200:1799" => $ts->translate("Twenty minutes ago"),
            "1800:3599" => $ts->translate("A half hour ago"),
            "3600:7199" => $ts->translate("A hour ago"),
            "7200:35999" => $ts->translate("Today"),
            "36000:86399" => $ts->translate("Yesterday"),
            "86400:604799" => "%l", //day (Lunedì/Martedì..)
            "604800:1209599" => $ts->translate("One week ago"),
            "1209600:2419199" => $ts->translate("Two weeks ago"),
            "2419200:3628799" => $ts->translate("Three weeks ago"),
            "3628800:4838399" => $ts->translate("A month ago"),
            "4838400:58060799" => "%d/m",
            "58060800:-1" => "%d/m/Y",
        );
//        $tsData = date_timestamp_get(date_create($data));
//        $tsNow = date_timestamp_get(date_create());
        $tsData = strtotime($data);
        $tsNow = strtotime("now");
        $diff = $tsNow - $tsData;
        if ($diff < 0) {
            $diff+= 3600;
        }
        foreach ($dateStrings as $dk => $ds) {
            list($min,$max) = explode(":",$dk);
            if ($max == -1 || ($min <= $diff && $diff <= $max)) {
                $tmp = $ds;
                if ($tmp{0} == "%") {
                    $tmp = substr($tmp, 1);
                    $tmp = $ts->translate(date($tmp,$tsData));
                }
                $dataStr = $tmp;
                break;
            }
        }
        return $toLower ? strtolower($dataStr) : $dataStr;
    }

}