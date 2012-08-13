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

        $length = $parameters["length"];
        $result = preg_match("/^([0-9]{1,2})h ([0-5][0-9])m$/", $length, $matches);
        if (!$result == 1 || !count($matches) == 3) { 
            $valid['length'][0] = false;
            $valid['length'][1] = 'Length should be of form "00h 00m"';
        }


        $url = $parameters["url"];
        //simple validator that checks to make sure that the url starts with 
        //http(s),
        //and that the domain is at least 1 letter long
        $result = preg_match("/^(http|https):\/\/.+/", $url, $matches);

        if ($result == 0) {
            $valid['url'][0] = false;
            $valid['url'][1] = 'URL should be of form "http://www.domain.com/mount"';
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

        return $valid; 
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

    /*
     * This function is a callback used by curl to let us work
     * with the contents returned from an http request. We don't
     * actually want to work with the contents however (we just want
     * the response headers), so immediately return a -1 in this function
     * which tells curl not to download the response body at all.
     */
    private function writefn($ch, $chunk) 
    { 
        return -1;
    }

    private function discoverStreamMime()
    {
        Logging::log($this->webstream->getDbUrl());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->webstream->getDbUrl());
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'writefn'));
        $result = curl_exec($ch);
        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);       

        Logging::log($mime);
        return $mime;
    }

    public static function save($parameters)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $length = $parameters["length"];
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

        $webstream = new CcWebstream();
        $webstream->setDbName($parameters["name"]);
        $webstream->setDbDescription($parameters["description"]);
        $webstream->setDbUrl($parameters["url"]);

        $webstream->setDbLength($dblength);
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));

        $ws = new Application_Model_Webstream($webstream);
       
        $mime = $ws->discoverStreamMime();
        if ($mime !== false) {
            $webstream->setDbMime($mime);
        } else {
            throw new Exception("Couldn't get MIME type!");
        }
        $webstream->save();
    }
}
