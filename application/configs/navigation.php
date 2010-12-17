<?php

/*
* Navigation container (config/array)
 
* Each element in the array will be passed to
* Zend_Navigation_Page::factory() when constructing
* the navigation container below.
*/
$pages = array(
	array(
		'label'      => 'Home',
		'module'     => 'default',
		'controller' => 'index',
		'action'     => 'index',
		'order'      => -100 // make sure home is the first page
	),
	array(
		'label'      => 'Add User',
		'module'     => 'default',
		'controller' => 'user',
		'action'     => 'add-user',
		'resource'	=>	'user'	
	),
	array(
		'label'      => 'Playlists',
		'module'     => 'default',
		'controller' => 'Playlist',
		'action'     => 'index',
		'pages'      => array(
		    array(
		        'label'      => 'New',
		        'module'     => 'default',
		        'controller' => 'Playlist',
		        'action'     => 'new',
				'visible'    => false
		    ),
			array(
		        'label'      => 'Edit',
		        'module'     => 'default',
		        'controller' => 'Playlist',
		        'action'     => 'edit',
				'visible'    => false
		    )
		)
	),
	array(
		'label'      => 'Media Library',
		'module'     => 'default',
		'controller' => 'Library',
		'action'     => 'index',
		'pages'      => array(
		    array(
		        'label'      => 'Add Audio',
		        'module'     => 'default',
		        'controller' => 'Plupload',
		        'action'     => 'plupload'
		    ),
			array(
		        'label'      => 'Search',
		        'module'     => 'default',
		        'controller' => 'Search',
		        'action'     => 'display'
		    )
		)
	),
	array(
		'label'      => 'Schedule',
		'module'     => 'default',
		'controller' => 'Schedule',
		'action'     => 'index',
		'resource'   => 'schedule'
	),
	array(
		'label'      => 'Logout',
		'module'     => 'default',
		'controller' => 'Login',
		'action'     => 'logout'
	)
);
 
// Create container from array
$container = new Zend_Navigation($pages);
 
//store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
