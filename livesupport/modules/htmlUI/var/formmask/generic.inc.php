<?php
$ui_fmask = array(
    /* ===================== list of system preferences which can be adjusted */
    'stationPrefs'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'changeStationPrefs'
        ),
        array(
            'element'   => 'basics',
            'type'      => 'header',
            'label'     => 'Station Settings',
        ),
        array(
            'rule'      => 'nopunctuation',
            'element'   => 'stationMaxfilesize',
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
            'label'     => 'Station Name',
            'required'  => TRUE
        ),
        array(
            'element'   => 'stationLogoPath',
            'isPref'    => TRUE,
            'type'      => 'hidden',
            'label'     => 'Station Logo Path',
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
            'label'     => 'Station Logo',
            'requiredmsg'=> 'please select Logo file',
            'attributes'=> array('multiple' => 'application/pdf')
        ),
        array(
            'element'   => 'systemsettings',
            'type'      => 'header',
            'label'     => 'System Settings'
        ),
        array(
            'element'   => UI_SCRATCHPAD_MAXLENGTH_KEY,
            'isPref'    => TRUE,
            'type'      => 'select',
            'label'     => 'Maximum length of ScratchPad',
            'options'   => array(
                            5   => 5,
                            10  => 10,
                            25  => 25
                           ),
            'required'  => TRUE
        ),
        array(
            'element'   => 'stationMaxfilesize',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Reduce Upload Filesize',
            'rule'      => 'numeric',
            'attributes'   => array(
                                'onClick'  => 'alert ("'.tra('Note: System Maximum is set to $1 in php.ini. You can just reduce this amount here.',
                                                            ini_get('upload_max_filesize')).'")'
                           )
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit'
        )
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
            'label'     => 'Old Password',
            'required'  => TRUE,
        ),
        array(
            'element'   => 'pass',
            'type'      => 'password',
            'label'     => 'New Password',
            'required'  => TRUE,
        ),
        array(
            'element'   => 'pass2',
            'type'      => 'password',
            'label'     => 'Retype Password',
            'required'  => TRUE,
        ),
        array(
            'rule'      => 'compare',
            'element'   => array('pass','pass2'),
            'rulemsg'   => 'The passwords do not match'
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit'
        )

    ),

    'addUser' => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'addUser'
        ),
        array(
            'element'   => 'login',
            'type'      => 'text',
            'label'     => 'Username',
            'required'  => TRUE
        ),
        array(
            'element'   =>'pass',
            'type'      =>'password',
            'label'     =>'User Password',
            'required'  =>TRUE
        ),
        array(
            'element'   =>'pass2',
            'type'      =>'password',
            'label'     =>'Repeat Password',
            'required'  =>TRUE
        ),
        array(
            'rule'      =>'compare',
            'element'   =>array('pass','pass2'),
            'rulemsg'   =>'The passwords do not match'
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit'
        )
    ),

    'addGroup' => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'addGroup'
        ),
        array(
            'element'   => 'login',
            'type'      => 'text',
            'label'     => 'Group Name',
            'required'  => TRUE
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
            'required'  => TRUE
        ),
        array(
            'element'   => 'pass',
            'type'      => 'password',
            'label'     => 'Password',
            'required'  => TRUE,
            'requiredmsg' => ""
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
            'label'     => 'Media File',
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
            'label'     => 'Media File',
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
            'attributes'=> array('maxlength' => 256)
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
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit')
        )
    ),

    'search'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SEARCH.newSearch'
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
                            'or'    => 'Or',
                            'and'   => 'And',
                            )
        ),
        array(
            'element'   => 'filetype',
            'type'      => 'select',
            'label'     => 'File type',
            'options'   => array(
                            'File'      => '*',
                            'audioclip' => 'Audio Clip',
                            'webstream' => 'Web Stream',
                            'playlist'  => 'Playlist'
                            )
        ),
        array(
            'element'   => 'limit',
            'type'      => 'select',
            'label'     => 'Rows per Page',
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
            'label'     => 'Reset Criteria',
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
                'partial'    => 'partial',
                'full'       => 'full',
                'prefix'     => 'prefix',
                '='          => '=' ,
                '<'         => '<',
                '<='        => '<=',
                '>'         => '>',
                '>='        => '>='
        ),
        1             => array(
                'partial'    => 'partial',
                'full'       => 'full',
                'prefix'     => 'prefix',
                '='          => '='
        ),

    ),

    'languages'    => array(
            array(
                'element'  => 'langid',
                'type'     => 'select',
                'label'    => 'Language',
                'options'  => array(
                            'ar_JO'        => 'Arabic(JO)',
                            'am_AM'        => 'Armenian(AM)',
                            'en_UK'        => 'English (UK)',
                            'en_US'        => 'English (US)',
                            'es_CO'        => 'Español (CO)',
                            'cz_CZ'        => 'Česky (CZ)',
                            'de_DE'        => 'Deutsch (DE)',
                            'hu_HU'        => 'Magyar (HU)',
                            'nl_NL'        => 'Nederlands (NL)',
                            'sr_CS'        => 'Srpski (CS)',
                            'ru_RU'        => 'Russia(RU)'
                            )
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
                'onChange'  => 'this.form.act.value="BROWSE.setCategory"; this.form.submit()',
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
                'onChange'  => 'this.form.act.value="BROWSE.setValue"; this.form.submit()'
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
            'label'     => 'File Type',
            'options'   => array(
                            'File'      => '*',
                            'audioclip' => 'Audio Clip',
                            'webstream' => 'Web Stream',
                            'playlist'  => 'Playlist'
                            ),
            'attributes'=> array('onChange' =>  'hpopup("'.UI_HANDLER.'?act=BROWSE.setFiletype&filetype=" + this.form.filetype.value)')
        ),
        array(
            'element'   => 'limit',
            'type'      => 'select',
            'label'     => 'Rows per Page',
            'options'   => array(
                            10  => 10,
                            25  => 25,
                            50  => 50,
                            100 => 100
                           ),
            'attributes'=> array('onChange' => 'hpopup("'.UI_HANDLER.'?act=BROWSE.setLimit&limit=" + this.form.limit.value)')
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
            'label'     => 'Reset Criteria',
            'attributes'=> array('class' => 'button_wide', 'onClick' => 'hpopup("'.UI_HANDLER.'?act=BROWSE.setDefaults")'),
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
            'label'     => 'Search Library',
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
                'group'     => array('duration'),
                'label'     => 'Duration (ms)'
            ),
            array(
                'group'     => array('switchdown', 'switchup'),
                'label'     => '&nbsp;'
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
    )
);