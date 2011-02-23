<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */


$playlistFormat = array(
    '_root'=>'playlist',
    'playlist'=>array(
        'childs'=>array(
            // 'repeatable'=>array('playlistElement'),
            'optional'=>array('metadata', 'playlistElement'),
        ),
        'attrs'=>array(
            'required'=>array('id'),
            'implied'=>array('title', 'playlength'),
        ),
    ),
    'playlistElement'=>array(
        'childs'=>array(
            'oneof'=>array('audioClip', 'playlist'),
            'optional'=>array('fadeInfo'),
        ),
        'attrs'=>array(
            'required'=>array('id', 'relativeOffset', 'clipStart', 'clipEnd', 'clipLength'),
        ),
    ),
    'audioClip'=>array(
        'childs'=>array(
            'optional'=>array('metadata'),
        ),
        'attrs'=>array(
            'implied'=>array('id', 'title', 'playlength', 'uri'),
        ),
    ),
    'fadeInfo'=>array(
        'attrs'=>array(
            'required'=>array('id', 'fadeIn', 'fadeOut'),
        ),
    ),
    'metadata'=>array(
        'childs'=>array(
            'optional'=>array(
                'dc:title', 'dcterms:extent', 'dc:creator', 'dc:description',
                'dcterms:alternative', 'ls:filename', 'ls:mtime',
            ),
        ),
        'namespaces'=>array(
            'dc'=>"http://purl.org/dc/elements/1.1/",
            'dcterms'=>"http://purl.org/dc/terms/",
            'xbmf'=>"http://www.streamonthefly.org/xbmf",
            'xsi'=>"http://www.w3.org/2001/XMLSchema-instance",
            'xml'=>"http://www.w3.org/XML/1998/namespace",
        ),
    ),
    'dc:title'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:alternative'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:extent'=>array(
        'type'=>'Time',
        'regexp'=>'^\d{2}:\d{2}:\d{2}.\d{6}$',
    ),
    'dc:creator'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:description'=>array(
        'type'=>'Longtext',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'playlength'=>array(
        'type'=>'Time',
        'regexp'=>'^((\d{2}:)?\d{2}:)?\d{1,2}(.\d{6})?$',
    ),
    'id'=>array(
        'type'=>'Attribute',
        'regexp'=>'^[0-9a-f]{16}$',
    ),
    'fadeIn'=>array(
        'type'=>'Attribute',
        'regexp'=>'^((\d{2}:)?\d{2}:)?\d{1,2}(.\d{6})?$',
    ),
    'fadeOut'=>array(
        'type'=>'Attribute',
        'regexp'=>'^((\d{2}:)?\d{2}:)?\d{1,2}(.\d{6})?$',
    ),
    'ls:filename'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:mtime'=>array(
        'type'=>'Int',
//        'regexp'=>'^\d{4}(-\d{2}(-\d{2}(T\d{2}:\d{2}(:\d{2}\.\d+)?(Z)|([\+\-]?\d{2}:\d{2}))?)?)?$',
    ),
/*
    ''=>array(
        'childs'=>array(''),
        'attrs'=>array('implied'=>array()),
    ),
*/
);

