<?php

declare(strict_types=1);

/**
 * Navigation container (config/array).
 *
 * Each element in the array will be passed to
 * Zend_Navigation_Page::factory() when constructing
 * the navigation container below.
 *
 * The class field is used to contain the icon rendered
 * before the nav entry for i18n purposes. When this
 * gets parsed the language is not yet set and the
 * strings stay plain en for now. They get retranslated
 * in the menu.phtml script when they are output.
 */
$pages = [];
// Disable My podcasts
// See https://github.com/libretime/libretime/issues/1320
// $pages[] = array(
//     'label'      => _('My Podcast'),
//     'module'     => 'default',
//     'controller' => 'podcast',
//     'action'     => 'station',
//     'resource'   => 'podcast',
//     'class'      => '<i class="icon-music icon-white"></i>'
// );
$pages[] = [
    'label' => _('Radio Page'),
    'uri' => '/',
    'resource' => '',
    'class' => '<i class="icon-globe icon-white"></i>',
    'pages' => [],
    'visible' => false,
];
$pages[] = [
    'label' => _('Calendar'),
    'module' => 'default',
    'controller' => 'schedule',
    'action' => 'index',
    'resource' => 'schedule',
    'class' => '<i class="icon-calendar icon-white"></i>',
];
$pages[] = [
    'label' => _('Widgets'),
    'module' => 'default',
    'controller' => 'embeddablewidgets',
    'action' => 'player',
    'resource' => 'embeddablewidgets',
    'class' => '<i class="icon-wrench icon-white"></i>',
    'title' => 'Widgets',
    'pages' => [
        [
            'label' => _('Player'),
            'module' => 'default',
            'controller' => 'embeddablewidgets',
            'action' => 'player',
        ],
        [
            'label' => _('Weekly Schedule'),
            'module' => 'default',
            'controller' => 'embeddablewidgets',
            'action' => 'schedule',
        ],
    ],
];
$pages[] = [
    'label' => _('Settings'),
    'action' => 'edit-user',
    'module' => 'default',
    'controller' => 'user',
    'class' => '<i class="icon-cog icon-white"></i>',
    'title' => 'Settings',
    'pages' => [
        [
            'label' => _('General'),
            'module' => 'default',
            'controller' => 'preference',
            'resource' => 'preference',
        ],
        [
            'label' => _('My Profile'),
            'controller' => 'user',
            'action' => 'edit-user',
        ],
        [
            'label' => _('Users'),
            'module' => 'default',
            'controller' => 'user',
            'action' => 'add-user',
            'resource' => 'user',
        ], [
            'label' => _('Track Types'),
            'module' => 'default',
            'controller' => 'tracktype',
            'action' => 'add-tracktype',
            'resource' => 'tracktype',
        ],
        [
            'label' => _('Streams'),
            'module' => 'default',
            'controller' => 'preference',
            'action' => 'stream-setting',
            'resource' => 'preference',
        ],
        [
            'label' => _('Status'),
            'module' => 'default',
            'controller' => 'systemstatus',
            'action' => 'index',
            'resource' => 'systemstatus',
        ],
    ],
];
$pages[] = [
    'label' => _('Analytics'),
    'module' => 'default',
    'controller' => 'playouthistory',
    'action' => 'index',
    'resource' => 'playouthistory',
    'class' => '<i class="icon-signal icon-white"></i>',
    'title' => 'Analytics',
    'pages' => [
        [
            'label' => _('Playout History'),
            'module' => 'default',
            'controller' => 'playouthistory',
            'action' => 'index',
            'resource' => 'playouthistory',
        ],
        [
            'label' => _('History Templates'),
            'module' => 'default',
            'controller' => 'playouthistorytemplate',
            'action' => 'index',
            'resource' => 'playouthistorytemplate',
        ],
        [
            'label' => _('Listener Stats'),
            'module' => 'default',
            'controller' => 'listenerstat',
            'action' => 'index',
            'resource' => 'listenerstat',
        ],
        [
            'label' => _('Show Listener Stats'),
            'module' => 'default',
            'controller' => 'listenerstat',
            'action' => 'show',
            'resource' => 'showlistenerstat',
        ],
    ],
];
$pages[] = [
    'label' => _('Help'),
    'controller' => 'dashboard',
    'action' => 'help',
    'resource' => 'dashboard',
    'class' => '<i class="icon-question-sign icon-white"></i>',
    'title' => 'Help',
    'pages' => [
        [
            'label' => _('Getting Started'),
            'module' => 'default',
            'controller' => 'dashboard',
            'action' => 'help',
            'resource' => 'dashboard',
        ],
        [
            'label' => _('User Manual'),
            'uri' => USER_MANUAL_URL,
            'target' => '_blank',
        ],
        [
            'label' => _('Get Help Online'),
            'uri' => LIBRETIME_DISCOURSE_URL,
            'target' => '_blank',
        ],
        [
            'label' => _('Contribute to LibreTime'),
            'uri' => LIBRETIME_CONTRIBUTE_URL,
            'target' => '_blank',
        ],
        [
            'label' => _('What\'s New?'),
            'uri' => LIBRETIME_WHATS_NEW_URL,
            'target' => '_blank',
        ],
    ],
];

// Create container from array
$container = new Zend_Navigation($pages);
$container->id = 'nav';

// store it in the registry:
Zend_Registry::set('Zend_Navigation', $container);
