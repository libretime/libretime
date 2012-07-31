<?php

class Application_Model_Webstream{

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function getName()
    {
        return "Default";
    }

    public static function getId()
    {
        return "id";
    }

    public static function getLastModified($p_type)
    {
        return "modified";
    }

    public static function getDefaultLength()
    {
        return "length";
    }

    public static function getDescription()
    {
        return "desc";
    }

    public function getMetadata()
    {
        $webstream = CcWebstreamQuery::create()->findPK($this->id);
        $subjs = CcSubjsQuery::create()->findPK($webstream->getDbCreatorId());

        $username = $subjs->getDbLogin();
        return array(
            "name" => $webstream->getDbName(),
            "length" => $webstream->getDbLength(),
            "description" => $webstream->getDbDescription(),
            "login"=> $username,
            "url" => $webstream->getDbUrl(),
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

    public static function save($request)
    {
        Logging::log($request->getParams());
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        Logging::log($userInfo);

        $length = trim($request->getParam("length"));
        preg_match("/^([0-9]{1,2})h ([0-5][0-9])m$/", $length, $matches);
        $hours = $matches[1];
        $minutes = $matches[2];
        $di = new DateInterval("PT{$hours}H{$minutes}M"); 
        $dblength = $di->format("%H:%I"); 

        #TODO: These should be validated by a Zend Form.
        $webstream = new CcWebstream();
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
