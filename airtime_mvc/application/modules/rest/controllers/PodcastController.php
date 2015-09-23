<?php

class Rest_PodcastController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->setScriptPath(APPLICATION_PATH . 'views/scripts/');
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
                ->appendBody(json_encode(Podcast::getPodcastById($id)));
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
            // $requestData = json_decode($this->getRequest()->getRawBody(), true);
            $requestData = $this->getRequest()->getPost();
            $podcast = Podcast::create($requestData);

            $path = 'podcast/podcast.phtml';
            $this->view->podcast = $podcast;
            $this->_helper->json->sendJson(array(
                                               "podcast"=>json_encode($podcast),
                                               "html"=>$this->view->render($path),
                                               "type"=>"podcast",  // TODO: get rid of these extraneous fields
                                               "id"=>$podcast["id"]
                                               // "id"=>$podcast->getDbId()
                                           ));

            // $this->getResponse()
            //      ->setHttpResponseCode(201)
            //      ->appendBody(json_encode($podcast));
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

            $podcast = Podcast::updateFromArray($id, $requestData);

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
            Podcast::deleteById($id);
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
        $path = 'podcast/podcast.phtml';
        $responseBody = [];

        switch($method) {
            case "DELETE":
                foreach($ids as $id) {
                    Podcast::deleteById($id);
                    $responseBody = "Success";  // TODO: make this more descriptive
                }
                break;
            case "GET":
                foreach($ids as $id) {
                    $podcast = Podcast::getPodcastById($id);
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
