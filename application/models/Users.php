<?php

class User {

	public function getUsers($type=NULL) {
		global $CC_DBC;

		$sql;

		$sql_gen = "SELECT id, login, type FROM cc_subjs";
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
	
		return  $CC_DBC->GetAll($sql);	
	}

	public function getHosts() {
		return $this->getUsers(array('H', 'A'));
	}

}
