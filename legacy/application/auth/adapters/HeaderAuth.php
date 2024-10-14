<?php

/**
 * Auth adaptor for basic header authentication.
 */
class LibreTime_Auth_Adaptor_Header implements Zend_Auth_Adapter_Interface
{
    public function locale(): string
    {
        return Config::get('header_auth.locale');
    }

    /**
     * @throws Exception
     */
    public function authenticate(): Zend_Auth_Result
    {
        $userHeader = Config::get('header_auth.user_header');
        $groupsHeader = Config::get('header_auth.groups_header');
        $emailHeader = Config::get('header_auth.email_header');
        $nameHeader = Config::get('header_auth.name_header');

        $userLogin = $this->getHeaderValueOf($userHeader);

        if ($userLogin == null) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
        }

        $subj = CcSubjsQuery::create()->findOneByDbLogin($userLogin);

        if ($subj == null) {
            $user = new Application_Model_User('');
            $user->setPassword('');
            $user->setLogin($userLogin);
        } else {
            $user = new Application_Model_User($subj->getDbId());
        }

        $name = $this->getHeaderValueOf($nameHeader);

        $user->setEmail($this->getHeaderValueOf($emailHeader));
        $user->setFirstName($this->getFirstName($name) ?? '');
        $user->setLastName($this->getLastName($name) ?? '');
        $user->setType($this->getUserType($this->getHeaderValueOf($groupsHeader)));
        $user->save();
        $this->user = $user;

        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
    }

    private function getUserType(?string $groups): string
    {
        if ($groups == null) {
            return UTYPE_GUEST;
        }

        $groups = array_map(fn ($group) => trim($group), explode(',', $groups));

        $superAdminGroup = Config::get('header_auth.group_map.superadmin');
        if (in_array($superAdminGroup, $groups)) {
            return UTYPE_SUPERADMIN;
        }

        $adminGroup = Config::get('header_auth.group_map.admin');
        if (in_array($adminGroup, $groups)) {
            return UTYPE_ADMIN;
        }

        $programManagerGroup = Config::get('header_auth.group_map.program_manager');
        if (in_array($programManagerGroup, $groups)) {
            return UTYPE_PROGRAM_MANAGER;
        }

        $hostGroup = Config::get('header_auth.group_map.host');
        if (in_array($hostGroup, $groups)) {
            return UTYPE_HOST;
        }

        return UTYPE_GUEST;
    }

    private function getFirstName(?string $name): ?string
    {
        if ($name == null) {
            return null;
        }

        $result = explode(' ', $name, 2);

        return $result[0];
    }

    private function getLastName(?string $name): ?string
    {
        if ($name == null) {
            return null;
        }

        $result = explode(' ', $name, 2);

        return end($result);
    }

    private function getHeaderValueOf(string $httpHeader): ?string
    {
        // Normalize the header name to match server's format
        $normalizedHeader = 'HTTP_' . strtoupper(str_replace('-', '_', $httpHeader));

        return $_SERVER[$normalizedHeader] ?? null;
    }

    // Needed for zend auth adapter

    private Application_Model_User $user;

    public function setIdentity($username)
    {
        return $this;
    }

    public function setCredential($password)
    {
        return $this;
    }

    /**
     * return dummy object for internal auth handling.
     *
     * we need to build a dummpy object since the auth layer knows nothing about the db
     *
     * @param null $returnColumns
     * @param null $omitColumns
     *
     * @return stdClass
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        $o = new stdClass();
        $o->id = $this->user->getId();
        $o->username = $this->user->getLogin();
        $o->password = $this->user->getPassword();
        $o->real_name = implode(' ', [$this->user->getFirstName(), $this->user->getLastName()]);
        $o->type = $this->user->getType();
        $o->login = $this->user->getLogin();

        return $o;
    }
}
