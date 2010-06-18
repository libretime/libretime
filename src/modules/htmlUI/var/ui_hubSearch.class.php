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
        if (PEAR::isError($trtokid)) {
            // don't know how to display error message in htmlUi- should be improved:
            echo "ERROR: {$trtokid->getMessage()} {$trtokid->getUserInfo()}".
                ($trtokid->getCode() ? " ({$trtokid->getCode()})" : "")."\n";
            echo "<br/>\n<a href=\"javascript:history.go(-1)\">Back</a>\n";
            exit;
            //$this->Base->_retMsg("ERROR_3: {$trtokid->getMessage()} {$trtokid->getUserInfo()}\n");
            //$this->Base->redirUrl = UI_BROWSER.'?popup[]=';
            return $trtokid;
        }

        $this->Base->redirUrl = UI_BROWSER.'?popup[]='.$this->prefix.'.getResults&trtokid='.$trtokid;
    } // fn newSearch


    function searchDB()
    {
        if (count($this->criteria) === 0) {
            return FALSE;
        }
        $this->results = array('page' => $this->criteria['offset'] / $this->criteria['limit']);

        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        if (PEAR::isError($results)) {
            return FALSE;
        }
        foreach ($results['results'] as $rec) {
            $tmpId = BasicStor::IdFromGunid($rec["gunid"]);
            $this->results['items'][] = $this->Base->getMetaInfo($tmpId);
        }
        $this->results['cnt'] = $results['cnt'];

        $this->pagination($results);

        return TRUE;
    } // fn searchDB


    function getSearchResults($trtokid, $andClose=TRUE)
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        $results = $this->Base->gb->getSearchResults($trtokid, $andClose);
        if ( PEAR::isError($results) && ($results->getCode() != TRERR_NOTFIN) ) {
             echo "ERROR: {$results->getMessage()} {$results->getUserInfo()}\n";
            return $results;
        }
        if (!is_array($results) || !count($results)) {
            return false;
        }
        $this->results['cnt'] = $results['cnt'];
/*
        foreach ($results['results'] as $rec) {
            // TODO: maybe this getMetaInfo is not correct for the remote results
            // yes, right :)
            // $this->results['items'][] = $this->Base->getMetaInfo(BasicStor::IdFromGunid($rec));
            $this->results['items'][] = $rec;
        }
*/
        $this->results['items'] = $results['results'];
        $this->pagination($results);
        return is_array($results);
    } // fn getSearchResults

} // class uiHubSearch
?>