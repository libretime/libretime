<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiSearch
{
    private $Base;
    private $prefix;
    private $criteria;
    private $reloadUrl;
    private $results;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->prefix = 'SEARCH';
        //$this->results =& $_SESSION[UI_SEARCH_SESSNAME]['results'];
        $this->criteria =& $_SESSION[UI_SEARCH_SESSNAME]['criteria'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        if (empty($this->criteria['limit'])) {
            $this->criteria['limit'] = UI_BROWSE_DEFAULT_LIMIT;
        }
    }


    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    function getResult()
    {
        $this->searchDB();
        return $this->results;
    }


    function getCriteria()
    {
        return $this->criteria;
    }


    function searchForm($id, $mask2)
    {
        include(dirname(__FILE__).'/formmask/metadata.inc.php');
        $form = new HTML_QuickForm('search', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $counter = isset($this->criteria['counter']) ? $this->criteria['counter'] : 1;
        $form->setConstants(array('id'=>$id, 'counter'=>$counter));

        foreach ($mask['pages'] as $key=>$val) {
            foreach ($mask['pages'][$key] as $v) {
                if (isset($v['type']) && $v['type']) {
                    $col1[uiBase::formElementEncode($v['element'])] = tra($v['label']);
                    if (isset($val['relation'])) {
                        $col2[uiBase::formElementEncode($v['element'])] = $mask2['relations'][$v['relation']];
                    } else {
                        $col2[uiBase::formElementEncode($v['element'])] = $mask2['relations']['standard'];
                    }
                }
            };
        };

        for ($n = 1; $n <= UI_SEARCH_MAX_ROWS; $n++) {
            unset($group);

            if ( ($n > 1) && ($n > $counter) ) {
                $activerow = FALSE;
            } else {
                $activerow = TRUE;
            }

            $form->addElement('static', 's1', NULL, "<div id='searchRow_$n'>");

            if ($activerow === FALSE) {
                $form->addElement('static', 's1_style', NULL, "<style type='text/css'>#searchRow_$n {display:none; height:0px}</style>");
            }

            $sel = &$form->createElement('hierselect', "row_$n", NULL);
            $sel->setOptions(array($col1, $col2));
            $group[] = &$sel;
            $group[] = &$form->createElement('text', "row_$n".'[2]', NULL, array('size' => 25, 'maxlength' => UI_INPUT_STANDARD_MAXLENGTH));

            if ($activerow) {
                $group[] = &$form->createElement('hidden', "row_$n".'[active]', TRUE);
            } else {
                $group[] = &$form->createElement('hidden', "row_$n".'[active]', FALSE);
            }

            if ($n === 1) {
                $group[] = &$form->createElement('button', "addRow", tra('+'), array('onClick' => "SearchForm_addRow('$n')", 'class' => UI_BUTTON_STYLE));
            } else {
                $group[] = &$form->createElement('button', "dropRow_$n", tra('-'), array('onClick' => "SearchForm_dropRow('$n')", 'class' => UI_BUTTON_STYLE));
            }

            $form->addGroup($group);
            $form->addElement('static', 's2', NULL, "</div id='searchRow_$n'>");
        }

        for ($i = 0; $i < count($mask2['search']); $i++) {
            if ($mask2['search'][$i]['element'] == "operator") {
                $mask2['search'][$i]['selected'] = strtolower($this->criteria['operator']);
                break;
            }
        }
        uiBase::parseArrayToForm($form, $mask2['search']);
        $constants = isset($this->criteria['form']) ? $this->criteria['form'] : null;
        $form->setConstants($constants);
        $form->validate();
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['dynform'] = $renderer->toArray();
        return $output;
    }


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

        foreach ($formdata as $key=>$val) {
            if (is_array($val) && $val['active']) {
                $this->criteria['counter']++;
                $this->criteria['conditions'][$key] = array('cat' => uiBase::formElementDecode($val[0]),
                                                            'op'  => $val[1],
                                                            'val' => stripslashes($val[2])
                                                      );
                $this->criteria['form'][$key] = array(0 => $val[0],
                                                      1 => $val[1],
                                                      2 => stripslashes($val[2])
                                                      );
            }
        }
        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
        //$this->searchDB();
    }


    function simpleSearchForm($mask)
    {
        $form = new HTML_QuickForm('simplesearch', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output = $renderer->toArray();
        //print_r($output);
        return $output;
    }


    function simpleSearch($formdata)
    {
        $this->results = NULL;
        $this->criteria['conditions'] = NULL;
        $this->criteria['offset'] = NULL;
        $this->criteria['operator'] = UI_SIMPLESEARCH_OPERATOR;
        $this->criteria['filetype'] = UI_SIMPLESEARCH_FILETYPE;
        $this->criteria['limit'] = UI_SIMPLESEARCH_LIMIT;
        $this->criteria['counter'] = UI_SIMPLESEARCH_ROWS;

        // $criteria['form'] is used for retransfer to form
        $this->criteria['form']['operator'] = 'OR';
        $this->criteria['form']['filetype'] = UI_SIMPLESEARCH_FILETYPE;
        $this->criteria['form']['limit'] = UI_SIMPLESEARCH_LIMIT;

        for ($n = 1; $n <= UI_SIMPLESEARCH_ROWS; $n++) {
            $this->criteria['conditions'][$n] = array('cat' => constant('UI_SIMPLESEARCH_CAT'.$n),
                                                      'op' => constant('UI_SIMPLESEARCH_OP'.$n),
                                                      'val' => stripslashes($formdata['criterium'])
                                               );
            $this->criteria['form']['row_'.$n]= array(0 => uiBase::formElementEncode(constant('UI_SIMPLESEARCH_CAT'.$n)),
                                                      1 => constant('UI_SIMPLESEARCH_OP'.$n),
                                                      2 => stripslashes($formdata['criterium'])
                                               );
        }
        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
        #$this->searchDB();
    }


    /**
     * Run the search query.  Use getResult() to get the results.
     *
     * @return boolean
     */
    function searchDB()
    {
        if (count($this->criteria) === 0) {
            return FALSE;
        }
        $offset = (isset($this->criteria['offset'])) ? $this->criteria['offset'] : 0;
        $this->results = array('page' => $offset / $this->criteria['limit']);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        $this->results['items'] = $results["results"];
        $this->results['cnt'] = $results['cnt'];
        $this->pagination();
        return TRUE;
    }


    function pagination()
    {
        if (sizeof($this->results['items']) == 0) {
            return FALSE;
        }
        $offset = isset($this->criteria['offset']) ? $this->criteria['offset'] : 0;
        $currentPage = ($offset / $this->criteria['limit']) + 1;
        $maxPage = ceil($this->results['cnt'] / $this->criteria['limit']);
        $deltaLower = UI_SEARCHRESULTS_DELTA;
        $deltaUpper = UI_SEARCHRESULTS_DELTA;
        $maxNumPaginationButtons = $deltaLower + $deltaUpper + 1;

        $start = 1;
		$end = $maxPage;

		// If there are enough pages to warrant "next" and "previous"
		// buttons...
        if ($maxPage > $maxNumPaginationButtons) {
        	// When currentPage goes past deltaLower
        	if ($currentPage <= $deltaLower) {
        		$end = min($deltaLower + $deltaUpper + 1, $maxPage);
        	}
	       	// When currentpage is near the end of the results.
        	elseif ($currentPage >= ($maxPage - $deltaUpper)) {
	            $start = max($maxPage - $deltaLower - $deltaUpper + 1, 1);
	        }
        	// somewhere in the middle
	        else {
	        	$start = max($currentPage - $deltaLower, 1);
	        	$end = min($currentPage + $deltaUpper, $maxPage);
	        }
        }

        for ($n = $start; $n <= $end; $n++) {
	        $this->results['pagination'][$n] = $n;
        }

        if (!isset($this->results['pagination'][1])) {
        	$this->results['pagination'][1] = '|<<';
        }
        if (!isset($this->results['pagination'][$maxPage])) {
        	$this->results['pagination'][$maxPage] = '>>|';
        }
        $this->results['next'] = ($this->results['cnt'] > $offset + $this->criteria['limit']) ? TRUE : FALSE;
        $this->results['prev'] = ($offset > 0) ? TRUE : FALSE;
        ksort($this->results['pagination']);
        return TRUE;
    }


    function reorder($by)
    {
        $this->criteria['offset'] = NULL;

        if ($this->criteria['orderby'] == $by && !$this->criteria['desc']) {
            $this->criteria['desc'] = TRUE;
        } else {
            $this->criteria['desc'] = FALSE;
        }
        $this->criteria['orderby'] = $by;
        $this->setReload();
        //$this->searchDB();
    }


    function clear()
    {
        $this->criteria["conditions"] = null;
        $this->criteria['form'] = NULL;
        $this->criteria['counter'] = NULL;
        $this->setReload();
    }


    function setOffset($page)
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
        //$this->searchDB();
    }

} // class uiSearch
?>