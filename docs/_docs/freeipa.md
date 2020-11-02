---
layout: article
title: FreeIPA Configuration
category: install
---

You can configure LibreTime to delegate all authentication to a FreeIPA server.

This allows you users to use their existing FreeIPA credentials. For this to
work you need to configure Apache to use `mod_authnz_pam` and `mod_intercept_form_submit`.

## Apache configuration {#apache}

After installing the needed modules you can set up Apache to intercept form logins and
check them against pam.

```apache
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

## PAM configuration {#pam}

The above configuration expects a PAM configuration for the `http-libretime` service.

To confiure this you need to create the file `/etc/pam.d/http-libretime` with the following contents.

```
auth    required   pam_sss.so
account required   pam_sss.so
```

## LDAP configuration {#ldap}

LibreTime needs direct access to LDAP so it can fetch additional information. It does so with
a [system account](https://www.freeipa.org/page/HowTo/LDAP#System_Accounts) that you need to
set up beforehand.

You can configure everything pertaining to how LibreTime accesses LDAP in 
`/etc/airtime/airtime.conf`. The default file has the following values you need to change.

```ini
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
[ldap]
hostname = ldap.example.org
binddn = 'uid=libretime,cn=sysaccounts,cn=etc,dc=int,dc=example,dc=org'
password = hackme
account_domain = INT.EXAMPLE.ORG
basedn = 'cn=users,cn=accounts,dc=int,dc=example,dc=org'
filter_field = uid
groupmap_guest = 'cn=guest,cn=groups,cn=accounts,dc=int,dc=example,dc=org'
groupmap_host = 'cn=host,cn=groups,cn=accounts,dc=int,dc=example,dc=org'
groupmap_program_manager = 'cn=program_manager,cn=groups,cn=accounts,dc=int,dc=example,dc=org'
groupmap_admin = 'cn=admins,cn=groups,cn=accounts,dc=int,dc=example,dc=org'
groupmap_superadmin = 'cn=superadmin,cn=groups,cn=accounts,dc=int,dc=example,dc=org'
```

## Enable FreeIPA Authentication {#freeipa}

After everything is set up properly you can enable FreeIPA auth in `airtime.conf`:

```
[general]
auth = LibreTime_Auth_Adaptor_FreeIpa
```

You should now be able to use your FreeIPA credentials to log in to LibreTime.
