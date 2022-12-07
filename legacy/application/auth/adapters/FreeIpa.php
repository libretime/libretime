<?php

declare(strict_types=1);

/**
 * Auth adaptor for FreeIPA.
 */
class LibreTime_Auth_Adaptor_FreeIpa implements Zend_Auth_Adapter_Interface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var Application_Model_User
     */
    private $user;

    /**
     * username from form.
     *
     * @param mixed $username
     *
     * @return self
     */
    public function setIdentity($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * password from form.
     *
     * This is ignored by FreeIPA but needs to get passed for completeness
     *
     * @param mixed $password
     *
     * @return self
     */
    public function setCredential($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Check if apache logged the user and get data from ldap.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (array_key_exists('EXTERNAL_AUTH_ERROR', $_SERVER)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, [$_SERVER['EXTERNAL_AUTH_ERROR']]);
        }
        if (!array_key_exists('REMOTE_USER', $_SERVER)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
        }
        // success, the user is good since the service populated the REMOTE_USER
        $remoteUser = $_SERVER['REMOTE_USER'];

        $subj = CcSubjsQuery::create()->findOneByDbLogin($remoteUser);
        $subjId = null;
        if ($subj) {
            $subjId = $subj->getDBId();
        }

        if ($subjId) {
            $user = new Application_Model_User($subjId);
        } else {
            // upsert the user on login for first time users
            $user = new Application_Model_User('');
        }

        // Always zap any local info with new info from ipa
        $user->setLogin($remoteUser);

        // Use a random password for IPA users, reset on each login... I may change this to get set to the IPA pass but hate that it is being stored as md5 behind the scenes
        // gets rescrambled on each succeful login for security purposes
        $ipaDummyPass = bin2hex(openssl_random_pseudo_bytes(10));
        $user->setPassword($ipaDummyPass);

        // grab user info from LDAP
        $userParts = explode('@', $remoteUser);
        $userInfo = LibreTime_Model_FreeIpa::GetUserInfo($userParts[0]);

        $user->setType($userInfo['type']);
        $user->setFirstName($userInfo['first_name']);
        $user->setLastName($userInfo['last_name']);
        $user->setEmail($userInfo['email']);
        $user->setCellPhone($userInfo['cell_phone']);
        $user->setSkype($userInfo['skype']);
        $user->setJabber($userInfo['jabber']);
        $user->save();
        $this->user = $user;

        try {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        } catch (Exception $e) {
            // exception occured
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
        }
    }

    /**
     * return dummy object for internal auth handling.
     *
     * we need to build a dummpy object since the auth layer knows nothing about the db
     *
     * @return stdClass
     */
    public function getResultRowObject()
    {
        $o = new \stdClass();
        $o->id = $this->user->getId();
        $o->username = $this->user->getLogin();
        $o->password = $this->user->getPassword();
        $o->real_name = implode(' ', [$this->user->getFirstName(), $this->user->getLastName()]);
        $o->type = $this->user->getType();
        $o->login = $this->user->getLogin();

        return $o;
    }
}
