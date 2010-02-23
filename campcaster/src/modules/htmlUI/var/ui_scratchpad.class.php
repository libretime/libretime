<?php
/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 * @copyright 2006 MDLF, Inc.
 * @link http://www.campware.org
 */
class uiScratchPad
{
	/**
	 * @var uiBase
	 */
	private $Base;

	/**
	 * The contents of the scratchpad.
	 *
	 * @var array
	 */
	private $items;

	/**
	 * @var array
	 */
	private $order;

	/**
	 * @var string
	 */
	private $reloadUrl;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->items =& $_SESSION[UI_SCRATCHPAD_SESSNAME]['content'];
        $this->order =& $_SESSION[UI_SCRATCHPAD_SESSNAME]['order'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }


    public function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    /**
     * Get the scratchpad, automatically load from the DB if necessary.
     *
     * @return array
     */
    public function &get()
    {
        if (!is_array($this->items)) {
            $this->_load();
        }
        return $this->items;
    }


    /**
     * Load the scratchpad from the database and save it in the session.
     * @return void
     */
    private function _load()
    {
        $this->items = array();
        // The scratchpad is kept as a user preference.
        // The value of the preference is a list of of media file IDs
        // separate by space characters.
        $spData = $this->Base->gb->loadPref($this->Base->sessid, UI_SCRATCHPAD_KEY);
        if (!PEAR::isError($spData)) {
            // The ScratchPad was found in the DB,
            // get the scratchpad list
            $arr = explode(' ', $spData);
            $maxLength = $this->Base->STATIONPREFS[UI_SCRATCHPAD_MAXLENGTH_KEY];
			$arr = array_slice($arr, 0, $maxLength);
            foreach ($arr as $gunid) {
                if (preg_match('/[0-9]{1,20}/', $gunid)) {
                	$id = BasicStor::IdFromGunid($this->Base->toHex($gunid));
                    if ($id != FALSE) {
                        if ($i = $this->Base->getMetaInfo($id)) {
                            $this->items[] = $i;
                        }
                    }
                }
            }
        }
    } // fn _load


    /**
     * Save the scratchpad to the database.
     * @return void
     */
    public function save()
    {
        foreach ($this->items as $val) {
            //$str .= $val['gunid'].':'.$val['added'].' ';       // new format
            $str .= $this->Base->toInt8($val['gunid']).' ';      // Akos old format
        }
        $this->Base->gb->savePref($this->Base->sessid, UI_SCRATCHPAD_KEY, $str);
    } // fn save


    /**
     * Add an item to the scratchpad
     *
     * @param string|array $ids
     * 		One or more media IDs.
     * @return boolean
     */
    public function addItem($ids)
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

        $scratchpad = $this->get();
        foreach ($ids as $id) {
            $item = $this->Base->getMetaInfo($id);

            foreach ($scratchpad as $key => $val) {
                if ($val['id'] == $item['id']) {
                    unset($scratchpad[$key]);
                    if (UI_VERBOSE) {
                    	$this->Base->_retMsg('Entry $1 is already on the scratchpad. It has been moved to the top of the list.', $item['title'], $val['added']);
                    }
                } else {
                    #$this->Base->incAccessCounter($id);
                }
            }
            $scratchpad = array_merge(array($item), is_array($scratchpad) ? $scratchpad : NULL);
        }

        $maxScratchpadLength = $this->Base->STATIONPREFS[UI_SCRATCHPAD_MAXLENGTH_KEY];
        for ($n = 0; $n < $maxScratchpadLength; $n++) {
        	if (!isset($scratchpad[$n])) {
        		break;
        	}
            if (is_array($scratchpad[$n])) {
            	$this->items[$n] = $scratchpad[$n];
            }
        }
        ksort($this->items);
        return true;
    } // fn addItem


    /**
     * Remove one or more items from the scratchpad.
     *
     * @param string|array $ids
     * @return boolean
     */
    public function removeItems($ids)
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


    /**
     * Enter description here...
     *
     * @param unknown_type $by
     * @return void
     */
    public function reorder($by)
    {
        if (count($this->items) == 0) {
            return;
        }

        foreach ($this->items as $key=>$val) {
            $s[$key] = $val[$by];
        }
        $curr = $this->order[$by];
        $this->order = array();
        if (is_null($curr) || $curr=='DESC') {
        	$this->order[$by] = 'ASC';
        } else {
        	$this->order[$by] = 'DESC';
        }
        switch ($this->order[$by]) {
            case "ASC":
            	asort($s);
            	break;
            case "DESC":
            	arsort($s);
            	break;
        }
        foreach ($s as $key=>$val) {
            $res[] = $this->items[$key];
        }
        $this->items = $res;
    }


    /**
     * Reload the metadata for the items in the scratchpad.
     *
     * @return void
     */
    public function reloadMetadata()
    {
        foreach ($this->items as $key => $val) {
            $this->items[$key] = $this->Base->getMetaInfo($val['id']);
        }
    }
} // class uiScratchPad
?>
