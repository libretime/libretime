<?php
/**
 * @author $Author$
 * @version $Revision$
 */

$CC_CONFIG = array(
    'dsn'       => array(           // data source definition
        'username' => 'test',
        'password' => 'test',
        'hostspec' => 'localhost',
        'phptype'  => 'pgsql',
        'database' => 'Campcaster-test'
    ),
    'tblNamePrefix'     => 'al_',
#    'tblNamePrefix'     => 'gb_',
    'RootNode'	=>'RootNode',
    'objtypes'  => array(
        'RootNode'      => array('Publication'),
        'Publication'   => array('Issue'),
        'Issue'         => array('Title', 'Section'),
        'Section'       => array('Title', 'Image', 'Par')
    ),
    'allowedActions'=> array(
        'RootNode'      => array(
            'addChilds', 'editPerms', 'read', 'edit', 'delete',
            'classes', 'subjects'),
        'Publication'   => array(
            'addChilds', 'editPerms', 'read', 'edit', 'delete'),
        'Issue'         => array(
            'addChilds', 'editPerms', 'read', 'edit', 'delete'),
        'Section'       => array(
            'addChilds', 'editPerms', 'read', 'edit', 'delete'),
        'Title'         => array('editPerms', 'read', 'edit', 'delete'),
        'Image'         => array('editPerms', 'read', 'edit', 'delete'),
        'Par'           => array('editPerms', 'read', 'edit', 'delete'),
        '_class'        => array(
            'addChilds', 'editPerms', 'read', 'edit', 'delete')
    ),
    'allActions'=> array(
        'editPerms', 'addChilds', 'read', 'edit', 'delete',
        'classes', 'subjects')
);
?>