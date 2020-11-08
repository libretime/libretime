---
layout: article
title: Built-in Microsite
category: admin
---

![](/img/radio-page.png)

LibreTime includes a microsite, which can be accessed at _serverIP_ or a domain you've set up for your server. The site includes your
logo and station description (set under Settings > General), the login button to the LibreTime interface, the schedule for the next seven days,
podcast tabs, and a live feed of your station with information on the the currently playing artist and track.

## Modifying the LibreTime Radio Page

The background of the mini-site that appears when you visit the server's domain in your web browser can be changed by modifying the page's CSS file, located at */usr/share/airtime/php/airtime_mvc/public/css/radio-page/radio-page.css*.

```
html {
    background: url("img/background-testing-3.jpg") no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    overflow-y: auto;
}
```

Place the new background image in the `/usr/share/airtime/public/css/radio-page/img/` folder and change the `background:` entry's URL to point to the new image. The new image should be at least 1280 x 720 in pixel size to avoid being blurry.
