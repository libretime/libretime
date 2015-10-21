<?php

require_once('PodcastFactory.php');

class Rest_PodcastController extends Zend_Rest_Controller
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
        $this->view->setScriptPath(APPLICATION_PATH . 'views/scripts/');
        $this->_service = new Application_Service_PodcastEpisodeService();
    }

    public function indexAction()
    {
        $totalPodcastCount = PodcastQuery::create()->count();

        // Check if offset and limit were sent with request.
        // Default limit to zero and offset to $totalFileCount
        $offset = $this->_getParam('offset', 0);
        $limit = $this->_getParam('limit', $totalPodcastCount);

        //Sorting parameters
        $sortColumn = $this->_getParam('sort', PodcastPeer::ID);
        $sortDir = $this->_getParam('sort_dir', Criteria::ASC);

        $query = PodcastQuery::create()
            ->setLimit($limit)
            ->setOffset($offset)
            ->orderBy($sortColumn, $sortDir);

        $queryResult = $query->find();

        $podcastArray = array();
        foreach ($queryResult as $podcast)
        {
            array_push($podcastArray, $podcast->toArray(BasePeer::TYPE_FIELDNAME));
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('X-TOTAL-COUNT', $totalPodcastCount)
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
        //If we do get an ID on a POST, then that doesn't make any sense
        //since POST is only for creating.
        if ($id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: ID should not be specified when using POST. POST is only used for podcast creation, and an ID will be chosen by Airtime");
            return;
        }

        try {
            //$requestData = json_decode($this->getRequest()->getRawBody(), true);
            $requestData = $this->getRequest()->getPost();
            $podcast = PodcastFactory::create($requestData["url"]);

            $path = 'podcast/podcast.phtml';
            $this->view->podcast = $podcast;
            $this->_helper->json->sendJson(array(
                                               "podcast"=>json_encode($podcast),
                                               "html"=>$this->view->render($path),
                                           ));
        }
        catch (PodcastLimitReachedException $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("ERROR: Podcast limit reached.");
        }
        catch (InvalidPodcastException $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("ERROR: Invalid Podcast.");
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
            throw $e;
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
            // Create placeholders in PodcastEpisodes so we know these episodes are being downloaded
            // to prevent the user from trying to download them again while Celery is running
            $episodes = $this->_service->addPodcastEpisodePlaceholders($requestData["podcast"]["id"],
                                                                       $requestData["podcast"]["episodes"]);
            $this->_service->downloadEpisodes($episodes);
            $podcast = Application_Service_PodcastService::updatePodcastFromArray($id, $requestData);

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($podcast));
        }
        catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
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
        }
        catch (PodcastNotFoundException $e) {
            $this->podcastNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    /**
     * Endpoint for performing bulk actions (deleting multiple podcasts, opening multiple editors)
     */
    public function bulkAction() {
        if ($this->_request->getMethod() != "POST") {
            $this->getResponse()
                ->setHttpResponseCode(405)
                ->appendBody("ERROR: Method not accepted");
            return;
        }

        $ids = $this->_getParam('ids', []);
        $method = $this->_getParam('method', 'GET');
        $responseBody = [];

        switch($method) {
            case "DELETE":
                foreach($ids as $id) {
                    Application_Service_PodcastService::deletePodcastById($id);
                }
                // XXX: do we need this to be more descriptive?
                $responseBody = "Successfully deleted podcasts";
                break;
            case "GET":
                foreach($ids as $id) {
                    // Check the StationPodcast table rather than checking
                    // the station podcast ID key in preferences for extensibility
                    $podcast = StationPodcastQuery::create()->findOneByDbPodcastId($id);
                    $path = $podcast ? 'podcast/station_podcast.phtml' : 'podcast/podcast.phtml';
                    $podcast = Application_Service_PodcastService::getPodcastById($id);
                    $responseBody[] = array(
                        "podcast"=>json_encode($podcast),
                        "html"=>$this->view->render($path),
                    );
                }
                break;
        }

        $this->_helper->json->sendJson($responseBody);
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

}
