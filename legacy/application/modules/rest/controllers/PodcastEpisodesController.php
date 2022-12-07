<?php

declare(strict_types=1);

class Rest_PodcastEpisodesController extends Zend_Rest_Controller
{
    /**
     * @var Application_Service_PodcastEpisodeService
     */
    protected $_service;

    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_service = new Application_Service_PodcastEpisodeService();
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
        // podcast ID
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $totalPodcastEpisodesCount = PodcastEpisodesQuery::create()
                ->filterByDbPodcastId($id)
                ->count();

            // Check if offset and limit were sent with request.
            // Default limit to zero and offset to $totalFileCount
            $offset = $this->_getParam('offset', 0);
            $limit = $this->_getParam('limit', $totalPodcastEpisodesCount);

            // Sorting parameters
            $sortColumn = $this->_getParam('sort', PodcastEpisodesPeer::ID);
            $sortDir = $this->_getParam('sort_dir', Criteria::ASC);

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->setHeader('X-TOTAL-COUNT', $totalPodcastEpisodesCount)
                ->appendBody(json_encode($this->_service->getPodcastEpisodes($id, $offset, $limit, $sortColumn, $sortDir)));
        } catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function getAction()
    {
        // podcast ID
        $id = $this->getId();
        if (!$id) {
            return;
        }

        $episodeId = $this->getEpisodeId();
        if (!$episodeId) {
            return;
        }

        try {
            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($this->_service->getPodcastEpisodeById($episodeId)));
        } catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (PodcastEpisodeNotFoundException $e) {
            $this->podcastEpisodeNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function postAction()
    {
        // If we do get an episode ID on a POST, then that doesn't make any sense
        // since POST is only for creating.
        if ($episodeId = $this->_getParam('episode_id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: Episode ID should not be specified when using POST. POST is only used for '
                . 'importing podcast episodes, and an episode ID will be chosen by Airtime');

            return;
        }

        // Make sure a podcast ID was specified
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $requestData = json_decode($this->getRequest()->getRawBody(), true);
            $episode = $this->_service->importEpisode($id, $requestData['episode']);
            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($episode));
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

        $episodeId = $this->getEpisodeId();
        if (!$episodeId) {
            return;
        }

        try {
            $this->_service->deletePodcastEpisodeById($episodeId);
            $this->getResponse()
                ->setHttpResponseCode(204);
        } catch (PodcastEpisodeNotFoundException $e) {
            $this->podcastEpisodeNotFoundResponse();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function putAction()
    {
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

    private function getEpisodeId()
    {
        if (!$episodeId = $this->_getParam('episode_id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: No podcast episode ID specified.');

            return false;
        }

        return $episodeId;
    }

    private function unknownErrorResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(400);
        $resp->appendBody('An unknown error occurred.');
    }

    private function podcastNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody('ERROR: Podcast not found.');
    }

    private function podcastEpisodeNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody('ERROR: Podcast episode not found.');
    }
}
