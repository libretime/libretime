<?php

define('UTYPE_HOST', 'H');
define('UTYPE_ADMIN', 'A');
define('UTYPE_GUEST', 'G');
define('UTYPE_PROGRAM_MANAGER', 'P');

class Application_Model_User {

    private $_userInstance;

    public function __construct($userId)
    {
        if (empty($userId)){
            $this->_userInstance = $this->createUser();
        }
        else {
            $this->_userInstance = CcSubjsQuery::create()->findPK($userId);

            if (is_null($this->_userInstance)){
                throw new Exception();
            }
        }
    }

    public function getId() {
        return $this->_userInstance->getDbId();
    }

    public function isHost($showId) {
    	return $this->isUserType(UTYPE_HOST, $showId);
    }
    
    public function isPM() {
        return $this->isUserType(UTYPE_PROGRAM_MANAGER);
    }

    public function isAdmin() {
        return $this->isUserType(UTYPE_ADMIN);
    }

    public function canSchedule($p_showId) {
       $type = $this->getType();
       $result = false;

       if ( $type === UTYPE_ADMIN ||
            $type === UTYPE_PROGRAM_MANAGER ||
            CcShowHostsQuery::create()->filterByDbShow($p_showId)->filterByDbHost($this->getId())->count() > 0 )
       {
           $result = true;
       }

       return $result;
    }

    public function isUserType($type, $showId=''){
    	if(is_array($type)){
    		$result = false;
    		foreach($type as $t){
	    		switch($t){
		    		case UTYPE_ADMIN:
		    			$result = $this->_userInstance->getDbType() === 'A';
		    			break;
		    		case UTYPE_HOST:
		    			$userId = $this->_userInstance->getDbId();
		        		$result = CcShowHostsQuery::create()->filterByDbShow($showId)->filterByDbHost($userId)->count() > 0;
		        		break;
		    		case UTYPE_PROGRAM_MANAGER:
		    			$result = $this->_userInstance->getDbType() === 'P';
		    			break;
		    	}
		    	if($result){
		    		return $result;
		    	}
    		}
    	}else{
	    	switch($type){
	    		case UTYPE_ADMIN:
	    			return $this->_userInstance->getDbType() === 'A';
	    		case UTYPE_HOST:
	    			$userId = $this->_userInstance->getDbId();
	        		return CcShowHostsQuery::create()->filterByDbShow($showId)->filterByDbHost($userId)->count() > 0;
	    		case UTYPE_PROGRAM_MANAGER:
	    			return $this->_userInstance->getDbType() === 'P';
	    	}
    	}
    }

    public function setLogin($login){
        $user = $this->_userInstance;
        $user->setDbLogin($login);
    }

    public function setPassword($password){
        $user = $this->_userInstance;
        $user->setDbPass(md5($password));
    }

    public function setFirstName($firstName){
        $user = $this->_userInstance;
        $user->setDbFirstName($firstName);
    }

    public function setLastName($lastName){
        $user = $this->_userInstance;
        $user->setDbLastName($lastName);
    }

    public function setType($type){
        $user = $this->_userInstance;
        $user->setDbType($type);
    }

    public function setEmail($email){
        $user = $this->_userInstance;
        $user->setDbEmail($email);
    }

    public function setSkype($skype){
        $user = $this->_userInstance;
        $user->setDbSkypeContact($skype);
    }

    public function setJabber($jabber){
        $user = $this->_userInstance;
        $user->setDbJabberContact($jabber);
    }

    public function getLogin(){
        $user = $this->_userInstance;
        return $user->getDbLogin();
    }

    public function getPassword(){
        $user = $this->_userInstance;
        return $user->getDbPass();
    }

    public function getFirstName(){
        $user = $this->_userInstance;
        return $user->getDbFirstName();
    }

    public function getLastName(){
        $user = $this->_userInstance;
        return $user->getDbLastName();
    }

    public function getType(){
        $user = $this->_userInstance;
        return $user->getDbType();
    }

    public function getEmail(){
        $user = $this->_userInstance;
        return $user->getDbEmail();
    }

    public function getSkype(){
        $user = $this->_userInstance;
        return $user->getDbSkypeContact();
    }

    public function getJabber(){
        $user = $this->_userInstance;
        return $user->getDbJabberContact();

    }

    public function save(){
        $this->_userInstance->save();
    }

    public function delete(){
        if (!$this->_userInstance->isDeleted())
            $this->_userInstance->delete();
    }

    private function createUser() {
        $user = new CcSubjs();
        return $user;
    }

    public static function getUsers($type, $search=NULL) {
        global $CC_DBC;

        $sql;

        $sql_gen = "SELECT login AS value, login AS label, id as index FROM cc_subjs ";
        $sql = $sql_gen;

        if(is_array($type)) {
            for($i=0; $i<count($type); $i++) {
                $type[$i] = "type = '{$type[$i]}'";
            }
            $sql_type = join(" OR ", $type);
        }
        else {
            $sql_type = "type = {$type}";
        }

        $sql = $sql_gen ." WHERE (". $sql_type.") ";

        if(!is_null($search)) {
            $like = "login ILIKE '%{$search}%'";

            $sql = $sql . " AND ".$like;
        }

        $sql = $sql ." ORDER BY login";

        return  $CC_DBC->GetAll($sql);
    }

    public static function getUserCount($type=NULL){
    	global $CC_DBC;

        $sql;

        $sql_gen = "SELECT count(*) AS cnt FROM cc_subjs ";

        if(!isset($type)){
        	$sql = $sql_gen;
        }
        else{
	        if(is_array($type)) {
	            for($i=0; $i<count($type); $i++) {
	                $type[$i] = "type = '{$type[$i]}'";
	            }
	            $sql_type = join(" OR ", $type);
	        }
	        else {
	            $sql_type = "type = {$type}";
	        }

	        $sql = $sql_gen ." WHERE (". $sql_type.") ";
        }

        return  $CC_DBC->GetOne($sql);
    }

    public static function getHosts($search=NULL) {
        return Application_Model_User::getUsers(array('H'), $search);
    }

    public static function getUsersDataTablesInfo($datatables) {
    	
    	$con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME);

        $displayColumns = array("id", "login", "first_name", "last_name", "type");
        $fromTable = "cc_subjs";

        // get current user
        $username = "";
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->login;
        }

        $res = Application_Model_Datatables::findEntries($con, $displayColumns, $fromTable, $datatables);
        
        // mark record which is for the current user
        foreach($res['aaData'] as &$record){
            if($record['login'] == $username){
                $record['delete'] = "self";
            } else {
                $record['delete'] = "";
            }
        }

        return $res;
    }

    public static function getUserData($id){
        global $CC_DBC;

        $sql = "SELECT login, first_name, last_name, type, id, email, skype_contact, jabber_contact"
        ." FROM cc_subjs"
        ." WHERE id = $id";

        return $CC_DBC->GetRow($sql);
    }

    public static function GetUserID($login){
        $user = CcSubjsQuery::create()->findOneByDbLogin($login);
        if (is_null($user)){
            return -1;
        } else {
            return $user->getDbId();
        }
    }

    public static function GetCurrentUser() {
        $userinfo = Zend_Auth::getInstance()->getStorage()->read();

        return new self($userinfo->id);
    }
}
