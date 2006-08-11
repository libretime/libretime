<?php
class uiHubSearch extends uiSearch
{
    function uiHubSearch(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->prefix     = 'HUBSEARCH';
        #$this->results    =& $_SESSION[UI_HUBSEARCH_SESSNAME]['results'];
        $this->criteria   =& $_SESSION[UI_HUBSEARCH_SESSNAME]['criteria'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        
        if (empty($this->criteria['limit']))     $this->criteria['limit']    = UI_BROWSE_DEFAULT_LIMIT;
    }

    function getResult()
    {
        //$this->searchDB();
        if (isset($_REQUEST['trtokid'])) {
            $this->getSearchResults($_REQUEST['trtokid']);
            return $this->results;
        }
        return false;
    }
    
    function newSearch(&$formdata)
    {
        $this->results                  = NULL;
        $this->criteria['conditions']   = NULL;
        $this->criteria['offset']       = NULL;
        $this->criteria['form']         = NULL;
        $this->criteria['operator']     = $formdata['operator'];
        $this->criteria['filetype']     = $formdata['filetype'];
        $this->criteria['limit']        = $formdata['limit'];
        $this->criteria['counter']      = 0;


        $this->criteria['form']['operator'] = $formdata['operator'];    ## $criteria['form'] is used for retransfer to form ##
        $this->criteria['form']['filetype'] = $formdata['filetype'];
        $this->criteria['form']['limit']    = $formdata['limit'];

        foreach ($formdata as $key=>$val) {
            if (is_array($val) && $val['active']) {
                $this->criteria['counter']++;
                $this->criteria['conditions'][$key] = array('cat' => $this->Base->_formElementDecode($val[0]),
                                                            'op'  => $val[1],
                                                            'val' => stripslashes($val[2])
                                                      );
                $this->criteria['form'][$key]       = array(0     => $val[0],
                                                            1     => $val[1],
                                                            2     => stripslashes($val[2])
                                                      );
            }
        }
        
        //echo '<XMP>this->criteria:'; print_r($this->criteria); echo "</XMP>\n"; 
        $trtokid = $this->Base->gb->globalSearch($this->criteria);
        
        $this->Base->redirUrl = UI_BROWSER.'?popup[]='.$this->prefix.'.getResults&trtokid='.$trtokid;
    }


    function searchDB()
    {
        if (count($this->criteria) === 0)
            return FALSE;

        $this->results = array('page' => $this->criteria['offset'] / $this->criteria['limit']);

        #print_r($this->criteria);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        if (PEAR::isError($results)) {
            #print_r($results);
            return FALSE;
        }
        foreach ($results['results'] as $rec) {
            $tmpId = $this->Base->gb->_idFromGunid($rec["gunid"]);
            $this->results['items'][] = $this->Base->_getMetaInfo($tmpId);
        }
        $this->results['cnt'] = $results['cnt'];

        #print_r($this->results);
        $this->pagination($results);
        
        return TRUE;
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
        //echo '<XMP>this->results:'; print_r($this->results); echo "</XMP>\n";
        //echo '<XMP>results:'; print_r($results); echo "</XMP>\n"; 
        return is_array($results);
    }

}
?>
