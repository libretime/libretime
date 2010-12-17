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

	public static function getUsers($type=NULL) {
		global $CC_DBC;

		$sql;

		$sql_gen = "SELECT id, login, type FROM cc_subjs ";
		$sql = $sql_gen;

	
		if(!is_null($type)){
			
			if(is_array($type)) {
				for($i=0; $i<count($type); $i++) {
					$type[$i] = "type = '{$type[$i]}'";
				}
				$sql_type = join(" OR ", $type);
			}
			else {
				$sql_type = "type = {$type}";
			}
			
			$sql = $sql_gen ." WHERE ". $sql_type;
		}

		$sql = $sql . " ORDER BY login";
	
		return  $CC_DBC->GetAll($sql);	
	}

	public static function getHosts() {
		return User::getUsers(array('H', 'A'));
	}

}
