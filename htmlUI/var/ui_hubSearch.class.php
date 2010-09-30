<?php

/**
 * @package Campcaster
 * @subpackage htmlUI
 */
class uiHubSearch extends uiSearch {

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->prefix = 'HUBSEARCH';
        $this->results =& $_SESSION[UI_HUBSEARCH_SESSNAME]['results'];
        $this->criteria =& $_SESSION[UI_HUBSEARCH_SESSNAME]['criteria'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit'])) {
        	$this->criteria['limit'] = UI_BROWSE_DEFAULT_LIMIT;
        }
    } // constructor


    function getResult()
    {
        return $this->results;
    } // fn getResult


    /**
     * This gets called when the user first fills in the search form.
     *
     */
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

        $results = $this->Base->gb->globalSearch($this->criteria);
        $this->results["cnt"] = $results["cnt"];
        $this->results["items"] = $results["results"];
        $this->pagination();
        $this->Base->redirUrl = UI_BROWSER."?act=HUBSEARCH";
    } // fn newSearch


    /**
     * This gets called when the user is paginating.
     *
     */
    function searchDB()
    {
        if (count($this->criteria) === 0) {
            return FALSE;
        }
        $this->results = array('page' => ($this->criteria['offset'] / $this->criteria['limit']));
        $results = $this->Base->gb->globalSearch($this->criteria);
        $this->results["cnt"] = $results["cnt"];
        $this->results["items"] = $results["results"];
        $this->pagination();

        return TRUE;
    } // fn searchDB


//    function getSearchResults($trtokid, $andClose=TRUE)
//    {
//        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
//        $results = $this->Base->gb->getSearchResults($trtokid, $andClose);
//        if ( PEAR::isError($results) && ($results->getCode() != TRERR_NOTFIN) ) {
//             echo "ERROR: {$results->getMessage()} {$results->getUserInfo()}\n";
//            return $results;
//        }
//        if (!is_array($results) || !count($results)) {
//            return false;
//        }
//        $this->results['cnt'] = $results['cnt'];
///*
//        foreach ($results['results'] as $rec) {
//            // TODO: maybe this getMetaInfo is not correct for the remote results
//            // yes, right :)
//            // $this->results['items'][] = $this->Base->getMetaInfo(BasicStor::IdFromGunid($rec));
//            $this->results['items'][] = $rec;
//        }
//*/
//        $this->results['items'] = $results['results'];
//        $this->pagination($results);
//        return is_array($results);
//    } // fn getSearchResults

} // class uiHubSearch
?>