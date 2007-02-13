<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiBrowse
{
    /**
     * @var uiBase
     */
    public $Base;

    /**
     * @var string
     */
    private $prefix;

    /**
     * A pointer to the SESSION variable: ['UI_BROWSE_SESSNAME']['col']
     *
     * It has the following structure:
     * The first index denotes which column you are referring to.
     * There are three possible columns.
     *
     * For each column, the following keys are possible:
     * ['category'] --> selected category
     * ['value'] --> an array of one value, the selected value
     * ['values']['cnt'] --> number of values
     * ['values']['results'] --> array of values, indexed numerically
     * ['criteria'] --> criteria for one column, see top of DataEngine.php
     *      for description of these values.  The criteria of all three
     *      columns are merged together to make $this->criteria.
     * ['form_value'] --> the value as used in the HTML form
     *
     * @var array
     */
    private $col;

    /**
     * A pointer to the SESSION variable: ['UI_BROWSE_SESSNAME']['criteria']
     *
     * This array ultimately is passed to DataEngine::localSearch().  Look
     * at the top of the DataEngine.php class for the structure of this
     * variable.
     *
     * @var array
     */
    private $criteria;

    /**
     * @var string
     */
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


    /**
     * Initialize $this->col.  Each column contains
     *
     * @param boolean $reload
     * @return void
     */
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
                                     'category' => $this->col[$col]['category']));
            $this->setValue(
                array('col'      => $col,
                      'category' => $this->col[$col]['category'],
                      'value'    => $this->col[$col]['value']));
        }

        if ($reload === TRUE) {
            $this->setReload();
        }
    } // fn setDefaults


    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    } // fn getCriteria


    /**
     * @return array
     */
    public function getResult()
    {
        $this->searchDB();
        return $this->results;
    } // fn getResult


    public function browseForm($id, $mask2)
    {
        include(dirname(__FILE__).'/formmask/metadata.inc.php');
        foreach ($mask['pages'] as $key => $val) {
            foreach ($mask['pages'][$key] as $v){
                if (isset($v['type']) && $v['type']) {
                    $tmp = uiBase::formElementEncode($v['element']);
                    $mask2['browse_columns']['category']['options'][$tmp] = tra($v['label']);
                }
            }
        }

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

        // form to change limit and file-type
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
     * you can set the category for.  All columns greater than the current
     * one will be cleared of their values.
     *
     * @param array $p_param
     * 		Has keys:
     * 		int ['col'] - the column you are setting the category for
     * 		string ['category'] - the category for the given column
     * @return void
     */
    public function setCategory($p_param)
    {
        // input values
        $columnNumber = $p_param['col'];
        $category = uiBase::formElementDecode($p_param['category']);

        // Set the new category for this column.
        $this->col[$columnNumber]['category'] = $category;
        $previousValue = $this->col[$columnNumber]['form_value'];

        // For this column and all columns above this one, reset the values
        for ($i = $columnNumber; $i <= 3; $i++) {
            $this->col[$i]['criteria'] = NULL;
            $this->col[$i]['form_value'] = '%%all%%';
        }

        // Reload the criteria
        $this->setCriteria();
        $tmpCriteria = $this->criteria;
        // We put a limit here to because some categories will select
        // way too many values.
        $tmpCriteria["limit"] = 1000;
        $tmpCriteria["offset"] = 0;

        // For this column and all columns above this one,
        // reload the values.
        for ($i = $columnNumber; $i <= 3; $i++) {
            $browseValues = $this->Base->gb->browseCategory(
                $this->col[$i]["category"], $tmpCriteria, $this->Base->sessid);
            if (!PEAR::isError($browseValues)) {
                $this->col[$i]['values'] = $browseValues;
            }
            $browseValues = null;
        }

        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
    } // fn setCategory


    /**
     * Set the value for a category.  This will cause the
     * search results to change.
     *
     * @param array $parm
     * 		contains the following indexes:
     * 		int ['col']: column number
     * 		string ['value'][0]: the search value for the given category
     * 		string ['category']: the category to search
     * @see DataEngine
     *      See the top of that file for a description of the search
     *      criteria structure.
     */
    public function setValue($p_param)
    {
        $columnNumber = $p_param['col'];
        $value = $p_param['value'][0];
        $category = $p_param['category'];

        $this->criteria['offset'] = 0;
        $this->col[$columnNumber]['form_value'] = $value;

        if ($value == '%%all%%') {
            unset($this->col[$columnNumber]['criteria']['conditions']);
        } else {
        	$conditions = array('cat' => uiBase::formElementDecode($category),
                                'op' => '=',
	                            'val' => $value);
    	    $this->col[$columnNumber]['criteria']['conditions'] = $conditions;
        }

        // Clear all columns above this one of selected values.
        for ($tmpColNum = $columnNumber + 1; $tmpColNum <= 3; $tmpColNum++) {
            $this->col[$tmpColNum]['criteria'] = NULL;
            $this->col[$tmpColNum]['form_value'] = '%%all%%';
        }

        // Update the criteria
        $this->setCriteria();
        $tmpCriteria = $this->criteria;
        // We put a limit here to because some categories will select
        // way too many values.
        $tmpCriteria["limit"] = 1000;
        $tmpCriteria["offset"] = 0;

        // For all columns greater than this one, reload the values.
        for ($tmpColNum = $columnNumber + 1; $tmpColNum <= 3; $tmpColNum++) {
            $tmpCategory = $this->col[$tmpColNum]['category'];
            $browseValues = $this->Base->gb->browseCategory(
                $tmpCategory, $tmpCriteria, $this->Base->sessid);
            if (!PEAR::isError($browseValues)) {
                $this->col[$tmpColNum]['values'] = $browseValues;
            }
        }
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
     * Reload the conditions as set in the three columns.
     * @return void
     */
    public function setCriteria() {
        unset($this->criteria['conditions']);
        $conditions = array();
        for ($col = 3; $col >= 1; $col--) {
            if (is_array($this->col[$col]['criteria']['conditions'])) {
                $conditions[] = $this->col[$col]['criteria']['conditions'];
            }
        }
        $this->criteria['conditions'] = $conditions;
    } // fn setCriteria


    public function searchDB()
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);

        if (!is_array($results) || !count($results)) {
            return false;
        }
        $this->results['cnt'] = $results['cnt'];
        $this->results['items'] = $results['results'];
        $this->pagination($results);
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


    public function reorder($p_orderBy)
    {
        $this->criteria['offset'] = NULL;

        if ( ($this->criteria['orderby'] == $p_orderBy) && !$this->criteria['desc']) {
            $this->criteria['desc'] = TRUE;
        } else {
            $this->criteria['desc'] = FALSE;
        }

        $this->criteria['orderby'] = $p_orderBy;
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


    public function setFiletype($p_filetype)
    {
        $this->criteria['filetype'] = $p_filetype;
        $this->criteria['offset'] = 0;

        for ($n = 1; $n <= 3; $n++) {
            $this->col[$n]['form_value'] = '%%all%%';
            $this->col[$n]['criteria'] = null;
        }
        $this->setCriteria();
        $tmpCriteria = $this->criteria;
        $tmpCriteria["limit"] = 1000;

        for ($n = 1; $n <= 3; $n++) {
            $browseValues = $this->Base->gb->browseCategory(
                $this->col[$n]['category'],
                $tmpCriteria,
                $this->Base->sessid);
            if (!PEAR::isError($browseValues)) {
                $this->col[$n]['values'] = $browseValues;
            }
        }

        $this->setReload();
    } // fn setFiletype

} // class uiBrowse
?>