<?php

/*
* Navigation container (config/array)

* Each element in the array will be passed to
* Zend_Navigation_Page::factory() when constructing
* the navigation container below.
*/
$pages = array(
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
        'label' => _("Settings"),
        'resource' => 'preference',
        'action' => 'index',
        'module' => 'default',
        'controller' => 'preference',
        'pages' => array(
            array(
                'label'      => _('Preferences'),
                'module'     => 'default',
                'controller' => 'Preference'
            ),
            array(
                'label' => _('My Profile'),
                'controller' => 'user',
                'action' => 'edit-user',
                'resource' => 'user'
            ),
            array(
                'label'      => _('Users'),
                'module'     => 'default',
                'controller' => 'user',
                'action'     => 'add-user',
                'resource'   =>    'user'
            ),
            array(
                'label'      => _('Streams'),
                'module'     => 'default',
                'controller' => 'Preference',
                'action'     => 'stream-setting'
            )
        )
    ),
    array(
        'label' => _("Analytics"),
        'module'     => 'default',
        'controller' => 'listenerstat',
        'action'     => 'index',
        'resource'   => 'listenerstat',
        'pages' => array(
            array(
                'label'      => _('Listener Stats'),
                'module'     => 'default',
                'controller' => 'listenerstat',
                'action'     => 'index',
                'resource'   => 'listenerstat'
            ),
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
            )
        )
    ),
    array(
        'label'      => _('Widgets'),
        'module'     => 'default',
        'controller' => 'embeddablewidgets',
        'action'     => 'player',
        'pages' => array(
            array(
                'label'      => _('Player'),
                'module'     => 'default',
                'controller' => 'embeddablewidgets',
                'action'     => 'player',
            ),
            array(
                'label'      => _('Weekly Schedule'),
                'module'     => 'default',
                'controller' => 'embeddablewidgets',
                'action'     => 'schedule',
            )
        )
    ),
    array(
        'label'      => _('Help'),
        'controller' => 'dashboard',
        'action'     => 'help',
        'resource'    =>    'dashboard',
        'pages'      => array(
            array(
                'label'      => _('Getting Started'),
                'module'     => 'default',
                'controller' => 'dashboard',
                'action'     => 'help',
                'resource'   =>    'dashboard'
            ),
            array(
                'label'      => _('Help Center'),
                'uri'        => HELP_URL,
                'target'     => "_blank"
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
        'controller' => 'billing',
        'action' => 'client',
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
