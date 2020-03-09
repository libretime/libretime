# Watch Folder
Automatically import files into LibreTime from a watch folder.

Watch folders behave in the following ways,
- Watch folders are scanned every minute for changes
- Subdirectories in watch folders are included
- Files in watch folders are not modified by LibreTime
- Only these metadata (tags) will be imported from watched files,
  - Title
  - Artist
  - Album
  - Tracknumber
  - Genre
  - Language
  - Label(Organization)
- WARNING: If a track is deleted from LibreTime, its file will also be removed from its watch folder.
