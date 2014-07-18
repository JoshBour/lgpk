<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 2:16 μμ
 */

namespace Application\Model;


class DateUtils {
    public static $translatedDays = array("Δευ","Τρι","Τετ","Πεμ","Παρ","Σαβ","Κυρ");

    public static function getTranslatedDate(\DateTime $date){
        $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if($locale == "el_GR" || $locale == "el"){
            $day = self::$translatedDays[$date->format("w")];
        }else{
            $day = $date->format("D");
        }
        return $day . " " . $date->format("j/m");
    }
} 