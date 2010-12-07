<?php

require_once 'Acl_plugin.php';

$ccAcl = new Zend_Acl();

$ccAcl->addRole(new Zend_Acl_Role('guest'))
      ->addRole(new Zend_Acl_Role('host'), 'guest')
      ->addRole(new Zend_Acl_Role('admin'), 'host');

$ccAcl->add(new Zend_Acl_Resource('library'))
	  ->add(new Zend_Acl_Resource('index'))
	  ->add(new Zend_Acl_Resource('error'))
      ->add(new Zend_Acl_Resource('login'))
	  ->add(new Zend_Acl_Resource('playlist'))
	  ->add(new Zend_Acl_Resource('plupload'))
	  ->add(new Zend_Acl_Resource('schedule'))
	  ->add(new Zend_Acl_Resource('search'));

/** Creating permissions */
$ccAcl->allow('guest', 'index')
	  ->allow('guest', 'login')
	  ->allow('guest', 'error')
	  ->allow('guest', 'library')
	  ->allow('guest', 'search')
      ->allow('host', 'plupload')
	  ->allow('host', 'playlist')
      ->allow('host', 'schedule');

$aclPlugin = new Zend_Controller_Plugin_Acl($ccAcl);

$front = Zend_Controller_Front::getInstance();  
$front->registerPlugin($aclPlugin);
