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
		'resource'	=>	'playlist',
		'pages'      => array(
		    array(
		        'label'      => 'New',
		        'module'     => 'default',
		        'controller' => 'Playlist',
		        'action'     => 'new',
				'resource'	=>	'playlist',
				'visible'    => false
		    ),
			array(
		        'label'      => 'Edit',
		        'module'     => 'default',
		        'controller' => 'Playlist',
		        'action'     => 'edit',
				'resource'	=>	'playlist',
				'visible'    => false
		    )
		)
	),
	array(
		'label'      => 'Media Library',
		'module'     => 'default',
		'controller' => 'Library',
		'action'     => 'index',
		'resource'	=>	'library',
		'pages'      => array(
		    array(
		        'label'      => 'Add Audio',
		        'module'     => 'default',
		        'controller' => 'Plupload',
		        'action'     => 'plupload',
				'resource'	=>	'plupload'
		    ),
			array(
		        'label'      => 'Search',
		        'module'     => 'default',
		        'controller' => 'Search',
		        'action'     => 'index',
				'resource'	=>	'search'
		    )
		)
	),
	array(
		'label'      => 'Test',
		'module'     => 'default',
		'controller' => 'Schedule',
		'action'     => 'get-scheduler-time'
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
		'action'     => 'logout',
		'resource'	=>	'login'
	)
);
 
// Create container from array
$container = new Zend_Navigation($pages);
 
//store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
