<?php

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, array(), array(
            'rest'=> array('media', 'show')));
        assert($router->addRoute('rest', $restRoute));

        /** MediaController Routes **/
        $downloadRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/download',
            array(
                'controller' => 'media',
                'action' => 'download',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+'
            )
        );
        $router->addRoute('download', $downloadRoute);

        $clearLibraryRoute = new Zend_Controller_Router_Route(
            'rest/media/clear',
            array(
                'controller' => 'media',
                'action' => 'clear',
                'module' => 'rest'
            )
        );
        $router->addRoute('clear', $clearLibraryRoute);
        
        /** ShowController Routes **/
        $uploadImageRoute = new Zend_Controller_Router_Route(
        		'rest/show/:id/upload-image',
        		array(
        				'controller' => 'show',
        				'action' => 'upload-image',
        				'module' => 'rest'
        		),
        		array(
        				'id' => '\d+'
        		)
        );
        $router->addRoute('upload-image', $uploadImageRoute);
        
        $deleteImageRoute = new Zend_Controller_Router_Route(
        		'rest/show/:id/delete-image',
        		array(
        				'controller' => 'show',
        				'action' => 'delete-image',
        				'module' => 'rest'
        		),
        		array(
        				'id' => '\d+'
        		)
        );
        $router->addRoute('delete-image', $deleteImageRoute);
    }
}
