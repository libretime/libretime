<?php

class Application_Model_Webstream{

    private $id;

    public function __construct($webstream)
    {
            $this->webstream = $webstream; 
    }

    public function getOrm()
    {
        return $this->webstream;
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
            return $di->format("%Hh %Im");
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
        $subjs = CcSubjsQuery::create()->findPK($this->webstream->getDbCreatorId());

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

    public static function analyzeFormData($parameters)
    {
        $valid = array("length" => array(true, ''), 
            "url" => array(true, ''),
            "name" => array(true, ''));

        $di = null;
        $length = $parameters["length"];
        $result = preg_match("/^([0-9]{1,2})h ([0-5]?[0-9])m$/", $length, $matches);
        if ($result == 1 && count($matches) == 3) { 
            $hours = $matches[1];
            $minutes = $matches[2];
            $di = new DateInterval("PT{$hours}H{$minutes}M");

            $totalMinutes = $di->h * 60 + $di->i;

            if ($totalMinutes == 0) {
                $valid['length'][0] = false;
                $valid['length'][1] = 'Length needs to be greater than 0 minutes';
            }

        } else {
            $valid['length'][0] = false;
            $valid['length'][1] = 'Length should be of form "00h 00m"';
        }


        $url = $parameters["url"];
        //simple validator that checks to make sure that the url starts with 
        //http(s),
        //and that the domain is at least 1 letter long
        $result = preg_match("/^(http|https):\/\/.+/", $url, $matches);

        $mime = null;
        if ($result == 0) {
            $valid['url'][0] = false;
            $valid['url'][1] = 'URL should be of form "http://domain"';
        } else {

            try {
                $mime = Application_Model_Webstream::discoverStreamMime($url);
            } catch (Exception $e) {
                $valid['url'][0] = false;
                $valid['url'][1] = $e->getMessage();
            }
        }

        $name = $parameters["name"];
        if (strlen($name) == 0) {
            $valid['name'][0] = false;
            $valid['name'][1] = 'Webstream name cannot be empty';
        }

        $id = $parameters["id"];

        if (!is_null($id)) {
            // user has performed a create stream action instead of edit
            // stream action. Check if user has the rights to edit this stream.

            Logging::log("CREATE");
        } else {
            Logging::log("EDIT");
        }

        return array($valid, $mime, $di); 
    }

    public static function isValid($analysis)
    {
        foreach ($analysis as $k => $v) {
            if ($v[0] === false) {
                return false;
            }
        }

        return true;
    }

    private static function discoverStreamMime($url)
    {
        $headers = get_headers($url);
        $mime = null;
        foreach ($headers as $h) {
            if (preg_match("/^content-type:/i", $h)) {
                list(, $value) = explode(":", $h, 2);
                $mime = trim($value);
            }
            if (preg_match("/^content-length:/i", $h)) {
                //if content-length appears, this is not a web stream!!!!
                //Aborting the save process. 
                throw new Exception("Invalid webstream - This appears to be a file download.");
            }
        }

        if (is_null($mime)) {
            throw new Exception("No MIME type found for webstream.");
        } else {
            if (!preg_match("/(mpeg|ogg)/", $mime)) {
                throw new Exception("Unrecognized stream type: $mime");
            }
        }

        return $mime;
    }

    public static function save($parameters, $mime, $di)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $webstream = new CcWebstream();
        $webstream->setDbName($parameters["name"]);
        $webstream->setDbDescription($parameters["description"]);
        $webstream->setDbUrl($parameters["url"]);

        $dblength = $di->format("%H:%I"); 
        $webstream->setDbLength($dblength);
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));

        $ws = new Application_Model_Webstream($webstream);
       
        $webstream->setDbMime($mime);
        $webstream->save();
    }
}
