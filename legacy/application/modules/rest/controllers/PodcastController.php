<?php

declare(strict_types=1);

class Rest_PodcastController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts/');
    }

    /**
     * headAction is needed as it is defined as an abstract function in the base controller.
     */
    public function headAction()
    {
        Logging::info('HEAD action received');
    }

    public function indexAction()
    {
        // Check if offset and limit were sent with request.
        // Default limit to zero and offset to $totalFileCount
        $offset = $this->_getParam('offset', 0);
        $limit = $this->_getParam('limit', 0);

        // Sorting parameters
        $sortColumn = $this->_getParam('sort', PodcastPeer::ID);
        $sortDir = $this->_getParam('sort_dir', Criteria::ASC);

        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        $result = PodcastQuery::create()
            // Don't return the Station podcast - we fetch it separately
            ->filterByDbId($stationPodcastId, Criteria::NOT_EQUAL)
            ->leftJoinImportedPodcast()
            ->withColumn('auto_ingest_timestamp');
        $total = $result->count();
        if ($limit > 0) {
            $result->setLimit($limit);
        }
        $result->setOffset($offset)
            ->orderBy($sortColumn, $sortDir);
        $result = $result->find();

        $podcastArray = $result->toArray(null, false, BasePeer::TYPE_FIELDNAME);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('X-TOTAL-COUNT', $total)
            ->appendBody(json_encode($podcastArray));
    }

    public function getAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody(json_encode(Application_Service_PodcastService::getPodcastById($id)));
        } catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
        }
    }

    public function postAction()
    {
        // If we do get an ID on a POST, then that doesn't make any sense
        // since POST is only for creating.
        if ($id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: ID should not be specified when using POST. POST is only used for podcast creation, and an ID will be chosen by Airtime');

            return;
        }

        try {
            $requestData = $this->getRequest()->getPost();
            $podcast = Application_Service_PodcastService::createFromFeedUrl($requestData['url']);

            $path = 'podcast/podcast.phtml';

            $this->view->podcast = $podcast;
            $this->_helper->json->sendJson([
                'podcast' => json_encode($podcast),
                'html' => $this->view->render($path),
            ]);
        } catch (InvalidPodcastException $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody('Invalid podcast!');
        } catch (Exception $e) {
            Logging::error($e->getMessage());
            $this->unknownErrorResponse();
        }
    }

    public function putAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $requestData = json_decode($this->getRequest()->getRawBody(), true);
            $podcast = Application_Service_PodcastService::updatePodcastFromArray($id, $requestData);

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($podcast));
        } catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function deleteAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            Application_Service_PodcastService::deletePodcastById($id);
            $this->getResponse()
                ->setHttpResponseCode(204);
        } catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    /**
     * Endpoint for performing bulk actions (deleting multiple podcasts, opening multiple editors).
     */
    public function bulkAction()
    {
        if ($this->_request->getMethod() != HttpRequestType::POST) {
            $this->getResponse()
                ->setHttpResponseCode(405)
                ->appendBody('ERROR: Method not accepted');

            return;
        }

        $ids = $this->_getParam('ids', []);
        $method = $this->_getParam('method', HttpRequestType::GET);
        $responseBody = [];

        // XXX: Should this be a map of HttpRequestType => function call instead? Would be a bit cleaner
        switch ($method) {
            case HttpRequestType::DELETE:
                foreach ($ids as $id) {
                    Application_Service_PodcastService::deletePodcastById($id);
                }

                break;

            case HttpRequestType::GET:
                $path = 'podcast/podcast.phtml';
                foreach ($ids as $id) {
                    $responseBody[] = [
                        'podcast' => json_encode(Application_Service_PodcastService::getPodcastById($id)),
                        'html' => $this->view->render($path),
                    ];
                }

                break;
        }

        $this->_helper->json->sendJson($responseBody);
    }

    /**
     * Endpoint for triggering the generation of a smartblock and playlist to match the podcast name.
     */
    public function smartblockAction()
    {
        $title = $this->_getParam('title', []);
        $id = $this->_getParam('id', []);
        if (!$id) {
            return;
        }
        $podcast = Application_Service_PodcastService::getPodcastById($id);

        // logging::info($podcast);
        Application_Service_PodcastService::createPodcastSmartblockAndPlaylist($podcast, $title);
    }

    /**
     * @throws PodcastNotFoundException
     *
     * @deprecated
     */
    public function stationAction()
    {
        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        $podcast = Application_Service_PodcastService::getPodcastById($stationPodcastId);
        $path = 'podcast/station.phtml';
        $this->view->podcast = $podcast;
        $this->_helper->json->sendJson([
            'podcast' => json_encode($podcast),
            'html' => $this->view->render($path),
        ]);
    }

    private function getId()
    {
        if (!$id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: No podcast ID specified.');

            return false;
        }

        return $id;
    }

    private function unknownErrorResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(500);
        $resp->appendBody('An unknown error occurred.');
    }

    private function podcastNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody('ERROR: Podcast not found.');
    }
}
