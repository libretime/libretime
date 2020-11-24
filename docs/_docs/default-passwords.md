---
title: How To Change Default Passwords
layout: article
category: admin
---

### Libretime

To change the password of the current user:

1. Log in to Libretime
2. Click on the username in the upper right corner (next to Log Out)
3. Enter the new password twice and click **Save**

To change the password for a different user (requires *Administrator* privileges):

1. Log in to Libretime
2. Go to **Settings** > **Manage Users**
3. Select the user, enter the new password twice, and click **Save**

### PostgreSQL

Two of the most important passwords that should be changed *immediately* after installation
are the passwords used by the PostgreSQL database.
It is strongly recommended that you do this before exposing your server to the internet beyond your internal network.

1. Login to PostgreSQL with `sudo -u postgres psql`. The PostgreSQL shell - `postgres=#` - means that you have logged in successfully.
2. Change the admin password with `ALTER USER postgres PASSWORD 'myPassword';`, where `myPassword` is the new password.
Make sure to include the semicolon at the end! A response of `ALTER ROLE` means that the command ran successfully.
3. Change the password for the *airtime* user with `ALTER USER airtime WITH PASSWORD 'new_password';`
A response of `ALTER ROLE` means that the command ran successfully.
4. If all is successful, logout of PostgreSQL with `\q`, go back to */etc/airtime/airtime.conf* to edit the password
in the config file, and restart all services mentioned in the previous section.

### Icecast

Random passwords are generated for Icecast during the installation. To look up and change the passwords, look in the file below.

`/etc/icecast2/icecast.xml`

Replace the admin and *changeme* fields below.

```
<authentication>
    <!-- Sources log in with username 'source' -->
    <source-password>changeme</source-password>
    <!-- Relays log in with username 'relay' -->
    <relay-password>changeme</relay-password>
    <!-- Admin logs in with the username given below -->
    <admin-user>admin</admin-user>
    <admin-password>changeme</admin-password>
  </authentication>
```

Then, restart your icecast2 service.

```
sudo systemctl restart icecast2
```

> Note: If you change the source password, you may need to manually configure Libretime to use the new password: go to **Settings** > **Streams**, set the streaming server to **Custom** and fill out the **Additional Options** below Stream 1.

### Rabbitmq

To change the default password for Rabbitmq, run the following command

```
sudo rabbitmqctl change_password airtime newpassword
```

and then update the `/etc/airtime/airtime.conf` file with the new password.