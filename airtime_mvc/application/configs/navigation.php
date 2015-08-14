<?php

/*
* Navigation container (config/array)

* Each element in the array will be passed to
* Zend_Navigation_Page::factory() when constructing
* the navigation container below.
*/
$pages = array(
    /*array(
        'label'      => _('Now Playing'),
        'module'     => 'default',
        'controller' => 'Showbuilder',
        'action'     => 'index',
        'resource'   =>    'showbuilder'
    ),*/
    array(
        'label'      => _('Add Media'),
        'module'     => 'default',
        'controller' => 'Plupload',
        'action'     => 'index',
        'resource'   => 'plupload'
    ),/*
    array(
        'label'      => _('Library'),
        'module'     => 'default',
        'controller' => 'Library',
        'action'     => 'index',
        'resource'   =>    'playlist'
    ),*/
    array(
        'label'      => _('Calendar'),
        'module'     => 'default',
        'controller' => 'Schedule',
        'action'     => 'index',
        'resource'   =>    'schedule'
    ),
    array(
        'label' => _('Radio Page'),
        'uri' => '/',
        'resource' => '',
        'pages' => array(
        )
    ),
    array(
        'label'      => _('System'),
        'uri'        => '#',
        'resource'   => 'preference',
        'pages'      => array(
            array(
                'label'      => _('Preferences'),
                'module'     => 'default',
                'controller' => 'Preference'
            ),
            array(
                'label'      => _('Users'),
                'module'     => 'default',
                'controller' => 'user',
                'action'     => 'add-user',
                'resource'   =>    'user'
            ),
            array(
                'label'      => _('Media Folders'),
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'directory-config',
                'id'         => 'manage_folder'
            ),
            array(
                'label'      => _('Streams'),
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'stream-setting'
            ),
            array(
                'label'      => _('Status'),
                'module'     => 'default',
                'controller' => 'systemstatus',
                'action'     => 'index',
                'resource'   =>    'systemstatus'
            ),
            array(
                'label'      => _('Listener Stats'),
                'module'     => 'default',
                'controller' => 'listenerstat',
                'action'     => 'index',
                'resource'   => 'listenerstat'
            ),
            array(
                'label'      => _('Embeddable Widgets'),
                'module'     => 'default',
                'controller' => 'embeddablewidgets',
                'action'     => 'index'
            )
        )
    ),
	array(
		'label' => _('History'),
		'uri' => '#',
		'resource'   => 'playouthistory',
		'pages'      => array(
			array(
				'label'      => _('Playout History'),
				'module'     => 'default',
				'controller' => 'playouthistory',
				'action'     => 'index',
				'resource'   => 'playouthistory'
			),
			array(
				'label'      => _('History Templates'),
				'module'     => 'default',
				'controller' => 'playouthistorytemplate',
				'action'     => 'index',
				'resource'   => 'playouthistorytemplate'
			),
		)
	),
    array(
        'label'      => _('Help'),
        'uri'     => '#',
        'resource'    =>    'dashboard',
        'pages'      => array(
            array(
                'label'      => _('Help Center'),
                'uri'        => HELP_URL,
                'target'     => "_blank"
            ),
            array(
                'label'      => _('Getting Started'),
                'module'     => 'default',
                'controller' => 'dashboard',
                'action'     => 'help',
                'resource'   =>    'dashboard'
            ),
            array(
                'label'      => _('FAQ'),
                'uri'        => FAQ_URL,
                'target'     => "_blank"
            ),
            array(
                'label'      => _('User Manual'),
                'uri'        => USER_MANUAL_URL,
                'target'     => "_blank"
            ),
            array(
                'label'      => _('About'),
                'module'     => 'default',
                'controller' => 'dashboard',
                'action'     => 'about',
                'resource'   =>    'dashboard'
            ),
            array(
                'label'      => _(sprintf("Help Translate %s", PRODUCT_NAME)),
                'uri'        => AIRTIME_TRANSIFEX_URL,
                'target'     => "_blank"
            )
        )
    ),
    array(
        'label' => _('Billing'),
        'uri' => '#',
        'resource' => 'billing',
        'pages' => array(
            array(
                'label' => _('Account Details'),
                'module' => 'default',
                'controller' => 'billing',
                'action' => 'client',
                'resource' => 'billing'
            ),
            array(
                'label' => _('Account Plans'),
                'module' => 'default',
                'controller' => 'billing',
                'action' => 'upgrade',
                'resource' => 'billing'
            ),
            array(
                'label' => _('View Invoices'),
                'module' => 'default',
                'controller' => 'billing',
                'action' => 'invoices',
                'resource' => 'billing'
            )
        )
    )
);


// Create container from array
$container = new Zend_Navigation($pages);
$container->id = "nav";

//store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
