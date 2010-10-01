<?php
if (strpos($_SERVER['PHP_SELF'], '~') !== false) {
    list(, $user, ) = explode('/', $_SERVER['PHP_SELF']);
    $base = "/$user/campcaster";
} else {
    $base = "/campcaster";   
}

header("LOCATION: $base/ui_browser.php");
?>