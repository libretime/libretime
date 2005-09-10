<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund

    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org

    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/audioClipFormat.php,v $

------------------------------------------------------------------------------*/

$audioClipFormat = array(
    '_root'=>'audioClip',
    'audioClip'=>array(
        'childs'=>array(
            'required'=>array('metadata'),
        ),
    ),
    'metadata'=>array(
        'childs'=>array(
            'required'=>array(
                'dc:title', 'dcterms:extent'
            ),
            'optional'=>array(
                'dc:identifier',
                'dc:creator', 'dc:source', 'ls:genre',
                'ls:year', 'dc:type', 'dc:description', 'dc:format',
                'ls:bpm', 'ls:rating', 'ls:encoded_by', 'ls:track_num',
                'ls:disc_num', 'ls:disc_num', 'dc:publisher', 'ls:composer',
                'ls:bitrate', 'ls:channels', 'ls:samplerate', 'ls:encoder',
                'ls:crc', 'ls:lyrics', 'ls:orchestra', 'ls:conductor',
                'ls:lyricist', 'ls:originallyricist', 'ls:radiostationname',
                'ls:audiofileinfourl', 'ls:artisturl', 'ls:audiosourceurl',
                'ls:radiostationurl', 'ls:buycdurl', 'ls:isrcnumber',
                'ls:catalognumber', 'ls:originalartist', 'dc:rights',
                'ls:license', 'dc:title', 'dcterms:temporal',
                'dcterms:spatial', 'dcterms:entity', 'dc:description',
                'dc:creator', 'dc:subject', 'dc:type', 'dc:format',
                'dc:contributor', 'dc:language', 'dc:rights',
                'dcterms:isPartOf', 'dc:date',
                'dc:publisher',
                // extra
                'dcterms:alternative', 'ls:filename', 'ls:mtime',
                // added lately by sebastian
                'ls:mood',
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
    'dc:identifier'=>array(
        'type'=>'Text',
        'auto'=>TRUE,
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
//        'regexp'=>'^\d{2}:\d{2}:\d{2}.\d{6}$',
        'regexp'=>'^((\d{1,2}:)?\d{1,2}:)?\d{1,20}(.\d{1,6})?$',
    ),
    'dc:creator'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:source'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:genre'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:year'=>array(
        'type'=>'Menu',
        'area'=>'Music',
    ),
    'dc:type'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:description'=>array(
        'type'=>'Longtext',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:format'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:bpm'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:rating'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:encoded_by'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:track_num'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:disc_num'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:disc_num'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:publisher'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:composer'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:bitrate'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:channels'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:samplerate'=>array(
        'type'=>'Menu',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:encoder'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:crc'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:lyrics'=>array(
        'type'=>'Longtext',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:orchestra'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:conductor'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:lyricist'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:originallyricist'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:radiostationname'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audiofileinfourl'=>array(
        'type'=>'URL',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:artisturl'=>array(
        'type'=>'URL',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audiosourceurl'=>array(
        'type'=>'URL',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:radiostationurl'=>array(
        'type'=>'URL',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:buycdurl'=>array(
        'type'=>'URL',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:isrcnumber'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:catalognumber'=>array(
        'type'=>'Number',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:originalartist'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:rights'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:license'=>array(
        'type'=>'Text',
        'area'=>'Music',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:title'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:temporal'=>array(
        'type'=>'Time/Date',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:spatial'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:entity'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:description'=>array(
        'type'=>'Longtext',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:creator'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:subject'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:type'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:format'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:contributor'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:language'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:rights'=>array(
        'type'=>'Menu',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:isPartOf'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:date'=>array(
        'type'=>'Date',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:publisher'=>array(
        'type'=>'Text',
        'area'=>'Talk',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    
    'ls:filename'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:mtime'=>array(
        'type'=>'Int',
    ),
/*
    ''=>array(
        'type'=>'',
        'area'=>'',
        'attrs'=>array(),
    ),
*/
);

/*
?
ls:filename                          Text       auto
*/
?>
