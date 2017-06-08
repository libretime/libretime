<?php

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, array(), array(
            'rest'=> array('media', 'show-image', 'podcast', 'podcast-episodes')));
        $router->addRoute('rest', $restRoute);

        $podcastBulkRoute = new Zend_Controller_Router_Route(
            'rest/podcast/bulk',
            array(
                'controller' => 'podcast',
                'action' => 'bulk',
                'module' => 'rest'
            )
        );
        $router->addRoute('podcast-bulk', $podcastBulkRoute);

        $stationPodcastRoute = new Zend_Controller_Router_Route(
            'rest/podcast/station',
            array(
                'controller' => 'podcast',
                'action' => 'station',
                'module' => 'rest'
            )
        );
        $router->addRoute('station-podcast', $stationPodcastRoute);

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

        $podcastEpisodeDownloadRoute = new Zend_Controller_Router_Route_Regex(
            'rest/media/(?<id>\d+)/download/(?<download_key>.+)\.(?<file_ext>\w+)',
            array(
                'controller' => 'media',
                'action' => 'download',
                'module' => 'rest'
            ),
            array(
                1 => "id",
                2 => "download_key",
                3 => "file_ext"
            )
        );
        $router->addRoute('podcast-episode-download', $podcastEpisodeDownloadRoute);

        $clearLibraryRoute = new Zend_Controller_Router_Route(
            'rest/media/clear',
            array(
                'controller' => 'media',
                'action' => 'clear',
                'module' => 'rest'
            )
        );
        $router->addRoute('clear', $clearLibraryRoute);

        $publishRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/publish',
            array(
                'controller' => 'media',
                'action' => 'publish',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+'
            )
        );
        $router->addRoute('publish', $publishRoute);

        $publishSourcesRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/publish-sources',
            array(
                'controller' => 'media',
                'action' => 'publish-sources',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+'
            )
        );
        $router->addRoute('publish-sources', $publishSourcesRoute);
    }
}
