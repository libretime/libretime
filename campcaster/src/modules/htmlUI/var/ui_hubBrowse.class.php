<?php
/**
 * @author Sebastian Gobel <sebastian.goebel@web.de>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class uiHubBrowse extends uiBrowse
{

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->prefix = 'HUBBROWSE';
        $this->col =& $_SESSION[UI_HUBBROWSE_SESSNAME]['col'];
        $this->criteria =& $_SESSION[UI_HUBBROWSE_SESSNAME]['criteria'];
        //$this->results =& $_SESSION[UI_HUBBROWSE_SESSNAME]['results'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit'])) {
        	$this->criteria['limit'] = UI_BROWSE_DEFAULT_LIMIT;
        }
        if (empty($this->criteria['filetype'])) {
        	$this->criteria['filetype'] = UI_FILETYPE_ANY;
        }

        if (!is_array($this->col)) {
            // init Categorys
            // This is broken - it initializes the columns from the local
            // storage instead of the remote storage. -- Paul
            //$this->setDefaults();
        }
    } // constructor


    function getResult()
    {
        $this->getSearchResults($this->searchDB());
        //return $this->searchDB();
        return $this->results;
    } // fn getResult


    function searchDB()
    {
        $trtokid = $this->Base->gb->globalSearch($this->criteria);
        return $trtokid;
    } // fn searchDB


    /**
     * @todo this function is broken
     *
     * @param string $trtokid
     * @return boolean
     */
    function getSearchResults($trtokid) {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        $results = $this->Base->gb->getSearchResults($trtokid);
        if (!is_array($results) || !count($results)) {
            return false;
        }
        $this->results['cnt'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            // TODO: maybe this getMetaInfo is not correct for the remote results
            $this->results['items'][] = $this->Base->getMetaInfo(BasicStor::IdFromGunid($rec));
        }
        $this->pagination($results);
        return is_array($results);
    } // fn getSearchResults

} // fn uiHubBrowse
?>