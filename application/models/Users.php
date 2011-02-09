<?php

class User {

	private $_userInstance;

	public function __construct($userId)
    {
        if (strlen($userId)==0){
            $this->userInstance = $this->createUser();
        } else {
            $this->userInstance = CcSubjsQuery::create()->findPK($userId);
        } 
    }

	public function getId() {
        return $this->_userInstance->getDbId();
	}

	public function isHost($showId) {
        $userId = $this->_userInstance->getDbId();
		return CcShowHostsQuery::create()->filterByDbShow($showId)->filterByDbHost($_userId)->count() > 0;
	}

	public function isAdmin() {
        return $userInstance->getDbType() === 'A';
	}
    
    public function setLogin($login){
 		$user = $this->userInstance;
		$user->setDbLogin($login);
		//$user->save();        
    }
     
    public function setPassword($password){
 		$user = $this->userInstance;
		$user->setDbPass(md5($password));
		//$user->save();        
    }
    
    public function setFirstName($firstName){
 		$user = $this->userInstance;
		$user->setDbFirstName($firstName);
		//$user->save();        
    }
    
    public function setLastName($lastName){
 		$user = $this->userInstance;
		$user->setDbLastName($lastName);
		//$user->save();        
    }
    
    public function setType($type){
 		$user = $this->userInstance;
		$user->setDbType($type);
		//$user->save();        
    }
    
    public function getLogin(){
 		$user = $this->userInstance;
		return $user->getDbLogin();       
    }    
    
    public function getPassword(){
 		$user = $this->userInstance;
		return $user->getDbPass();       
    }
    
    public function getFirstName(){
 		$user = $this->userInstance;
		return $user->getDbFirstName();          
    }
    
    public function getLastName(){
 		$user = $this->userInstance;
		return $user->getDbLastName();           
    }
    
    public function getType(){
 		$user = $this->userInstance;
		return $user->getDbType();          
    }
    
    public function save(){
        $this->userInstance->save();
    }
    
    public function delete(){
        if (!$this->userInstance->isDeleted())
            $this->userInstance->delete();
    }

	private function createUser() {
		$user = new CcSubjs();
		//$user->save();
        
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
