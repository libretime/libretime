---
layout: page
title: Features
blurb: Just some of LibreTime's greatest hits
---

* *Web-based remote station management* - authorized personnel can add
   programme material, create playlists or smart blocks, and stream in live,
   all via a web interface.
* *Automation* - LibreTime has a scheduler function that enables users to
   create shows with content for playback at the exact date and time specified.
   Playlists, smart blocks and remote stream URLs can be used multiple times.
* *Solid playout* - LibreTime uses the open source Liquidsoap streaming language
   for reliable and precise playback to multiple outputs.
* *Open source* - run LibreTime royalty-free, make changes to the code, and contribute to the project as you see fit, under the GNU AGPLv3 license.
* *Multilingual* - supports over 15 languages both in the interface and inside file metadata
* *Low system requirements*
  * For servers: 1Ghz processor, 2 GB RAM, and a wired ethernet connection with a static IP address
  * For end-users: a modern version of Firefox, Chrome, or Safari, and a screen resolution of at least 1280x768

![](img/Screenshot540-Now_playing_250.png)

LibreTime is intended to provide a solution for a wide range of broadcast
projects, from community to public and commercial stations. The scalability of
LibreTime allows implementation in a number of scenarios, ranging from an
unmanned broadcast unit accessed remotely through the Internet, to a local
network of machines accessing a central LibreTime storage system. LibreTime
supports the playout of lossy compressed audio files in both MP3 and AAC
formats and the open, royalty-free equivalent
[Ogg Vorbis](http://www.vorbis.com/ "Ogg Vorbis homepage"). It also supports
playout of lossless FLAC and WAV format audio files.

LibreTime manages the [Liquidsoap](http://savonet.sourceforge.net/) stream
generator at the heart of the system. Liquidsoap generates streams from files
in the LibreTime library and any remote input streams that you specify.
Available stream output formats include Ogg Vorbis, Ogg Opus, MP3, and AAC. The
library is indexed in a [PostgreSQL](http://www.postgresql.org/) database to
enable searching. Live shows can be recorded automatically with
[Ecasound](http://eca.cx/ecasound/ "Ecasound homepage"), using the sound card
line input. News editors, DJs and station controllers can use LibreTime to
build playlists or smart blocks and manage media files (upload, edit metadata,
manage advertisements) at the station or via the Internet.

The scheduler in LibreTime has a calendar view, organized by months, weeks or
days. Program editors can schedule playlists and shows here for their
broadcast station. In some scenarios, the transmitter is situated outside the
reach of the broadcaster and all program management has to be maintained
through the web interface. Possible reasons for this scenario might be of a
pragmatic nature (running many stations from one central office due to limited
human resources) or an emergency (running a transmitter in a crisis area
without putting staff at risk).

LibreTime services
----------------

| Service | Description |
|---------|-------------|
| libretime-analyzer | Keeps track of files being added, renamed, moved or removed from storage, and reads their metadata using the Mutagen library. |
| [PostgreSQL](https://www.postgresql.org/) | Contains the location of those media files and their metadata. This means you can search for and playlist a set of media files according to the specific metadata that you require, or use a 'smart block' to select the files for you. The database also contains details of specified remote input streams. |
| Pypo | (Python Playout engine) Downloads media from the storage up to 24 hours ahead of playout and checks it for average level (with ReplayGain tools) and leading or trailing silence (with Silan). At playout time, the media to be broadcast is sent to Liquidsoap. |
| [Liquidsoap](https://www.liquidsoap.info/) | Takes individual media files and remote input streams, and assembles them into a continuous output stream. This stream can be sent to a sound card (e.g. for a broadcast mixer, on the way to an FM or DAB transmitter) or to a streaming server for IP network distribution, over the LAN, local WiFi or the Internet. You can stream to a sound card and up to three different stream distribution servers with the same LibreTime server, if you wish. |
| [Icecast](https://www.icecast.org/) or [Shoutcast](https://shoutcast.com/) | Audio streaming server, used for creating an internet radio stream from LibreTime. Icecast is included in the LibreTime installation by default. Note: If a suitable Liquidsoap output is not available for your streaming service of choice, you can send audio from Liquidsoap to a separate encoding or streaming machine via a sound card or relay stream. |
| [Monit](https://mmonit.com/monit/) | Monitors the health of pypo, libretime-analyzer and Liquidsoap, and reports the status of these services to LibreTime. |
| [RabbitMQ](https://www.rabbitmq.com/) | Pushes messages from LibreTime to libretime-analyzer and pypo about changes to media files and the playout schedule. |

Example studio broadcast system
-------------------------------

In the diagram of an FM radio station below, LibreTime is hosted on a server
connected to the local network, with direct soundcard access. Liquidsoap
outputs streams to both the transmitter, via the main studio mixer, and
streaming media servers. The machine running LibreTime is behind a firewall
because it is also connected to the Internet for remote access by media
contributors. This enables LibreTime to offer password-protected access to the
media library and scheduling from both inside and outside the studio building.

![](img/libretime_architecture.svg)

Example web broadcast system
----------------------------

In the diagram below, LibreTime is hosted on a remote web server, and has no
soundcard. There does not need to be a centralised studio, although LibreTime
can enable remote studios to stream in to Liquidsoap at authorised times.
Optionally, the outgoing Icecast stream can be relayed to a transmitter.

![](img/libretime_web_architecture.svg)

<html>
  <section id="faq">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <h2 class="section-heading">Frequently Asked Questions</h2>
          <hr class="my-4">
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6 text-center">
          <div class="service-box mt-5 mx-auto">
            <h3 class="mb-3">"What is LibreTime?"</h3>
            <p class="text-mute mb-0">LibreTime is a community managed fork of the AirTime project. It is managed by a friendly inclusive community of stations from around the globe that use, document and improve LibreTime.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="service-box mt-5 mx-auto">
            <h3 class="mb-3">"Can I upgrade to LibreTime?"</h3>
            <p class="text-muted mb-0">In theory you can update any pre 3.0 version of AirTime to LibreTime 3.0.0 and above. More information is <a href="upgrading">here</a>.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="service-box mt-5 mx-auto">
            <h3 class="mb-3">"Why are Cue-In/Out points wrong in some tracks?"</h3>
            <p class="text-muted mb-0">The silan silence detection is currently outdated on almost all distributions. The older versions report clearly wrong information and may segfault at the worst. Versions starting with 0.3.3 (and some patched 0.3.2 builds) are much better but still need thorough testing.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 text-center">
          <div class="service-box mt-5 mx-auto">
            <h3 class="mb-3">"Why did you fork AirTime?"</h3>
            <p class="text-muted mb-0">See this <a href="https://gist.github.com/hairmare/8c03b69c9accc90cfe31fd7e77c3b07d">open letter to the Airtime community</a>.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</html>