<?php
class uiBrowse
{
    function uiBrowse(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->col        =& $_SESSION[UI_BROWSE_SESSNAME]['col'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (!is_array($this->col)) {
            #$this->col[1]['category'] = 'dc:genre';
            #$this->col[2]['category'] = 'dc:creator';
            #$this->col[3]['category'] = 'dc:title';
            #$this->setCategory(array('col' => 1, 'category' => $this->col[1]['category']));
        }
        #print_r($this->col);
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }



    function browseForm($id, $mask2)
    {
        include dirname(__FILE__).'/formmask/metadata.inc.php';
        $mask2['category']['options'][0] = tra('Select a Value');
        foreach ($mask['pages'] as $key=>$val) {
            foreach ($mask['pages'][$key] as $v){
                if ($v['type']) $mask2['category']['options'][$this->Base->_formElementEncode($v['element'])] = tra($v['label']);
            }
        };

        for($n=1; $n<=3; $n++) {
            $form = new HTML_QuickForm('col'.$n, UI_STANDARD_FORM_METHOD, UI_HANDLER);
            $form->setConstants(array('id' => $id, 'col' => $n, 'category' => $this->Base->_formElementEncode($this->col[$n]['category'])));
            $mask2['value']['options'] = $this->_options($this->col[$n]['values']['results']);
            $mask2['value']['default'] = $this->col[$n]['form_value'];
            $this->Base->_parseArr2Form($form, $mask2);
            $form->validate();
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            $output[$n]['dynform'] = $renderer->toArray();
        }
        #print_r($output);
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
        $which = $formdata['col'];
        $next  = $which + 1;
        $this->col[$which]['form_value'] =  $formdata['value'][0];
        if ($formdata['value'][0] == '%%all%%') {
            $this->col[$next]['criteria'] = NULL;
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
        $this->Base->redirUrl = UI_BROWSER.'?act=BROWSE';
    }


    function _options($arr)
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


    function getResult()
    {
        for($col=4; $col>=1; $col--) {
            if (is_array($this->col[$col]['criteria'])) {
                #echo $col; print_r($this->col[$col]['criteria']);
                break;
            }
        }
        $results = $this->Base->gb->localSearch($this->col[$col]['criteria'], $this->Base->sessid);
        $this->results['count'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            $this->results['items'][] = $this->Base->_getMetaInfo($this->Base->gb->_idFromGunid($rec));
        }
        #print_r($this->results);
        return $this->results;
    }

}
?>
