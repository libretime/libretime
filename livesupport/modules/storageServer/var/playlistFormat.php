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


    Author   : $Author: tomas $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/playlistFormat.php,v $

------------------------------------------------------------------------------*/

$playlistFormat = array(
    '_root'=>'playlist',
    'playlist'=>array(
        'childs'=>array(
            // 'repeatable'=>array('playlistelement'),
            'optional'=>array('metadata', 'playlistelement'),
        ),
        'attrs'=>array(
            'required'=>array('id', 'playlength'),
            'implied'=>array('title'),
        ),
    ),
    'playlistelement'=>array(
        'childs'=>array(
            'oneof'=>array('audioclip', 'playlist'),
            'optional'=>array('fadeinfo'),
        ),
        'attrs'=>array(
            'required'=>array('id', 'relativeoffset'),
        ),
    ),
    'audioclip'=>array(
        'childs'=>array(
            'optional'=>array('metadata'),
        ),
        'attrs'=>array(
            'implied'=>array('id', 'title', 'playlength'),
        ),
    ),
    'fadeinfo'=>array(
        'attrs'=>array(
            'required'=>array('id', 'fadein', 'fadeout'),
        ),
    ),
    'metadata'=>array(
        'childs'=>array(
            'optional'=>array(
                'dc:title', 'dcterms:extent', 'dc:creator', 'dc:description',
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
    'id'=>array(
        'type'=>'Attribute',
        'regexp'=>'^[0-9a-f]{16}$',
    ),
    'fadein'=>array(
        'type'=>'Attribute',
        'regexp'=>'^((\d{2}:)?\d{2}:)?\d{2}.\d{6}$',
    ),
    'fadeout'=>array(
        'type'=>'Attribute',
        'regexp'=>'^((\d{2}:)?\d{2}:)?\d{2}.\d{6}$',
    ),
/*
    ''=>array(
        'childs'=>array(''),
        'attrs'=>array('implied'=>array()),
    ),
*/
);

/*
?
ls:filename                          Text       auto
*/
?>
