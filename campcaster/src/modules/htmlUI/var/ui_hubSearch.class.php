<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiHubSearch extends uiSearch {

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->prefix = 'HUBSEARCH';
        #$this->results =& $_SESSION[UI_HUBSEARCH_SESSNAME]['results'];
        $this->criteria =& $_SESSION[UI_HUBSEARCH_SESSNAME]['criteria'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit'])) {
        	$this->criteria['limit']    = UI_BROWSE_DEFAULT_LIMIT;
        }
    } // constructor


    function getResult()
    {
        //$this->searchDB();
        if (isset($_REQUEST['trtokid'])) {
            $this->getSearchResults($_REQUEST['trtokid']);
            return $this->results;
        }
        return false;
    } // fn getResult


    function newSearch(&$formdata)
    {
        $this->results = NULL;
        $this->criteria['conditions'] = NULL;
        $this->criteria['offset'] = NULL;
        $this->criteria['form'] = NULL;
        $this->criteria['operator'] = $formdata['operator'];
        $this->criteria['filetype'] = $formdata['filetype'];
        $this->criteria['limit'] = $formdata['limit'];
        $this->criteria['counter'] = 0;

        // $criteria['form'] is used for retransfer to form
        $this->criteria['form']['operator'] = $formdata['operator'];
        $this->criteria['form']['filetype'] = $formdata['filetype'];
        $this->criteria['form']['limit'] = $formdata['limit'];

        foreach ($formdata as $key => $val) {
            if (is_array($val) && $val['active']) {
                $this->criteria['counter']++;
                $this->criteria['conditions'][$key] = array('cat' => uiBase::formElementDecode($val[0]),
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
    } // fn newSearch


    function searchDB()
    {
        if (count($this->criteria) === 0) {
            return FALSE;
        }
        $this->results = array('page' => $this->criteria['offset'] / $this->criteria['limit']);

        //print_r($this->criteria);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        if (PEAR::isError($results)) {
            #print_r($results);
            return FALSE;
        }
        foreach ($results['results'] as $rec) {
            $tmpId = $this->Base->gb->idFromGunid($rec["gunid"]);
            $this->results['items'][] = $this->Base->getMetaInfo($tmpId);
        }
        $this->results['cnt'] = $results['cnt'];

        //print_r($this->results);
        $this->pagination($results);

        return TRUE;
    } // fn searchDB


    function getSearchResults($trtokid, $andClose=TRUE)
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        #sleep(4);
        $results = $this->Base->gb->getSearchResults($trtokid, $andClose);
        // echo"<pre><b>RESULTS:</b><br>";print_r($results);echo "</pre>";
/*
        if (PEAR::isError($results)) {
             echo "ERROR: {$results->getMessage()} {$results->getUserInfo()}\n";
        }
*/
        if (!is_array($results) || !count($results)) {
            return false;
        }
        $this->results['cnt'] = $results['cnt'];
/*
        foreach ($results['results'] as $rec) {
            // TODO: maybe this getMetaInfo is not correct for the remote results
            // yes, right :)
            // $this->results['items'][] = $this->Base->getMetaInfo($this->Base->gb->idFromGunid($rec));
            $this->results['items'][] = $rec;
        }
*/
        $this->results['items'] = $results['results'];
        $this->pagination($results);
        return is_array($results);
    } // fn getSearchResults

} // class uiHubSearch
?>