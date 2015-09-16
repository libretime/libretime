<?php

class Rest_PodcastController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
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
            //https://github.com/aaronsnoswell/itunes-podcast-feed/blob/master/feed.php

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
            $requestData = json_decode($this->getRequest()->getRawBody(), true);
            $podcast = Podcast::create($requestData);

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($podcast));
        }
        catch (PodcastLimitReachedException $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("ERROR: Podcast limit reached.");
        }
        catch (InvalidPodcastException $e) {
            $this->invalidDataResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
            throw $e;
        }
    }

    public function putAction()
    {
        Logging::info("podcast put");
    }

    public function deleteAction()
    {
        Logging::info("delete podcast");
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

}
