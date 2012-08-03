<?php

class Application_Model_Webstream{

    private $id;

    public function __construct($id=-1)
    {
        if ($id == -1) {
            $this->webstream = new CcWebstream();

            //We're not saving this object in the database, so -1 ID is OK.
            $this->webstream->setDbId(-1);
            $this->webstream->setDbName("Untitled Webstream");
            $this->webstream->setDbDescription("");
            $this->webstream->setDbUrl("http://");
            $this->webstream->setDbLength("00h 00m");
            $this->webstream->setDbName("Untitled Webstream");
        } else {
            $this->id = $id;
            $this->webstream = CcWebstreamQuery::create()->findPK($this->id);
        }
    }

    public function getName()
    {
        return $this->webstream->getDbName();
    }

    public function getId()
    {
        return $this->webstream->getDbId();
    }

    public function getLastModified($p_type)
    {
        return "modified";
    }

    public function getDefaultLength()
    {
        $dateString = $this->webstream->getDbLength();
        $arr = explode(":", $dateString);
        if (count($arr) == 3) {
            list($hours, $min, $sec) = $arr;
            $di = new DateInterval("PT{$hours}H{$min}M{$sec}S");
            return $di->format("%Hh %im");
        }
        return "";
    }

    public function getDescription()
    {
        return $this->webstream->getDbDescription();
    }

    public function getUrl()
    {
        return $this->webstream->getDbUrl();
    }

    public function getMetadata()
    {
        $webstream = CcWebstreamQuery::create()->findPK($this->id);
        $subjs = CcSubjsQuery::create()->findPK($webstream->getDbCreatorId());

        $username = $subjs->getDbLogin();
        return array(
            "name" => $this->webstream->getDbName(),
            "length" => $this->webstream->getDbLength(),
            "description" => $this->webstream->getDbDescription(),
            "login"=> $username,
            "url" => $this->webstream->getDbUrl(),
        );

    }

    public static function deleteStreams($p_ids, $p_userId)
    {
        $leftOver = self::streamsNotOwnedByUser($p_ids, $p_userId);
        if (count($leftOver) == 0) {
            CcWebstreamQuery::create()->findPKs($p_ids)->delete();
        } else {
            throw new Exception("Invalid user permissions");
        }
    }
    
    // This function returns that are not owen by $p_user_id among $p_ids
    private static function streamsNotOwnedByUser($p_ids, $p_userId)
    {
        $ownedByUser = CcWebstreamQuery::create()->filterByDbCreatorId($p_userId)->find()->getData();
        $ownedStreams = array();
        foreach ($ownedByUser as $pl) {
            if (in_array($pl->getDbId(), $p_ids)) {
                $ownedStreams[] = $pl->getDbId();
            }
        }
        
        $leftOvers = array_diff($p_ids, $ownedStreams);
        return $leftOvers;
        
    }
/*
Array
(
    [controller] => Webstream
    [action] => save
    [module] => default
    [format] => json
    [description] => desc
    [url] => http://
    [length] => 00h 20m
    [name] => Default
)
 */


    public static function analyzeFormData($request)
    {
        $valid = array("length" => array(true, ''), 
            "url" => array(true, ''),
            "name" => array(true, ''));

        $length = trim($request->getParam("length"));
        $result = preg_match("/^([0-9]{1,2})h ([0-5][0-9])m$/", $length, $matches);
        if (!$result == 1 || !count($matches) == 3) { 
            $valid['length'][0] = false;
            $valid['length'][1] = 'Length should be of form "00h 00m"';
        }


        $url = trim($request->getParam("url"));
        //simple validator that checks to make sure that the url starts with http(s),
        //and that the domain is at least 1 letter long followed by a period.
        $result = preg_match("/^(http|https):\/\/.+\./", $url, $matches);

        if ($result == 0) {
            $valid['url'][0] = false;
            $valid['url'][1] = 'URL should be of form "http://www.domain.com/mount"';
        }


        $name = trim($request->getParam("name"));
        if (strlen($name) == 0) {
            $valid['name'][0] = false;
            $valid['name'][1] = 'Webstream name cannot be empty';
        }

        $id = trim($request->getParam("id"));

        if (!is_null($id)) {
            // user has performed a create stream action instead of edit
            // stream action. Check if user has the rights to edit this stream.

            Logging::log("CREATE");
        } else {
            Logging::log("EDIT");
        }



        return $valid; 
    }

    public static function isValid($analysis)
    {
        foreach ($analysis as $k => $v) {
            if ($v[0] == false) {
                return false;
            }
        }

        return true;
    }

    public static function save($request)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $length = trim($request->getParam("length"));
        $result = preg_match("/^([0-9]{1,2})h ([0-5][0-9])m$/", $length, $matches);

        if ($result == 1 && count($matches) == 3) { 
            $hours = $matches[1];
            $minutes = $matches[2];
            $di = new DateInterval("PT{$hours}H{$minutes}M"); 
            $dblength = $di->format("%H:%I"); 
        } else {
            //This should never happen because we should have already validated
            //in the controller
            throw new Exception("Invalid date format: $length");
        }

        $id = $request->getParam("id");

        if (is_null($id)) {
            $webstream = new CcWebstream();
        } else {
            $webstream = CcWebstreamQuery::create()->findPK($id);
        }
        
        $webstream->setDbName($request->getParam("name"));
        $webstream->setDbDescription($request->getParam("description"));
        $webstream->setDbUrl($request->getParam("url"));

        $webstream->setDbLength($dblength);
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->save();
    }
}
