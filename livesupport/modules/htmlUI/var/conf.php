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


    Author   : $Author: sebastian $
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/htmlUI/var/Attic/conf.php,v $

------------------------------------------------------------------------------*/

/**
 *  configuration structure:
 *  <dl>
 *   <dt>dsn<dd> datasource setting
 *   <dt>tblNamePrefix <dd>prefix for table names in the database
 *   <dt>authCookieName <dd>secret token cookie name
 *   <dt>storageDir <dd>main directory for storing binary media files
 *   <dt>bufferDir <dd>directory for temporary files
 *   <dt>transDir <dd>directory for incomplete transferred files
 *   <dt>accessDir <dd>directory for symlinks to accessed files
 *   <dt>storageUrlPath<dd>path-URL-part of storageServer base dir
 *   <dt>storageXMLRPC<dd>XMLRPC server script address relative to storageUrlPath
 *   <dt>storageUrlHost, storageUrlPort<dd>host and port of storageServer
 *   <dt>archiveUrlPath<dd>path-URL-part of archiveServer base dir
 *   <dt>archiveXMLRPC<dd>XMLRPC server script address relative to archiveUrlPath
 *   <dt>archiveUrlHost, archiveUrlPort<dd>host and port of archiveServer
 *  </dl>
 */
$config = array(
    /* ================================================== basic configuration */
    'dsn'           => array(
        'username'      => 'test',
        'password'      => 'test',
        'hostspec'      => 'localhost',
        'phptype'       => 'pgsql',
        'database'      => 'LiveSupport-test',
    ),
    'tblNamePrefix' => 'ls_',
    'authCookieName'=> 'lssid',
    #'storageDir'    =>  dirname(getcwd()).'/stor',
    #'bufferDir'     =>  dirname(getcwd()).'/stor/buffer',
    #'transDir'      =>  dirname(getcwd()).'/trans',
    #'accessDir'     =>  dirname(getcwd()).'/access',
    'storageDir'    =>  dirname(__FILE__).'/../../storageServer/var/stor',
    'bufferDir'     =>  dirname(__FILE__).'/../../storageServer/var/stor/buffer',
    'transDir'      =>  dirname(__FILE__).'/../../storageServer/var/trans',
    'accessDir'     =>  dirname(__FILE__).'/../../storageServer/var/access',

    /* ==================================================== URL configuration */
    'storageUrlPath'        => '/livesupportStorageServer',
    'storageXMLRPC'         => 'xmlrpc/xrLocStor.php',
    'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,

    /* ================================================ archive configuration */
    'archiveUrlPath'        => '/livesupportArchiveServer',
    'archiveXMLRPC'         => 'xmlrpc/xrArchive.php',
    'archiveUrlHost'        => 'localhost',
    'archiveUrlPort'        => 80,
    'archiveAccountLogin'   => 'root',
    'archiveAccountPass'    => 'q',

    'objtypes'      => array(
        'RootNode'      => array('Folder'),
        'Storage'       => array('Folder', 'File', 'Replica'),
        'Folder'        => array('Folder', 'File', 'Replica'),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
        'Replica'       => array(),
    ),
    'allowedActions'=> array(
        'RootNode'      => array('classes', 'subjects'),
        'Folder'        => array('editPrivs', 'write', 'read'),
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
        'Replica'       => array('editPrivs', 'write', 'read'),
        '_class'        => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', 'classes', 'subjects'
    ),

    /* ============================================== auxiliary configuration */
    'RootNode'        => 'RootNode',
    'tmpRootPass'   => 'q',
);




define('UI_HANDLER', 'ui_handler.php');
define('UI_BROWSER', 'ui_browser.php');
define('UI_FORM_STANDARD_METHOD', 'POST');
define('UI_INPUT_STANDARD_SIZE', 20);
define('UI_INPUT_STANDARD_MAXLENGTH', 50);
define('UI_TEXTAREA_STANDART_ROWS', 5);
define('UI_TEXTAREA_STANDART_COLS', 17);
define('UI_QFORM_REQUIRED',     'templates/form_parts/required.tpl');
define('UI_QFORM_REQUIREDNOTE', 'templates/form_parts/requirednote.tpl');
define('UI_QFORM_ERROR',        'templates/form_parts/error.tpl');
define('UI_SEARCH_MAX_ROWS', 8);
define('UI_SEARCH_MIN_ROWS', 2);
define('UI_REGEX_URL', '/^(ht|f)tps?:\/\/[^ ]+$/');
?>