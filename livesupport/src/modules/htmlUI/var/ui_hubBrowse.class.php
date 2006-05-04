<?php
class uiHubBrowse extends uiBrowse
{
    function uiHubBrowse(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->prefix     = 'HUBBROWSE';
        $this->col        =& $_SESSION[UI_HUBBROWSE_SESSNAME]['col'];
        $this->criteria   =& $_SESSION[UI_HUBBROWSE_SESSNAME]['criteria'];
        #$this->results    =& $_SESSION[UI_HUBBROWSE_SESSNAME]['results'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit']))     $this->criteria['limit']    = UI_BROWSE_DEFAULT_LIMIT;
        if (empty($this->criteria['filetype']))  $this->criteria['filetype'] = UI_FILETYPE_ANY;

        if (!is_array($this->col)) {
            ## init Categorys
            $this->setDefaults();
        }
    }

    function getResult()
    {   
        $this->getSearchResults($this->searchDB());
        //return $this->searchDB();
        return $this->results;
    }

    function searchDB()
    {
        $trtokid = $this->Base->gb->globalSearch($this->criteria);
        return $trtokid;    
    }
    
    function getSearchResults($trtokid) {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        $results = $this->Base->gb->getSearchResults($trtokid);
        if (!is_array($results) || !count($results)) {
            return false;    
        }
        $this->results['cnt'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            // TODO: maybe this _getMetaInfo is not correct for the remote results
            $this->results['items'][] = $this->Base->_getMetaInfo($this->Base->gb->_idFromGunid($rec));
        }
        $this->pagination($results);
//        echo '<XMP>this->results:'; print_r($this->results); echo "</XMP>\n";
//        echo '<XMP>results:'; print_r($results); echo "</XMP>\n"; 
        return is_array($results);
    }
}
?>