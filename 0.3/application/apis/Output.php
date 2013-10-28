<?php

class Engine_Api_Output {

    private static $_patterns = array(
        "#^(.){1}#e",
        "#([\.!\?])([a-z]){1}#ei",//"#([\.!\?])(.){1}#e",
        "# ([\.!\?])#e",
        "#,(.)#",
        "# ,#",
        "#\[at\]|\(at\)|@|http://|www|\\\\|/#i",
        //
        "# \.#",
        "# ,#",
        "#  #",//elimino doppi spazi
        //gli altri vengono convertiti con niente.
        "#<.*>#msU",
        "#\.\.\\\\#",
        "#\.\\\\#",
        "#\.\./#",
        "#\./#",
        "#-\. #",
    );
    private static $_replacements = array(
        "ucfirst('\\1');",
        "'\\1 '.ucfirst('\\2');",
        "'\\1 ';",
        ", \\1",
        ", ",
        "-",
        //
        ". ",
        ", ",
        " ",
        //gli altri assumono "" di default
    );

    public static function clean($text) {
        return (preg_replace(self::$_patterns, self::$_replacements, $text));
    }

    public static function charsetFix($in) {
        $pattern = array("'é'", "'è'", "'ë'", "'ê'", "'É'", "'È'", "'Ë'", "'Ê'", "'á'", "'à'", "'ä'", "'â'", "'å'", "'Á'", "'À'", "'Ä'", "'Â'", "'Å'", "'ó'", "'ò'", "'ö'", "'ô'", "'Ó'", "'Ò'", "'Ö'", "'Ô'", "'í'", "'ì'", "'ï'", "'î'", "'Í'", "'Ì'", "'Ï'", "'Î'", "'ú'", "'ù'", "'ü'", "'û'", "'Ú'", "'Ù'", "'Ü'", "'Û'", "'ý'", "'ÿ'", "'Ý'", "'ø'", "'Ø'", "'œ'", "'Œ'", "'Æ'", "'ç'", "'Ç'");
        $replace = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 'I', 'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 'y', 'y', 'Y', 'o', 'O', 'a', 'A', 'A', 'c', 'C');
        return preg_replace($pattern, $replace, $in);
        //Then we remove unwanted characters by only allowing a-z, A-Z, comma, 'minus' and white space
//        return preg_replace("/[^a-zA-Z-,\s]/", "", $keywords);
    }

}