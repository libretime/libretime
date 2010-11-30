<?php
require_once('../conf.php');
require_once('../backend/Schedule.php');

$api_key = $_GET['api_key'];
if(!in_array($api_key, $CC_CONFIG["apiKey"]))
{
    header('HTTP/1.0 401 Unauthorized');
    print 'You are not allowed to access this resource.';
    exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

$schedule_group_id = $_GET["schedule_id"];
$media_id = $_GET["media_id"];
$f = StoredFile::RecallByGunid($media_id);

if (is_numeric($schedule_group_id)) {
    $sg = new ScheduleGroup($schedule_group_id);
    if ($sg->exists()) {
        $result = $sg->notifyMediaItemStartPlay($f->getId());
        if (!PEAR::isError($result)) {
            echo json_encode(array("status"=>1, "message"=>""));
            exit;
        } else {
            echo json_encode(array("status"=>0, "message"=>"DB Error:".$result->getMessage()));
            exit;
        }
    } else {
        echo json_encode(array("status"=>0, "message"=>"Schedule group does not exist: ".$schedule_group_id));
        exit;
    }
} else {
    echo json_encode(array("status"=>0, "message" => "Incorrect or non-numeric arguments given."));
}
?>