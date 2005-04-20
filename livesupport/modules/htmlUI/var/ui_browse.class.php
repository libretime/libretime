<?php
class uiBrowse
{
    function uiBrowse(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->col        =& $_SESSION[UI_BROWSE_SESSNAME]['col'];
        $this->criteria   =& $_SESSION[UI_BROWSE_SESSNAME]['criteria'];
        #$this->results    =& $_SESSION[UI_BROWSE_SESSNAME]['results'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        $this->criteria['limit'] ? NULL : $this->criteria['limit'] = 10;

        if (!is_array($this->col)) {
            $this->setDefaults();
        }
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }


    function setDefaults($reload=FALSE)
    {
        $this->col[1]['category'] = 'dc:type';
        $this->col[1]['value'][0] = '%%all%%';
        $this->col[2]['category'] = 'dc:creator';
        $this->col[2]['value'][0] = '%%all%%';
        $this->col[3]['category'] = 'dc:source';
        $this->col[3]['value'][0] = '%%all%%';
        for ($col=1; $col<=3; $col++) {
            $this->setCategory(array('col' => $col, 'category' => $this->col[$col]['category'], 'value' => array(0=>'%%all%%')));
        }

        $this->setValue(array('col' => 1,
                              'category' => 'dc:type',
                              'value' => Array(0 => '%%all%%')
                        )
        );

        if ($reload === TRUE) $this->setReload();
    }

    function getCriteria()
    {
        return $this->criteria;
    }


    function getResult()
    {
        $this->searchDB();
        return $this->results;
    }


    function browseForm($id, $mask2)
    {
        include dirname(__FILE__).'/formmask/metadata.inc.php';
        #$mask2['browse_columns']['category']['options'][0] = tra('Select a Value');
        foreach ($mask['pages'] as $key=>$val) {
            foreach ($mask['pages'][$key] as $v){
                if ($v['type']) $mask2['browse_columns']['category']['options'][$this->Base->_formElementEncode($v['element'])] = tra($v['label']);
            }
        };

        for($n=1; $n<=3; $n++) {
            $form = new HTML_QuickForm('col'.$n, UI_STANDARD_FORM_METHOD, UI_HANDLER);
            $form->setConstants(array('id' => $id, 'col' => $n, 'category' => $this->Base->_formElementEncode($this->col[$n]['category'])));
            $mask2['browse_columns']['value']['options'] = $this->options($this->col[$n]['values']['results']);
            $mask2['browse_columns']['value']['default'] = $this->col[$n]['form_value'];
            $this->Base->_parseArr2Form($form, $mask2['browse_columns']);
            $form->validate();
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            $output['col'.$n]['dynform'] = $renderer->toArray();
        }

        ## just to change limit and file-type
        $form = new HTML_QuickForm('switch', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $mask2['browse_global']);
        $form->setDefaults(array('limit'    => $this->criteria['limit'],
                                 'filetype' => $this->criteria['filetype']));
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['global']['dynform'] = $renderer->toArray();

        return $output;
    }


    function setCategory($formdata)
    {
        $which = $formdata['col'];
        $this->col[$which]['category'] = $this->Base->_formElementDecode($formdata['category']);
        $this->col[$which]['values']   = $this->Base->gb->browseCategory($this->col[$which]['category'], $this->col[$which]['criteria'], $this->Base->sessid);

        $this->Base->redirUrl = UI_BROWSER.'?act=BROWSE';

        $this->clearHierarchy($which);
        #print_r($this->col);
    }

    function setValue($formdata)
    {
        $this->criteria['offset'] = 0;
        $which = $formdata['col'];
        $next  = $which + 1;
        $this->col[$which]['form_value'] =  $formdata['value'][0];
        if ($formdata['value'][0] == '%%all%%') {
            $this->col[$next]['criteria'] = array('operator' => 'and');
        } else {
            $this->col[$next]['criteria'] = array(
                                            'operator' => 'and',
                                            'conditions'  =>
                                                array_merge($this->col[$which]['criteria']['conditions'],
                                                            array(
                                                                array('cat'  => $this->Base->_formElementDecode($formdata['category']),
                                                                      'op'   => '=',
                                                                      'val'  => $formdata['value'][0]
                                                                )
                                                            )
                                                        )
                                            );
        }
        $this->col[$next]['values'] = $this->Base->gb->browseCategory($this->col[$next]['category'], $this->col[$next]['criteria'], $this->Base->sessid);

        #echo "cat: ".$this->col[$next]['category']."\n";
        #echo "criteria: "; print_r($this->col[$next]['criteria']);
        #echo "\nvalues: "; print_r($this->col[$next]['values']);

        $this->clearHierarchy($next);
        #$this->searchDB();
        $this->Base->redirUrl = UI_BROWSER.'?act=BROWSE';
    }


    function options($arr)
    {   $ret['%%all%%'] = '---all---';
        if (is_array($arr)) {
            foreach ($arr as $val)
                $ret[$val]  = $val;
        }

        return $ret;
    }

    function clearHierarchy($which)
    {
        $this->col[$which]['form_value'] = NULL;
        $which++;
        for ($col=$which; $col<=4; $col++) {
            $this->col[$col]['criteria']    = NULL;
            $this->col[$col]['values']      = NULL;
            $this->col[$col]['form_value']  = NULL;
        }
    }


    function searchDB()
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);

        $this->criteria['conditions'] = array();
        for($col=4; $col>=1; $col--) {
            if (is_array($this->col[$col]['criteria'])) {
                $this->criteria = array_merge ($this->criteria, $this->col[$col]['criteria']);
                break;
            }
        }
        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        $this->results['cnt'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            $this->results['items'][] = $this->Base->_getMetaInfo($this->Base->gb->_idFromGunid($rec));
        }

        /*
        ## test
        for ($n=0; $n<=$this->criteria['limit']; $n++) {
            $this->results['items'][] = Array
                (
                    'id' => 24,
                    'gunid' => '1cc472228d0cb2ac',
                    'title' => 'Strom 10min',
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
    }


    function pagination(&$results)
    {
        if (sizeof($this->results['items']) == 0) {
            return FALSE;
        }
        $currp =  ($this->criteria['offset']/$this->criteria['limit']) + 1;   # current page
        $maxp  =  ceil($results['cnt'] / $this->criteria['limit']);           # maximum page

        for ($n = 1; $n <= $maxp; $n = $n+$width) {
            $width = pow(10, floor(($n)/10));
            $this->results['pagination'][$n] = $n;
        }

        #array_pop($this->results['pagination']);
        $this->results['pagination'][1] = '|<<';
        $this->results['pagination'][$maxp] = '>>|';
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

    function setLimit($limit)
    {
        $this->criteria['limit'] = $limit;
        $this->setReload();
        #$this->searchDB();
    }

    function setFiletype($filetype)
    {
        $this->criteria['filetype'] = $filetype;
        $this->setReload();
        #$this->searchDB();
    }
}
?>
