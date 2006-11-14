<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiScratchPad
{
	private $Base;
	private $items;
	private $order;
	private $reloadUrl;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->items =& $_SESSION[UI_SCRATCHPAD_SESSNAME]['content'];
        $this->order =& $_SESSION[UI_SCRATCHPAD_SESSNAME]['order'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    function &get()
    {
        if (!is_array($this->items)) {
            $this->_load();
        }
        //print_r($this->items);
        return $this->items;
    }


    function _load()
    {
        $this->items = array();
        $spData = $this->Base->gb->loadPref($this->Base->sessid, UI_SCRATCHPAD_KEY);
        if (!PEAR::isError($spData)) {
            // ScratchPad found in DB
            $arr = explode(' ', $spData);

            foreach ($arr as $gunid) {
                if (preg_match('/[0-9]{1,20}/', $gunid)) {
                    if ($this->Base->gb->_idFromGunid($this->Base->toHex($gunid)) != FALSE) {
                        if ($i = $this->Base->getMetaInfo($this->Base->gb->_idFromGunid($this->Base->toHex($gunid)))) {
                            $this->items[] = $i;
                        }
                    }
                }
            }
        }
    } // fn _load


    function save()
    {
        foreach ($this->items as $val) {
            //$str .= $val['gunid'].':'.$val['added'].' ';         ## new format ###
            $str .= $this->Base->toInt8($val['gunid']).' ';      ## Akos� old format ###
        }
        $this->Base->gb->savePref($this->Base->sessid, UI_SCRATCHPAD_KEY, $str);
    } // fn save


    function addItem($ids)
    {
        if (!$this->Base->STATIONPREFS[UI_SCRATCHPAD_MAXLENGTH_KEY]) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('The scratchpad length is not set in system preferences, so it cannot be used.');
            }
            return false;
        }
        if (!$ids) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('No item(s) selected.');
            }
            return FALSE;
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $sp   = $this->get();
        foreach ($ids as $id) {
            $item = $this->Base->getMetaInfo($id);

            foreach ($sp as $key=>$val) {
                if ($val['id'] == $item['id']) {
                    unset($sp[$key]);
                    if (UI_VERBOSE) {
                    	$this->Base->_retMsg('Entry $1 is already on the scratchpad. It has been moved to the top of the list.', $item['title'], $val['added']);
                    }
                } else {
                    #$this->Base->incAccessCounter($id);
                }
            }
            $sp = array_merge(array($item), is_array($sp) ? $sp : NULL);
        }

        for ($n=0; $n < $this->Base->STATIONPREFS[UI_SCRATCHPAD_MAXLENGTH_KEY]; $n++) {
            if (is_array($sp[$n])) {
            	$this->items[$n] = $sp[$n];
            }
        }
        ksort($this->items);
    } // fn addItem


    function removeItems($ids)
    {
        if (!$ids) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('No item(s) selected.');
            }
            return FALSE;
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            $sp =& $this->get();
            foreach ($sp as $key=>$val) {
                if ($val['id'] === $id) {
                    unset ($sp[$key]);
                    #$this->Base->decAccessCounter($id);
                }
            }
        }

        return TRUE;
    } // fn removeItems


    function reOrder($by)
    {
        if (count($this->items) == 0) {
            return FALSE;
        }

        foreach ($this->items as $key=>$val) {
            $s[$key] = $val[$by];
        }
        $curr =  $this->order[$by];
        $this->order = array();
        if (is_null($curr) || $curr=='DESC') {
        	$this->order[$by] = 'ASC';
        } else {
        	$this->order[$by] = 'DESC';
        }
        switch ($this->order[$by]) {
            case "ASC":   asort($s); break;
            case "DESC": arsort($s); break;
        }
        foreach ($s as $key=>$val) {
            $res[] = $this->items[$key];
        }
        $this->items = $res;
    }


    function reLoadM()
    {
        foreach($this->items as $key=>$val) {
            $this->items[$key] = $this->Base->getMetaInfo($val['id']);
        }
    }
} // class uiScratchPad
?>