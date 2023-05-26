---
title: Install using the installer
sidebar_position: 20
---

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';
import CodeBlock from '@theme/CodeBlock';
import vars from '@site/vars';

This guide walk you though the steps required to install LibreTime on your system using the installer.

The installer is shipped in the released tarballs or directly in the project repository.

Installing on one of the following [distribution releases](../../releases/README.md#distributions-releases-support) is recommend:

- [Debian 11](https://www.debian.org/releases/)
- [Ubuntu 20.04 LTS](https://wiki.ubuntu.com/Releases)

## Before installing

Before installing LibreTime, you need to make sure you operating system is **up to date** and configured.

### Operating system time configuration

Check your operating system time configuration using the following command:

```bash
sudo timedatectl
```

```
               Local time: Fri 2022-08-05 12:43:39 CEST
           Universal time: Fri 2022-08-05 10:43:39 UTC
                 RTC time: Fri 2022-08-05 10:43:40
                Time zone: Europe/Berlin (CEST, +0200)
System clock synchronized: yes
              NTP service: active
          RTC in local TZ: no
```

Make sure that your time zone is configured, if not you can set it using the [`timedatectl set-timezone` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-timezone%20%5BTIMEZONE%5D). The following command configure the timezone to `Europe/Paris`, make sure to set your own timezone:

```bash
sudo timedatectl set-timezone Europe/Paris
```

If the NTP service is inactive, you should consider enabling it using the [`timedatectl set-ntp` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-ntp%20%5BBOOL%5D). The following command enables the `NTP service`:

```bash
sudo timedatectl set-ntp true
```

Finally, check that everything was applied by running `timedatectl`:

```bash
sudo timedatectl
```

## Download

You can either download the latest released tarball or clone the repository.

<Tabs>
<TabItem label="Release tarball" value="tarball" default>

Download the [latest released](https://github.com/libretime/libretime/releases) tarball from Github.

Or directly from the command-line:

<CodeBlock language="bash">
wget https://github.com/libretime/libretime/releases/download/{vars.version}/libretime-{vars.version}.tar.gz
</CodeBlock>

And extract the tarball:

<CodeBlock language="bash">
tar -xvf libretime-{vars.version}.tar.gz && cd libretime
</CodeBlock>

</TabItem>
<TabItem label="Git repository" value="git">

Clone the project repository:

```bash
git clone https://github.com/libretime/libretime
cd libretime
```

:::caution

Don't use the https://github.com/libretime/libretime-debian-packaging repository, it's only used to create LibreTime packages.

:::

:::info

When upgrading, you should clean the local repository, pull the latest changes and finally check out the desired version:

```bash
cd libretime
git clean -xdf
git pull
```

:::

And checkout the latest version:

<CodeBlock language="bash">
git checkout {vars.version}
</CodeBlock>

</TabItem>
</Tabs>

## Run the installer

By default the installer will configure LibreTime to listen at the port `80`, but this isn't the recommended way to install LibreTime. Instead you should configure a [reverse proxy in front of LibreTime](./reverse-proxy.md) to secure the connection using HTTPS, and route the traffic to the LibreTime server.

Install LibreTime with the following command, be sure to replace `https://libretime.example.com` with the public url of your installation:

```bash
sudo ./install --listen-port 8080 https://libretime.example.com
```

:::caution

When upgrading be sure to run the installer using the same arguments you used during the initial install.

:::

:::warning

To update the LibreTime nginx configuration file, for example to change the `--listen-port`, make sure to add the `--update-nginx` flag to allow overwriting the existing configuration file.

:::

If you need to change some configuration, the install script can be configured using flags or environment variables. Changing the listening port of LibreTime or whether you want to install some dependency by yourself, you could run the following:

```bash
# Install LibreTime on your system with the following tweaks:
# - don't install the liquidsoap package (remember to install liquidsoap yourself)
# - set the listen port to 8080
# - don't run the PostgreSQL setup (remember to setup PostgreSQL yourself)
sudo \
LIBRETIME_PACKAGES_EXCLUDES='liquidsoap' \
./install \
  --listen-port 8080 \
  --no-setup-postgresql \
  https://libretime.example.com
```

You can persist the install configuration in a `.env` file next to the install script. For example, the above command could be persisted using the `.env` file below, and you should be able to run the install script without arguments:

```
LIBRETIME_PACKAGES_EXCLUDES='liquidsoap'
LIBRETIME_LISTEN_PORT='8080'
LIBRETIME_SETUP_POSTGRESQL=false
LIBRETIME_PUBLIC_URL='https://libretime.example.com'
```

:::note

The install script will use generated passwords to create the PostgreSQL user, RabbitMQ user and to update the default Icecast passwords. Those passwords will be saved to the configuration files.

:::

Feel free to run `./install --help` to get more details.

### Using the system audio output

If you plan to output analog audio directly to a mixing console or transmitter, the user running LibreTime needs to be added to the `audio` user group using the command below:

```bash
sudo adduser libretime audio
```

## Setup LibreTime

Once the installation is completed, edit the [configuration file](../configuration.md) at `/etc/libretime/config.yml` to fill required information and to match your needs.

You may have to configure your timezone to match the one configured earlier:

```git title="/etc/libretime/config.yml"
   # The server timezone, should be a lookup key in the IANA time zone database,
   # for example Europe/Berlin.
   # > default is UTC
-  timezone: UTC
+  timezone: Europe/Paris
```

Next, run the following commands to setup the database:

```bash
sudo -u libretime libretime-api migrate
```

Finally, start the services, and check that they're running using the following commands:

```bash
sudo systemctl start libretime.target

sudo systemctl --all --plain | grep libretime
```

## Securing LibreTime

### Install Certbot

The first step to using Let’s Encrypt to obtain an SSL certificate is to install the Certbot software on your server:

```bash
sudo apt install certbot python3-certbot-nginx
```

Let’s Encrypt’s certificates are only valid for ninety days. The certbot package takes care of this for you by adding a systemd timer that will run twice a day and automatically renew any certificate that’s within thirty days of expiration.

You can query the status of the timer using:

```bash
sudo systemctl status certbot.timer
```

### Configure a reverse proxy

Next, you have to [configure a reverse proxy](./reverse-proxy.md) to route the traffic from port `80` to LibreTime (port `8080`).

Copy the following in a new Nginx configuration file, make sure to replace `libretime.example.com` with your own domain name:

```nginx title="/etc/nginx/sites-available/libretime.example.com.conf"
server {
  listen 80;
  listen [::]:80;

  server_name libretime.example.com;

  location / {
    proxy_set_header Host              $host;
    proxy_set_header X-Real-IP         $remote_addr;
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Host  $host;
    proxy_set_header X-Forwarded-Port  $server_port;

    proxy_pass http://localhost:8080/;
  }
}
```

Enable the new reverse proxy configuration, make sure to replace `libretime.example.com` with your own domain name:

```bash
sudo ln -s /etc/nginx/sites-{available,enabled}/libretime.example.com.conf
```

Then, check that the nginx config is valid and reload nginx:

```bash
sudo nginx -t

sudo systemctl reload nginx
```

#### Obtain a certificate

Certbot provides a variety of ways to obtain SSL certificates through plugins. The Nginx plugin will take care of reconfiguring Nginx and reloading the config whenever necessary.

To request a Let’s Encrypt certificate using Certbot with the Nginx plugin, be sure to replace `libretime.example.com` with the domain name of your installation and run the following:

```bash
sudo certbot --nginx -d libretime.example.com
```

### Setup the certificate for Icecast

By default, browsers will [prevent loading mixed content](https://developer.mozilla.org/en-US/docs/Web/Security/Mixed_content) on secure pages, so you won't be able to listen the insecure Icecast streams on a secure website. To fix that you need to secure the Icecast streams.

Create a Icecast specific SSL certificate bundle, be sure to replace `libretime.example.com` with the domain name of your installation:

```bash
sudo bash -c "install \
  --group=icecast \
  --mode=640 \
  <(cat /etc/letsencrypt/live/libretime.example.com/{fullchain,privkey}.pem) \
  /etc/icecast2/bundle.pem"
```

Enable the secure socket and set the SSL certificate bundle path in the Icecast configuration file:

```git title="/etc/icecast2/icecast.xml"
     <!-- You may have multiple <listen-socket> elements -->
     <listen-socket>
         <port>8000</port>
         <!-- <bind-address>127.0.0.1</bind-address> -->
         <!-- <shoutcast-mount>/stream</shoutcast-mount> -->
     </listen-socket>
     <!--
     <listen-socket>
         <port>8080</port>
     </listen-socket>
     -->
-    <!--
     <listen-socket>
         <port>8443</port>
         <ssl>1</ssl>
     </listen-socket>
-    -->
```

```git title="/etc/icecast2/icecast.xml"
         <!-- Aliases: can also be used for simple redirections as well,
              this example will redirect all requests for http://server:port/ to
              the status page
         -->
         <alias source="/" destination="/status.xsl"/>
         <!-- The certificate file needs to contain both public and private part.
              Both should be PEM encoded.
         <ssl-certificate>/usr/share/icecast2/icecast.pem</ssl-certificate>
         -->
+        <ssl-certificate>/etc/icecast2/bundle.pem</ssl-certificate>
     </paths>
```

Restart Icecast to apply the changes:

```bash
sudo systemctl restart icecast2
```

Next, you need to change the LibreTime `stream.outputs.icecast.*.public_url` configuration to use the newly enabled Icecast secure port:

```git title="/etc/libretime/config.yml"
     # Icecast output streams.
     # > max items is 3
     icecast:
       - <<: *default_icecast_output
         enabled: true
-        public_url:
+        public_url: https://libretime.example.com:8443/main.ogg
         mount: main.ogg
         audio:
           format: ogg
           bitrate: 256

       - <<: *default_icecast_output
         enabled: true
-        public_url:
+        public_url: https://libretime.example.com:8443/main.mp3
         mount: main.mp3
         audio:
           format: mp3
           bitrate: 320
```

Restart the LibreTime to apply the changes:

```bash
sudo systemctl restart libretime.target
```

Finally, you need to configure the Certbot renewal to bundle a Icecast specific SSL certificate and restart the Icecast service:

```git title="/etc/letsencrypt/renewal/libretime.example.com.conf"
 # Options used in the renewal process
 [renewalparams]
 account = d76ce6a241c7c74f79e5443216ee420e
 authenticator = nginx
 installer = nginx
 server = https://acme-v02.api.letsencrypt.org/directory
+
+deploy_hook = 'bash -c "install --group=icecast --mode=640 <(cat $RENEWED_LINEAGE/{fullchain,privkey}.pem) /etc/icecast2/bundle.pem && systemctl restart icecast2"'
```

Check that the renewal configuration is valid:

```bash
sudo certbot renew --dry-run
```

### Setup the certificate for Liquidsoap

To stream audio content from an external source to the LibreTime server, Liquidsoap creates input harbors (Icecast mount points) for the clients to connect to. These mount points are insecure by default, so it's recommended secure them.

To enable the secure input streams, edit the [configuration file](../configuration.md) at `/etc/libretime/config.yml` with the following, be sure to replace `libretime.example.com` with the domain name of your installation:

```git title="/etc/libretime/config.yml"
 liquidsoap:
-  harbor_ssl_certificate:
-  harbor_ssl_private_key:
+  harbor_ssl_certificate: /etc/letsencrypt/live/libretime.example.com/fullchain.pem
+  harbor_ssl_private_key: /etc/letsencrypt/live/libretime.example.com/privkey.pem
```

```git title="/etc/libretime/config.yml"
 stream:
   inputs:
     main:
       public_url:
       mount: main
       port: 8001
-      secure: false
+      secure: true

     show:
       public_url:
       mount: show
       port: 8002
-      secure: false
+      secure: true
```

Restart the LibreTime to apply the changes:

```bash
sudo systemctl restart libretime.target
```

## First login

Once the setup is completed, log in the interface (with the default user `admin` and password `admin`), and edit the project settings (go to **Settings** > **General**) to match your needs.

:::warning

Remember to change your password.

:::
