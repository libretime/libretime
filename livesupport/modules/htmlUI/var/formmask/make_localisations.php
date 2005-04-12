<?php
/*
this is used to extract all "label"-fields from metadataform for adding to localizer
*/

include ('metadata.inc.php');

function flat($in)
{
    global $ret;

    foreach ($in as $key=>$val) {
        if (is_array($val)) {
            flat($val);
        } else {
            if ($key==='label')
                echo "##$val##\r";
        }
    }

}

#print_r($mask);
flat($mask);
?>
