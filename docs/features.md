# What is LibreTime?

[LibreTime](http://libretime.org/ "LibreTime homepage") is the open broadcast
software for scheduling and remote station management. Web browser access to
the station's media library, multi-file upload and automatic metadata import
features are coupled with a collaborative on-line scheduling calendar and
playlist management. The scheduling calendar is managed through an easy-to-use
interface and triggers playout with sub-second precision.

![](static/Screenshot540-Now_playing_250.png)

Main features include:

* Web-based remote station management - authorized personnel can add
   programme material, create playlists or smart blocks, and stream in live,
   all via a web interface.
* Automation - LibreTime has a scheduler function that enables users to
   create shows with content for playback at the exact date and time specified.
   Playlists, smart blocks and remote stream URLs can be used multiple times.
* Solid playout - LibreTime uses the open source Liquidsoap streaming language
   for reliable and precise playback to multiple outputs.
* Open, extensible architecture - stations are free to extend and alter
   all parts of the program code, under the GNU AGPLv3 license.
* Multilingual - supports over 15 languages both in the interface and inside file metadata
* Low system requirements
  * For servers: 1Ghz processor, 2 GB RAM, 500 MB, and a wired ethernet connection with a static IP address
  * For end-users: a modern version of Firefox, Chrome, or Safari, and a screen resolution of at least 1280x768 (1920x1080 recommended)

LibreTime has been intended to provide a solution for a wide range of broadcast
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

The scheduler in LibreTime has a calendar view, organized by months, weeks and
days. Here the program editors can schedule playlists and shows for their
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
| `media-monitor` | Keeps track of files being added, renamed, moved or removed from storage, and reads their metadata using the Mutagen library. |
| PostgreSQL | Contains the location of those media files and their metadata. This means you can search for and playlist a set of media files according to the specific metadata that you require, or use a 'smart block' to select the files for you. The database also contains details of specified remote input streams. |
| Pypo | (Python Playout engine) Downloads media from the storage up to 24 hours ahead of playout and checks it for average level (with ReplayGain tools) and leading or trailing silence (with Silan). At playout time, the media to be broadcast is sent to Liquidsoap. |
| Liquidsoap | Takes individual media files and remote input streams, and assembles them into a continuous output stream. This stream can be sent to a sound card (e.g. for a broadcast mixer, on the way to an FM or DAB transmitter) or to a streaming server for IP network distribution, over the LAN, local WiFi or the Internet. You can stream to a sound card and up to three different stream distribution servers with the same LibreTime server, if you wish. |
| Icecast or Shoutcast | Audio streaming server, used for creating an internet radio stream from LibreTime. Icecast is included in the LibreTime installation by default. Note: If a suitable Liquidsoap output is not available for your streaming service of choice, you can send audio from Liquidsoap to a separate encoding or streaming machine via a sound card or relay stream. |
| Monit | Monitors the health of pypo, media-monitor and Liquidsoap, and reports the status of these services to LibreTime. |
| RabbitMQ | Pushes messages from LibreTime to media-monitor and pypo about changes to media files and the playout schedule. |

LibreTime manages all of these components, and provides an easy,
multi-user web interface to the system. It enables your station staff,
depending on the permissions you have granted them, to:
- upload media files to the storage server via the **Add Media** page
- automatically import file metadata into the PostgreSQL database
- search for and download media files, and edit the metadata of individual
files, if required, on the **Library** page
- create and edit playlists of media files or create smart blocks of content
based on metadata, edit cue points and fades, and audition them. Playlists and
smart blocks are also saved in the database, and can be searched for
- schedule colour-coded broadcast shows (which can contain playlists, smart
blocks, pre-recorded complete shows, timed remote input streams, or be live)
for specific dates and times on the **Calendar** page. Regular shows can be
scheduled by the day of the week or month, and can be linked to share content
- automatically record live shows at specific times and dates (in 256 kbps Ogg
Vorbis format by default) from the sound card input with Ecasound, upload them
to the storage server and import them into the database
- manage presenter, staff and guest access to LibreTime, and contact details,
via the **Manage Users** page
- see what is about to be played by Liquidsoap on the **Now Playing** page,
with support for last-minute changes to the content
- upload media files from LibreTime to a third-party hosting service, such as
SoundCloud
- audition available output streams from the server using the **Listen**
button
- check the status and resource usage of system components on the **Status**
page
- export the broadcast schedule to external sites via the Schedule API
- see logs on the **Playout History** page and view graphs on the
**Listener Stats** page
- configure the LibreTime system on the **Preferences**, **Media Folders** and
**Streams** pages.

Example studio broadcast system
-------------------------------

In the diagram of an FM radio station below, LibreTime is hosted on a server
connected to the local network, with direct soundcard access. Liquidsoap
outputs streams to both the transmitter, via the main studio mixer, and
streaming media servers. The machine running LibreTime is behind a firewall
because it is also connected to the Internet for remote access by media
contributors. This enables LibreTime to offer password-protected access to the
media library and scheduling from both inside and outside the studio building.

![](static/libretime_architecture.svg)

Example web broadcast system
----------------------------

In the diagram below, LibreTime is hosted on a remote web server, and has no
soundcard. There does not need to be a centralised studio, although LibreTime
can enable remote studios to stream in to Liquidsoap at authorised times.
Optionally, the outgoing Icecast stream can be relayed to a transmitter.

![](static/libretime_web_architecture.svg)
