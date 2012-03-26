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
        'controller' => 'Showbuilder',
        'action'     => 'index',
        'resource'	=>	'showbuilder'
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
        'label'      => 'System',
        'uri' => '#',
        'resource' => 'preference',
        'pages'      => array(
            array(
                'label'      => 'Preferences',
                'module'     => 'default',
                'controller' => 'Preference'
            ),
            array(
                'label'      => 'Users',
                'module'     => 'default',
                'controller' => 'user',
                'action'     => 'add-user',
                'resource'	=>	'user'
            ),
            array(
                'label'      => 'Media Folders',
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'directory-config',
                'id'		 => 'manage_folder'
            ),
            array(
                'label'      => 'Streams',
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'stream-setting'
            ),
            array(
                'label'      => 'Support Feedback',
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'support-setting'
            ),
            array(
                'label'      => 'Status',
                'module'     => 'default',
                'controller' => 'systemstatus',
                'action'     => 'index',
                'resource'	=>	'systemstatus'
            ),
        	array(
        		'label'      => 'Playout History',
        		'module'     => 'default',
        		'controller' => 'playouthistory',
        		'action'     => 'index',
        		'resource'	=>	'playouthistory'
        	)
        )
    ),
	array(
		'label'      => 'Help',
		'uri'     => '#',
		'resource'	=>	'dashboard',
        'pages'      => array(
            array(
                'label'      => 'Getting Started',
                'module'     => 'default',
                'controller' => 'dashboard',
                'action'     => 'help',
                'resource'   =>	'dashboard'
            ),
            array(
                'label'      => 'User Manual',
                'uri'        => "http://www.sourcefabric.org/en/airtime/manuals/",
                'target'     => "_blank"
            ),
            array(
                'label'      => 'About',
                'module'     => 'default',
                'controller' => 'dashboard',
                'action'     => 'about',
                'resource'   =>	'dashboard'
            )
        )
	)
);


// Create container from array
$container = new Zend_Navigation($pages);
$container->id = "nav";

//store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
