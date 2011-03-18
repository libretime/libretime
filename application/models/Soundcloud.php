<?php

require_once 'Soundcloud.php';


/*

require_once 'Soundcloud.php';


$soundcloud = new Services_Soundcloud('2CLCxcSXYzx7QhhPVHN4A', 'pZ7beWmF06epXLHVUP1ufOg2oEnIt9XhE8l8xt0bBs');

$token = $soundcloud->accessTokenResourceOwner('naomiaro@gmail.com', 'airtime17');

$track_data = array(
    'track[sharing]' => 'private',
    'track[title]' => 'Test',
    'track[asset_data]' => '@/home/naomi/Music/testoutput.mp3'
);

try {
    $response = json_decode(
        $soundcloud->post('tracks', $track_data),
        true
    );
} 
catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
    show_error($e->getMessage());
}

*/
