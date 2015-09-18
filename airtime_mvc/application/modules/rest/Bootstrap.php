<?php

require_once 'RouteController.php';

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, array(), array(
            'rest'=> array('media', 'show-image', 'podcast', 'podcast-episodes')));
        assert($router->addRoute('rest', $restRoute));

        $podcastBulkRoute = new Zend_Controller_Router_Route(
            'rest/podcast/bulk',
            array(
                'controller' => 'podcast',
                'action' => 'bulk',
                'module' => 'rest'
            )
        );
        $router->addRoute('podcast-bulk', $podcastBulkRoute);

        $route = new Rest_RouteController($front,
            'rest/podcast/:id/episodes',
            array(
                'controller' => 'podcast-episodes',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+'
            )
        );
        $router->addRoute('podcast-episodes-index', $route);

        $route = new Rest_RouteController($front,
            'rest/podcast/:id/episodes/:episode_id',
            array(
                'controller' => 'podcast-episodes',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+',
                'episode_id' => '\d+'
            )
        );
        $router->addRoute('podcast-episodes', $route);

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
    }
}
