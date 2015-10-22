<?php

class Rest_PodcastEpisodesController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        // podcast ID
        $id = $this->getId();
        if (!$id) {
            return;
        }
        try {

            $podcast = PodcastQuery::create()->findPk($id);
            if (!$podcast) {
                throw new PodcastNotFoundException();
            }

            $totalPodcastEpisodesCount = PodcastEpisodesQuery::create()
                ->filterByDbPodcastId($id)
                ->count();

            // Check if offset and limit were sent with request.
            // Default limit to zero and offset to $totalFileCount
            $offset = $this->_getParam('offset', 0);
            $limit = $this->_getParam('limit', $totalPodcastEpisodesCount);

            //Sorting parameters
            $sortColumn = $this->_getParam('sort', PodcastEpisodesPeer::ID);
            $sortDir = $this->_getParam('sort_dir', Criteria::ASC);

            $episodes = PodcastEpisodesQuery::create()
                ->filterByDbPodcastId($id)
                ->setLimit($limit)
                ->setOffset($offset)
                ->orderBy($sortColumn, $sortDir)
                ->find();

            $episodesArray = array();
            foreach ($episodes as $episode) {
                array_push($episodesArray, $episode->toArray(BasePeer::TYPE_FIELDNAME));
            }

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($episodesArray));

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
        //TODO: can we delete this?
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
                ->appendBody(json_encode(Application_Service_PodcastEpisodeService::getPodcastEpisodeById($episodeId)));

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
        //If we do get an episode ID on a POST, then that doesn't make any sense
        //since POST is only for creating.
        if ($episodeId = $this->_getParam('episode_id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: Episode ID should not be specified when using POST. POST is only used for importing podcast episodes, and an episode ID will be chosen by Airtime");
            return;
        }

        // Make sure a podcast ID was specified
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $requestData = json_decode($this->getRequest()->getRawBody(), true);
            //$requestData = $this->getRequest()->getPost();

            $episode = Application_Service_PodcastEpisodeService::createPodcastEpisode($id, $requestData);

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
        Logging::info("delete - episodes");

        //TODO: can we delete this?
        $id = $this->getId();
        if (!$id) {
            return;
        }

        $episodeId = $this->getEpisodeId();
        if (!$episodeId) {
            return;
        }

        try {
            Application_Service_PodcastEpisodeService::deletePodcastEpisodeById($episodeId);
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
            $resp->appendBody("ERROR: No podcast ID specified.");
            return false;
        }
        return $id;
    }

    private function getEpisodeId()
    {
        if (!$episodeId = $this->_getParam('episode_id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No podcast episode ID specified.");
            return false;
        }
        return $episodeId;
    }

    private function unknownErrorResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(400);
        $resp->appendBody("An unknown error occurred.");
    }

    private function podcastNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody("ERROR: Podcast not found.");
    }

    private function podcastEpisodeNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody("ERROR: Podcast episode not found.");
    }

}
