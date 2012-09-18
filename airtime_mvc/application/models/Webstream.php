<?php

class Application_Model_Webstream implements Application_Model_LibraryEditable
{
    private $id;

    public function __construct($webstream)
    {
        //TODO: hacky...
        if (is_int($webstream)) {
            $this->webstream = CcWebstreamQuery::create()->findPK($webstream);
            if (is_null($this->webstream)) {
                throw new Exception();
            }
        } else {
            $this->webstream = $webstream;
        }
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

    public function getCreatorId()
    {
        return $this->Webstream->getCcSubjs()->getDbId();
    }

    public function getLastModified($p_type)
    {
        return $this->webstream->getDbMtime();
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

    public function getLength()
    {
        return $this->getDefaultLength();
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
            "name"        => $this->webstream->getDbName(),
            "length"      => $this->webstream->getDbLength(),
            "description" => $this->webstream->getDbDescription(),
            "login"       => $username,
            "url"         => $this->webstream->getDbUrl(),
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
        $result = preg_match("/^(?:([0-9]{1,2})h)?\s*(?:([0-9]{1,2})m)?$/", $length, $matches);

        $invalid_date_interval = false;
        if ($result == 1 && count($matches) == 2) {
            $hours = $matches[1];
            $minutes = 0;
        } elseif ($result == 1 && count($matches) == 3) {
            $hours = $matches[1];
            $minutes = $matches[2];
        } else {
            $invalid_date_interval = true;
        }

        if (!$invalid_date_interval) {

            //Due to the way our Regular Expression is set up, we could have $minutes or $hours
            //not set. Do simple test here
            if (!is_numeric($hours)) {
                $hours = 0;
            }
            if (!is_numeric($minutes)) {
                $minutes = 0;
            }

            //minutes cannot be over 59. Need to convert anything > 59 minutes into hours.
            $hours += intval($minutes/60);
            $minutes = $minutes%60;

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
        $mediaUrl = null;
        if ($result == 0) {
            $valid['url'][0] = false;
            $valid['url'][1] = 'URL should be of form "http://domain"';
        } elseif (strlen($url) > 512) {
            $valid['url'][0] = false;
            $valid['url'][1] = 'URL should be 512 characters or less';
        } else {

            try {
                list($mime, $content_length_found) = self::discoverStreamMime($url);
                if (is_null($mime)) {
                    throw new Exception("No MIME type found for webstream.");
                }
                $mediaUrl = self::getMediaUrl($url, $mime, $content_length_found);

                if (preg_match("/(x-mpegurl)|(xspf\+xml)|(pls\+xml)/", $mime)) {
                     list($mime, $content_length_found) = self::discoverStreamMime($mediaUrl);
                }
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

        return array($valid, $mime, $mediaUrl, $di);
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

    // TODO : Fix this interface
    public function setMetadata($key, $val)
    {
        //This function should not be defined in the interface.
        throw new Exception("Not implemented.");
    }

    public function setName($name)
    {
        $this->webstream->setDbName($name);
    }
    
    public function setLastPlayed($timestamp)
    {
        $this->webstream->setDbLPtime($timestamp);
        $this->webstream->save();
    }

    private static function getUrlData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // grab URL and pass it to the browser
        //TODO: What if invalid url?
        $content = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);
        
        return $content;
    }

    private static function getXspfUrl($url)
    {
        $content = self::getUrlData($url);

        $dom = new DOMDocument;
        //TODO: What if invalid xml?
        $dom->loadXML($content);
        $tracks = $dom->getElementsByTagName('track');

        foreach ($tracks as $track) {
            $locations = $track->getElementsByTagName('location');
            foreach ($locations as $loc) {
                return $loc->nodeValue;
            }
        }

        throw new Exception("Could not parse XSPF playlist");
    }
    
    private static function getPlsUrl($url)
    {
        $content = self::getUrlData($url);
        $ini = parse_ini_string($content, true);

        if ($ini !== false && isset($ini["playlist"]) && isset($ini["playlist"]["File1"])) {
            return $ini["playlist"]["File1"];
        }

        throw new Exception("Could not parse PLS playlist");
    }

    private static function getM3uUrl($url)
    {
        $content = self::getUrlData($url);

        //split into lines:
        $delim = "\n";
        if (strpos($content, "\r\n") !== false) {
            $delim = "\r\n";
        }
        $lines = explode("$delim", $content);
        #$lines = preg_split('/$\R?^/m', $content);

        if (count($lines) > 0) {
            return $lines[0];
        }

        throw new Exception("Could not parse M3U playlist");
    }

    private static function getMediaUrl($url, $mime, $content_length_found)
    {

        if (preg_match("/x-mpegurl/", $mime)) {
            $media_url = self::getM3uUrl($url);
        } elseif (preg_match("/xspf\+xml/", $mime)) {
            $media_url = self::getXspfUrl($url);
        } elseif (preg_match("/pls\+xml/", $mime)) {
            $media_url = self::getPlsUrl($url);
        } elseif (preg_match("/(mpeg|ogg)/", $mime)) {
            if ($content_length_found) {
                throw new Exception("Invalid webstream - This appears to be a file download.");
            }
            $media_url = $url;
        } else {
            throw new Exception("Unrecognized stream type: $mime");
        }

        return $media_url;

    }

    private static function discoverStreamMime($url)
    {
        //TODO: What if invalid URL?
        $headers = get_headers($url);
        $mime = null;
        $content_length_found = false;
        foreach ($headers as $h) {
            if (preg_match("/^content-type:/i", $h)) {
                list(, $value) = explode(":", $h, 2);
                $mime = trim($value);
            }
            if (preg_match("/^content-length:/i", $h)) {
                $content_length_found = true;
            }
        }

        return array($mime, $content_length_found);
    }

    public static function save($parameters, $mime, $mediaUrl, $di)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $id = $parameters['id'];
        if ($id != -1) {
            $webstream = CcWebstreamQuery::create()->findPK($id);
        } else {
            $webstream = new CcWebstream();
        }

        $webstream->setDbName($parameters["name"]);
        $webstream->setDbDescription($parameters["description"]);
        $webstream->setDbUrl($mediaUrl);

        $dblength = $di->format("%H:%I");
        $webstream->setDbLength($dblength);
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));

        $ws = new Application_Model_Webstream($webstream);

        $webstream->setDbMime($mime);
        $webstream->save();

        return $webstream->getDbId();
    }
}
