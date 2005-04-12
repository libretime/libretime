<?php
/*
this is used to extract relations between label and fieldname from metadataform.
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
                echo "'".$in['element']."' => '".$in['label']."',<br>";
        }
    }

}

#print_r($mask);
flat($mask);
?>
