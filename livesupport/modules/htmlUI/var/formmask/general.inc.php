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
            'label'     => 'Basic Settings',
        ),
        array(
            'element'   => 'stationMaxfilesize',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Reduce Upload Filesize<br><small>(must be smaller than ' .ini_get('upload_max_filesize').')</small>',
            'rule'      => 'numeric',
            'attributes'   => array(
                                'onClick'  => 'alert ("Note: System Maximum is set to '.
                                                ini_get('upload_max_filesize')
                                                .' in php.ini\n You can just reduce this amount this here.")'
                           )
        ),
        array(
            'rule'      => 'nopunctuation',
            'element'   => 'maxfilesize',
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
            'label'     => 'Staion Name',
            'required'  => TRUE
        ),
        array(
            'element'   => 'stationLogoPath',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Station Logo path',
            'default'   => 'img/logo.jpg',
            'required'  => TRUE
        ),
        array(
            'rule'      => 'regex',
            'element'   => 'stationLogoPath',
            'format'    => '/^img\/[a-z0-9.-_]*$/',
            'rulemsg'   => 'Path seems invalid'
        ),
        array(
            'element'   => 'stationURL',
            'isPref'    => TRUE,
            'type'      => 'text',
            'label'     => 'Station URL',
            'default'   => 'http://',
            'required'  => TRUE
        ),
        array(
            'rule'      => 'regex',
            'element'   => 'stationURL',
            'format'    => UI_REGEX_URL,
            'rulemsg'   => 'URL seems not to be valid'
        ),
        array(
            'element'   => UI_SCRATCHPAD_MAXLENGTH_KEY,
            'isPref'    => TRUE,
            'type'      => 'select',
            'label'     => 'Maximun length of ScratchPad',
            'options'   => array(
                            0   => '--',
                            5   => 5,
                            10  => 10,
                            20  => 20
                           )
        ),
        array(
            'element'   => 'upload',
            'type'      => 'header',
            'label'     => 'Upload'
        ),
        array(
            'element'   => 'stationlogo',
            'type'      => 'file',
            'label'     => 'Station Logo',
            'requiredmsg'=> 'please select Logo file'
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
            'rulemsg'   => 'The Passwords do not match'
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
            'label'     =>'Users Password',
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
            'rulemsg'   =>'The Passwords do not match'
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
            'required'  => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit'
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'window.close()')
        )
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
            'label'     => 'Mediafile',
            'required'  => TRUE,
            'requiredmsg'=> 'please select Media file'
        ),
        array(
            'element'   => 'mdatafile',
            'type'      => 'file',
            'label'     => 'Metadata',
            'required'  => TRUE,
            'requiredmsg'=> 'please select Metadata file'
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit'
        )
    ),

    'uploadFile'    => array(
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
            'label'     => 'Mediafile',
            'required'  => TRUE,
            'requiredmsg'=> 'please select Media file'
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit'
        )
    ),

    'addWebstream'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
        ),
        array(
            'element'   => 'id',
            'type'      => 'hidden'
        ),
        array(
            'element'   => 'name',
            'type'      => 'text',
            'label'     => 'Name',
            'required'  => TRUE,
            'rule'      => 'alphanumeric',
            'rulemsg'   => 'Name must be alphanumeric'
        ),
        array(
            'element'   => 'url',
            'type'      => 'text',
            'default'   => 'http://',
            'label'     => 'Stream URL',
            'required'  => TRUE,
            'requiredmsg'=> 'URL is missing',
            'rule'      => 'regex',
            'format'    => UI_REGEX_URL,
            'rulemsg'   => 'URL seems invalid',
        ),
        array(
            'element'   => 'duration',
            'type'      => 'date',
            'label'     => 'Duration',
            'options'   => array(
                            'format'    => 'His',
                            )
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit'
        )
    ),

    'search'    => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SEARCH.newsearch'
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
            'label'     => 'Filetype',
            'options'   => array(
                            'File'      => '*',
                            'audioclip' => 'Audioclip',
                            'webstream' => 'Webstream',
                            'playlist'  => 'Playlist'
                            )
        ),
        array(
            'element'   => 'limit',
            'type'      => 'select',
            'label'     => 'Rows per Page',
            'options'   => array(
                                1   => 1,
                                5   => 5,
                                10  => 10,
                                25  => 25,
                                50  => 50,
                                100 => 100
                           )
        ),
        array(
            'element'   => 'clear',
            'type'      => 'button',
            'label'     => 'Clear',
            'attributes'  => array('onClick' => "this.form.reset(); hpopup('".UI_HANDLER."?act=SEARCH.clear', 'SF')"),
            'groupit'   => TRUE,
        ),
        array(
            'element'   => 'addrow',
            'type'      => 'button',
            'label'     => 'One more Row',
            'attributes'  => array('onClick' => 'SearchForm_addRow()'),
            'groupit'   => TRUE,
        ),
        array(
            'element'   => 'submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE,
        ),
        array('group'   => array('clear', 'addrow', 'submit')
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
        1  => array(
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
                                'en'    => 'English',
                                'cz'    => 'Czech',
                                'de'    => 'German',
                                'hu'    => 'Hungarian',
                              )
            )
    )
); 
