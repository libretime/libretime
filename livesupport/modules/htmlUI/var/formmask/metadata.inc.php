<?php    /* =========================================================== Matadata-Mask */
$mask = array(
    'basics' => array(
        array(
            'element' => 'act',
            'type'    => 'hidden',
            'constant'=> 'editMetaData'
        ),
        array(
            'element' => 'id',
            'type'    => 'hidden'
        ),
        array(
            'element' => 'langid',
            'type'    => 'hidden'
        ),
    ),
    'buttons' => array (
        array(
            'element' =>'reset',
            'type'    =>'reset',
            'label'   =>'Reset',
            'groupit' => TRUE,
        ),
        array(
            'element' =>'button',
            'type'    =>'button',
            'label'   =>'Submit',
            'groupit' => TRUE,
            'attributes'=> array(
                            'onClick' => 'return switchMDataLang();'
                          ),
        ),
        array(
            'group'   => array('reset', 'button'),
        )
    ),
    'tabs'  => array(
        array(
            'element' => 'Main',
            'type'    => 'button',
            'label'   => 'Main',
            'groupit' => TRUE,
            'attributes' => array('onClick' => 'showMain()')
        ),
        array(
            'element' => 'Music_Basic',
            'type'    => 'button',
            'label'   => 'Music_Basic',
            'groupit' => TRUE,
            'attributes' => array('onClick' => 'showMusic_Basic()')
        ),
        array(
            'element' => 'Music_Advanced',
            'type'    => 'button',
            'label'   => 'Music_Advanced',
            'groupit' => TRUE,
            'attributes' => array('onClick' => 'showMusic_Advanced()')
        ),
        array(
            'element' => 'Talk_Basic',
            'type'    => 'button',
            'label'   => 'Talk_Basic',
            'groupit' => TRUE,
            'attributes' => array('onClick' => 'showTalk_Basic()')
        ),
        array(
            'element' => 'Talk_Advanced',
            'type'    => 'button',
            'label'   => 'Talk_Advanced',
            'groupit' => TRUE,
            'attributes' => array('onClick' => 'showTalk_Advanced()')
        ),
        'group' => array(
            'group'   => array('Main', 'Music_Basic', 'Music_Advanced', 'Talk_Basic', 'Talk_Advanced'),
        )

    ),
    'langswitch'    => array(
        array(
            'element'  => 'langid',
            'type'     => 'select',
            'label'    => 'Language',
            'options'  => array(
                            'en'    => 'English',
                            'cz'    => 'Czech',
                            'de'    => 'German',
                            'hu'    => 'Hungarian',
                          ),
            'attributes'=> array('onChange'   => 'return switchMDataLang()')
        )
    ),
    'pages' => array(
        'Main'  => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
                'required'  => TRUE
            ),
            array(
                'element'   => 'Creator',
                'type'      => 'text',
                'label'     => 'Creator',
                #'required'  => TRUE,
            ),
            array(
                'element'   => 'Type_Genre',
                'type'      => 'text',
                'label'     => 'Type_Genre',
                #'required'  => TRUE,
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'Format',
                #'required'  => TRUE,
                'options'   => array(
                    ''              => '',
                    'audio/mpeg'    => 'audio/mpeg',
                    'File'          => 'File',
                    'live stream'   => 'Live Stream',
                    'networked file'=> 'Networked File',
                 )
            ),
            array(
                'element'   => 'dcterms:extent',
                'type'      => 'text',
                'label'     => 'Extent',
                #'attributes'=> array('readonly' => 'on')
            ),
            /*
            array(
                'element'   => 'Format_Extent_h',
                'type'      => 'select',
                'options'   => getDArr('h'),
                'groupit'   => TRUE
            ),
            array(
                'element'   => 'Format_Extent_m',
                'type'      => 'select',
                'options'   => getDArr('m'),
                'groupit'   => TRUE
            ),
            array(
                'element'   => 'Format_Extent_s',
                'type'      => 'select',
                'options'   => getDArr('h'),
                'groupit'   => TRUE
            ),
            array(
                'group'     => array('Main__Format_Extent_h', 'Main__Format_Extent_m', 'Main__Format_Extent_s'),
                #'name'      => 'gr_Format_Extent',
                'label'     => 'Format_Extent',
                'rule'      => 'required',
                #'grouprule' => 'regex',
                #'format'    => '/([1-9]0)|([1-9]{2})|(0[1-9])/',
                #'arg1'      => 'Please enter Format_Extent',
                #'howmany'   => 1
            ), */
        ),
        'Music_Basic'  => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
            ),
            array(
                'element'   => 'dc:creator',
                'type'      => 'text',
                'label'     => 'Creator',
            ),
            array(
                'element'   => 'Source_Album',
                'type'      => 'text',
                'label'     => 'Source_Album',
            ),
            /*
            array(
                'element'   => 'Source_Year',
                'type'      => 'date',
                'label'     => 'Source_Year',
                'options'   => array(
                    'language'      => 'en',
                    'format'        => 'dMY',
                    'addEmptyOption'=> TRUE,
                    'minYear'       => 1900
                )
            ),
            */
            array(
                'element'   => 'Type_Genre',
                'type'      => 'text',
                'label'     => 'Type_Genre',
            ),
            array(
                'element'   => 'dc:description',
                'type'      => 'textarea',
                'label'     => 'Description',
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'Format',
                'options'   => array(
                    ''              => '',
                    'audio/mpeg'    => 'audio/mpeg',
                    'File'          => 'File',
                    'live stream'   => 'Live Stream',
                    'networked file'=> 'Networked File'
                )
            ),
            array(
                'element'   => 'Type_BPM',
                'type'      => 'text',
                'label'     => 'Type_BPM',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'Description_Rating',
                'type'      => 'text',
                'label'     => 'Description_Rating',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'dcterms:extent',
                'type'      => 'text',
                'label'     => 'Extent',
                'attributes'=> array('readonly' => 'on')
            ),
        ),
        'Music_Advanced'=> array(
            array(
                'element'   => 'Creator_Role_Encoder',
                'type'      => 'text',
                'label'     => 'Creator_Role_Encoder',
            ),
            array(
                'element'   => 'Source_Album_TrackNumber',
                'type'      => '',
                'label'     => 'Source_Album_TrackNumber',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'Source_Album_DiscNumber',
                'type'      => 'text',
                'label'     => 'Source_Album_DiscNumber',
                'rule'      => 'numeric',
            ),
            array(
                'element'   => 'Description_Mood',
                'type'      => 'text',
                'label'     => 'Description_Mood',
            ),
            array(
                'element'   => 'Publisher',
                'type'      => 'text',
                'label'     => 'Publisher',
            ),
            array(
                'element'   => 'Creator_Role_Composer',
                'type'      => 'text',
                'label'     => 'Creator_Role_Composer',
            ),
            array(
                'element'   => 'Format_Medium_Bitrate',
                'type'      => 'text',
                'label'     => 'Format_Medium_Bitrate',
                'rule'      => 'numeric'
            ),
            array(
                'element'   => 'Format_Medium_Channels',
                'type'      => 'select',
                'label'     => 'Format_Medium_Channels',
                'options'   => array(
                    ''       => '',
                    'mono'   => 'Mono',
                    'stereo' => 'Stereo',
                    '5.1'    => '5.1'
                )
            ),
            array(
                'element'   => 'Format_Medium_Samplerate',
                'type'      => 'text',
                'label'     => 'Format_Medium_Samplerate',
                'rule'      => 'numeric'
            ),
            array(
                'element'   => 'Format_Medium_Encoder',
                'type'      => 'text',
                'label'     => 'Format_Medium_Encoder',
            ),
            array(
                'element'   => 'Format_CRC',
                'type'      => 'text',
                'label'     => 'Format_CRC',
                'rule'      => 'numeric'
            ),
            array(
                'element'   => 'Description_Lyrics',
                'type'      => 'textarea',
                'label'     => 'Description_Lyrics',
            ),
            array(
                'element'   => 'Creator_Role_Orchestra',
                'type'      => 'text',
                'label'     => 'Creator_Role_Orchestra',
            ),
            array(
                'element'   => 'Creator_Role_Conductor',
                'type'      => 'text',
                'label'     => 'Creator_Role_Conductor',
            ),
            array(
                'element'   => 'Creator_Role_Lyricist',
                'type'      => 'text',
                'label'     => 'Creator_Role_Lyricist',
            ),
            array(
                'element'   => 'Creator_Role_OriginalLyricist',
                'type'      => 'text',
                'label'     => 'Creator_Role_OriginalLyricist',
            ),
            array(
                'element'   => 'Creator_Role_RadioStationName',
                'type'      => 'text',
                'label'     => 'Creator_Role_RadioStationName',
            ),
            array(
                'element'   => 'Description_AudioFileInfoURL',
                'type'      => 'text',
                'label'     => 'Description_AudioFileInfoURL',
            ),
            array(
                'element'   => 'Description_ArtistURL',
                'type'      => 'text',
                'label'     => 'Description_ArtistURL',
            ),
            array(
                'element'   => 'Description_AudioSourceURL',
                'type'      => 'text',
                'label'     => 'Description_AudioSourceURL',
            ),
            array(
                'element'   => 'Description_RadioStationURL',
                'type'      => 'text',
                'label'     => 'Description_RadioStationURL',
            ),
            array(
                'element'   => 'Description_BuyCDURL',
                'type'      => 'text',
                'label'     => 'Description_BuyCDURL',
            ),
            array(
                'element'   => 'Identifier_ISRCNumber',
                'type'      => 'text',
                'label'     => 'Identifier_ISRCNumber',
                'rule'      => 'numeric'
            ),
            array(
                'element'   => 'Identifier_CatalogNumber',
                'type'      => 'text',
                'label'     => 'Identifier_CatalogNumber',
                'rule'      => 'numeric'
            ),
            array(
                'element'   => 'Creator_Role_OriginalArtist',
                'type'      => 'text',
                'label'     => 'Creator_Role_OriginalArtist',
            ),
            array(
                'element'   => 'Rights_Copyright',
                'type'      => 'text',
                'label'     => 'Rights_Copyright',
            ),
        ),
        'Talk_Basic'   => array(
            array(
                'element'   => 'dc:title',
                'type'      => 'text',
                'label'     => 'Title',
            ),
            array(
                'element'   => 'Coverage',
                'type'      => 'text',
                'label'     => 'Coverage',
            ),
            array(
                'element'   => 'dc:description',
                'type'      => 'textarea',
                'label'     => 'Description',
            ),
            array(
                'element'   => 'Creator',
                'type'      => 'text',
                'label'     => 'Creator',
            ),
            array(
                'element'   => 'Subject',
                'type'      => 'text',
                'label'     => 'Subject',
            ),
            array(
                'element'   => 'Type_Genre',
                'type'      => 'text',
                'label'     => 'Type_Genre',
            ),
            array(
                'element'   => 'dc:format',
                'type'      => 'select',
                'label'     => 'Format',
                'options'   => array(
                    ''              => '',
                    'audio/mpeg'    => 'audio/mpeg',
                    'File'          => 'File',
                    'live stream'   => 'Live Stream',
                    'networked file'=> 'Networked File',
                )
            ),
        ),
        'Talk_Advanced' => array(
            array(
                'element'   => 'Contributor',
                'type'      => 'text',
                'label'     => 'Contributor',
            ),
            array(
                'element'   => 'Language',
                'type'      => 'text',
                'label'     => 'Language',
            ),
            array(
                'element'   => 'Rights',
                'type'      => 'text',
                'label'     => 'Rights',
            ),
        )
    )
);
?>