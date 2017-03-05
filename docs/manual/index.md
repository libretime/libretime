What is Airtime Pro?
====================

[Airtime](http://airtime.sourcefabric.org/ "Airtime homepage") is the open broadcast software for scheduling and remote station management. Web browser access to the station's media library, multi-file upload and automatic metadata import features are coupled with a collaborative on-line scheduling calendar and playlist management. The scheduling calendar is managed through an easy-to-use interface and triggers playout with sub-second precision.

<img src="what-is-airtime/static/Screenshot540-Now_playing_250.png" width="595" height="455" />

A fully managed [Airtime Pro](https://www.airtime.pro/ "Airtime Pro") service is available from Sourcefabric. Airtime source code is also available for download, under the GNU Affero General Public License version 3. If you're an [Airtime Pro](https://www.airtime.pro/ "Airtime Pro") user, you can go straight to the *Getting started* chapter of this book and log in. Alternatively, a demonstration server is available for public use at:

<http://airtime-demo.sourcefabric.org/>

Airtime has been intended to provide a solution for a wide range of broadcast projects, from community to public and commercial stations. Airtime supports the playout of lossy compressed audio files in both MP3 and AAC formats and the open, royalty-free equivalent [Ogg Vorbis](http://www.vorbis.com/ "Ogg Vorbis homepage"). It also supports playout of lossless FLAC and WAV format audio files.

Available stream output formats include Ogg Vorbis, Ogg Opus, MP3, and AAC. The Airtime library is indexed in a database to enable searching. News editors, DJs and station controllers can use Airtime to build playlists or smart blocks and manage media files (upload, edit metadata, manage advertisements) at the station or via the Internet.

The Airtime administration interface is designed to work with any web browser, on any desktop or mobile platform with a minimum display size of 1280x768 pixels. Airtime looks best on a high definition display of 1920x1080 pixels. The recommended web browsers are **Mozilla Firefox 37** or **Google Chrome 42** (or later versions). **Apple Safari 6** (or later) is also supported.

International UTF-8 metadata in media files is supported throughout, and the Airtime interface can be localized into any language or dialect. Localizations that are installed by default include Austrian, Brazilian, British, Canadian, Chinese, Czech, French, German, Greek, Hungarian, Italian, Korean, Polish, Russian, Spanish and USA.

<img src="what-is-airtime/static/Screenshot541-Chinese_localization_250.png" width="595" height="405" />

Airtime workflow
----------------

This typical workflow is intended to clarify the difference between the various components that make up a complete Airtime system. 

1. There are media files on a storage server, which include metadata in their tags (title, creator, genre and so on).

2. The Airtime media-monitor keeps track of files being added, renamed, moved or removed from storage, and reads their metadata using the Mutagen library.

3. A PostgreSQL database contains the location of those media files and their metadata. The database also contains details of specified remote input streams.

4. Pypo, the Python Playout engine, downloads media from the storage up to 24 hours ahead of playout and checks it for average level (with ReplayGain tools) and leading or trailing silence (with Silan). At playout time, the media to be broadcast is sent to Liquidsoap.

5. Liquidsoap takes individual media files and remote input streams, and assembles them into a continuous output stream. This output stream can be sent to an Icecast streaming server for distribution over the Internet. You can stream to up to three different distribution servers with the same Airtime server, if you wish.

6. RabbitMQ pushes messages from Airtime to media-monitor and pypo about changes to media files and the playout schedule.

7. Airtime manages all of these components, and provides an easy, multi-user web interface to the system. It enables your station staff, depending on the permissions you have granted them, to:

a) upload media files to the storage server via the **Add Media** page

b) automatically import file metadata into the PostgreSQL database

c) search for and download media files, and edit the metadata of individual files, if required, on the **Library** page

d) create and edit playlists of media files or create smart blocks of content based on metadata, edit cue points and fades, and audition them. Playlists and smart blocks are also saved in the database, and can be searched for on the **Library** page

e) schedule colour-coded broadcast shows (which can contain playlists, smart blocks, pre-recorded complete shows, timed remote input streams, or be live) for specific dates and times on the **Calendar** page. Regular shows can be scheduled by the day of the week or month, and can be linked to share content

f) manage presenter, staff and guest access to Airtime, and contact details, via the **Manage Users** page

g) see what is about to be played by Liquidsoap on the **Now Playing** page, with support for last-minute changes to the content

h) audition available output streams from the server using the **Listen** button

i) check the status and resource usage of system components on the **Status** page

j) export the broadcast schedule to external sites via the Public Airtime API on the **Preferences** page

k) see logs on the **Playout History** page and view graphs on the **Listener Stats** page

l) configure the Airtime system on the **Preferences** and **Streams** pages

m) copy and paste code into a third-party website from the **Embeddable Player** page

