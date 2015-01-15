<?php

function GenerateRandomString($p_len=20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $string = '';
    for ($i = 0; $i < $p_len; $i++)
    {
        $pos = mt_rand(0, strlen($p_chars)-1);
        $string .= $p_chars{$pos};
    }
    return $string;
}

$connection = pg_connect("host=localhost dbname=airtime user=airtime password=airtime");

for ($i=0; $i<1000000; $i++){
    $md5 = md5($i);
    $sql = "INSERT INTO cc_files (gunid, name, artist_name, track_title, album_title) VALUES ('$md5', '".md5($i."salt")."', '".md5($i."salt1")."', '".md5($i."salt2")."', '".md5($i."salt3")."')";
    pg_exec($connection, $sql);
}
