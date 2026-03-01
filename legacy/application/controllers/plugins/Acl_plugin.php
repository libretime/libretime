<?php

class Zend_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * @var string
     */
    protected $_roleName;

    /**
     * @var array
     */
    protected $_errorPage;

    /**
     * Constructor.
     *
     * @param mixed $aclData
     * @param mixed $roleName
     */
    public function __construct(Zend_Acl $aclData, $roleName = 'G')
    {
        $this->_errorPage = [
            'module' => 'default',
            'controller' => 'error',
            'action' => 'error',
        ];

        $this->_roleName = $roleName;

        if (null !== $aclData) {
            $this->setAcl($aclData);
        }
    }

    /**
     * Sets the ACL object.
     *
     * @param mixed $aclData
     */
    public function setAcl(Zend_Acl $aclData)
    {
        $this->_acl = $aclData;
    }

    /**
     * Returns the ACL object.
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Returns the ACL role used.
     *
     * @return string
     *
     * @author
     */
    public function getRoleName()
    {
        return $this->_roleName;
    }

    public function setRoleName($type)
    {
        $this->_roleName = $type;
    }

    /**
     * Sets the error page.
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function setErrorPage($action, $controller = 'error', $module = 'default')
    {
        $this->_errorPage = [
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
        ];
    }

    /**
     * Returns the error page.
     *
     * @return array
     */
    public function getErrorPage()
    {
        return $this->_errorPage;
    }

    /**
     * Predispatch
     * Checks if the current user identified by roleName has rights to the requested url (module/controller/action)
     * If not, it will call denyAccess to be redirected to errorPage.
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controller = strtolower($request->getControllerName());

        if (in_array($controller, [
            'index',
            'login',
            'api',
            'auth',
            'error',
            'locale',
            'upgrade',
            'embed',
            'feeds',
        ])) {
            $this->setRoleName('G');
        } elseif (Zend_Session::isStarted() && !Zend_Auth::getInstance()->hasIdentity()) {
            // The controller uses sessions but we don't have an identity yet.

            // If we don't have an identity and we're making a RESTful request,
            // we need to do API key verification
            if ($request->getModuleName() == 'rest') {
                if (!$this->verifyAuth()) {
                    // $this->denyAccess();
                    // $this->getResponse()->sendResponse();
                    // $r->gotoSimpleAndExit('index', 'login', $request->getModuleName());

                    // die();
                    throw new Zend_Controller_Exception('Incorrect API key', 401);
                }
            } else { // Non-REST, regular Airtime web app requests
                // Redirect user to the landing page if they are trying to
                // access a resource that requires a valid session.
                // Skip the redirection if they are already on the landing page
                // or the login page.
                if ($controller !== 'index' && $controller !== 'login') {
                    if ($request->isXmlHttpRequest()) {
                        $url = 'http://' . $request->getHttpHost() . '/';
                        $json = Zend_Json::encode(['auth' => false, 'url' => $url]);

                        // Prepare response
                        $this->getResponse()
                            ->setHttpResponseCode(401)
                            ->setBody($json)
                            ->sendResponse();

                        // redirectAndExit() cleans up, sends the headers and stops the script
                        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->redirectAndExit();
                    } else {
                        $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                        $r->gotoSimpleAndExit('index', 'index', $request->getModuleName());
                    }
                }
            }
        } else { // We have a session/identity.
            // If we have an identity and we're making a RESTful request,
            // we need to check the CSRF token
            if ($_SERVER['REQUEST_METHOD'] != 'GET' && $request->getModuleName() == 'rest') {
                $token = $request->getParam('csrf_token');
                // PUT requests don't parameterize the data in the body, so we can't
                // fetch it with getParam or getPost; instead we have to parse the body and
                // check for the token in the JSON. (Hopefully we can find a better way to do this) -- Duncan
                if (empty($token)) {
                    $token = json_decode($this->getRequest()->getRawBody(), true)['csrf_token'];
                }
                $tokenValid = $this->verifyCSRFToken($token);

                if (!$tokenValid) {
                    $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
                    $csrf_namespace->authtoken = sha1(openssl_random_pseudo_bytes(128));

                    Logging::warn("Invalid CSRF token: {$token}");
                    $this->getResponse()
                        ->setHttpResponseCode(401)
                        ->appendBody('ERROR: CSRF token mismatch.')
                        ->sendResponse();

                    exit;
                }
            }

            $userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $this->setRoleName($userInfo->type);

            Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->_acl);
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_roleName);

            $resourceName = '';

            if ($request->getModuleName() != 'default') {
                $resourceName .= strtolower($request->getModuleName()) . ':';
            }

            $resourceName .= $controller;

            // Check if the controller/action can be accessed by the current user
            if (!$this->getAcl()->has($resourceName)) {
                $this->setErrorPage('error404');
                $this->denyAccess();
            } elseif (!$this->getAcl()->isAllowed(
                $this->_roleName,
                $resourceName,
                $request->getActionName()
            )) {
                // Redirect to access denied page
                $this->setErrorPage('error403');
                $this->denyAccess();
            }
        }
    }

    private function verifyAuth()
    {
        if ($this->verifyAPIKey() || $this->isVerifiedDownload()) {
            return true;
        }

        $this->getResponse()
            ->setHttpResponseCode(401)
            ->appendBody('ERROR: Incorrect API key.');

        return false;
    }

    /**
     * Check if the requested file can be downloaded.
     * It should satisfy the following requirements:
     *  * request path is /rest/media/:id/download
     *  * download key is correct
     *  * requested file belongs to the station podcast.
     *
     * @return bool
     */
    private function isVerifiedDownload()
    {
        $request = $this->getRequest();
        $fileId = $request->getParam('id');
        $key = $request->getParam('download_key');
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $stationPodcast = StationPodcastQuery::create()
            ->findOneByDbPodcastId(Application_Model_Preference::getStationPodcastId());

        return $module == 'rest' && $controller == 'media' && $action == 'download'
            && $key === Application_Model_Preference::getStationPodcastDownloadKey()
            && $stationPodcast->hasEpisodeForFile($fileId);
    }

    private function verifyCSRFToken($token)
    {
        return SecurityHelper::verifyCSRFToken($token);
    }

    private function verifyAPIKey()
    {
        // The API key is passed in via HTTP "basic authentication":
        // https://en.wikipedia.org/wiki/Basic_access_authentication
        $CC_CONFIG = Config::getConfig();

        // Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader('Authorization');
        $encodedRequestApiKey = substr($authHeader, strlen('Basic '));
        $encodedStoredApiKey = base64_encode($CC_CONFIG['apiKey'][0] . ':');

        return $encodedRequestApiKey === $encodedStoredApiKey;
    }

    /**
     * Deny Access Function
     * Redirects to errorPage, this can be called from an action using the action helper.
     */
    public function denyAccess()
    {
        $this->_request->setModuleName($this->_errorPage['module']);
        $this->_request->setControllerName($this->_errorPage['controller']);
        $this->_request->setActionName($this->_errorPage['action']);
    }
}
