<?php

$v = "ciao ,m@ac?a\n\rco ;caz
    zobuoi.de[at]ip!ae(at)si .tuo
http://sito.com
porcocane@gmail.com

ciao belli...ccasc
che cazzo ?

i";
$p = array(
    "#([\.!\?])([a-z0-9]){1}#ei",
    "# ([\.!\?])#e",
    "#,(.)#",
    "#;(.)#",
    //
    "# \.#",
    "# ,#",
    "# ;#",
    "#\[at\]|\(at\)|@|http://|www#",
    );
$r = array(
    "'\\1 '.strtoupper('\\2');",
    "'\\1 ';",
    ", \\1",
    "; \\1",
    //
    ". ",
    ", ",
    "; ",
    "-",
    );

echo preg_replace($p, $r, $v);