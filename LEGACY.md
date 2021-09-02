# Campcaster / Airtime (Legacy)

This document regroup all information about Campcaster / AirTime and its authors before the LibreTime fork.

Table of content:

- [Changelog](#changelog)
- [Credits](#credits)

## Changelog

#### 2.5.0 - October 8th, 2013

New features:

- Playout History feature overhaul.
  - custom templates for log sheets.
  - ability to manually log an item.
  - ability to edit a history item.
  - 3 history views: log, file summary, show summary.
  - hosts can now view/log their own show history.
- Ubuntu 13.10 Saucy Salamander support (PHP 5.5)

Bug Fixes:

- Several important timezone handling improvements
- Rebroadcast shows bugfix
- Reduce likelihood of playout stalls under high memory pressure
- Fixed calendar not loading sometimes
- Fixed webstream disconnections due to Now Playing page
- Prevent admin password changes in demo mode
- Plus dozens of other bugfixes!

#### 2.4.1 - August 28th, 2013

Bug Fixes:

- Playout Engine locking issue
- Liquidsoap input harbor blocking scheduled contents
- Mono file playout problems
- Adding watched folder sometimes causes an exception based on length format

#### 2.4.0 - June 18th, 2013

New features:

- Show linking
- Repeating shows on every nth week of the month (2nd Monday etc)
- Waveform Editor (for cues/fades)
- Global crossfade setting, separate global fade in/fade out setting
- Library view shows which tracks are scheduled and/or in playlist
- Opus and AAC/AAC+ output support

Improvements:

- Show creation overhaul
- Library view re-ordering columns issue fixed
- Improved track length detection

#### 2.3.1 - March 19th, 2013

Bug Fixes:

- Security fixes with running unescaped shell commands
- Fix backend services not coming back online if RabbitMQ crashed and then restarted
- Fix uninstall not properly cleaning system
- Improved Services watchdog (test whether Services are running and responding, not just running)
- Much faster library import (Silan analyzer runs in background)
- Fixed zombie process sometimes being created

Other:

- Upgrade to Mutagen (tag reader) 1.21

#### 2.3.0 - Jan 21st, 2013

New features:

- Localization (Chinese, Czech, English, French, German, Italian, Korean, Portuguese, Russian, Spanish)
- User management page for non-admin users
- Listener statistics (Icecast/Shoutcast)
- Airtime no longer requires Apache document root
- Replay Gain offset in real-time
- Enable/disable replay gain
- Liquidsoap memory footprint improvements
- Automatically set cue points on import to avoid leading/trailing silence

#### 2.2.1 - December 4th, 2012

Bug Fixes:

- Improved fades between webstreams
- Fix webstreams disconnecting occasionally
- Put 'and' and 'or' connectors between smart blocks
- Fix inability to preview webstreams in the Now Playing page on some browsers
- Fix airtime-import script failing on FLAC files
- Fix DJ's being able to delete files they don't own
- Add support for 'x-scpls' webstream playlist types
- Fix media-monitor requiring a restart for initial import.

#### 2.2.0 - October 25th, 2012

New features:

- Smart Playlists
- Webstream rebroadcasts
- Replaygain support
- FLAC + WAV support (AAC if you compile your own Liquidsoap)
- Huge performance increase on library import
- User ownership of files
- Stereo/mono streams
- Rescan watched folders button (useful for network drives where keeping in sync is more difficult)

#### 2.1.3 - July 4th, 2012

Changes:

- Clarify inputs and output labels under stream settings

Bug Fixes:

- Fix playout engine crashing in rare cases after the system is restarted
- Fix entries in the Calendar unable to have multiple icons (recorded icon, soundcloud icon etc.)
- Fixed unwatching a watched folder with a large number of files (50,000+) can take a long time
- Fixed files deleted in the Web UI would delete files from the disk in watched folders
- Fixed jQuery widgets not showing the incorrectly showing the past Sunday on Sunday
- Fixed dragging and dropping tracks into a live show could cause to web UI to become unsynchronized from what is actually playing
- Fixed unable to receive mono streams for Master or Show source rebroadcasts

#### 2.1.2 - June 18th, 2012

Bug Fixes:

- Fixed problem where playout engine may not retrieve program schedule after extended periods of user inactivity.

#### 2.1.1 - June 12th, 2012

Changes:

- Add Media page will now display error message and reject uploaded file if it is corrupt
- jQuery schedule widget now show upcoming Sunday instead of previous Sunday
- fixed uploading files with upper case file extensions
- fixed master/source override URL being reverted to original setting after clicking 'Save' in stream settings.
- Add several helpful tips in the Stream Settings page and some UI cleanup
- DJ user type cannot delete Playlists that aren't their own or delete tracks
- Playlist Builder should remember your position instead of resetting to the first page every time an operation was performed
- If Master or Live input source is disconnected, Airtime will no longer automatically switch off that source. This should allow the source to reconnect and continue playback.

Bug Fixes:

- Fixed playout engine sometimes not receiving new schedule which could result in dead air
- Fixed script timeout which caused Apache to become unresponsive
- Fixed various Apache warnings
- Fixed not being able to delete some tracks that had been played
- Fixed calendar highlighting the wrong day due to server timezone being different from client browser timezone
- Promote my station opt-out button now works
- Fixed recording working sporadically on some system configurations

#### 2.1.0 - June 5th, 2012

New features:

- Real-time show editing in the Now Playing and Calendar screens
  - Add/Remove/Rearrange tracks within a show, even if it currently playing
  - Color-coded which tracks are inside the show, which are on the show boundary, and which are outside the show boundary
  - Ability to cut all tracks that are outside the show boundary
  - Edit the length of a show that is currently playing
- Live Stream Rebroadcasting
  - Two live streams can be connected to Airtime: DJ stream and Master stream. The DJ stream can connect to Airtime and override the scheduled playout. The Master stream can connect to Airtime and override the DJ stream and the scheduled playout. Live Streams are activated in the interface and the interface displays which stream is being output.
- Library usability improvements
  - Bulk actions added: "Add to Playlist", "Add to Show", and "Delete File"
  - Ability to choose which columns to display and the order of the columns, sort by multiple columns
  - Any metadata can be shown in the list
  - Added a "Date Uploaded" column
- Preview tracks
  - Ability to seek to a specific part of a track
  - Ability to listen to all tracks in a playlist or show back-to-back.
- Media Monitor - added support for network and USB drives.
  - Removing a watched directory and adding it again preserves playlists & shows with those files.
  - An icon in the playlist shows whether a file is missing on disk, warning the user that the playlist will not go according to plan.
  - Media monitor detects add and removal of watched temporary local storage (USB disks for example) and network drives.
- Broadcast Log - export play count of tracks within a given time range. Useful for royalty reporting purposes.

Improvements:

- Ability to turn off the broadcast.
- Editing metadata in the library will update the metadata on disk.
- Password reset - user can request a password reset if they forgot it.
- Overbooked shows now fade out properly
- Playlists & Shows can now be infinite length. Previously they were limited to 24 hours long.
- Default fade time set to 0.5 of a second
- Repeating shows default to "No End"
- Ability to "View on Soundcloud" for recorded shows in the calendar
- "Listen" preview player no longer falls behind the broadcast (you can only mute the stream now, not stop it)
- Tracks that cannot be played will be rejected on upload and put in to the directory "/srv/airtime/stor/problem_files" (but currently it will not tell you that it rejected them - sorry\!)
- Library is automatically refreshed when media import is finished
- Show "Disk Full" message when trying to upload a file that won't fit on the disk
- Reduced CPU utilization for OGG streams
- New command line utilities:
  - airtime-test-soundcard - verify that the soundcard is working
  - airtime-test-icecast - verify that you can connect to an Icecast server

#### 2.0.3 - April 3rd, 2012

Bug Fixes:

- monit user should have read-only permissions by default
- pypo user's shell should be /bin/false by default

#### 2.0.2 - February 28, 2012

Bug Fixes:

- Fixed Airtime could stop automatically playing after 24 hours if the web interface isn't used (regression in 2.0.1).
- Fixed Airtime could stop automatically recording after 2 hours if the web interface isn't used.
- Fixed upgrading from 1.8.2 when the stor directory was a symlink would cause filenames to not be preserved.
- Fixed Day View in the Now Playing tab showed some items on incorrect days.
- Fixed problems with having an equal '=' sign as an icecast password

Other:

- Various optimizations to make Airtime feel snappier in the browser. Various views should load much quicker.

#### 2.0.1 - February 14, 2012

Changes:

- Widgets should have a version string so users can make sure widgets are up to date

Bug Fixes:

- Fixed: Media monitor should not change the ownership of watched files if unnecessary
- Fixed: Sometimes the Current Playing Item on the top Panel in the UI incorrectly shows "Nothing Scheduled"
- Fixed: Stream settings page does not remember the stream metadata format
- Fixed: Airtime is missing Australia and Arctic timezones
- Fixed: Shows not recorded after upgrade to 2.0.0 from 1.8.2
- Fixed: Don't make it necessary users clear their browser cache when Airtime is upgraded
- Fixed: Media-Monitor does not handle corrupt audio metadata gracefully

#### 2.0.0 - January 24, 2012

New features:

- Stream configuration through the browser
  - You can have up to three streams with different bitrates and point them to different Icecast/SHOUTcast servers
  - Any connection problems between Liquidsoap and Icecast are shown in the interface (no more blaming Airtime for your misconfigured Icecast server!)
  - Ability to change the hardware output API from the browser: you can now switch between ALSA, OSS, AO, Pulseaudio, and Portaudio.
  - Listen from the browser: You can now listen to the streams directly from the web interface without having to start a 3rd party application(such as VLC) to listen to the stream.
  - Service monitoring from the browser: You can now see the status of the services and the disk space available.
  - Time zone can now be set in the browser. Times are now stored in the database in UTC time, there is no more need to adjust server time or values stored in ".htaccess" or "php.ini" files.
  - Ability to change the "Start Day of the Week" that is shown in the calendar.
  - View settings saved in calendar(time scale and time increments) and playlists(number of items displayed, search term)
  - Soundcloud integration improvements
    - Upload any clip to Soundcloud (not just the recorded shows as it was in previous versions)
    - Upload many files at once
    - View the file on Soundcloud.com once it is uploaded
    - Re-upload a file to Soundcloud (for example, if the file has been edited)
    - Automatically set the Soundcloud "Downloadable" flag
  - Protection against brute-force password guessing attacks: after three failed login attempts, the user will be presented with a RECAPTCHA.
  - Right-click on an item in the library to see the metadata for the audio file.
  - Notification of new Airtime releases built into the interface.

Improvements:

- Add Show: only allow valid input when entering times
- Login page auto-focuses on user name field
- Install checks that Virtualenv is functional before proceeding
- Added a 404 page design
- Changing password does not require first & last name to be filled in
- The playlist now expands as you add items instead of keeping everything within a small scrolled box.
- Better error checking in cases where two users alter the same data at the same time (for example, in playlists and shows)
- Playlists: Removed intermediate "Add Playlist" screen where it asked you to fill in the name and description of the playlist. This wasn't necessary since everything could be changed from the playlist editor itself.
- Added "airtime-log" command to display, dump, and view all of Airtime's log files

Bug fixes:

- Liquidsoap logs are now logrotated
- Media monitor
  - Now correctly handles the case where a watched directory or subdirectory is deleted or moved
  - Fixed bug where Airtime import could start reading a file before it had finished copying, leading to import failure. This only happened in rare cases.

#### 1.9.5 - Nov 2, 2011

Bug Fixes:

- (CC-2743, CC-2769) Fixed problem where Media-Monitor would try to parse a file's metadata while the OS was still copying it
- (CC-2882) Fixed a bug where a couldn't unregister an old directory name from Airtime after it was renamed.
- (CC-2891) Fixed a bug with parsing Unicode metadata in audio files.
- (CC-2972) Fixed a bug where systems behind a firewall would have Airtime services communicating via its external IP.
- (CC-2975) Issue with older python-virtualenv identified. Airtime installer now requires virtualenv >= 1.4.9
- (CC-3012, CC-3013) Fixed an issue with Media-Monitor crashing when parsing certain audio tracks

#### 1.9.4 - Sept 13, 2011

Improvements:

- DEB packages now available for Ubuntu & Debian
- "airtime-easy-install" DEB package now available which will install everything with a single click
- "airtime-import" command-line utility now offers better help when invalid parameters have been passed.

Bug Fixes:

- Fixed "Show Contents" displaying full-length of tracks, even if cue-points had been set.
- Fixed start date of show not updating after dragging and dropping.
- Fixed audio preview still playing after deleting a file in the Playlist Builder.
- Fixed uploads via the web-interface while using Internet Explorer failing when tracks contained "+" or whitespace characters.
- Fixed issue where deleting a file from the Playlist Builder wouldn't always refresh the list to remove the file.
- Fixed issue where upgrading from any previous Airtime would set "Toronto/America" as the default timezone
- Fixed playout engine (Pypo) using a large amount of CPU when there was a long history of played shows
- Fixed playout engine (Pypo) using 100% CPU when it could not connect to RabbitMQ
- Fixed issue where incorrect Start Date and Time entered for a new show was not handled gracefully
- Fixed issue where using Cyrillic characters in a show name would sometimes cause it to not play
- Fixed pypo hanging if web server is unavailable
- Fixed items that were being dragged and dropped in the Playlist Builder being obscured by other UI elements.

#### 1.9.3 - August 26th, 2011

Improvements:

- It is now possible to upgrade your system while a show is playing. Playout will be temporarily interrupted for about 5-10 seconds and then playout will resume. Previously playout would not resume until the next scheduled show.

Bug Fixes:

- Fixed bug where playout system did not work with mono files.
- Fixed bug where sometimes audio files could be played out of order.

#### 1.9.2 - August 23rd, 2011

Bug Fixes:

- Fixed restarting sometimes caused media-monitor to forget all of its watched directories
- Fixed Media-monitor crashes when moving sub-directories within its watched directory
- Upgrade script would crash on upgrade from 1.8.2

#### 1.9.1 - August 17th, 2011

Changes:

- Support Settings moved to a separate page accessible by Admin user only.

Bug Fixes:

- "airtime-user" shell script failing to start
- Progress bar for tracks appearing when no content scheduled
- Fix upgrades from Airtime 1.8.2 failing
- Fix various install issues with virtualenv
- Prevent users from doing a manual install of Airtime if they already have the Debian package version installed

#### 1.9.0 - August 9, 2011

New features:

- New file storage system:
  - Human-readable file structure. The directory structure and file names on disk are now human-readable. This means you can easily find files using your file browser on your server.
  - Magic file synchronization. Edits to your files are automatically noticed by Airtime. If you edit any files on disk, such as trimming the length of a track, Airtime will automatically notice this and adjust the playlist lengths and shows for that audio file.
  - Auto-import and multiple-directory support. You can set any number of directories to be watched by Airtime. Any new files you add to watched directories will be automatically imported into Airtime, and any deleted files will be automatically removed.
  - The "airtime-import" command line tool can now set watched directories and change the storage directory.
  - Graceful recovery from reboot. If the playout engine starts up and detects that a show should be playing at the current time, it will skip to the right point in the track and start playing. Previously, Airtime would not play anything until the next show started. This also fixes a problem where the metadata on the stream was lost when a file had cue-in/out values set. Thanks to the Liquidsoap developers for implementing the ability to do all of this!
  - Output to Shoutcast. Now both Shoutcast and Icecast are supported.
  - A new "Program Manager" role. A program manager can create shows but can't change the preferences or modify users.
  - No more rebooting after install! Airtime now uses standard SystemV initd scripts instead of non-standard daemontools. This also makes for a much faster install.
  - Frontend widgets are much easier to use and their theme can be modified with CSS (Click here for more info and installation instructions).
  - Improved installation - only one command to install on Ubuntu!

Changes:

- Cumulative time shown on playlists. The Playlist Builder now shows the total time since the beginning of the playlist for each song.
- "End Time" instead of "Duration". In the Add/Edit Show dialog, we replaced the "Duration" field with "End Time". Users reported that this was a much more intuitive way to schedule the show. Duration is still shown as a read-only field.
- Feedback & promotion system. Airtime now includes a way to send feedback and promote your site on the Sourcefabric web page. This will greatly enhance our ability to understand who is using the software, which in turn will allow us to make appropriate features and receive grant funding.
- The show recorder can now instantly cancel a show thanks to the use of RabbitMQ.
- Only admins have the ability to delete files now.
- The playout engine now runs with a higher priority. This should help prevent any problems with audio skipping.
- Airtime has been contained. It is now easier to run other apps on the same system with Airtime because it no longer messes with the system-wide Python or PHP configurations. The python libraries needed for Airtime are now contained in a local Python virtualenv, and the PHP config variables are set in the Apache virtualhost and .htaccess files.
- Message indicating import status is now displayed on Playlist Builder page( above the search box).

Bug Fixes:

- Fixed bug where you couldn't import a file with a name longer than 255 characters.
- Fixed bug where searching an audio archive of 15K+ files was slow.
- Fixed bug where upgrading from more than one version back (e.g. 1.8.0 -> 1.9.0) did not work.
- Fixed bug where the wrong file length was reported for very large CBR mp3 files (thanks to mutagen developers for the patch!)

#### 1.8.2 - June 8, 2011

Changes:

- You can now download audio files from the search screen and from the "Show Content" screen.
- The "Now Playing" screen now shows whether a show is being recorded.
- In the "Playlist Builder" screen, you can now edit the title of the playlist and the description inline, without having to switch to another page.
- When you click on "Add Show", the cursor is placed on the show title field and a default name is automatically filled in.
- It is now possible to cancel a show that was recording.
- An new command-line program was added to verify an installation and help identify where problems are: "airtime-check-system"
- Airtime now runs on Ubuntu 11.04 (though we do not offer support for this).

Bug Fixes:

- Fixed serious problem with the upgrading and installing process. The Airtime install will now automatically detect if you should upgrade or install and take the appropriate action. Reinstalls cannot happen except by using a specific command.
- "Show Contents" screen will now display properly on smaller screens.
- Install/uninstall now works on Debian without needing the "sudo" command.
- Editing a playlist name or deleting a playlist now reflects immediately in the media search window.
- In the "Add Media" page, the "start upload" button vanished after upload. Now it remains there after upload so it is possible to upload again.
- When canceling a playing show, the currently playing audio file still showed as playing. This has been fixed.
- Audio files greater than 100MB were not being played.
- Fixed uploading audio on Chrome 11 and higher
- Fixed various editing show problems
- Fixed airtime-pypo-stop/start causing playback problems
- Fixed incorrect information being occasionally shown in the top panel
- Fixed problem with Record Check box occasionally being greyed-out when creating new show
- Fixed a problem with default genre not being applied to recorded shows
- Fixed a problem where shows repeating bi-weekly or monthly did not update properly when edited.
- Fixed problem when a user changed the name of a recorded show right before it started playing would cause the recorded audio not to be linked to the show.
- and many more...

#### 1.8.1 - May 2, 2011

Bug Fixes:

- Fixed issue where an track's progress bar would keep updating, even if the track was no longer playing.
- Fixed problem where editing a show would only update some of the show instances.
- Fixed an issue related to editing a show that had instances scheduled in the past.
- airtime-clean-storage command-line utility should now work properly
- Fixed an issue related the "airtime-import" command-line utility
- Fixed an issue with the Airtime Debian package overwriting configuration files
- Fixed some database install issues on Debian
- Fixed an issue with show names and foreign characters causing the show to not start (temporarily disabled allowing the usage of these characters)

#### 1.8.0 - April 19, 2011

Changes:

- The biggest feature of this release is the ability to edit shows. You can change everything from 'Name', 'Description', and 'URL', to repeat and rebroadcast days. Show instances will be dynamically created or removed as needed. Radio stations will be pleased to know they can now have up to ten rebroadcast shows too.
- Airtime's calendar now looks, feels and performs better than ever. Loading a station schedule is now five to eight times faster. In our tests of 1.7, if the month calendar had shows scheduled for every hour of every day, it used to take 16 seconds to load. Now in 1.8 it takes two seconds.
- It is possible to have up to ten rebroadcast shows now, in 1.7 it was only up to five.
- Airtime's new installation script has two options for increased install flexibility: --preserve to keep your existing config files, or --overwrite to replace your existing config files with new ones. Uninstall no longer removes Airtime config files or the music storage directory.
- New improved look & feel of the calendar (thanks to the "FullCalendar" jQuery project).
- Installation now puts files in standard locations in the Linux file hierarchy, which prepares the project to be accepted into Ubuntu and Debian. Also because of our wish to be part of those projects, the default output stream type is now OGG instead of MP3 -- due to MP3 licensing issues. This configuration can be changed in "/etc/airtime/liquidsoap.conf".
- You now have the ability to start and stop pypo and the show recorder from the command line with the commands "airtime-pypo-start", "airtime-pypo-stop", "airtime-show-recorder-start", and "airtime-show-recorder-stop".

Bug Fixes:

- CC-2192 Schedule sent to pypo is not sorted by start time.
- CC-2175 Overbooking: Cut off shows when they are done
- CC-2174 Need formatting and a warning icon for the message for overbooking a show
- CC-2039 Upload file: file name cropped
- CC-2177 Schedule editing does not work under Firefox 4

#### 1.7.0 - April 4, 2011

Changes:

- Recording and automatic scheduling/broadcasting of live shows
  - Recording/rebroadcast status of a show is shown in "Now Playing" and "Calendar"
  - Can rebroadcast a show at multiple times and dates
- Automatic upload of recorded shows to Soundcloud
- Frontend JQuery widgets for public-facing websites to show your visitors what's playing and the upcoming schedule.
- Ability to over-book a show and automatically cut and fade out song if it goes beyond the show time
- Ability to delete audio files
- Ability to cancel the currently playing show
- Any changes to the schedule are immediately seen by the playout engine
  - In version 1.6, you had to make sure that your show was ready to go 30 seconds before it started.
- Upgrade support (should be able to upgrade from any version, unlike 1.6.1 which required an uninstall of 1.6.0 first)
- "Now Playing" list view:
  - audio items are now grouped by show.
  - If a show is not fully scheduled, the user is notified how many seconds of silence are at the end of the show in this View.
  - Audio items that play past the show's end time have a visual notification that they will be cut off
- Ability to change metadata tag display format for web streams
- Config files moved to /etc/airtime. This means all config files are in one convenient location and separated from the code, so you can upgrade your code independently of your config files.
- Redesign of Preferences screen

Bug Fixes:

- CC-2082 OGG stream dies after every song when using MPlayer
- CC-1894 Warn users about time zone differences or clock drift problems on the server
- CC-2058 Utilities are not in the system $PATH
- CC-2051 Unable to change user password
- CC-2030 Icon needed for Cue In/Out
- CC-1955 Special character support in the library search

#### 1.6.1 - Feb 23, 2011

Bug fixes:

- CC-1973 Liquidsoap crashes after multi-day playout
- CC-1970 API key fix (Security fix) - Each time you run the install scripts, a new API key is now generated.
- CC-1992 Editing metadata goes blank on 'submit'
- CC-1993 ui start time and song time unsynchronized

#### 1.6.0 - Feb 14, 2011

First official version of Airtime.

## Credits

#### Version 2.5.2

- Albert Santoni (albert.santoni@sourcefabric.org)
- Denise Rigato (denise.rigato@sourcefabric.org)
- Cliff Wang (cliff.wang@sourcefabric.org)
- Nareg Asmarian (nareg.asmarian@sourcefabric.org)
- Daniel James (daniel.james@sourcefabric.org)

Community Contributors:

- Robbt E

#### Version 2.5.1

- Albert Santoni (albert.santoni@sourcefabric.org)
  Role: Developer Team Lead
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

Community Contributors:

- John Chewter

#### Version 2.5.0

- Albert Santoni (albert.santoni@sourcefabric.org)
  Role: Developer Team Lead
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

#### Version 2.4.1

- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

Localizations:

- Albert (French)
- Helmut Müller, Christoph Rombach, Micz Flor, Silvio Mende (German)
- Claudia Cruz (Spanish)
- Katerina Michailidis (Greek)
- Erich Pöttinger (Austrian)
- Luba Sirina (Russian)
- Luciano De Fazio (Brazilian Portuguese)
- Sebastian Matuszewski (Polish)
- Staff Pingu (Italian)
- Magyar Zsolt (Hungarian)

#### Version 2.3.0...2.3.1

- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Developer Team Lead
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Mikayel Karapetian (michael.karapetian@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

Localizations:

- Albert (French)
- Helmut Müller, Christoph Rombach, Micz Flor (German)
- Claudia Cruz (Spanish)
- Katerina Michailidis (Greek)
- Erich Pöttinger (Austrian)
- Luba Sirina (Russian)
- Luciano De Fazio (Brazilian Portuguese)
- Sebastian Matuszewski (Polish)
- Staff Pingu (Italian)

#### Version 2.2.1

- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Developer Team Lead
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Mikayel Karapetian (michael.karapetian@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

#### Version 2.2.0

- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Developer Team Lead
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Rudi Grinberg (rudi.grinberg@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Mikayel Karapetian (michael.karapetian@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA

#### Version 2.1.0...2.1.3

- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Developer Team Lead
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Denise Rigato (denise.rigato@sourcefabric.org)
  Role: Software Developer
- Cliff Wang (cliff.wang@sourcefabric.org)
  Role: QA
- Mikayel Karapetian (michael.karapetian@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA
- Paul Baranowski (paul.baranowski@sourcefabric.org)
  Role: Project Manager

#### Version 2.0.1...2.0.3

- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Software Developer
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Daniel Franklin (daniel.franklin@sourcefabric.org)
  Role: Software Developer
- Ofir Gal (ofir.gal@sourcefabric.org)
  Role: QA
- Daniel James (daniel.james@sourcefabric.org)
  Role: Documentor & QA
- Paul Baranowski (paul.baranowski@sourcefabric.org)
  Role: Project Manager

#### Version 2.0.0

- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Software Developer
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Yuchen Wang (yuchen.wang@sourcefabric.org)
  Role: Software Developer
- Ofir Gal (ofir.gal@sourcefabric.org)
  Role: QA
- Daniel James
  Role: Documentor & QA
- Paul Baranowski (paul.baranowski@sourcefabric.org)
  Role: Project Manager
- Vladimir Stefanovic (vladimir.stefanovic@sourcefabric.org)
  Role: User Interface Designer

#### Version 1.8.2...1.9.5

Welcome to James Moon!

- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Software Developer
- James Moon (james.moon@sourcefabric.org)
  Role: Software Developer
- Ofir Gal (ofir.gal@sourcefabric.org)
  Role: QA
- Daniel James
  Role: Documentor & QA
- Paul Baranowski (paul.baranowski@sourcefabric.org)
  Role: Project Manager
- Vladimir Stefanovic (vladimir.stefanovic@sourcefabric.org)
  Role: User Interface Designer

#### Version 1.6.0...1.8.1

This version marks a major change to the project, completely replacing the
custom audio player with liquidsoap, dropping the custom desktop GUI, and
completely rewriting the web interface. The project has also been renamed
from "Campcaster" to "Airtime" for this release.

- Paul Baranowski (paul.baranowski@sourcefabric.org)
  Role: Project Lead / Software Developer
  Highlights: - Integration and development of liquidsoap scheduler - Separation of playlists from the scheduler
- Naomi Aro (naomi.aro@sourcefabric.org)
  Role: Software Developer
  Highlights: - New User Interface - Conversion to Propel DB backend
- Martin Konecny (martin.konecny@sourcefabric.org)
  Role: Software Developer
  Highlights: - New User Interface - Scheduler/Backend
- Vladimir Stefanovic (vladimir.stefanovic@sourcefabric.org)
  Role: User Interface Designer
- Ofir Gal (ofir.gal@sourcefabric.org)
  Role: QA
- Daniel James
  Role: Documentor & QA

#### Version 1.4.0 - "Monrovia"

The great deal of the work on Campcaster 1.4 "Monrovia" was commissioned by the
Open Society Initiative for West Africa (www.osiwa.org), and by West Africa
Democracy Radio (www.wadr.org). We would like to thank Ben Akoh at OSIWA and
Peter Kahler at WADR for their immeasurable contributions to the project.

A number of improvements to Campcaster 1.4 were commissioned by Openbroadcast, a user-
generated radio station based in Basel, Switzerland powered by Campcaster. We are
very grateful for their contributions, and specifically to Thomas Gilgen, Dirk Claes,
Rigzen Latshang and Fabiano Sidler.

- Douglas Arellanes
  Role: Tester and user feedback
- Robin Gareus
  Role: Packaging
- Ferenc Gerlits
  Role: Studio GUI
- Sebastian Göbel
  Role: Web interface, storage server
- Nebojsa Grujic
  Role: Scheduler, XML-RPC interface, Gstreamer plugins
- Tomáš Hlava
  Role: Bug fixes
- Sava Tatić
  Role: Manager

#### Version 1.3.0 - "Dakar"

- Douglas Arellanes
  Role: Tester and user feedback
- Ferenc Gerlits
  Role: Studio GUI, scheduler, packaging
- Sebastian Göbel
  Role: Web interface
- Tomáš Hlava
  Role: Bug fixes
- Sava Tatić
  Role: Manager

#### Version 1.2.0 - "Kotor"

- Douglas Arellanes
  Role: Tester and user feedback
- Paul Baranowski
  Role: Project manager, HTML UI, storage server
- Ferenc Gerlits
  Role: Studio GUI, scheduler, packaging
- Tomáš Hlava
  Role: Bug fixes
- Robert Klajn
  Role: Superuser feedback
- Mark Kretschmann
  Role: Audio player
- Sava Tatić
  Role: Manager

#### Version 1.1.X - "Freetown"

- Douglas Arellanes
  Role: Tester and user feedback
- Paul Baranowski
  Role: Project manager, HTML UI, storage server, scheduler
- János Csikós
  Role: HTML UI
- Ferenc Gerlits
  Role: Studio GUI, scheduler, packaging
- Tomáš Hlava
  Role: Storage server, network hub
- Mark Kretschmann
  Role: Audio player
- Ákos Maróy
  Role: Architecture design, scheduler, audio player
- Sava Tatić
  Role: Manager

#### Version 1.0

The original Campcaster (LiveSupport) concept was drafted by Micz Flor. It was
fully developed by Robert Klajn, Douglas Arellanes, Ákos Maróy, and Sava Tatić.
The user interface has been designed by Charles Truett, based on the initial work
done by a team of his then-fellow Parsons School of Design students Turi McKinley,
Catalin Lazia and Sangita Shah. The team was led by then-head of the school's
Department of Digital Design Colleen Macklin, assisted by Kunal Jain.

- Douglas Arellanes
- Michael Aschauer
- Micz Flor
- Ferenc Gerlits
- Sebastian Göbel
- Tomáš Hlava
- Nadine Kokot
- Ákos Maróy
- Sava Tatić
- Charles Truett
