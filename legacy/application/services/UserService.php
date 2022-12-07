<?php

declare(strict_types=1);

class Application_Service_UserService
{
    private $currentUser;

    public function __construct()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        if (!is_null($userInfo->id)) {
            $this->currentUser = CcSubjsQuery::create()->findPK($userInfo->id);
        }
    }

    /**
     * Returns a CcSubjs object.
     */
    public function getCurrentUser()
    {
        if (is_null($this->currentUser)) {
            throw new Exception();
        }

        return $this->currentUser;
    }
}
