<?php
//
// This file contains a list of all the HTML forms in the system,
// encoded as arrays.
//
$tmpAct = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

$ui_fmask = array(
    /* ===================== list of system preferences which can be adjusted */
    'stationPrefs'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'changeStationPrefs'
        ),
        array(
            'element'   => 'stationFrequency',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Frequency',
            'required'  => TRUE
        ),
        array(
            'element'   => 'stationName',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Station name',
            'required'  => TRUE
        ),
        array(
            'element'   => 'stationLogoPath',
            'isPref'    => TRUE,
            'type'      => 'hidden',
            'label'     => 'Station logo path',
            'default'   => 'img/stationlogo.image',
            'required'  => TRUE,
        ),
        array(
            'rule'      => 'regex',
            'element'   => 'stationLogoPath',
            'format'    => '/^img\/[a-z0-9.-_]*$/',
            'rulemsg'   => 'Path appears invalid'
        ),
        array(
            'element'   => 'stationURL',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Station URL',
            'default'   => 'http://',
            'required'  => TRUE,
            'attributes'=> array('maxlength' => 256)
        ),
        array(
            'rule'      => 'regex',
            'element'   => 'stationURL',
            'format'    => UI_REGEX_URL,
            'rulemsg'   => 'URL seems not to be valid'
        ),
        array(
            'element'   => 'stationlogo',
            'type'      => 'file',
            'label'     => 'Station logo (maximum 128x128)',
            'requiredmsg'=> 'please select logo file'
        ),
        array(
            'element'   => 'schedulerStartupScript',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Scheduler startup script',
            'required'  => false,
        ),
        array(
            'element'   => UI_SCRATCHPAD_MAXLENGTH_KEY,
            'isPref'    => TRUE,
            'type'      => 'select',
            'label'     => 'Maximum length of scratchpad',
            'options'   => array(
                            5   => 5,
                            10  => 10,
                            25  => 25,
                            50  => 50
                           ),
            'default'   => 10,
            'required'  => TRUE
        ),
        array(
            'element'   =>'cancel',
            'type'      =>'button',
            'label'     =>'Cancel',
            'attributes'=>array('onclick' => 'location.href="'.UI_BROWSER.'"'),
            'groupit'   => TRUE
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit'),
            'label'     => ' '
        ),
    ),

    'chgPasswd' => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'default'   => 'chgPasswd'
        ),
        array(
            'element'   => 'uid',
            'type'      => 'hidden',
        ),
        array(
            'element'   => 'oldpass',
            'type'      => 'password',
            'label'     => 'Old password',
            'required'  => TRUE,
        ),
        array(
            'element'   => 'pass',
            'type'      => 'password',
            'label'     => 'New password',
            'required'  => TRUE,
        ),
        array(
            'element'   => 'pass2',
            'type'      => 'password',
            'label'     => 'Repeat password',
            'required'  => TRUE,
        ),
        array(
            'rule'      => 'compare',
            'element'   => array('pass','pass2'),
            'rulemsg'   => 'The passwords do not match.'
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit'
        )

    ),

    'login' => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'login'
        ),
        array(
            'element'   => 'login',
            'type'      => 'text',
            'label'     => 'Username',
            'required'  => TRUE,
            'attributes' => array('size' => 20)
        ),
        array(
            'element'   => 'pass',
            'type'      => 'password',
            'label'     => 'Password',
            'required'  => TRUE,
            'attributes' => array('size' => 20)
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'window.close()'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit')
        ),
    ),

    'uploadFileM'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'mediafile',
            'type'      => 'file',
            'label'     => 'Media file',
            'required'  => TRUE,
            'requiredmsg'=> 'Please select media file'
        ),
        array(
            'element'   => 'mdatafile',
            'type'      => 'file',
            'label'     => 'Metadata',
            'required'  => TRUE,
            'requiredmsg'=> 'Please select metadata file'
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit'
        )
    ),

    'file'          => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'folderId',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'mediafile',
            'type'      => 'file',
            'label'     => 'Media file',
            'required'  => TRUE,
            'requiredmsg'=> 'please select media file'
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array(
                'class'=> "button",
                'onClick'  => "location.href='".UI_BROWSER."'"),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE,
            'attributes' => array(
                'class'=> "button",
                ),
        ),
        array(
            'group'     => array('cancel', 'Submit')
        )
    ),

    'webstream'     => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'folderId',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'title',
            'type'      => 'text',
            'label'     => 'Title',
            'required'  => TRUE
        ),
        array(
            'element'   => 'url',
            'type'      => 'text',
            'label'     => 'Stream URL',
            'required'  => TRUE,
            'requiredmsg'=> 'URL is missing',
            'rule'      => 'regex',
            'format'    => UI_REGEX_URL,
            'rulemsg'   => 'URL seems invalid',
            'attributes'=> array('maxlength' => 256),
        ),
        array(
            'element'   => 'length',
            'type'      => 'date',
            'label'     => 'Length<br><small>Enter zero for Live Stream</small>',
            'options'   => array('format' => 'His'),
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick'  => "location.href='".UI_BROWSER."'"),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'test',
            'type'      => 'button',
            'label'     => 'Test',
            'groupit'   => TRUE,
            'attributes'=> array('onclick' => "if (validate_addWebstream(document.forms['addWebstream'])) popup('".UI_BROWSER."?popup[]=testStream&url=' + document.forms['addWebstream'].elements['url'].value, 'testStream', 400, 250)")
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'test', 'Submit')
        )
    ),

    'search'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => "$tmpAct.newSearch"
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'counter',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'max_rows',
            'type'      => 'hidden',
            'constant'  => UI_SEARCH_MAX_ROWS
        ),
        array(
            'element'   => 'operator',
            'type'      => 'select',
            'label'     => 'Operator',
            'options'   => array(
                            'and'   => '##And##',
                            'or'    => '##Or##',
                            )
        ),
        array(
            'element'   => 'filetype',
            'type'      => 'select',
            'label'     => 'File type',
            'options'   => array(
                            UI_FILETYPE_ANY       => '*',
                            UI_FILETYPE_AUDIOCLIP => '##Audioclip##',
                            UI_FILETYPE_WEBSTREAM => '##Webstream##',
                            UI_FILETYPE_PLAYLIST  => '##Playlist##'
                           ),
        ),
        array(
            'element'   => 'limit',
            'type'      => 'select',
            'label'     => 'Rows per page',
            'options'   => array(
                            10  => 10,
                            25  => 25,
                            50  => 50,
                            100 => 100
                           )
        ),
        array(
            'element'   => 'clear',
            'type'      => 'button',
            'label'     => 'Reset criteria',
            'attributes'  => array('class' => 'button_wide', 'onClick' => "this.form.reset(); hpopup('".UI_HANDLER."?act=SEARCH.clear', 'SF')"),
            'groupit'   => TRUE,
        ),
        array(
            'element'   => 'spacer',
            'type'      => 'static',
            'text'      => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
        ),
        array(
            'element'   => 'submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE,
            'attributes'  => array('class' => 'button_wide')
        ),
        array('group'   => array('clear', 'spacer', 'submit')
        ),
    ),

    'relations'     => array(
        'standard'    => array(
                'partial'    => '##partial##',
                'full'       => '##full##',
                'prefix'     => '##prefix##',
                '='          => '=' ,
                '<'         => '<',
                '<='        => '<=',
                '>'         => '>',
                '>='        => '>='
        ),
        1             => array(
                'partial'    => '##partial##',
                'full'       => '##full##',
                'prefix'     => '##prefix##',
                '='          => '='
        ),

    ),

    'languages'    => array(
            array(
                'element'  => 'langid',
                'type'     => 'select',
                'label'    => 'Language',
                'options'  => _getLanguages(),
                'default'  => UI_DEFAULT_LANGID
            )
    ),

    'browse_columns'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        'form'      => array(
            'element'   => 'col',
            'type'      => 'hidden'
        ),
        'category'  => array(
            'element'   => 'category',
            'type'      => 'select',
            'label'     => 'Category',
            'attributes'=> array(
                'onChange'  => 'this.form.act.value="'.$tmpAct.'.setCategory"; this.form.submit()',
                'style'     => 'width: 180px;',
                'id'        => 'category_1'
                )
        ),
        'value'      => array(
            'element'   => 'value',
            'type'      => 'select',
            'multiple'  => TRUE,
            'attributes'=> array(
                'size'      => 10,
                'class'     => 'area_browse',
                'onChange'  => 'this.form.act.value="'.$tmpAct.'.setValue"; this.form.submit()'
            )
        )
    ),

    'browse_global'  => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        array(
            'element'   => 'filetype',
            'type'      => 'select',
            'label'     => 'File type',
            'options'   => array(
                            UI_FILETYPE_ANY       => '*',
                            UI_FILETYPE_AUDIOCLIP => '##Audioclip##',
                            UI_FILETYPE_WEBSTREAM => '##Webstream##',
                            UI_FILETYPE_PLAYLIST  => '##Playlist##'
                           ),
            'attributes'=> array('onChange' =>  'hpopup("'.UI_HANDLER.'?act='.$tmpAct.'.setFiletype&filetype=" + this.form.filetype.value)')
        ),
        array(
            'element'   => 'limit',
            'type'      => 'select',
            'label'     => 'Rows per page',
            'options'   => array(
                            10  => 10,
                            25  => 25,
                            50  => 50,
                            100 => 100
                           ),
            'attributes'=> array('onChange' => 'hpopup("'.UI_HANDLER.'?act='.$tmpAct.'.setLimit&limit=" + this.form.limit.value)')
        ),
        /*        do we need reset?
        array(
            'element'   => 'spacer',
            'type'      => 'static',
            'constant'  => '',
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'reset',
            'type'      => 'button',
            'label'     => 'Reset criteria',
            'attributes'=> array('class' => 'button_wide', 'onClick' => 'hpopup("'.UI_HANDLER.'?act='.$_REQUEST['act'].'.setDefaults")'),
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('spacer', 'reset')
        )
        */
    ),

    'simplesearch'  => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SEARCH.simpleSearch'
        ),
        array(
            'element'   => 'simplesearch',
            'type'      => 'header',
            'label'     => 'Search library',
        ),
        array(
            'element'   => 'criterium',
            'type'      => 'text',
            'label'     => NULL,
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'submit',
            'type'      => 'submit',
            'label'     => 'Search',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('criterium', 'submit')
        )
    ),

    'PL.changeTransition'  => array(
        'transition' => array(
            array(
                'element'   => 'headline',
                'type'      => 'static'
            ),
            /*
            array(
                'element'   => 'type',
                'type'      => 'radio',
                'label'     => 'Type',
                'options'   => array(
                                'fadeX'      => 'Crossfade',
                                'pause'      => 'Pause'
                               ),
                'default'   => 'fadeX'
            )  */
            array(
                'element'   => 'type',
                'type'      => 'hidden',
                'default'   => 'fadeX'
            ),
        ),
        'fadeIn' => array(
            array(
                'element'   => 'headline',
                'type'      => 'static'
            ),
            /*
            array(
                'element'   => 'type',
                'type'      => 'radio',
                'label'     => 'Type',
                'options'   => array('fadeIn' => 'Fade in'),
                'default'   => 'fadeIn'
            )  */
            array(
                'element'   => 'type',
                'type'      => 'hidden',
                'default'   => 'fadeIn'
            ),

        ),
        'fadeOut' => array(
            array(
                'element'   => 'headline',
                'type'      => 'static'
            ),
            /*
            array(
                'element'   => 'type',
                'type'      => 'radio',
                'label'     => 'Type',
                'options'   => array('fadeOut' => 'Fade out'),
                'default'   => 'fadeOut'
            )    */
            array(
                'element'   => 'type',
                'type'      => 'hidden',
                'default'   => 'fadeOut'
            ),
        ),
        'all'   => array(
            array(
                'element'   => 'act',
                'type'      => 'hidden',
                'constant'  => 'PL.changeTransition'
            ),
            array(
                'element'   => 'id',
                'type'      => 'hidden'
            ),
            array(
                'element'   => 'duration',
                'type'      => 'text',
                'rule'      => 'numeric',
                'attributes'=> array('size' => 4, 'maxlength' => 4),
                'groupit'   => TRUE
            ),
            array(
                'element'   => 'switchdown',
                'type'      => 'button',
                'label'     => '-',
                'attributes'=> array('class' => 'button_small', 'onClick' => 'pl_switchDown()', 'onMousedown' => 'pl_start("Down")', 'onMouseUp' => "pl_stop()", 'onMouseOut' => "pl_stop()"),
                'groupit'   => TRUE
            ),
            array(
                'element'   => 'switchup',
                'type'      => 'button',
                'label'     => '+',
                'attributes'=> array('class' => 'button_small', 'onClick' => 'pl_switchUp()', 'onMousedown' => 'pl_start("Up")', 'onMouseUp' => "pl_stop()", 'onMouseOut' => "pl_stop()"),
                'groupit'   => TRUE
            ),
            array(
                'group'     => array('duration', 'switchdown', 'switchup'),
                'label'     => 'Duration (ms)'
            ),
            array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => 'Cancel',
                'attributes'=> array('onClick' => 'window.close()'),
                'groupit'   => TRUE,
            ),
            array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Reset',
                'groupit'   => TRUE,
            ),
            array(
                'element'   => 'submit',
                'type'      => 'submit',
                'label'     => 'Submit',
                'groupit'   => TRUE,
            ),
            array(
                'group'     => array('cancel', 'reset', 'submit')
            )
        )
    ),
    'PL.setClipLength' => array(
        'act'       => array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        'id'        => array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        'elemId'    => array(
            'element'   => 'elemId',
            'type'      => 'hidden'
        ),
        'duration'    => array(
            'element'   => 'duration',
            'type'      => 'hidden'
        ),
        'clipStart'  => array(
            'element'   => 'clipStart',
            'type'      => 'select',
            'label'     => 'Cue in: ',
            'options'   => array(),
            'attributes' => 'onChange="return PL_setClipLength(this)"',
            'groupit'   => true
        ),
        'clipLength'  => array(
            'element'   => 'clipLength',
            'type'      => 'select',
            'label'     => 'Length: ',
            'options'   => array(),
            'attributes' => 'onChange="return PL_setClipLength(this)"',
            'groupit'   => true
        ),
        'clipEnd'  => array(
            'element'   => 'clipEnd',
            'type'      => 'select',
            'label'     => 'Cue out: ',
            'options'   => array(),
            'attributes' => 'onChange="return PL_setClipLength(this)"',
            'groupit'   => true
        ),
        array(
            'group'     => array('clipStart', 'clipLength', 'clipEnd')
        ),
        array(
            'elemnt'     => 'linebreak',
            'type'      => 'static',
            'text'     => '<p></p>'
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'window.close()'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'reset',
            'type'      => 'reset',
            'label'     => 'Reset',
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'submitter',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'reset', 'submitter')
        )
    ),
    'schedule'  => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SCHEDULER.addItem'
        ),
        array(
            'element'   => 'playlist',
            'type'      => 'hidden'
        ),
        'date'      => array(
            'element'   => 'date',
            'type'      => 'date',
            'label'     => 'Date',
            'options'   => array('format' => 'Ymd'),
        ),
        'time'      => array(
            'element'   => 'time',
            'type'      => 'date',
            'label'     => 'Time',
            'options'   => array('format' => 'His'),
        ),
        'gunid_duration'  => array(
            'element'   => 'gunid_duration',
            'type'      => 'select',
            'label'     => 'Playlist',
            'required'  => TRUE,
        ),
        array(
            'element'   => 'snap2Prev',
            'type'      => 'button',
            'label'     => 'Snap to previous',
            'attributes'=> array('onClick' => 'SCHEDULE_snap2Prev()'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'snap2Hour',
            'type'      => 'button',
            'label'     => 'Snap to hour',
            'attributes'=> array('onClick' => 'SCHEDULE_snap2Hour()'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'snap2Next',
            'type'      => 'button',
            'label'     => 'Snap to next',
            'attributes'=> array('onClick' => 'SCHEDULE_snap2Next()'),
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('snap2Prev', 'snap2Hour', 'snap2Next')
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'window.close()'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'reset',
            'type'      => 'reset',
            'label'     => 'Reset',
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'submitter',
            'type'      => 'button',
            'label'     => 'Submit',
            'attributes'=> array('onClick' => 'SCHEDULE_submit()'),
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'reset', 'submitter')
        )
    ),
    'PL.export'	=> array(
        'act'       => array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        'id'        => array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'exporttype',
            'type'      => 'select',
            'label'     => 'Type',
            'options'   => array('allComponents' => 'All components','playlistOnly' => 'Playlist only')
        ),
        array(
            'element'   => 'playlisttype',
            'type'      => 'select',
            'label'     => 'File Format',
            'options'   => array(
                             'smil' => 'SMIL',
                           //  'xspf' => 'XSPF',
                             'm3u' => 'M3U'
                           )
        ),
	    array(
	        'element'   => 'cancel',
	        'type'      => 'button',
	        'label'     => 'Cancel',
	        'attributes'=> array('onClick' => 'window.close()'),
	        'groupit'   => TRUE
	    ),
	    array(
	        'element'   => 'submitter',
	        'type'      => 'button',
	        'label'     => 'OK',
	        'attributes'=> array('onClick' => 'this.form.submit()'),
	        'groupit'   => TRUE
	    ),
        array(
            'group'     => array('cancel', 'submitter')
        )
    ),
    'PL.import'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'label'     => 'PL.import'
        ),
        array(
            'element'   => 'playlist',
            'type'      => 'file',
            'label'     => 'Playlist file',
            'required'  => TRUE,
            'requiredmsg'=> 'Please select playlist file'
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => "location.href='".UI_BROWSER."'"),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'submitter',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'submitter')
        )
    )
);
?>