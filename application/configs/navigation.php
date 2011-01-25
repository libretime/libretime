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
		'label'      => 'Now Playing',
		'module'     => 'default',
		'controller' => 'Nowplaying',
		'action'     => 'index'
	),
	array(
		'label'      => 'Schedule',
		'module'     => 'default',
		'controller' => 'Schedule',
		'action'     => 'index',
		'resource'   => 'schedule',
		'pages'      => array(
		    array(
		        'label'      => 'Add Show',
		        'module'     => 'default',
		        'controller' => 'Schedule',
		        'action'     => 'add-show-dialog',
				'resource'	=>	'schedule'
		    )
		)
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
