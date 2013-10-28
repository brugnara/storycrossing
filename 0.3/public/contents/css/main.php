<?php
header("Content-type: text/css");
$css = file_get_contents("_main.css");
$patterns = array(
    "#\+baseurl\+#",
    "#\+iconset\+#",
);
$replacements = array(
    @$_GET["baseurl"],
    @$_GET["iconset"],
);
echo @preg_replace($patterns, $replacements, $css);