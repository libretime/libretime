<?php

use Airtime\CcSubjsQuery;

class Application_Service_UserService
{
    private $currentUser;

    public function __construct()
    {
    	//called from a daemon process
        if (!class_exists("Zend_Auth", false) || !Zend_Auth::getInstance()->hasIdentity()) {
            $id = null;
        } 
        else {
            $auth = Zend_Auth::getInstance();
            $id = $auth->getIdentity()->id;
        }
        
        if (!is_null($id)) {	
        	try {
        		$this->currentUser = CcSubjsQuery::create()->findPK($id);
        	}
        	catch(Exception $e) {
        		//user must have been deleted sometime after logging in.
        		$this->currentUser = null;
        	}
        }
    }

    /**
     *
     * Returns a CcSubjs object
     */
    public function getCurrentUser()
    {
        if (is_null($this->currentUser)) {
            throw new Exception();
        }

        return $this->currentUser;
    }
}