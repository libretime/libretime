# How to broadcast live with LibreTime and Butt

This how to is intended for DJs using butt to stream to their LibreTime
server with an external USB audio card setup to route a mixer and sound.

**Audience**: DJs

## Set Up

1. Download and install butt from <https://danielnoethen.de/> for your OS.
*Note: be sure you have butt version 0.1.17 or newer installed*
1. Open up butt
1. Click **settings**
1. Under **Main** under **Server **click **ADD**
    * Type LibreTime (or your station) under Name
    * Click the radio button next to **IceCast** under Type
    * Type your stations URL (webpage address) under **Address**:
    * Type **8002** under **Port**:
    * Type your DJ login password under **Password**
    *  Type **/show** under IceCast mountpoint:
    * Type your dj login under **IceCast user:**
1. Click **ADD**
1. Still in settings click, **Audio** and select your external sound card under
**Audio Device** *Note: if you only have an internal sound card you maybe able
to use it but that is OS specific and outside of this tutorial. We are assuming
you have a mic and mixer or a USB mixer hooked up to or as an external soundcard*

## Show Time

1. When its almost your show time go to your LibreTime page and look at the time
in the top right when your show starts go to Butt.
1. Click the white Play button (third button in the middle).
1. If it says connecting… and then stream time with a counter– congratulations,
your are connected!
1.  Go to the LibreTime page and at the top right under Source Streams the
tab besides Show Source is to the left and Orange – if it is and Current
shows Live Show you are connected.
1. If it is gray, click on the **Show Source** switch to the right of it and it
will toggle your show on and you will be broadcasting. *Note: whether auto
connect is turned on is a station specific setting so it could work either way*

## Recording your show

You can record your show under butt by clicking the red circle record button on
the left. It will save a mp3 based upon the date and time in your home/user
directory by default.

Everything should now be working and you can broadcast for your entire time
slot. If you choose to stop streaming before it is over click the white square
**Stop** button to disconnect. Then go to the LibreTime page and if the Show
Source didn’t automatically disconnect you can click it to the right and it
should turn gray.

You are now done streaming.

If you have issues connecting check with your system administrator to see if you
have the details right.
