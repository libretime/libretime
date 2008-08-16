<?php
if (strpos($_SERVER['PHP_SELF'], '~') !== false) {
    list(, $user, ) = explode('/', $_SERVER['PHP_SELF']);
    $base = "/$user/campcaster";
} else {
    $base = "/campcaster";   
}

header("LOCATION: $base/htmlUI/var/html/ui_browser.php");
?>