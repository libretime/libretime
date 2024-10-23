---
title: Custom authentication
sidebar_position: 40
---

:::warning

Since LibreTime v3.0.0-alpha.13, this documentation is out of date, as it relies on the Apache2 web server and the default web server installed by LibreTime is now NGINX.

:::

## Setup FreeIPA authentication

You can configure LibreTime to delegate all authentication to a FreeIPA server.

This allows you users to use their existing FreeIPA credentials. For this to
work you need to configure Apache to use `mod_authnz_pam` and `mod_intercept_form_submit`.

### Apache configuration

After installing the needed modules you can set up Apache to intercept form logins and
check them against pam.

```apacheconf
<Location /login>
    InterceptFormPAMService http-libretime
    InterceptFormLogin username
    InterceptFormPassword password
    InterceptFormLoginSkip admin
    InterceptFormPasswordRedact on
    InterceptFormLoginRealms INT.RABE.CH
    Require pam-account http-libretime
</Location>

<Location />
    <RequireAny>
       <RequireAny>
           Require pam-account http-libretime
           Require all granted
       </RequireAny>
       <RequireAll>
           Require expr %{REQUEST_URI} =~  /(index.php|login|favicon.ico|js|css|locale)/
           Require all granted
       </RequireAll>
    </RequireAny>
</Location>
```

### PAM configuration

The above configuration expects a PAM configuration for the `http-libretime` service.

To configure this you need to create the file `/etc/pam.d/http-libretime` with the following contents.

```
auth    required   pam_sss.so
account required   pam_sss.so
```

### LDAP configuration

LibreTime needs direct access to LDAP so it can fetch additional information. It does so with
a [system account](https://www.freeipa.org/page/HowTo/LDAP#System_Accounts) that you need to
set up beforehand.

You can configure everything pertaining to how LibreTime accesses LDAP in
`/etc/libretime/config.yml`. The default file has the following values you need to change.

```yml
#
# ----------------------------------------------------------------------
#                          L D A P
# ----------------------------------------------------------------------
#
# hostname:       Hostname of LDAP server
#
# binddn:         Complete DN of user used to bind to LDAP
#
# password:       Password for binddn user
#
# account_domain: Domain part of username
#
# basedn:         base search DN
#
# filter_field:   Name of the uid field for searching
#                 Usually uid, may be cn
#
# groupmap_*:     Map LibreTime user types to LDAP groups
#                 Lets LibreTime assign user types based on the
#                 group a given user is in.
#
ldap:
  hostname: ldap.example.org
  binddn: "uid=libretime,cn=sysaccounts,cn=etc,dc=int,dc=example,dc=org"
  password: hackme
  account_domain: INT.EXAMPLE.ORG
  basedn: "cn=users,cn=accounts,dc=int,dc=example,dc=org"
  filter_field: uid
  groupmap_guest: "cn=guest,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_host: "cn=host,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_program_manager: "cn=program_manager,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_admin: "cn=admins,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
  groupmap_superadmin: "cn=superadmin,cn=groups,cn=accounts,dc=int,dc=example,dc=org"
```

### Enable FreeIPA authentication

After everything is set up properly you can enable FreeIPA auth in `config.yml`:

```yml
general:
  auth: LibreTime_Auth_Adaptor_FreeIpa
```

You should now be able to use your FreeIPA credentials to log in to LibreTime.

## Setup Header Authentication

If you have an SSO system that supports trusted SSO header authentication such as [Authelia](https://www.authelia.com/),
you can configure LibreTime to login users based on those trusted headers.

This allows users to only need to log in once on the SSO system and not need to log in again. It also allows LibreTime
to indirectly support other authentication mechanisms such as OAuth2.

### Configure Headers

LibreTime needs to know what headers are sent, and what information is available to it. You can also
setup a predefined group mapping so users are automatically granted the desired permissions.

This configuration is in `/etc/libretime/config.yml`. The following is an example configuration for an SSO service
that does the following:

- Sends the username in the `Remote-User` HTTP header.
- Sends the email in the `Remote-Email` HTTP header.
- Sends the name in the `Remote-Name` HTTP header. Example `John Doe`
- Sends the comma delimited groups in the `Remote-Groups` HTTP header. Example `group 1,lt-admin,group2`
- Has an IP of `10.0.0.34` (not required). When not provided it is not checked.
- Users with the `lt-host` group should get host privileges.
- Users with the `lt-admin` group should get admin privileges.
- Users with the `lt-pm` group should get program manager privileges.
- Users with the `lt-superadmin` group should get super admin privileges.
- All other users should get guest privileges.

```yml
header_auth:
  user_header: Remote-User # This is the default and could be omitted
  groups_header: Remote-Groups # This is the default and could be omitted
  email_header: Remote-Email # This is the default and could be omitted
  name_header: Remote-Name # This is the default and could be omitted
  proxy_ip: 10.0.0.34
  group_map:
    host: lt-host
    program_manager: lt-pm
    admin: lt-admin
    superadmin: lt-superadmin
```

If the `user_header` is not found in the request, users will be kicked to the login page
with a message that their username/password is invalid and will not be able to log in. When `proxy_ip` is provided
it will check that the request is coming from the correct proxy before doing the login. This prevents users who have
internal network access from being able to login as whoever they want in LibreTime.

::: warning

If `proxy_ip` is not provided any user on the internal network can log in as any user in LibreTime.

:::

### Enable Header authentication

After everything is set up properly you can enable header auth in `config.yml`:

```yml
general:
  auth: LibreTime_Auth_Adaptor_Header
```

You should now be automatically logged into LibreTime when you click the `Login` button.
