<?php

/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiTransfers
{
    private $Base;
    private $allItems;
    private $rows;
    private $trShowInfo;
    private $reloadUrl;


    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->trShowInfo =& $_SESSION[UI_TRANSFER_SESSNAME]['trShowInfo'];
        $this->trShowInfo['limit'] = UI_BROWSE_DEFAULT_LIMIT;
        $this->trShowInfo['offset'] = 0;
        $this->trShowInfo['desc'] = FALSE;
        $this->trShowInfo['orderby'] = FALSE;
    }


    function reorder($by)
    {
        $this->trShowInfo['offset'] = NULL;

        if ($this->trShowInfo['orderby'] == $by && !$this->trShowInfo['desc']) {
            $this->trShowInfo['desc'] = TRUE;
        } else {
            $this->trShowInfo['desc'] = FALSE;
        }
        $this->trShowInfo['orderby'] = $by;
        $this->setReload();
        //echo '<XMP>this:'; print_r($this); echo "</XMP>\n";
    }


    function getTransfers()
    {
        $this->buildList();
        return $this->rows;
    }


    function buildList() {
        // set items
        $transfers = $this->Base->gb->getHubInitiatedTransfers();
        foreach ($transfers as $transfer) {
            $token = $transfer['trtok'];
            $data = $this->Base->gb->getTransportInfo($token);
            if (!PEAR::isError($data) && ($data['state'] != 'finished') ){
            	$this->allItems[] = array_merge($data,array('id' => $token));
            }
        }
        $this->rows['cnt'] = count($this->allItems);
        $this->pagination();
        $this->showItems();
        //echo '<XMP>this'; print_r($this); echo "</XMP>\n";
    }


    function pagination()
    {
        if (sizeof($this->allItems) == 0) {
            return FALSE;
        }
        $delta = 4;
        $currp = ($this->trShowInfo['offset'] / $this->trShowInfo['limit']) + 1;
        $this->rows['page'] = ($this->trShowInfo['offset'] / $this->trShowInfo['limit']);   # current page
        $maxp  = ceil($this->rows['cnt'] / $this->trShowInfo['limit']);           # maximum page

        $deltaLower = UI_BROWSERESULTS_DELTA;
        $deltaUpper = UI_BROWSERESULTS_DELTA;
        $start = $currp;

        if ($start+$delta-$maxp > 0) {
        	$deltaLower += $start+$delta-$maxp;  ## correct lower boarder if page is near end
        }

        for ($n = $start-$deltaLower; $n <= $start+$deltaUpper; $n++) {
            if ($n <= 0) {
            	$deltaUpper++;                        ## correct upper boarder if page is near zero
            } elseif ($n <= $maxp) {
            	$this->rows['pagination'][$n] = $n;
            }
        }

        $this->rows['pagination'][1] ? NULL : $this->rows['pagination'][1] = '|<<';
        $this->rows['pagination'][$maxp] ? NULL : $this->rows['pagination'][$maxp] = '>>|';
        $this->rows['next']  = $this->rows['cnt'] > $this->trShowInfo['offset'] + $this->trShowInfo['limit'] ? TRUE : FALSE;
        $this->rows['prev']  = $this->trShowInfo['offset'] > 0 ? TRUE : FALSE;
        ksort($this->rows['pagination']);
    } // fn pagination


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    function setOffset($page)
    {
        //echo '<XMP>page:'; print_r($page); echo "</XMP>\n";
        $o =& $this->trShowInfo['offset'];
        $l =& $this->trShowInfo['limit'];

        if ($page == 'next') {
            $o += $l;
        } elseif ($page == 'prev') {
            $o -= $l;
        } elseif (is_numeric($page)) {
            $o = $l * ($page-1);
        }
        $this->setReload();
    }


    function cmp($a, $b)
    {
        //echo '<XMP>cmp:'; echo($a[$this->trShowInfo['orderby']].' - '.$b[$this->trShowInfo['orderby']]); echo "</XMP>\n";
        if ($a[$this->trShowInfo['orderby']] == $b[$this->trShowInfo['orderby']]) {
        	return 0;
        }
        if ($a[$this->trShowInfo['orderby']] < $b[$this->trShowInfo['orderby']]) {
            return $this->trShowInfo['desc'] ? 1 : -1;
        } else {
            return $this->trShowInfo['desc'] ? -1 : 1;
        }
    }


    function showItems()
    {
        // array sort
        if (is_array($this->allItems) && $this->trShowInfo['orderby']!==FALSE) {
            usort($this->allItems,array($this,'cmp'));
        }

        // pagination
        for ($i=$this->trShowInfo['offset'];$i<$this->trShowInfo['offset']+$this->trShowInfo['limit'];$i++) {
            if (!is_null($this->allItems[$i])) {
                $this->rows['items'][]=$this->allItems[$i];
            }
        }
        //$this->rows['page'] = $this->trShowInfo['offset'] % $this->trShowInfo['limit'];
    }


    function upload2Hub($id)
    {
        $gunid = BasicStor::GunidFromId($id);
        $type = BasicStor::GetType($gunid);

        switch ($type) {
            case 'audioClip':
            case 'audioclip':
                $r = $this->Base->gb->upload2Hub($gunid);
            break;
            case 'playlist':
                $this->Base->gb->upload2Hub($gunid);
            break;
            default:
                // TODO: it is not implemented in gb, and this way maybe impossible
                //$this->Base->gb->uploadFile2Hub($gunid);
                return false;
        }
    }


    function downloadFromHub($sessid, $gunid /*,$type*/)
    {
        $this->Base->gb->downloadFromHub($sessid, $gunid);
/*
         switch ($type) {
            case 'audioClip':
                $this->Base->gb->downloadAudioClipFromHub($id);
            break;
            case 'playlist':
                $this->Base->gb->downloadPlaylistFromHub($id,false);
            break;
            default:
                // TODO: it is not implemented in gb, and this way maybe impossible
                //$this->Base->gb->downloadFileFromHub($gunid);
                return false;
        }
*/
    }


    function doTransportAction($trtokens,$action) {
        //echo '<XMP>ids:'; print_r($trtokens); echo "</XMP>\n";
        if (!is_array($trtokens)) {
            $trtokens = array ($trtokens);
        }
        foreach ($trtokens as  $trtoken) {
            $ret[$trtoken] = $this->Base->gb->doTransportAction($trtoken,$action);
        }
        return $ret;
    }
}
?>