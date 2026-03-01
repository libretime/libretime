---
title: Broadcasting live
---

## MIXXX

[Mixxx](https://www.mixxx.org) is a cross-platform Open Source application for DJs.

Installed on a desktop or laptop computer, Mixxx complements your LibreTime server to provide a complete system
for both live and scheduled broadcasting. Although Mixxx has many features designed for dance music DJs
that require beat matching and pitch independent time stretching, the program can be used for any kind of
manually triggered broadcast playout, including live speech shows such as news or current affairs.

Mixxx supports a wide variety of popular hardware control surfaces, which can be connected to your
computer using a USB cable. A control surface might replace or augment an analogue mixer in your studio,
depending on your live mixing and playout requirements.

Mixxx 1.9.0 or later includes a live streaming client which, like LibreTime, is compatible with the Icecast
and SHOUTcast media servers. This feature can also be used to stream from Mixxx directly into LibreTime,
using either the **Show Source** or **Master Source**.

To configure Mixxx for streaming into LibreTime, click **Options**, **Preferences**, then
**Live Broadcasting** on the main Mixxx menu. For server **Type**, select the default of **Icecast 2**.
For **Host**, **Mount**, **Port**, **Login** and **Password**, use the **Input Stream Settings**
configured in the LibreTime **Streams** page, on LibreTime's **System** menu.

## B.U.T.T. (Broadcast Using This Tool)

<iframe
   width="560"
   height="315"
   src="https://www.youtube-nocookie.com/embed/4GLsU9hPTtM"
   frameborder="0"
   allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
   allowfullscreen
></iframe>

### Setup

1. Download and install [BUTT](https://danielnoethen.de/) for your OS.
   _Note: be sure you have butt version 0.1.17 or newer installed_
2. Open up BUTT
3. Click **settings**
4. Under **Main** > **Server** click **ADD**
   - Type LibreTime (or your station) under Name
   - Click the radio button next to **IceCast** under Type
   - Type your stations URL (webpage address) under **Address**:
   - Type **8002** under **Port**:
   - Type your DJ login password under **Password**
   - Type **/show** under IceCast mountpoint:
   - Type your dj login under **IceCast user:**
5. Click **ADD**
6. Still in settings click, **Audio** and select your audio input device under
   **Audio Device**

### Show time

1. When its almost your show time go to your LibreTime page and look at the time in the top right when your show starts go to Butt.
2. Click the white Play button (third button in the middle).
3. If it says connecting... and then stream time with a counter- congratulations, your are connected.
4. Go to the LibreTime page and at the top right under Source Streams the
   tab besides Show Source is to the left and Orange - if it's and Current
   shows Live Show you are connected.
5. If it's gray, click on the **Show Source** switch to the right of it and it
   will toggle your show on and you will be broadcasting. _Note: whether auto
   connect is turned on is a station specific setting so it could work either way_

### Recording your show

You can record your show under butt by clicking the red circle record button on
the left. It will save a mp3 based upon the date and time in your home/user
directory by default.

Everything should now be working and you can broadcast for your entire time
slot. If you choose to stop streaming before it's over click the white square
**Stop** button to disconnect. Then go to the LibreTime page and if the Show
Source didn't automatically disconnect you can click it to the right and it
should turn gray.
