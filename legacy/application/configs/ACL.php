<?php

declare(strict_types=1);

$ccAcl = new Zend_Acl();

$ccAcl->addRole(new Zend_Acl_Role('G'))
    ->addRole(new Zend_Acl_Role('H'), 'G')
    ->addRole(new Zend_Acl_Role('P'), 'H')
    ->addRole(new Zend_Acl_Role('A'), 'P')
    ->addRole(new Zend_Acl_Role('S'), 'A');

$ccAcl->add(new Zend_Acl_Resource('library'))
    ->add(new Zend_Acl_Resource('index'))
    ->add(new Zend_Acl_Resource('user'))
    ->add(new Zend_Acl_Resource('tracktype'))
    ->add(new Zend_Acl_Resource('error'))
    ->add(new Zend_Acl_Resource('login'))
    ->add(new Zend_Acl_Resource('playlist'))
    ->add(new Zend_Acl_Resource('plupload'))
    ->add(new Zend_Acl_Resource('schedule'))
    ->add(new Zend_Acl_Resource('api'))
    ->add(new Zend_Acl_Resource('systemstatus'))
    ->add(new Zend_Acl_Resource('dashboard'))
    ->add(new Zend_Acl_Resource('preference'))
    ->add(new Zend_Acl_Resource('showbuilder'))
    ->add(new Zend_Acl_Resource('playouthistory'))
    ->add(new Zend_Acl_Resource('playouthistorytemplate'))
    ->add(new Zend_Acl_Resource('listenerstat'))
    ->add(new Zend_Acl_Resource('showlistenerstat'))
    ->add(new Zend_Acl_Resource('usersettings'))
    ->add(new Zend_Acl_Resource('audiopreview'))
    ->add(new Zend_Acl_Resource('webstream'))
    ->add(new Zend_Acl_Resource('locale'))
    ->add(new Zend_Acl_Resource('upgrade'))
    ->add(new Zend_Acl_Resource('downgrade'))
    ->add(new Zend_Acl_Resource('rest:media'))
    ->add(new Zend_Acl_Resource('rest:show-image'))
    ->add(new Zend_Acl_Resource('rest:podcast'))
    ->add(new Zend_Acl_Resource('rest:podcast-episodes'))
    ->add(new Zend_Acl_Resource('podcast'))
    ->add(new Zend_Acl_Resource('player'))
    ->add(new Zend_Acl_Resource('render'))
    ->add(new Zend_Acl_Resource('embeddablewidgets'))
    ->add(new Zend_Acl_Resource('setup'))
    ->add(new Zend_Acl_Resource('feeds'));

// Creating permissions
$ccAcl->allow('G', 'index')
    ->allow('G', 'login')
    ->allow('G', 'error')
    ->allow('G', 'user', 'edit-user')
    ->allow('G', 'showbuilder')
    ->allow('G', 'render')
    ->allow('G', 'api')
    ->allow('G', 'schedule')
    ->allow('G', 'dashboard')
    ->allow('G', 'audiopreview')
    ->allow('G', 'webstream')
    ->allow('G', 'locale')
    ->allow('G', 'upgrade')
    ->allow('G', 'downgrade')
    ->allow('G', 'rest:show-image', 'get')
    ->allow('G', 'rest:media', 'get')
    ->allow('G', 'rest:podcast', 'get')
    ->allow('G', 'rest:podcast-episodes', 'get')
    ->allow('G', 'setup')
    ->allow('G', 'embeddablewidgets')
    ->allow('G', 'feeds')
    ->allow('H', 'rest:show-image')
    ->allow('H', 'rest:media')
    ->allow('H', 'rest:podcast')
    ->allow('H', 'rest:podcast-episodes')
    ->allow('H', 'podcast')
    ->allow('H', 'preference', 'is-import-in-progress')
    ->allow('H', 'usersettings')
    ->allow('H', 'plupload')
    ->allow('H', 'library')
    ->allow('H', 'playlist')
    ->allow('H', 'playouthistory')
    ->allow('H', 'listenerstat')
    ->allow('H', 'showlistenerstat')
    ->allow('A', 'playouthistorytemplate')
    ->allow('A', 'user')
    ->allow('A', 'tracktype')
    ->allow('A', 'systemstatus')
    ->allow('A', 'preference');

$aclPlugin = new Zend_Controller_Plugin_Acl($ccAcl);

Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($ccAcl);

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin($aclPlugin);
