
Watch Folders
===============
Automatically import files into LibreTime from a watch folder.

Watch folders behave in the following ways:
- they are are scanned every minute for changes
- their subdirectories are included
- their files are not modified by LibreTime
- if a file is deleted from your LibreTime library, then it will be deleted from its watch folder.



libretime_watch
===============
- Started by a init.d script (should be init later; TODO install services through LibreTime install script)
- Watches for files in a watch directory which the user ads through the web interface. Watch directories are `ccMusicDirs` with the attribute `watch = true` (Is this correct???)
- Kicked off by a cronjob `libretime_watch` which calls the script libretime-watch-trigger. This script sends messages to `libretime_watch` telling it to scan directories.
- Analyses the media file (ID3 tags, cue in, cue out, and rgain) and writes this information to the database. Only these tags are imported from watched files (TODO: use airtime_analyser),
  - Title
  - Artist
  - Album
  - Tracknumber
  - Genre
  - Language
  - Label(Organization)
- Adds or updates a file found in the watch folder to the database

Most of the code was been inspired by the airtime programs media-monitor and 
airtime_analyze.


