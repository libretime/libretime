<?php

/*
* Navigation container (config/array)

* Each element in the array will be passed to
* Zend_Navigation_Page::factory() when constructing
* the navigation container below.
*/
$pages = array(
	array(
        'label'      => 'Now Playing',
        'module'     => 'default',
        'controller' => 'Nowplaying',
        'action'     => 'index',
        'resource'	=>	'nowplaying'
	),
	array(
		'label'      => 'Add Media',
		'module'     => 'default',
		'controller' => 'Plupload',
		'action'     => 'index',
		'resource'	=>	'plupload'
	),
	array(
		'label'      => 'Playlist Builder',
		'module'     => 'default',
		'controller' => 'Library',
		'action'     => 'index',
		'resource'	=>	'library'
	),
	array(
		'label'      => 'Calendar',
        'module'     => 'default',
        'controller' => 'Schedule',
        'action'     => 'index',
        'resource'	=>	'schedule'
	),
    array(
        'label'      => 'Configure',
        'uri' => '#',
        'resource' => 'preference',
        'pages'      => array(
            array(
                'label'      => 'Preferences',
                'module'     => 'default',
                'controller' => 'Preference'
            ),
            array(
                'label'      => 'Manage Users',
                'module'     => 'default',
                'controller' => 'user',
                'action'     => 'add-user',
                'resource'	=>	'user'
            ),
            array(
                'label'      => 'Manage Media Folders',
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'directory-config'
            ),
            array(
                'label'      => 'Stream Setting',
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'stream-setting'
            )
        )
    ),
	array(
		'label'      => 'Help',
		'module'     => 'default',
		'controller' => 'dashboard',
		'action'     => 'help',
		'resource'	=>	'dashboard'
	)
);


// Create container from array
$container = new Zend_Navigation($pages);
$container->id = "nav";

//store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
