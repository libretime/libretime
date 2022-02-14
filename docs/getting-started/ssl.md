---
title: SSL Configuration
sidebar_position: 4
---

To increase the security of your server, you can enable encrypted access to the LibreTime administration interface, and direct your users towards this more secure login page. The main advantage of using this encryption is that your remote users' login names and passwords are not sent in plain text across the public Internet or untrusted local networks, such as shared Wi-Fi access points.

## Deploying a certificate with Certbot

One of the fastest, easiest, and cheapest ways to get an SSL certificate is through [Certbot](https://certbot.eff.org/), as created by the
Electronic Frontier Foundation. To use Certbot, your LibreTime installation must be open to the internet on port 80.

Follow [Certbot's documentation](https://certbot.eff.org/instructions) for your OS and webserver to install an SSL certificate. You'll need to renew the certificate every 90 days to keep your installation secure.

## Mixed encrypted and unencrypted content

Whether your certificate is self-signed or not, you will see browser security warnings whenever a https:// page is delivering unencrypted content, such as the stream from an Icecast server. In Firefox, an exclamation mark icon is displayed in the address bar of the **Listen** pop-up.
