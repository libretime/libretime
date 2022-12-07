<?php

declare(strict_types=1);

class LibreTime_Model_FreeIpa
{
    /**
     * get userinfo in the format needed by the Auth Adaptor.
     *
     * @param mixed $username
     *
     * @return array
     */
    public static function GetUserInfo($username)
    {
        $config = Config::getConfig();
        $conn = self::_getLdapConnection();

        $ldapResults = $conn->search(sprintf('%s=%s', $config['ldap_filter_field'], $username, $config['ldap_basedn']));

        if ($ldapResults->count() !== 1) {
            throw new Exception('Could not find logged user in LDAP');
        }
        $ldapUser = $ldapResults->getFirst();

        $groupMap = [
            UTYPE_GUEST => $config['ldap_groupmap_guest'],
            UTYPE_HOST => $config['ldap_groupmap_host'],
            UTYPE_PROGRAM_MANAGER => $config['ldap_groupmap_program_manager'],
            UTYPE_ADMIN => $config['ldap_groupmap_admin'],
            UTYPE_SUPERADMIN => $config['ldap_groupmap_superadmin'],
        ];
        $type = UTYPE_GUEST;
        foreach ($groupMap as $groupType => $group) {
            if (in_array($group, $ldapUser['memberof'])) {
                $type = $groupType;
            }
        }

        // grab first value for multivalue field
        $firstName = $ldapUser['givenname'][0];
        $lastName = $ldapUser['sn'][0];
        $mail = $ldapUser['mail'][0];

        // return full user info for auth adapter
        return [
            'type' => $type,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $mail,
            'cell_phone' => '', // empty since I did not find it in ldap
            'skype' => '', // empty until we decide on a field
            'jabber' => '',  // empty until we decide on a field
        ];
    }

    /**
     * Bind to ldap so we can fetch additional user info.
     *
     * @return Zend_Ldap
     */
    private static function _getLdapConnection()
    {
        $config = Config::getConfig();

        $options = [
            'host' => $config['ldap_hostname'],
            'username' => $config['ldap_binddn'],
            'password' => $config['ldap_password'],
            'bindRequiresDn' => true,
            'accountDomainName' => $config['ldap_account_domain'],
            'baseDn' => $config['ldap_basedn'],
        ];
        $conn = new Zend_Ldap($options);
        $conn->connect();

        return $conn;
    }
}
