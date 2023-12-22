---
title: Reverse proxy
sidebar_position: 30
---

In some deployments, the LibreTime server is deployed behind a reverse proxy,
for example in containerization use-cases such as Docker and LXC. LibreTime
makes extensive use of its API for some site features, which causes
[Cross-Origin Resource Sharing (CORS)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
to occur. By default, CORS requests are blocked by your browser and the origins
need to be added to the **Allowed CORS URLs** block in
[**General Settings**](../../user-manual/settings.md). These origins should include any
domains that are used externally to connect to your reverse proxy that you
want handled by LibreTime. These URLS can also be set during the first run configuration
that's displayed when you first install LibreTime

### Reverse proxy basics

A reverse proxy allows the LibreTime server to not be connected to the open internet. In
this configuration, it's rather behind another server that proxies traffic to it from
users. This provides some advantages in the containerization space, as this means that
the containers can be on their own internal network, protected from outside access.

A reverse proxy also allows SSL to be terminated in a single location for multiple sites.
This means that all your traffic to the proxy from clients is encrypted, but the reverse
proxy's traffic to the containers on the internal network isn't. All the SSL certificates
live on the reverse proxy and can be renewed there instead of on the individual
containers.

### Setup

For SSL redirection to work, you need two domains: one for LibreTime and one for Icecast.
Here, these will be `libretime.example.com` and `icecast.example.com`.

You require two VMs, servers or containers. Alternatively the reverse proxy can
be located on the server, proxying connections to containers also on the host. Setting up
a containerization environment is beyond the scope of this guide. It assumes that you have
Nginx set up on `localhost` and LibreTime will be installed on `192.168.1.10`. You will need root
access on both. `192.168.1.10` also needs to be able to be accessed from `localhost`
(`ping 192.168.1.10` on `localhost`).

On `192.168.1.10`, install LibreTime as described in the [install guide](./install.md). Once it has installed, replace `<hostname>localhost</hostname>` in
`/etc/icecast2/icecast.xml` with the following:

```xml
<hostname>icecast.example.com</hostname>
```

This is the hostname that people listening to your stream will connect to and what
LibreTime will use to stream out to them. You will then need to restart Icecast using `sudo systemctl restart icecast2`.

On `localhost`, run the following:

```bash
cat << EOF | sudo tee /etc/nginx/sites-available/libretime.conf
server {
    listen 80;
    server_name libretime.example.com;
    location / {
        rewrite ^ https://$server_name$request_uri? permanent;
    }
}
server {
    listen 443 ssl;
    server_name libretime.example.com;
    ssl_certificate /etc/letsencrypt/live/libretime.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/libretime.example.com/privkey.pem;
    add_header Strict-Transport-Security "max-age=15552000;";
    add_header X-Frame-Options "SAMEORIGIN";
    client_max_body_size 512M;
    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://192.168.1.10/;
    }
}
EOF
```

This Nginx configuration ensures that all traffic uses SSL to the reverse proxy, and
traffic is proxied to `192.168.1.10`.

Next, the SSL certificate needs to be generated and the site activated.

```
sudo apt install certbot
sudo systemctl stop nginx
sudo certbot certonly -d libretime.example.com -a standalone
sudo systemctl start nginx
```

You can now go to `https://libretime.example.com` and go
through the installer. On `General Settings`, you need to change the Webserver Port to
`443` and add the following CORS URLs:

```
https://libretime.example.com
http://libretime.example.com
https://localhost
http://localhost
```

Finally, the configuration file needs updating. Under `general.force_ssl`
needs to be set to true:

```yml
general:
  force_ssl: true
```

## SSL Configuration

To increase the security of your server, you can enable encrypted access to the LibreTime administration interface, and direct your users towards this more secure login page. The main advantage of using this encryption is that your remote users' login names and passwords are not sent in plain text across the public Internet or untrusted local networks, such as shared Wi-Fi access points.

## Deploying a certificate with Certbot

One of the fastest, easiest, and cheapest ways to get an SSL certificate is through [Certbot](https://certbot.eff.org/), as created by the
Electronic Frontier Foundation. To use Certbot, your LibreTime installation must be open to the internet on port 80.

Follow [Certbot's documentation](https://certbot.eff.org/instructions) for your OS and webserver to install an SSL certificate. You'll need to renew the certificate every 90 days to keep your installation secure.

## Mixed encrypted and unencrypted content

Whether your certificate is self-signed or not, you will see browser security warnings whenever a https:// page is delivering unencrypted content, such as the stream from an Icecast server. In Firefox, an exclamation mark icon is displayed in the address bar of the **Listen** pop-up.
