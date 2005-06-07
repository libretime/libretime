<?php
class uiSearch
{
    function uiSearch(&$uiBase)
    {
        $this->Base       =& $uiBase;
        #$this->results    =& $_SESSION[UI_SEARCH_SESSNAME]['results'];
        $this->criteria   =& $_SESSION[UI_SEARCH_SESSNAME]['criteria'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
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

    function searchForm($id, &$mask2)
    {
        #print_r($this->criteria['form']);
        include dirname(__FILE__).'/formmask/metadata.inc.php';
        $form = new HTML_QuickForm('search', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setConstants(array('id'=>$id, 'counter'=>$this->criteria['counter'] ? $this->criteria['counter'] : 1));

        foreach ($mask['pages'] as $key=>$val) {
            foreach ($mask['pages'][$key] as $v){
                if ($v['type']) {
                    $col1[$this->Base->_formElementEncode($v['element'])] = tra($v['label']);
                    if (isset($val['relation']))
                        $col2[$this->Base->_formElementEncode($v['element'])] = $mask2['relations'][$v['relation']];
                    else
                        $col2[$this->Base->_formElementEncode($v['element'])] = $mask2['relations']['standard'];
                }
            };
        };

        for($n = 1; $n <= UI_SEARCH_MAX_ROWS; $n++) {
            unset ($group);

            if ($n > 1 && $n > $this->criteria['counter'])
                 $activerow = FALSE;
            else $activerow = TRUE;

            $form->addElement('static', 's1', NULL, "<div id='searchRow_$n'>");

            if ($activerow===FALSE) $form->addElement('static', 's1_style', NULL, "<style type='text/css'>#searchRow_$n {display:none; height:0px}</style>");

            $sel = &$form->createElement('hierselect', "row_$n", NULL);
            $sel->setOptions(array($col1, $col2));
            $group[] = &$sel;
            $group[] = &$form->createElement('text', "row_$n".'[2]', NULL, array('size' => 25, 'maxlength' => UI_INPUT_STANDARD_MAXLENGTH));

            if ($activerow) $group[] = &$form->createElement('hidden', "row_$n".'[active]', TRUE);
            else            $group[] = &$form->createElement('hidden', "row_$n".'[active]', FALSE);

            if ($n === 1)   $group[] = &$form->createElement('button', "addRow", tra('+'), array('onClick' => "SearchForm_addRow('$n')", 'class' => UI_BUTTON_STYLE));
            else            $group[] = &$form->createElement('button', "dropRow_$n", tra('-'), array('onClick' => "SearchForm_dropRow('$n')", 'class' => UI_BUTTON_STYLE));

            $form->addGroup($group);
            $form->addElement('static', 's2', NULL, "</div id='searchRow_$n'>");
        }

        $this->Base->_parseArr2Form($form, $mask2['search']);
        $form->setConstants($this->criteria['form']);
        $form->validate();
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['dynform'] = $renderer->toArray();
        #print_r($output);
        return $output;
    }


    function newSearch(&$formdata)
    {
        #print_r($formdata);

        $this->results                  = NULL;
        $this->criteria['conditions']   = NULL;
        $this->criteria['offset']       = NULL;
        $this->criteria['form']         = NULL;
        $this->criteria['operator']     = $formdata['operator'];
        $this->criteria['filetype']     = $formdata['filetype'];
        $this->criteria['limit']        = $formdata['limit'];
        $this->criteria['counter']      = 0;


        $this->criteria['form']['operator'] = $formdata['operator'];    ## $criteria['form'] is used for retransfer to form ##
        $this->criteria['form']['filetype'] = $formdata['filetype'];
        $this->criteria['form']['limit']    = $formdata['limit'];

        foreach ($formdata as $key=>$val) {
            if (is_array($val) && $val['active']) {
                $this->criteria['counter']++;
                $this->criteria['conditions'][$key] = array('cat' => $this->Base->_formElementDecode($val[0]),
                                                            'op'  => $val[1],
                                                            'val' => stripslashes($val[2])
                                                      );
                $this->criteria['form'][$key]       = array(0     => $val[0],
                                                            1     => $val[1],
                                                            2     => stripslashes($val[2])
                                                      );
            }
        }
        $this->Base->redirUrl = UI_BROWSER.'?act=SEARCH';
        #$this->searchDB();
    }


    function simpleSearchForm(&$mask)
    {
        $form = new HTML_QuickForm('simplesearch', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $mask);
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output = $renderer->toArray();
        #print_r($output);
        return $output;

    }


    function simpleSearch(&$formdata)
    {
        $this->results                  = NULL;
        $this->criteria['conditions']   = NULL;
        $this->criteria['offset']       = NULL;

        $this->criteria['operator']         = UI_SIMPLESEARCH_FILETYPE;
        $this->criteria['filetype']         = UI_SIMPLESEARCH_OPERATOR;
        $this->criteria['limit']            = UI_SIMPLESEARCH_LIMIT;
        $this->criteria['counter']          = UI_SIMPLESEARCH_ROWS;
        $this->criteria['form']['operator'] = 'OR';    ## $criteria['form'] is used for retransfer to form ##
        $this->criteria['form']['filetype'] = 'File';
        $this->criteria['form']['limit']    = UI_SIMPLESEARCH_LIMIT;

        for ($n = 1; $n<=UI_SIMPLESEARCH_ROWS; $n++) {
            $this->criteria['conditions'][$n] = array('cat'     => constant('UI_SIMPLESEARCH_CAT'.$n),
                                                      'op'      => constant('UI_SIMPLESEARCH_OP'.$n),
                                                      'val'     => stripslashes($formdata['criterium'])
                                               );
            $this->criteria['form']['row_'.$n]= array(0     => $this->Base->_formElementEncode(constant('UI_SIMPLESEARCH_CAT'.$n)),
                                                      1     => constant('UI_SIMPLESEARCH_OP'.$n),
                                                      2     => stripslashes($formdata['criterium'])
                                               );
        }
        $this->Base->redirUrl = UI_BROWSER.'?act=SEARCH';
        #$this->searchDB();
    }


    function searchDB()
    {
        if (count($this->criteria) === 0)
            return FALSE;

        $this->results = array('page' => $this->criteria['offset'] / $this->criteria['limit']);

        #print_r($this->criteria);
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        if (PEAR::isError($results)) {
            #print_r($results);
            return FALSE;
        }
        foreach ($results['results'] as $rec) {
            $this->results['items'][] = $this->Base->_getMetaInfo($this->Base->gb->_idFromGunid($rec));
        }
        $this->results['cnt'] = $results['cnt'];

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

        #print_r($this->results);
        $this->pagination($results);
    }


    function pagination(&$results)
    {
        if (sizeof($this->results['items']) == 0) {
            return FALSE;
        }

        $currp = ($this->criteria['offset'] / $this->criteria['limit']) + 1;   # current page
        $maxp  = ceil($results['cnt'] / $this->criteria['limit']);           # maximum page

        /*
        for ($n = 1; $n <= $maxp; $n = $n+$width) {
            $width = pow(10, floor(($n)/10));
            $this->results['pagination'][$n] = $n;
        }
        */

        $deltaLower = UI_SEARCHRESULTS_DELTA;
        $deltaUpper = UI_SEARCHRESULTS_DELTA;
        $start = $currp;

        if ($start+$delta-$maxp > 0) $deltaLower += $start+$delta-$maxp;  ## correct lower boarder if page is near end

        for ($n = $start-$deltaLower; $n <= $start+$deltaUpper; $n++) {
            if ($n <= 0)            $deltaUpper++;                        ## correct upper boarder if page is near zero
            elseif ($n <= $maxp)    $this->results['pagination'][$n] = $n;
        }

        #array_pop($this->results['pagination']);
        $this->results['pagination'][1] ? NULL : $this->results['pagination'][1] = '|<<';
        $this->results['pagination'][$maxp] ? NULL : $this->results['pagination'][$maxp] = '>>|';
        $this->results['next']  = $results['cnt'] > $this->criteria['offset'] + $this->criteria['limit'] ? TRUE : FALSE;
        $this->results['prev']  = $this->criteria['offset'] > 0 ? TRUE : FALSE;
        ksort($this->results['pagination']);
    }


    function reOrder($by)
    {
        $this->criteria['offset'] = NULL;

        if ($this->criteria['orderby'] == $by && !$this->criteria['desc'])
            $this->criteria['desc'] = TRUE;
        else
            $this->criteria['desc'] = FALSE;

        $this->criteria['orderby'] = $by;
        $this->setReload();
        #$this->searchDB();
    }


    function clear()
    {
        #$this->results    = NULL;
        $this->criteria['form']    = NULL;
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
        #$this->searchDB();
    }
}
?>
