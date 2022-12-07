<?php

declare(strict_types=1);

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, [], [
            'rest' => ['media', 'show-image', 'podcast', 'podcast-episodes'],
        ]);
        $router->addRoute('rest', $restRoute);

        $podcastBulkRoute = new Zend_Controller_Router_Route(
            'rest/podcast/bulk',
            [
                'controller' => 'podcast',
                'action' => 'bulk',
                'module' => 'rest',
            ]
        );
        $router->addRoute('podcast-bulk', $podcastBulkRoute);

        $smartblockPodcastRoute = new Zend_Controller_Router_Route(
            'rest/podcast/smartblock',
            [
                'controller' => 'podcast',
                'action' => 'smartblock',
                'module' => 'rest',
            ]
        );
        $router->addRoute('podcast-smartblock', $smartblockPodcastRoute);

        $stationPodcastRoute = new Zend_Controller_Router_Route(
            'rest/podcast/station',
            [
                'controller' => 'podcast',
                'action' => 'station',
                'module' => 'rest',
            ]
        );
        $router->addRoute('station-podcast', $stationPodcastRoute);

        $route = new Rest_RouteController(
            $front,
            'rest/podcast/:id/episodes',
            [
                'controller' => 'podcast-episodes',
                'module' => 'rest',
            ],
            [
                'id' => '\d+',
            ]
        );
        $router->addRoute('podcast-episodes-index', $route);

        $route = new Rest_RouteController(
            $front,
            'rest/podcast/:id/episodes/:episode_id',
            [
                'controller' => 'podcast-episodes',
                'module' => 'rest',
            ],
            [
                'id' => '\d+',
                'episode_id' => '\d+',
            ]
        );
        $router->addRoute('podcast-episodes', $route);

        /** MediaController Routes */
        $downloadRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/download',
            [
                'controller' => 'media',
                'action' => 'download',
                'module' => 'rest',
            ],
            [
                'id' => '\d+',
            ]
        );
        $router->addRoute('download', $downloadRoute);

        $podcastEpisodeDownloadRoute = new Zend_Controller_Router_Route_Regex(
            'rest/media/(?<id>\d+)/download/(?<download_key>.+)\.(?<file_ext>\w+)',
            [
                'controller' => 'media',
                'action' => 'download',
                'module' => 'rest',
            ],
            [
                1 => 'id',
                2 => 'download_key',
                3 => 'file_ext',
            ]
        );
        $router->addRoute('podcast-episode-download', $podcastEpisodeDownloadRoute);

        $clearLibraryRoute = new Zend_Controller_Router_Route(
            'rest/media/clear',
            [
                'controller' => 'media',
                'action' => 'clear',
                'module' => 'rest',
            ]
        );
        $router->addRoute('clear', $clearLibraryRoute);

        $publishRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/publish',
            [
                'controller' => 'media',
                'action' => 'publish',
                'module' => 'rest',
            ],
            [
                'id' => '\d+',
            ]
        );
        $router->addRoute('publish', $publishRoute);

        $publishSourcesRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/publish-sources',
            [
                'controller' => 'media',
                'action' => 'publish-sources',
                'module' => 'rest',
            ],
            [
                'id' => '\d+',
            ]
        );
        $router->addRoute('publish-sources', $publishSourcesRoute);
    }
}
