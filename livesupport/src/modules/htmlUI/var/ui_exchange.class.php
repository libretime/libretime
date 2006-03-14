<?php
class uiExchange
{
    function uiExchange(&$uiBase)
    {
        $this->Base         =& $uiBase;
    }
    
    function getBackupToken()
    {
        return "12345";
        
        $token = $this->Base->gb->loadPref($this->Base->sessid, UI_BACKUPTOKEN_KEY);
        
        if (PEAR::isError($token)) {
            return false;    
        }
        
        return $token;
        
    }
    
    function createBackupOpen()
    {
        $criteria = array('filetype' => UI_FILETYPE_ANY);
        $token = $this->Base->gb->createBackupOpen($this->Base->sessid, $criteria);
        
        if (PEAR::isError($token)) {
            $this->Base->_retMsg('Error initializing backup: $1', $token->getMessage());
            return false;    
        }
        
        $this->createBackupCheck();
        
        $this->Base->gb->savePref($this->Base->sessid, UI_BACKUPTOKEN_KEY, $token);
        
        return true;
    }       
    
    function createBackupCheck()
    {
        return array('status' => 'success', 'tmpfile' => '/tmp/xxx.backup');
        
        
        
        $token = $this->getBackupToken();
        
        if ($token === false) {
            return flase;    
        }       
        
        $res   = $this->Base->gb->createBackupCheck($token);
        
        if (PEAR::isError($res)) {
            $this->Base->_retMsg('Unable to check backup status: $1', $res->getMessage());
            return false;    
        }
        
        return $res;
    }
    
    function createBackupClose()
    {
        $token  = $token = $this->getBackupToken(); 
        
        if ($token === false) {
            $this->Base->_retMsg('Token not available');
            return false;    
        } 
        
        $status = $this->Base->gb->createBackupClose($token);   
        
        if (PEAR::isError($status)) {
            $this->Base->_retMsg('Error closing backup: $1', $status->getMessage());
            return false;    
        }
        
        if ($status === true) {
            $this->Base->gb->delPref($this->Base->sessid, UI_BACKUPTOKEN_KEY);        
        }
        
        return $status;
    }
}
?>