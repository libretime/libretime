<?php

declare(strict_types=1);

class FeedsController extends Zend_Controller_Action
{
    public function stationRssAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $response = $this->getResponse();

        if ((Application_Model_Preference::getStationPodcastPrivacy()
                && $request->getParam('sharing_token') != Application_Model_Preference::getStationPodcastDownloadKey())
            && !RestAuth::verifyAuth(true, false, $this)
        ) {
            $response->setHttpResponseCode(401);

            return;
        }

        CORSHelper::enableCrossOriginRequests($request, $response);

        $rssData = Application_Service_PodcastService::createStationRssFeed();

        $mimeType = 'text/xml';
        header("Content-Type: {$mimeType}; charset=UTF-8");

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }
        header("Content-Type: {$mimeType}");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        $size = strlen($rssData);

        $begin = 0;
        $end = $size - 1;

        // ob_start(); //Must start a buffer here for these header() functions

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if ($size > 0) {
            header('Content-Length:' . (($end - $begin) + 1));
            if (isset($_SERVER['HTTP_RANGE'])) {
                header("Content-Range: bytes {$begin}-{$end}/{$size}");
            }
        }

        echo $rssData;
    }
}
