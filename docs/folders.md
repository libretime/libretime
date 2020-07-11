---
sidebar: appendix
---

# Media Library Folders

LibreTime's media library is inside the */srv/airtime/stor/* folder on your server, by default.
Tracks are uploaded into the imported subdirectory and then a separate directory based upon the user
ID # of the user who uploaded it and then into a folder based upon the artist. 

LibreTime unlike Airtime does not currently monitor the files for changes after they are uploaded.
The media library is not designed to be edited directly after files are uploaded. This was done as part of a
move towards enabling cloud-based file hosting. There are currently two works in progress to support filesystem
imports and sync but neither of them have been finished as of the time of this writing. See
[#70](https://github.com/LibreTime/libretime/issues/70). In addition LibreTime does not write metadata changes
back to the files. See [#621](https://github.com/LibreTime/libretime/issues/621)
