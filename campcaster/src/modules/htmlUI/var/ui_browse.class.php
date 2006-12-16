<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiBrowse
{
    public $Base; // uiBase object
    private $prefix;
    private $col;
    private $criteria;
    private $reloadUrl;


    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->prefix = 'BROWSE';
        $this->col =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['col'];
        $this->criteria =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['criteria'];
        //$this->results =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['results'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit'])) {
            $this->criteria['limit'] = UI_BROWSE_DEFAULT_LIMIT;
        }
        if (empty($this->criteria['filetype'])) {
            $this->criteria['filetype'] = UI_FILETYPE_ANY;
        }

        if (!is_array($this->col)) {
            // init Categorys
            $this->setDefaults();
        }
    } // constructor


    public function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    } // fn setReload


    public function setDefaults($reload=FALSE)
    {
        $this->col[1]['category'] = UI_BROWSE_DEFAULT_KEY_1;
        $this->col[1]['value'][0] = '%%all%%';
        $this->col[2]['category'] = UI_BROWSE_DEFAULT_KEY_2;
        $this->col[2]['value'][0] = '%%all%%';
        $this->col[3]['category'] = UI_BROWSE_DEFAULT_KEY_3;
        $this->col[3]['value'][0] = '%%all%%';

        for ($col = 1; $col <= 3; $col++) {
            $this->setCategory(array('col' => $col,
                                     'category' => $this->col[$col]['category'],
                                     'value' => array(0 => '%%all%%')));
            $this->setValue(
                array('col'      => $col,
                      'category' => $this->col[$col]['category'],
                      'value'    => $this->col[$col]['value']
                )
            );
        }

        if ($reload === TRUE) {
            $this->setReload();
        }
    } // fn setDefaults


    public function getCriteria()
    {
        return $this->criteria;
    } // fn getCriteria


    public function getResult()
    {
        $this->searchDB();
        return $this->results;
    } // fn getResult


    public function browseForm($id, $mask2)
    {
        include(dirname(__FILE__).'/formmask/metadata.inc.php');
        #$mask2['browse_columns']['category']['options'][0] = tra('Select a Value');
        foreach ($mask['pages'] as $key => $val) {
            foreach ($mask['pages'][$key] as $v){
                if (isset($v['type']) && $v['type']) {
                    $tmp = uiBase::formElementEncode($v['element']);
                    $mask2['browse_columns']['category']['options'][$tmp] = tra($v['label']);
                }
            }
        };

        for ($n = 1; $n <= 3; $n++) {
            $form = new HTML_QuickForm('col'.$n, UI_STANDARD_FORM_METHOD, UI_HANDLER);
            $form->setConstants(array('id' => $id,
                                      'col' => $n,
                                      'category' => uiBase::formElementEncode($this->col[$n]['category'])));
            $mask2['browse_columns']['value']['options'] = $this->options($this->col[$n]['values']['results']);
            $mask2['browse_columns']['value']['default'] = $this->col[$n]['form_value'];
            uiBase::parseArrayToForm($form, $mask2['browse_columns']);
            $form->validate();
            $renderer = new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            $output['col'.$n]['dynform'] = $renderer->toArray();
        }

        ## form to change limit and file-type
        $form = new HTML_QuickForm('switch', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask2['browse_global']);
        $form->setDefaults(array('limit'    => $this->criteria['limit'],
                                 'filetype' => $this->criteria['filetype']));
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['global']['dynform'] = $renderer->toArray();

        return $output;
    } // fn browseForm


    /**
     * Set the category for audio file browser.  There are three columns
     * you can set the category for.
     *
     * @param array $parm
     * 		Has keys:
     * 		int ['col'] - the column you are setting the category for
     * 		string ['category'] - the category for the given column
     * 		string ['value'] - the search value for the given category
     * @return void
     */
    public function setCategory($parm)
    {
        $columnNumber = $parm['col'];
        $category = $parm['category'];

        $this->col[$columnNumber]['category'] = uiBase::formElementDecode($category);
        $criteria = isset($this->col[$columnNumber]['criteria']) ? $this->col[$columnNumber]['criteria'] : null;
        $this->col[$columnNumber]['values'] = $this->Base->gb->browseCategory($this->col[$columnNumber]['category'], $criteria, $this->Base->sessid);

        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
        $this->clearHierarchy($columnNumber);
    } // fn setCategory


    /**
     * @param array $parm
     * 		contains the following indexes:
     * 		int ['col']: column number
     * 		string ['value'][0]: the search value for the given category
     * 		string ['category']: the category to search
     */
    public function setValue($parm)
    {
        $this->criteria['offset'] = 0;
        $columnNumber = $parm['col'];
        $next  = $columnNumber + 1;
        $this->col[$columnNumber]['form_value'] = $parm['value'][0];
        $tmpFiletype = isset($this->criteria['filetype'])?$this->criteria['filetype']:UI_FILETYPE_ANY;
        if ($parm['value'][0] == '%%all%%') {
            $this->col[$next]['criteria'] = array('operator' => 'and',
            									  'filetype' => $tmpFiletype);
        } else {
        	$conditions = array(
	                        array('cat' => uiBase::formElementDecode($parm['category']),
	                              'op' => '=',
	                              'val' => $parm['value'][0]
	                        ));
        	if (isset($this->col[$columnNumber]['criteria']['conditions'])
        		&& is_array($this->col[$columnNumber]['criteria']['conditions'])) {
        		$conditions = array_merge($conditions,
        								  $this->col[$columnNumber]['criteria']['conditions']);
        	}
            $this->col[$next]['criteria'] = array(
                'operator' => 'and',
                'filetype' => $this->criteria['filetype'],
                'conditions'  => $conditions);
        }
        $nextCriteria = isset($this->col[$next]['criteria']) ? $this->col[$next]['criteria'] : null;
        $category = isset($this->col[$next]['category']) ? $this->col[$next]['category'] : null;
        $this->col[$next]['values'] = $this->Base->gb->browseCategory($category, $nextCriteria, $this->Base->sessid);

        $this->setCriteria();
        $this->clearHierarchy($next);
        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
    } // fn setValue


    /**
     * Given an array of category values, prepare the array for display:
     * add "--all--" option to the beginning and index all elements by
     * their own name.  Return the new array created.
     *
     * @param array $p_categoryValues
     * @return array
     */
    public function options($p_categoryValues)
    {
        $ret['%%all%%'] = '---all---';
        if (is_array($p_categoryValues)) {
            foreach ($p_categoryValues as $val) {
                $ret[$val]  = $val;
            }
        }
        return $ret;
    } // fn options


    /**
     * Clear all categories from the given column number to column 3.
     *
     * @param int $columnNumber
     * @return void
     */
    private function clearHierarchy($columnNumber)
    {
        $this->col[$columnNumber]['form_value'] = NULL;
        $columnNumber++;
        for ($col = $columnNumber; $col <= 3; $col++) {
            $this->col[$col]['criteria']    = NULL;
            $this->col[$col]['values']      = NULL;
            $this->col[$col]['form_value']  = NULL;
        }
    } // fn clearHierarchy


    public function setCriteria() {
        unset($this->criteria['conditions']);
        for ($col = 3; $col >= 1; $col--) {
            if (is_array($this->col[$col]['criteria'])) {
                $this->criteria = array_merge($this->col[$col]['criteria'], $this->criteria);
                break;
            }
        }
    } // fn setCriteria


    public function searchDB()
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);

        if (!is_array($results) || !count($results)) {
            return false;
        }

        $this->results['cnt'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            $tmpId = BasicStor::IdFromGunid($rec["gunid"]);
            $this->results['items'][] = $this->Base->getMetaInfo($tmpId);
        }

        /*
        ## test
        for ($n=0; $n<=$this->criteria['limit']; $n++) {
            $this->results['items'][] = Array
                (
                    'id' => 24,
                    'gunid' => '1cc472228d0cb2ac',
                    'title' => 'Item '.$n,
                    'creator' => 'Sebastian',
                    'duration' => '&nbsp;&nbsp;&nbsp;10:00',
                    'type' => 'webstream'
                );
        }
        $results['cnt'] = 500;
        $this->results['cnt'] = $results['cnt'];
        ## end test
        */

        $this->pagination($results);
        #print_r($this->criteria);
        #print_r($this->results);
        return TRUE;
    } // fn searchDB


    public function pagination(&$results)
    {
        if (sizeof($this->results['items']) == 0) {
            return FALSE;
        }
        $delta = 4;
        // current page
        $currp = ($this->criteria['offset'] / $this->criteria['limit']) + 1;
        // maximum page
        $maxp = ceil($results['cnt'] / $this->criteria['limit']);

        $deltaLower = UI_BROWSERESULTS_DELTA;
        $deltaUpper = UI_BROWSERESULTS_DELTA;
        $start = $currp;

        if ( ($start+$delta-$maxp) > 0) {
            // correct lower border if page is near end
            $deltaLower += $start+$delta-$maxp;
        }

        for ($n = $start-$deltaLower; $n <= $start+$deltaUpper; $n++) {
            if ($n <= 0) {
                // correct upper border if page is near zero
                $deltaUpper++;
            } elseif ($n <= $maxp) {
                $this->results['pagination'][$n] = $n;
            }
        }

        if (!isset($this->results['pagination'][1])) {
        	$this->results['pagination'][1] = '|<<';
        }
        if (!isset($this->results['pagination'][$maxp])) {
        	$this->results['pagination'][$maxp] = '>>|';
        }
        $this->results['next']  = ($results['cnt'] > ($this->criteria['offset'] + $this->criteria['limit'])) ? TRUE : FALSE;
        $this->results['prev']  = ($this->criteria['offset'] > 0) ? TRUE : FALSE;
        ksort($this->results['pagination']);
    } // fn pagination


    public function reorder($by)
    {
        $this->criteria['offset'] = NULL;

        if ( ($this->criteria['orderby'] == $by) && !$this->criteria['desc']) {
            $this->criteria['desc'] = TRUE;
        } else {
            $this->criteria['desc'] = FALSE;
        }

        $this->criteria['orderby'] = $by;
        $this->setReload();
    } // fn reorder


    /**
     * Set the current page of results to display.
     *
     * @param int $page
     * @return void
     */
    public function setOffset($page)
    {
        $o =& $this->criteria['offset'];
        $l =& $this->criteria['limit'];

        if ($page == 'next') {
            $o += $l;
        } elseif ($page == 'prev') {
            $o -= $l;
        } elseif (is_numeric($page)) {
            $o = $l * ($page-1);
        }
        $this->setReload();
    } // fn setOffset


    /**
     * Set the number of results to return.
     *
     * @param int $limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->criteria['limit'] = $limit;
        $this->setReload();
    } // fn setLimit


    public function setFiletype($filetype)
    {
        $this->criteria['filetype'] = $filetype;
        $this->criteria['offset'] = 0;

        for ($n = 1; $n <= 3; $n++) {
            $this->col[$n]['criteria']['filetype'] = $filetype;
            $this->col[$n]['values'] = $this->Base->gb->browseCategory($this->col[$n]['category'], $this->col[$n]['criteria'], $this->Base->sessid);
            $this->clearHierarchy($n);
        }

        $this->setReload();
    } // fn setFiletype
} // class uiBrowse
?>