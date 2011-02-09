<?php

class User {

	private $_userInstance;

	public function __construct($userId)
    {
        if (strlen($userId)==0){
            $this->_userInstance = $this->createUser();
        } else {
            $this->_userInstance = CcSubjsQuery::create()->findPK($userId);
        } 
    }

	public function getId() {
        return $this->_userInstance->getDbId();
	}

	public function isHost($showId) {
        $userId = $this->_userInstance->getDbId();
		return CcShowHostsQuery::create()->filterByDbShow($showId)->filterByDbHost($userId)->count() > 0;
	}

	public function isAdmin() {
        return $this->_userInstance->getDbType() === 'A';
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

		$sql_gen = "SELECT id AS value, login AS label FROM cc_subjs ";
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
		
		$sql = $sql_gen ." WHERE (". $sql_type.")";
	
		if(!is_null($search)) {
			$like = "login ILIKE '%{$search}%'";

			$sql = $sql . " AND ".$like." ORDER BY login";
		}
	
		return  $CC_DBC->GetAll($sql);	
	}

	public static function getHosts($search=NULL) {
		return User::getUsers(array('H', 'A'), $search);
	}
    
	public static function getUsersDataTablesInfo($datatables_post) {

		$fromTable = "cc_subjs";
		return StoredFile::searchFiles($fromTable, $datatables_post);
	}
    
    public static function getUserData($id){
        global $CC_DBC;
        
        $sql = "SELECT login, first_name, last_name, type, id"
        ." FROM cc_subjs"
        ." WHERE id = $id";
        
        return $CC_DBC->GetRow($sql);
    }

}
