---
title: Icecast configuration
sidebar_position: 30
---

## Background

LibreTime supports direct connection to two popular streaming media servers, the open source Icecast (https://www.icecast.org/) and the proprietary SHOUTcast (https://www.shoutcast.com). Apart from the software license, the main difference between these two servers is that Icecast supports simultaneous MP3, AAC, Ogg Vorbis or Ogg Opus streaming from LibreTime, whereas SHOUTcast only supports MP3 and AAC streams. The royalty-free Ogg Vorbis format has the advantage of better sound quality than MP3 at lower bitrates, which has a direct impact on the amount of bandwidth that your station will require to serve the same number of listeners. Ogg Opus also benefits from good sound quality at low bitrates, with the added advantage of lower latency than other streaming formats. Opus is now an [IETF standard](https://datatracker.ietf.org/doc/html/rfc6716) and requires Icecast 2.4 or later to be installed on the streaming server.

Ogg Vorbis playback is supported in most modern web browsers (see [this MDN article](https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Audio_codecs#opus) for more information) and desktop players like [VLC](https://www.videolan.org/vlc/).

Streaming MP3 below a bitrate of 128kbps is not recommended for music, because of a perceptible loss of high audio frequencies in the broadcast playout. A 96kbps or 64kbps MP3 stream may be acceptable for voice broadcasts if there is a requirement for compatibility with legacy hardware playback devices which do not support Ogg Vorbis or Opus streams.

Because LibreTime supports simultaneous streaming in multiple formats, it is possible to offer one or more streams via your website, and another independent stream for direct connection from hardware players. You can test whether Ogg streams sound better at low bitrates for yourself, by using the **LISTEN** button in LibreTime's **Master Panel** to switch between streaming formats.

:::tip

Setting a higher bitrate for your output stream will only benefit your listeners if you have high bitrate source material to play. LibreTime can convert bitrates down for lower-quality streams but _cannot_ convert up for higher-quality streams.

:::

## UTF-8 metadata in Icecast MP3 streams

When sending metadata about your stream to an Icecast server in non-Latin alphabets, you may find that Icecast does not display the characters correctly for an MP3 stream, even though they're displayed correctly for an Ogg Vorbis stream. In the following screenshot, Russian characters are being displayed incorrectly in the _Current Song_ field for the MP3 stream:

![](./icecast-screenshot223-icecast_utf-8_metadata.png)

The solution is to specify that the metadata for the MP3 mount point you are using should be interpreted using UTF-8 encoding. You can do this by adding the following stanza to the `/etc/icecast2/icecast.xml` file, where `libretime.mp3` is the name of your mount point:

```xml
<mount>
  <mount-name>/libretime.mp3</mount-name>
  <charset>UTF-8</charset>
</mount>
```

After saving the `/etc/icecast2/icecast.xml` file, restart the Icecast server with `sudo systemctl restart icecast2`.

## Icecast handover configuration

In a typical radio station configuration, the live output from the broadcast studio and the scheduled output from LibreTime are mixed together before being sent further along the broadcast chain, to a transmitter or streaming media server on the Internet. (This may not be the case if your LibreTime server is remote from the studio, and you are using the **Show Source Mount Point** or **Master Source Mount Point** to mix live and scheduled content. See the _Stream Settings_ chapter for details).

If your Icecast server is hosted in a remote data centre, you may not have the option to handover the streaming media source manually, because you have no physical access to connect a broadcast mixer to the server. Disconnecting the stream and beginning another is less than ideal, because the audience's media players will also be disconnected when that happens.

The Icecast server has a _fallback-mount_ feature which can be used to move clients (media players used by listeners or viewers) from one source to another, as new sources become available. This makes it possible to handover from LibreTime output to a show from another source, and handover to LibreTime again once the other show has ended.

To enable fallback mounts, edit the main Icecast configuration file (`/etc/icecast2/icecast.xml`) to define the mount points you will use, and the relationship between them.

The example mount section provided in the `icecast.xml` file is commented out by default. Before or after the commented section, add three mount point definitions. The default mount point used by LibreTime is `/main` which is shown in the `/etc/libretime/liquidsoap.cfg` file. You must also define a mount point for the live source (ex. `/live.ogg`) and a mount point for the public to connect to (ex. `/stream.ogg`).

```xml title="/etc/icecast2/icecast.xml"
<mount>
     <mount-name>/main</mount-name>
     <hidden>0</hidden>
</mount>

<mount>
     <mount-name>/live.ogg</mount-name>
     <fallback-mount>/main</fallback-mount>
     <fallback-override>1</fallback-override>
     <hidden>0</hidden>
</mount>

<mount>
     <mount-name>/stream.ogg</mount-name>
     <fallback-mount>/live.ogg</fallback-mount>
     <fallback-override>1</fallback-override>
     <hidden>0</hidden>
</mount>
```

These mount point definitions mean that a client connecting to a URL such as *http://icecast.example.com:8000/stream.ogg* will first fall back to the `/live.ogg` mount point if it is available. If not, the client will fall back in turn to the `/main` mount point for LibreTime playout.

Setting the value of _fallback-override_ to 1 (enabled) means that when the `/live.ogg` mount point becomes available again, the client will be re-connected to it. If you wish to hide the `/main` and `/live.ogg` mount points from the public Icecast web interface, set the value of _hidden_ in each of these definitions to 1.

## Source configuration

Connect the other source to the Icecast server with the same parameters defined in the `/etc/libretime/liquidsoap.cfg` file, except for the mount point. This should one of the mount points you have defined in the `/etc/icecast2/icecast.xml` file, such as `/live.ogg` in the example above.

:::tip Streaming with Mixxx

To configure Mixxx for streaming to Icecast, click _Options_, _Preferences_, then _Live Broadcasting_. For server _Type_, select the default of _Icecast 2_ when streaming to Debian or Ubuntu servers.

:::

By default, Icecast streams are buffered to guard against network problems, which causes latency for remote listeners. When monitoring the stream from a remote location, you may have to begin the live stream a few seconds before the previous stream ends to enable a smooth transition.

## Promoting your station

:::note

This section covers how to edit Icecast's configuration to broadcast your station's information to online radio station directories. If you aren't using Icecast (or don't want to edit the configuration file), many online directories will allow you to manually add your station to their listings.

:::

There are many online radio station directories you can add your station to for additional exposure.

On an Icecast server, you can uncomment the `directory` section in the `/etc/icecast2/icecast.xml` file to have
your station automatically listed on the [Icecast directory website](https://dir.xiph.org/).

```xml
<!-- Uncomment this if you want directory listings -->

<directory>
     <yp-url-timeout>15</yp-url-timeout>
     <yp-url>https://dir.xiph.org/cgi-bin/yp-cgi</yp-url>
</directory>
```

The Indymedia stream directory at https://radio.indymedia.org links to grassroots independent radio projects around the world. You can add your station to their list with an additional _directory_ section, as follows:

```xml
<directory>
  <yp-url-timeout>15</yp-url-timeout>
  <yp-url>https://radio.indymedia.org/cgi-bin/yp-cgi</yp-url>
</directory>
```
