---
layout: article
title: Icecast Configuration
category: admin
---

## Background

LibreTime supports direct connection to two popular streaming media servers, the open source **Icecast** (<http://www.icecast.org>) and the proprietary **SHOUTcast** (<http://www.shoutcast.com>). Apart from the software license, the main difference between these two servers is that Icecast supports simultaneous MP3, AAC, Ogg Vorbis or Ogg Opus streaming from LibreTime, whereas SHOUTcast supports MP3 and AAC streams but not Ogg Vorbis or Opus. The royalty-free Ogg Vorbis format has the advantage of better sound quality than MP3 at lower bitrates, which has a direct impact on the amount of bandwidth that your station will require to serve the same number of listeners. Ogg Opus also benefits from good sound quality at low bitrates, with the added advantage of lower latency than other streaming formats. Opus is now an IETF standard (<http://tools.ietf.org/html/rfc6716>) and requires Icecast 2.4 or later to be installed on the streaming server.

Ogg Vorbis playback is supported in **Mozilla Firefox**, **Google Chrome** and **Opera** browsers, via **jPlayer** (<http://jplayer.org/>), and is also supported in several popular media players, including VideoLAN Client, also known as VLC (<http://www.videolan.org/vlc/>). (See the chapter *Stream player for your website* on how to deliver **jPlayer** to your audience). Ogg Opus is relatively new and is supported natively in the very latest browsers, such as Mozilla Firefox 25.0, and media players including VLC 2.0.4 or later.

Streaming MP3 below a bitrate of 128kbps is not recommended for music, because of a perceptible loss of high audio frequencies in the broadcast playout. A 96kbps or 64kbps MP3 stream may be acceptable for voice broadcasts if there is a requirement for compatibility with legacy hardware playback devices which do not support Ogg Vorbis or Opus streams.

Because LibreTime supports simultaneous streaming in multiple formats, it is possible to offer one or more streams via your website, and another independent stream for direct connection from hardware players. You can test whether Ogg streams sound better at low bitrates for yourself, by using the **LISTEN** button in LibreTime's **Master Panel** to switch between streaming formats.

Conversely, you may have a music station which wants to stream at 160kbps or 192kbps to offer a quality advantage over stations streaming at 128kbps or less. Since Ogg, AAC and MP3 formats use lossy compression, listeners will only hear the benefit of higher streaming bitrates if the media files in the LibreTime storage server are encoded at an equivalent bitrate, or higher.

## UTF-8 metadata in Icecast MP3 streams

When sending metadata about your stream to an Icecast server in non-Latin alphabets, you may find that Icecast does not display the characters correctly for an MP3 stream, even though they are displayed correctly for an Ogg Vorbis stream. In the following screenshot, Russian characters are being displayed incorrectly in the *Current Song* field for the MP3 stream:

![](/img/Screenshot223-Icecast_UTF-8_metadata.png)

The solution is to specify that the metadata for the MP3 mount point you are using should be interpreted using UTF-8 encoding. You can do this by adding the following stanza to the */etc/icecast2/icecast.xml* file, where *libretime.mp3* is the name of your mount point:

      <mount>
           <mount-name>/libretime.mp3</mount-name>
           <charset>UTF-8</charset>
      </mount>

After saving the */etc/icecast2/icecast.xml* file, you should restart the Icecast server:

    sudo invoke-rc.d icecast2 restart
    Restarting icecast2: Starting icecast2
    Detaching from the console
    icecast2.

## Icecast handover configuration

In a typical radio station configuration, the live output from the broadcast studio and the scheduled output from LibreTime are mixed together before being sent further along the broadcast chain, to a transmitter or streaming media server on the Internet. (This may not be the case if your LibreTime server is remote from the studio, and you are using the **Show Source Mount Point** or **Master Source Mount Point** to mix live and scheduled content. See the *Stream Settings* chapter for details).

If your **Icecast** server is hosted in a remote data centre, you may not have the option to handover the streaming media source manually, because you have no physical access to connect a broadcast mixer to the server. Disconnecting the stream and beginning another is less than ideal, because the audience's media players will also be disconnected when that happens.

The **Icecast** server has a *fallback-mount* feature which can be used to move clients (media players used by listeners or viewers) from one source to another, as new sources become available. This makes it possible to handover from LibreTime output to a show from another source, and handover to LibreTime again once the other show has ended.

To enable fallback mounts, edit the main Icecast configuration file to define the mount points you will use, and the relationship between them.

    sudo nano /etc/icecast2/icecast.xml

The example *<mount>* section provided in the *icecast.xml* file is commented out by default. Before or after the commented section, add three mount point definitions. The default mount point used by LibreTime is */airtime\_128* which is shown in the */etc/airtime/liquidsoap.cfg* file. You must also define a mount point for the live source (called */live.ogg* in this example) and a mount point for the public to connect to (called */stream.ogg* in this example).

       <mount>
            <mount-name>/airtime_128</mount-name>
            <hidden>0</hidden>
       </mount>

       <mount>
            <mount-name>/live.ogg</mount-name>
            <fallback-mount>/airtime_128</fallback-mount>
            <fallback-override>1</fallback-override>
            <hidden>0</hidden>
       </mount>

       <mount>
            <mount-name>/stream.ogg</mount-name>
            <fallback-mount>/live.ogg</fallback-mount>
            <fallback-override>1</fallback-override>
            <hidden>0</hidden>
       </mount>

These mount point definitions mean that a client connecting to a URL such as *http://icecast.example.com:8000/stream.ogg* will first fall back to the */live.ogg* mount point if it is available. If not, the client will fall back in turn to the */airtime\_128* mount point for LibreTime playout.

Setting the value of *<fallback-override>* to 1 (enabled) means that when the */live.ogg* mount point becomes available again, the client will be re-connected to it.  If you wish to hide the */airtime\_128* and */live.ogg* mount points from the public Icecast web interface, set the value of *<hidden>* in each of these definitions to 1.

## Source configuration

Connect the other source to the Icecast server with the same parameters defined in the */etc/airtime/liquidsoap.cfg* file, except for the mount point. This should one of the mount points you have defined in the */etc/icecast2/icecast.xml* file, such as */live.ogg* in the example above.

To configure **Mixxx** for streaming to Icecast, click *Options*, *Preferences*, then *Live Broadcasting*. For server *Type*, select the default of *Icecast 2* when streaming to Debian or Ubuntu servers, as this is the current version of Icecast supplied with those GNU/Linux distributions.

![](/img/Screenshot123-Mixxx_Preferences.png) 

By default, Icecast streams are buffered to guard against network problems, which causes latency for remote listeners. When monitoring the stream from a remote location, you may have to begin the live stream a few seconds before the previous stream ends to enable a smooth transition.

## Promoting your station through Icecast

If you have an Icecast server, you can put a link to the Icecast status page (by default at port 8000) on your station's homepage,
to provide an overview of available streams. See the chapter *Interface customization* for tips on theming the
Icecast status page. You can also use Now Playing widgets (see the chapter *Exporting the schedule*) or HTML5 stream players (see the chapter *Stream player for your website*) to help grow your audience.

On an Icecast server, you can uncomment the `<directory>` section in the _/etc/icecast2/icecast.xml_ file to have
your station automatically listed on the Icecast directory website <http://dir.xiph.org> which could help you pick
up more listeners.

        <!-- Uncomment this if you want directory listings -->

        <directory>
            <yp-url-timeout>15</yp-url-timeout>
            <yp-url>http://dir.xiph.org/cgi-bin/yp-cgi</yp-url>
        </directory>

The Indymedia stream directory at <http://radio.indymedia.org/en/yp> links to grassroots independent radio projects around the world. You can add your station to their list with an additional *<directory>* section, as follows:

        <directory>
             <yp-url-timeout>15</yp-url-timeout>
             <yp-url>http://radio.indymedia.org/cgi-bin/yp-cgi</yp-url>
        </directory>

Another stream directory service is provided by the Liquidsoap Flows! site <http://flows.liquidsoap.fm/>. The following section can be added to the file */usr/lib/airtime/pypo/bin/liquidsoap\_scripts/ls\_script.liq* after *add\_skip\_command(s)* on line 174, for a stream named '*ourstation*':

    ourstation = register_flow(
      radio="Rock 'n Roll Radio",
      website="http://radio.example.com/",
      description="Canada's most rockin' radio!",
      genre="Rock",
      user="",
      password="",
      streams=[("ogg/128k","http://streaming.example.com/libretime_128")],
      ourstation)

> **Note:** For the time being, a stream can be registered on the Liquidsoap Flows! site with any username and password. Authenticated services may be offered in future.