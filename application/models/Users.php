<?php

class User {

	private $_userRole;
	private $_userId;

	public function __construct($userId, $userType='G')
    {
        $this->_userRole = $userType;
		$this->_userId = $userId;     
    }

	public function getType() {
		return $this->userRole;
	}

	public function getId() {
		return $this->_userId;
	}

	public function isHost($showId) {
		return CcShowHostsQuery::create()->filterByDbShow($showId)->filterByDbHost($this->_userId)->count() > 0;
	}

	public function isAdmin() {
		return $this->_userRole === 'A';
	}

	public static function addUser($data) {

		$user = new CcSubjs();
		$user->setDbLogin($data['login']);
		$user->setDbPass(md5($data['password']));
		$user->setDbFirstName($data['first_name']);
		$user->setDbLastName($data['last_name']);
		$user->setDbType($data['type']);
		$user->save();
		
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

}
