<?php


/**
 * Base class that represents a query for the 'cc_files' table.
 *
 * 
 *
 * @method     CcFilesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcFilesQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcFilesQuery orderByDbMime($order = Criteria::ASC) Order by the mime column
 * @method     CcFilesQuery orderByDbFtype($order = Criteria::ASC) Order by the ftype column
 * @method     CcFilesQuery orderByDbDirectory($order = Criteria::ASC) Order by the directory column
 * @method     CcFilesQuery orderByDbFilepath($order = Criteria::ASC) Order by the filepath column
 * @method     CcFilesQuery orderByDbState($order = Criteria::ASC) Order by the state column
 * @method     CcFilesQuery orderByDbCurrentlyaccessing($order = Criteria::ASC) Order by the currentlyaccessing column
 * @method     CcFilesQuery orderByDbEditedby($order = Criteria::ASC) Order by the editedby column
 * @method     CcFilesQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcFilesQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method     CcFilesQuery orderByDbLPtime($order = Criteria::ASC) Order by the lptime column
 * @method     CcFilesQuery orderByDbMd5($order = Criteria::ASC) Order by the md5 column
 * @method     CcFilesQuery orderByDbTrackTitle($order = Criteria::ASC) Order by the track_title column
 * @method     CcFilesQuery orderByDbArtistName($order = Criteria::ASC) Order by the artist_name column
 * @method     CcFilesQuery orderByDbBitRate($order = Criteria::ASC) Order by the bit_rate column
 * @method     CcFilesQuery orderByDbSampleRate($order = Criteria::ASC) Order by the sample_rate column
 * @method     CcFilesQuery orderByDbFormat($order = Criteria::ASC) Order by the format column
 * @method     CcFilesQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method     CcFilesQuery orderByDbAlbumTitle($order = Criteria::ASC) Order by the album_title column
 * @method     CcFilesQuery orderByDbGenre($order = Criteria::ASC) Order by the genre column
 * @method     CcFilesQuery orderByDbComments($order = Criteria::ASC) Order by the comments column
 * @method     CcFilesQuery orderByDbYear($order = Criteria::ASC) Order by the year column
 * @method     CcFilesQuery orderByDbTrackNumber($order = Criteria::ASC) Order by the track_number column
 * @method     CcFilesQuery orderByDbChannels($order = Criteria::ASC) Order by the channels column
 * @method     CcFilesQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method     CcFilesQuery orderByDbBpm($order = Criteria::ASC) Order by the bpm column
 * @method     CcFilesQuery orderByDbRating($order = Criteria::ASC) Order by the rating column
 * @method     CcFilesQuery orderByDbEncodedBy($order = Criteria::ASC) Order by the encoded_by column
 * @method     CcFilesQuery orderByDbDiscNumber($order = Criteria::ASC) Order by the disc_number column
 * @method     CcFilesQuery orderByDbMood($order = Criteria::ASC) Order by the mood column
 * @method     CcFilesQuery orderByDbLabel($order = Criteria::ASC) Order by the label column
 * @method     CcFilesQuery orderByDbComposer($order = Criteria::ASC) Order by the composer column
 * @method     CcFilesQuery orderByDbEncoder($order = Criteria::ASC) Order by the encoder column
 * @method     CcFilesQuery orderByDbChecksum($order = Criteria::ASC) Order by the checksum column
 * @method     CcFilesQuery orderByDbLyrics($order = Criteria::ASC) Order by the lyrics column
 * @method     CcFilesQuery orderByDbOrchestra($order = Criteria::ASC) Order by the orchestra column
 * @method     CcFilesQuery orderByDbConductor($order = Criteria::ASC) Order by the conductor column
 * @method     CcFilesQuery orderByDbLyricist($order = Criteria::ASC) Order by the lyricist column
 * @method     CcFilesQuery orderByDbOriginalLyricist($order = Criteria::ASC) Order by the original_lyricist column
 * @method     CcFilesQuery orderByDbRadioStationName($order = Criteria::ASC) Order by the radio_station_name column
 * @method     CcFilesQuery orderByDbInfoUrl($order = Criteria::ASC) Order by the info_url column
 * @method     CcFilesQuery orderByDbArtistUrl($order = Criteria::ASC) Order by the artist_url column
 * @method     CcFilesQuery orderByDbAudioSourceUrl($order = Criteria::ASC) Order by the audio_source_url column
 * @method     CcFilesQuery orderByDbRadioStationUrl($order = Criteria::ASC) Order by the radio_station_url column
 * @method     CcFilesQuery orderByDbBuyThisUrl($order = Criteria::ASC) Order by the buy_this_url column
 * @method     CcFilesQuery orderByDbIsrcNumber($order = Criteria::ASC) Order by the isrc_number column
 * @method     CcFilesQuery orderByDbCatalogNumber($order = Criteria::ASC) Order by the catalog_number column
 * @method     CcFilesQuery orderByDbOriginalArtist($order = Criteria::ASC) Order by the original_artist column
 * @method     CcFilesQuery orderByDbCopyright($order = Criteria::ASC) Order by the copyright column
 * @method     CcFilesQuery orderByDbReportDatetime($order = Criteria::ASC) Order by the report_datetime column
 * @method     CcFilesQuery orderByDbReportLocation($order = Criteria::ASC) Order by the report_location column
 * @method     CcFilesQuery orderByDbReportOrganization($order = Criteria::ASC) Order by the report_organization column
 * @method     CcFilesQuery orderByDbSubject($order = Criteria::ASC) Order by the subject column
 * @method     CcFilesQuery orderByDbContributor($order = Criteria::ASC) Order by the contributor column
 * @method     CcFilesQuery orderByDbLanguage($order = Criteria::ASC) Order by the language column
 * @method     CcFilesQuery orderByDbFileExists($order = Criteria::ASC) Order by the file_exists column
 * @method     CcFilesQuery orderByDbSoundcloudId($order = Criteria::ASC) Order by the soundcloud_id column
 * @method     CcFilesQuery orderByDbSoundcloudErrorCode($order = Criteria::ASC) Order by the soundcloud_error_code column
 * @method     CcFilesQuery orderByDbSoundcloudErrorMsg($order = Criteria::ASC) Order by the soundcloud_error_msg column
 * @method     CcFilesQuery orderByDbSoundcloudLinkToFile($order = Criteria::ASC) Order by the soundcloud_link_to_file column
 * @method     CcFilesQuery orderByDbSoundCloundUploadTime($order = Criteria::ASC) Order by the soundcloud_upload_time column
 * @method     CcFilesQuery orderByDbReplayGain($order = Criteria::ASC) Order by the replay_gain column
 * @method     CcFilesQuery orderByDbOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method     CcFilesQuery orderByDbCuein($order = Criteria::ASC) Order by the cuein column
 * @method     CcFilesQuery orderByDbCueout($order = Criteria::ASC) Order by the cueout column
 * @method     CcFilesQuery orderByDbSilanCheck($order = Criteria::ASC) Order by the silan_check column
 * @method     CcFilesQuery orderByDbHidden($order = Criteria::ASC) Order by the hidden column
 * @method     CcFilesQuery orderByDbIsScheduled($order = Criteria::ASC) Order by the is_scheduled column
 * @method     CcFilesQuery orderByDbIsPlaylist($order = Criteria::ASC) Order by the is_playlist column
 *
 * @method     CcFilesQuery groupByDbId() Group by the id column
 * @method     CcFilesQuery groupByDbName() Group by the name column
 * @method     CcFilesQuery groupByDbMime() Group by the mime column
 * @method     CcFilesQuery groupByDbFtype() Group by the ftype column
 * @method     CcFilesQuery groupByDbDirectory() Group by the directory column
 * @method     CcFilesQuery groupByDbFilepath() Group by the filepath column
 * @method     CcFilesQuery groupByDbState() Group by the state column
 * @method     CcFilesQuery groupByDbCurrentlyaccessing() Group by the currentlyaccessing column
 * @method     CcFilesQuery groupByDbEditedby() Group by the editedby column
 * @method     CcFilesQuery groupByDbMtime() Group by the mtime column
 * @method     CcFilesQuery groupByDbUtime() Group by the utime column
 * @method     CcFilesQuery groupByDbLPtime() Group by the lptime column
 * @method     CcFilesQuery groupByDbMd5() Group by the md5 column
 * @method     CcFilesQuery groupByDbTrackTitle() Group by the track_title column
 * @method     CcFilesQuery groupByDbArtistName() Group by the artist_name column
 * @method     CcFilesQuery groupByDbBitRate() Group by the bit_rate column
 * @method     CcFilesQuery groupByDbSampleRate() Group by the sample_rate column
 * @method     CcFilesQuery groupByDbFormat() Group by the format column
 * @method     CcFilesQuery groupByDbLength() Group by the length column
 * @method     CcFilesQuery groupByDbAlbumTitle() Group by the album_title column
 * @method     CcFilesQuery groupByDbGenre() Group by the genre column
 * @method     CcFilesQuery groupByDbComments() Group by the comments column
 * @method     CcFilesQuery groupByDbYear() Group by the year column
 * @method     CcFilesQuery groupByDbTrackNumber() Group by the track_number column
 * @method     CcFilesQuery groupByDbChannels() Group by the channels column
 * @method     CcFilesQuery groupByDbUrl() Group by the url column
 * @method     CcFilesQuery groupByDbBpm() Group by the bpm column
 * @method     CcFilesQuery groupByDbRating() Group by the rating column
 * @method     CcFilesQuery groupByDbEncodedBy() Group by the encoded_by column
 * @method     CcFilesQuery groupByDbDiscNumber() Group by the disc_number column
 * @method     CcFilesQuery groupByDbMood() Group by the mood column
 * @method     CcFilesQuery groupByDbLabel() Group by the label column
 * @method     CcFilesQuery groupByDbComposer() Group by the composer column
 * @method     CcFilesQuery groupByDbEncoder() Group by the encoder column
 * @method     CcFilesQuery groupByDbChecksum() Group by the checksum column
 * @method     CcFilesQuery groupByDbLyrics() Group by the lyrics column
 * @method     CcFilesQuery groupByDbOrchestra() Group by the orchestra column
 * @method     CcFilesQuery groupByDbConductor() Group by the conductor column
 * @method     CcFilesQuery groupByDbLyricist() Group by the lyricist column
 * @method     CcFilesQuery groupByDbOriginalLyricist() Group by the original_lyricist column
 * @method     CcFilesQuery groupByDbRadioStationName() Group by the radio_station_name column
 * @method     CcFilesQuery groupByDbInfoUrl() Group by the info_url column
 * @method     CcFilesQuery groupByDbArtistUrl() Group by the artist_url column
 * @method     CcFilesQuery groupByDbAudioSourceUrl() Group by the audio_source_url column
 * @method     CcFilesQuery groupByDbRadioStationUrl() Group by the radio_station_url column
 * @method     CcFilesQuery groupByDbBuyThisUrl() Group by the buy_this_url column
 * @method     CcFilesQuery groupByDbIsrcNumber() Group by the isrc_number column
 * @method     CcFilesQuery groupByDbCatalogNumber() Group by the catalog_number column
 * @method     CcFilesQuery groupByDbOriginalArtist() Group by the original_artist column
 * @method     CcFilesQuery groupByDbCopyright() Group by the copyright column
 * @method     CcFilesQuery groupByDbReportDatetime() Group by the report_datetime column
 * @method     CcFilesQuery groupByDbReportLocation() Group by the report_location column
 * @method     CcFilesQuery groupByDbReportOrganization() Group by the report_organization column
 * @method     CcFilesQuery groupByDbSubject() Group by the subject column
 * @method     CcFilesQuery groupByDbContributor() Group by the contributor column
 * @method     CcFilesQuery groupByDbLanguage() Group by the language column
 * @method     CcFilesQuery groupByDbFileExists() Group by the file_exists column
 * @method     CcFilesQuery groupByDbSoundcloudId() Group by the soundcloud_id column
 * @method     CcFilesQuery groupByDbSoundcloudErrorCode() Group by the soundcloud_error_code column
 * @method     CcFilesQuery groupByDbSoundcloudErrorMsg() Group by the soundcloud_error_msg column
 * @method     CcFilesQuery groupByDbSoundcloudLinkToFile() Group by the soundcloud_link_to_file column
 * @method     CcFilesQuery groupByDbSoundCloundUploadTime() Group by the soundcloud_upload_time column
 * @method     CcFilesQuery groupByDbReplayGain() Group by the replay_gain column
 * @method     CcFilesQuery groupByDbOwnerId() Group by the owner_id column
 * @method     CcFilesQuery groupByDbCuein() Group by the cuein column
 * @method     CcFilesQuery groupByDbCueout() Group by the cueout column
 * @method     CcFilesQuery groupByDbSilanCheck() Group by the silan_check column
 * @method     CcFilesQuery groupByDbHidden() Group by the hidden column
 * @method     CcFilesQuery groupByDbIsScheduled() Group by the is_scheduled column
 * @method     CcFilesQuery groupByDbIsPlaylist() Group by the is_playlist column
 *
 * @method     CcFilesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcFilesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcFilesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcFilesQuery leftJoinFkOwner($relationAlias = '') Adds a LEFT JOIN clause to the query using the FkOwner relation
 * @method     CcFilesQuery rightJoinFkOwner($relationAlias = '') Adds a RIGHT JOIN clause to the query using the FkOwner relation
 * @method     CcFilesQuery innerJoinFkOwner($relationAlias = '') Adds a INNER JOIN clause to the query using the FkOwner relation
 *
 * @method     CcFilesQuery leftJoinCcSubjsRelatedByDbEditedby($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 * @method     CcFilesQuery rightJoinCcSubjsRelatedByDbEditedby($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 * @method     CcFilesQuery innerJoinCcSubjsRelatedByDbEditedby($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 *
 * @method     CcFilesQuery leftJoinCcMusicDirs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcMusicDirs relation
 * @method     CcFilesQuery rightJoinCcMusicDirs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcMusicDirs relation
 * @method     CcFilesQuery innerJoinCcMusicDirs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcMusicDirs relation
 *
 * @method     CcFilesQuery leftJoinCcShowInstances($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method     CcFilesQuery rightJoinCcShowInstances($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method     CcFilesQuery innerJoinCcShowInstances($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method     CcFilesQuery leftJoinCcPlaylistcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcFilesQuery rightJoinCcPlaylistcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcFilesQuery innerJoinCcPlaylistcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method     CcFilesQuery leftJoinCcBlockcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlockcontents relation
 * @method     CcFilesQuery rightJoinCcBlockcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlockcontents relation
 * @method     CcFilesQuery innerJoinCcBlockcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlockcontents relation
 *
 * @method     CcFilesQuery leftJoinCcSchedule($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method     CcFilesQuery rightJoinCcSchedule($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method     CcFilesQuery innerJoinCcSchedule($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method     CcFilesQuery leftJoinCcPlayoutHistory($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcFilesQuery rightJoinCcPlayoutHistory($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method     CcFilesQuery innerJoinCcPlayoutHistory($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method     CcFiles findOne(PropelPDO $con = null) Return the first CcFiles matching the query
 * @method     CcFiles findOneOrCreate(PropelPDO $con = null) Return the first CcFiles matching the query, or a new CcFiles object populated from the query conditions when no match is found
 *
 * @method     CcFiles findOneByDbId(int $id) Return the first CcFiles filtered by the id column
 * @method     CcFiles findOneByDbName(string $name) Return the first CcFiles filtered by the name column
 * @method     CcFiles findOneByDbMime(string $mime) Return the first CcFiles filtered by the mime column
 * @method     CcFiles findOneByDbFtype(string $ftype) Return the first CcFiles filtered by the ftype column
 * @method     CcFiles findOneByDbDirectory(int $directory) Return the first CcFiles filtered by the directory column
 * @method     CcFiles findOneByDbFilepath(string $filepath) Return the first CcFiles filtered by the filepath column
 * @method     CcFiles findOneByDbState(string $state) Return the first CcFiles filtered by the state column
 * @method     CcFiles findOneByDbCurrentlyaccessing(int $currentlyaccessing) Return the first CcFiles filtered by the currentlyaccessing column
 * @method     CcFiles findOneByDbEditedby(int $editedby) Return the first CcFiles filtered by the editedby column
 * @method     CcFiles findOneByDbMtime(string $mtime) Return the first CcFiles filtered by the mtime column
 * @method     CcFiles findOneByDbUtime(string $utime) Return the first CcFiles filtered by the utime column
 * @method     CcFiles findOneByDbLPtime(string $lptime) Return the first CcFiles filtered by the lptime column
 * @method     CcFiles findOneByDbMd5(string $md5) Return the first CcFiles filtered by the md5 column
 * @method     CcFiles findOneByDbTrackTitle(string $track_title) Return the first CcFiles filtered by the track_title column
 * @method     CcFiles findOneByDbArtistName(string $artist_name) Return the first CcFiles filtered by the artist_name column
 * @method     CcFiles findOneByDbBitRate(int $bit_rate) Return the first CcFiles filtered by the bit_rate column
 * @method     CcFiles findOneByDbSampleRate(int $sample_rate) Return the first CcFiles filtered by the sample_rate column
 * @method     CcFiles findOneByDbFormat(string $format) Return the first CcFiles filtered by the format column
 * @method     CcFiles findOneByDbLength(string $length) Return the first CcFiles filtered by the length column
 * @method     CcFiles findOneByDbAlbumTitle(string $album_title) Return the first CcFiles filtered by the album_title column
 * @method     CcFiles findOneByDbGenre(string $genre) Return the first CcFiles filtered by the genre column
 * @method     CcFiles findOneByDbComments(string $comments) Return the first CcFiles filtered by the comments column
 * @method     CcFiles findOneByDbYear(string $year) Return the first CcFiles filtered by the year column
 * @method     CcFiles findOneByDbTrackNumber(int $track_number) Return the first CcFiles filtered by the track_number column
 * @method     CcFiles findOneByDbChannels(int $channels) Return the first CcFiles filtered by the channels column
 * @method     CcFiles findOneByDbUrl(string $url) Return the first CcFiles filtered by the url column
 * @method     CcFiles findOneByDbBpm(int $bpm) Return the first CcFiles filtered by the bpm column
 * @method     CcFiles findOneByDbRating(string $rating) Return the first CcFiles filtered by the rating column
 * @method     CcFiles findOneByDbEncodedBy(string $encoded_by) Return the first CcFiles filtered by the encoded_by column
 * @method     CcFiles findOneByDbDiscNumber(string $disc_number) Return the first CcFiles filtered by the disc_number column
 * @method     CcFiles findOneByDbMood(string $mood) Return the first CcFiles filtered by the mood column
 * @method     CcFiles findOneByDbLabel(string $label) Return the first CcFiles filtered by the label column
 * @method     CcFiles findOneByDbComposer(string $composer) Return the first CcFiles filtered by the composer column
 * @method     CcFiles findOneByDbEncoder(string $encoder) Return the first CcFiles filtered by the encoder column
 * @method     CcFiles findOneByDbChecksum(string $checksum) Return the first CcFiles filtered by the checksum column
 * @method     CcFiles findOneByDbLyrics(string $lyrics) Return the first CcFiles filtered by the lyrics column
 * @method     CcFiles findOneByDbOrchestra(string $orchestra) Return the first CcFiles filtered by the orchestra column
 * @method     CcFiles findOneByDbConductor(string $conductor) Return the first CcFiles filtered by the conductor column
 * @method     CcFiles findOneByDbLyricist(string $lyricist) Return the first CcFiles filtered by the lyricist column
 * @method     CcFiles findOneByDbOriginalLyricist(string $original_lyricist) Return the first CcFiles filtered by the original_lyricist column
 * @method     CcFiles findOneByDbRadioStationName(string $radio_station_name) Return the first CcFiles filtered by the radio_station_name column
 * @method     CcFiles findOneByDbInfoUrl(string $info_url) Return the first CcFiles filtered by the info_url column
 * @method     CcFiles findOneByDbArtistUrl(string $artist_url) Return the first CcFiles filtered by the artist_url column
 * @method     CcFiles findOneByDbAudioSourceUrl(string $audio_source_url) Return the first CcFiles filtered by the audio_source_url column
 * @method     CcFiles findOneByDbRadioStationUrl(string $radio_station_url) Return the first CcFiles filtered by the radio_station_url column
 * @method     CcFiles findOneByDbBuyThisUrl(string $buy_this_url) Return the first CcFiles filtered by the buy_this_url column
 * @method     CcFiles findOneByDbIsrcNumber(string $isrc_number) Return the first CcFiles filtered by the isrc_number column
 * @method     CcFiles findOneByDbCatalogNumber(string $catalog_number) Return the first CcFiles filtered by the catalog_number column
 * @method     CcFiles findOneByDbOriginalArtist(string $original_artist) Return the first CcFiles filtered by the original_artist column
 * @method     CcFiles findOneByDbCopyright(string $copyright) Return the first CcFiles filtered by the copyright column
 * @method     CcFiles findOneByDbReportDatetime(string $report_datetime) Return the first CcFiles filtered by the report_datetime column
 * @method     CcFiles findOneByDbReportLocation(string $report_location) Return the first CcFiles filtered by the report_location column
 * @method     CcFiles findOneByDbReportOrganization(string $report_organization) Return the first CcFiles filtered by the report_organization column
 * @method     CcFiles findOneByDbSubject(string $subject) Return the first CcFiles filtered by the subject column
 * @method     CcFiles findOneByDbContributor(string $contributor) Return the first CcFiles filtered by the contributor column
 * @method     CcFiles findOneByDbLanguage(string $language) Return the first CcFiles filtered by the language column
 * @method     CcFiles findOneByDbFileExists(boolean $file_exists) Return the first CcFiles filtered by the file_exists column
 * @method     CcFiles findOneByDbSoundcloudId(int $soundcloud_id) Return the first CcFiles filtered by the soundcloud_id column
 * @method     CcFiles findOneByDbSoundcloudErrorCode(int $soundcloud_error_code) Return the first CcFiles filtered by the soundcloud_error_code column
 * @method     CcFiles findOneByDbSoundcloudErrorMsg(string $soundcloud_error_msg) Return the first CcFiles filtered by the soundcloud_error_msg column
 * @method     CcFiles findOneByDbSoundcloudLinkToFile(string $soundcloud_link_to_file) Return the first CcFiles filtered by the soundcloud_link_to_file column
 * @method     CcFiles findOneByDbSoundCloundUploadTime(string $soundcloud_upload_time) Return the first CcFiles filtered by the soundcloud_upload_time column
 * @method     CcFiles findOneByDbReplayGain(string $replay_gain) Return the first CcFiles filtered by the replay_gain column
 * @method     CcFiles findOneByDbOwnerId(int $owner_id) Return the first CcFiles filtered by the owner_id column
 * @method     CcFiles findOneByDbCuein(string $cuein) Return the first CcFiles filtered by the cuein column
 * @method     CcFiles findOneByDbCueout(string $cueout) Return the first CcFiles filtered by the cueout column
 * @method     CcFiles findOneByDbSilanCheck(boolean $silan_check) Return the first CcFiles filtered by the silan_check column
 * @method     CcFiles findOneByDbHidden(boolean $hidden) Return the first CcFiles filtered by the hidden column
 * @method     CcFiles findOneByDbIsScheduled(boolean $is_scheduled) Return the first CcFiles filtered by the is_scheduled column
 * @method     CcFiles findOneByDbIsPlaylist(boolean $is_playlist) Return the first CcFiles filtered by the is_playlist column
 *
 * @method     array findByDbId(int $id) Return CcFiles objects filtered by the id column
 * @method     array findByDbName(string $name) Return CcFiles objects filtered by the name column
 * @method     array findByDbMime(string $mime) Return CcFiles objects filtered by the mime column
 * @method     array findByDbFtype(string $ftype) Return CcFiles objects filtered by the ftype column
 * @method     array findByDbDirectory(int $directory) Return CcFiles objects filtered by the directory column
 * @method     array findByDbFilepath(string $filepath) Return CcFiles objects filtered by the filepath column
 * @method     array findByDbState(string $state) Return CcFiles objects filtered by the state column
 * @method     array findByDbCurrentlyaccessing(int $currentlyaccessing) Return CcFiles objects filtered by the currentlyaccessing column
 * @method     array findByDbEditedby(int $editedby) Return CcFiles objects filtered by the editedby column
 * @method     array findByDbMtime(string $mtime) Return CcFiles objects filtered by the mtime column
 * @method     array findByDbUtime(string $utime) Return CcFiles objects filtered by the utime column
 * @method     array findByDbLPtime(string $lptime) Return CcFiles objects filtered by the lptime column
 * @method     array findByDbMd5(string $md5) Return CcFiles objects filtered by the md5 column
 * @method     array findByDbTrackTitle(string $track_title) Return CcFiles objects filtered by the track_title column
 * @method     array findByDbArtistName(string $artist_name) Return CcFiles objects filtered by the artist_name column
 * @method     array findByDbBitRate(int $bit_rate) Return CcFiles objects filtered by the bit_rate column
 * @method     array findByDbSampleRate(int $sample_rate) Return CcFiles objects filtered by the sample_rate column
 * @method     array findByDbFormat(string $format) Return CcFiles objects filtered by the format column
 * @method     array findByDbLength(string $length) Return CcFiles objects filtered by the length column
 * @method     array findByDbAlbumTitle(string $album_title) Return CcFiles objects filtered by the album_title column
 * @method     array findByDbGenre(string $genre) Return CcFiles objects filtered by the genre column
 * @method     array findByDbComments(string $comments) Return CcFiles objects filtered by the comments column
 * @method     array findByDbYear(string $year) Return CcFiles objects filtered by the year column
 * @method     array findByDbTrackNumber(int $track_number) Return CcFiles objects filtered by the track_number column
 * @method     array findByDbChannels(int $channels) Return CcFiles objects filtered by the channels column
 * @method     array findByDbUrl(string $url) Return CcFiles objects filtered by the url column
 * @method     array findByDbBpm(int $bpm) Return CcFiles objects filtered by the bpm column
 * @method     array findByDbRating(string $rating) Return CcFiles objects filtered by the rating column
 * @method     array findByDbEncodedBy(string $encoded_by) Return CcFiles objects filtered by the encoded_by column
 * @method     array findByDbDiscNumber(string $disc_number) Return CcFiles objects filtered by the disc_number column
 * @method     array findByDbMood(string $mood) Return CcFiles objects filtered by the mood column
 * @method     array findByDbLabel(string $label) Return CcFiles objects filtered by the label column
 * @method     array findByDbComposer(string $composer) Return CcFiles objects filtered by the composer column
 * @method     array findByDbEncoder(string $encoder) Return CcFiles objects filtered by the encoder column
 * @method     array findByDbChecksum(string $checksum) Return CcFiles objects filtered by the checksum column
 * @method     array findByDbLyrics(string $lyrics) Return CcFiles objects filtered by the lyrics column
 * @method     array findByDbOrchestra(string $orchestra) Return CcFiles objects filtered by the orchestra column
 * @method     array findByDbConductor(string $conductor) Return CcFiles objects filtered by the conductor column
 * @method     array findByDbLyricist(string $lyricist) Return CcFiles objects filtered by the lyricist column
 * @method     array findByDbOriginalLyricist(string $original_lyricist) Return CcFiles objects filtered by the original_lyricist column
 * @method     array findByDbRadioStationName(string $radio_station_name) Return CcFiles objects filtered by the radio_station_name column
 * @method     array findByDbInfoUrl(string $info_url) Return CcFiles objects filtered by the info_url column
 * @method     array findByDbArtistUrl(string $artist_url) Return CcFiles objects filtered by the artist_url column
 * @method     array findByDbAudioSourceUrl(string $audio_source_url) Return CcFiles objects filtered by the audio_source_url column
 * @method     array findByDbRadioStationUrl(string $radio_station_url) Return CcFiles objects filtered by the radio_station_url column
 * @method     array findByDbBuyThisUrl(string $buy_this_url) Return CcFiles objects filtered by the buy_this_url column
 * @method     array findByDbIsrcNumber(string $isrc_number) Return CcFiles objects filtered by the isrc_number column
 * @method     array findByDbCatalogNumber(string $catalog_number) Return CcFiles objects filtered by the catalog_number column
 * @method     array findByDbOriginalArtist(string $original_artist) Return CcFiles objects filtered by the original_artist column
 * @method     array findByDbCopyright(string $copyright) Return CcFiles objects filtered by the copyright column
 * @method     array findByDbReportDatetime(string $report_datetime) Return CcFiles objects filtered by the report_datetime column
 * @method     array findByDbReportLocation(string $report_location) Return CcFiles objects filtered by the report_location column
 * @method     array findByDbReportOrganization(string $report_organization) Return CcFiles objects filtered by the report_organization column
 * @method     array findByDbSubject(string $subject) Return CcFiles objects filtered by the subject column
 * @method     array findByDbContributor(string $contributor) Return CcFiles objects filtered by the contributor column
 * @method     array findByDbLanguage(string $language) Return CcFiles objects filtered by the language column
 * @method     array findByDbFileExists(boolean $file_exists) Return CcFiles objects filtered by the file_exists column
 * @method     array findByDbSoundcloudId(int $soundcloud_id) Return CcFiles objects filtered by the soundcloud_id column
 * @method     array findByDbSoundcloudErrorCode(int $soundcloud_error_code) Return CcFiles objects filtered by the soundcloud_error_code column
 * @method     array findByDbSoundcloudErrorMsg(string $soundcloud_error_msg) Return CcFiles objects filtered by the soundcloud_error_msg column
 * @method     array findByDbSoundcloudLinkToFile(string $soundcloud_link_to_file) Return CcFiles objects filtered by the soundcloud_link_to_file column
 * @method     array findByDbSoundCloundUploadTime(string $soundcloud_upload_time) Return CcFiles objects filtered by the soundcloud_upload_time column
 * @method     array findByDbReplayGain(string $replay_gain) Return CcFiles objects filtered by the replay_gain column
 * @method     array findByDbOwnerId(int $owner_id) Return CcFiles objects filtered by the owner_id column
 * @method     array findByDbCuein(string $cuein) Return CcFiles objects filtered by the cuein column
 * @method     array findByDbCueout(string $cueout) Return CcFiles objects filtered by the cueout column
 * @method     array findByDbSilanCheck(boolean $silan_check) Return CcFiles objects filtered by the silan_check column
 * @method     array findByDbHidden(boolean $hidden) Return CcFiles objects filtered by the hidden column
 * @method     array findByDbIsScheduled(boolean $is_scheduled) Return CcFiles objects filtered by the is_scheduled column
 * @method     array findByDbIsPlaylist(boolean $is_playlist) Return CcFiles objects filtered by the is_playlist column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcFilesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcFilesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcFiles', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcFilesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcFilesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcFilesQuery) {
			return $criteria;
		}
		$query = new CcFilesQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CcFiles|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcFilesPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcFilesPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcFilesPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcFilesPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbName($dbName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbName)) {
				$dbName = str_replace('*', '%', $dbName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the mime column
	 * 
	 * @param     string $dbMime The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbMime($dbMime = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbMime)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbMime)) {
				$dbMime = str_replace('*', '%', $dbMime);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::MIME, $dbMime, $comparison);
	}

	/**
	 * Filter the query on the ftype column
	 * 
	 * @param     string $dbFtype The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbFtype($dbFtype = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFtype)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFtype)) {
				$dbFtype = str_replace('*', '%', $dbFtype);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FTYPE, $dbFtype, $comparison);
	}

	/**
	 * Filter the query on the directory column
	 * 
	 * @param     int|array $dbDirectory The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbDirectory($dbDirectory = null, $comparison = null)
	{
		if (is_array($dbDirectory)) {
			$useMinMax = false;
			if (isset($dbDirectory['min'])) {
				$this->addUsingAlias(CcFilesPeer::DIRECTORY, $dbDirectory['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbDirectory['max'])) {
				$this->addUsingAlias(CcFilesPeer::DIRECTORY, $dbDirectory['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::DIRECTORY, $dbDirectory, $comparison);
	}

	/**
	 * Filter the query on the filepath column
	 * 
	 * @param     string $dbFilepath The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbFilepath($dbFilepath = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFilepath)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFilepath)) {
				$dbFilepath = str_replace('*', '%', $dbFilepath);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FILEPATH, $dbFilepath, $comparison);
	}

	/**
	 * Filter the query on the state column
	 * 
	 * @param     string $dbState The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbState($dbState = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbState)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbState)) {
				$dbState = str_replace('*', '%', $dbState);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::STATE, $dbState, $comparison);
	}

	/**
	 * Filter the query on the currentlyaccessing column
	 * 
	 * @param     int|array $dbCurrentlyaccessing The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbCurrentlyaccessing($dbCurrentlyaccessing = null, $comparison = null)
	{
		if (is_array($dbCurrentlyaccessing)) {
			$useMinMax = false;
			if (isset($dbCurrentlyaccessing['min'])) {
				$this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbCurrentlyaccessing['max'])) {
				$this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $dbCurrentlyaccessing, $comparison);
	}

	/**
	 * Filter the query on the editedby column
	 * 
	 * @param     int|array $dbEditedby The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbEditedby($dbEditedby = null, $comparison = null)
	{
		if (is_array($dbEditedby)) {
			$useMinMax = false;
			if (isset($dbEditedby['min'])) {
				$this->addUsingAlias(CcFilesPeer::EDITEDBY, $dbEditedby['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbEditedby['max'])) {
				$this->addUsingAlias(CcFilesPeer::EDITEDBY, $dbEditedby['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::EDITEDBY, $dbEditedby, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $dbMtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbMtime($dbMtime = null, $comparison = null)
	{
		if (is_array($dbMtime)) {
			$useMinMax = false;
			if (isset($dbMtime['min'])) {
				$this->addUsingAlias(CcFilesPeer::MTIME, $dbMtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbMtime['max'])) {
				$this->addUsingAlias(CcFilesPeer::MTIME, $dbMtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::MTIME, $dbMtime, $comparison);
	}

	/**
	 * Filter the query on the utime column
	 * 
	 * @param     string|array $dbUtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbUtime($dbUtime = null, $comparison = null)
	{
		if (is_array($dbUtime)) {
			$useMinMax = false;
			if (isset($dbUtime['min'])) {
				$this->addUsingAlias(CcFilesPeer::UTIME, $dbUtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbUtime['max'])) {
				$this->addUsingAlias(CcFilesPeer::UTIME, $dbUtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::UTIME, $dbUtime, $comparison);
	}

	/**
	 * Filter the query on the lptime column
	 * 
	 * @param     string|array $dbLPtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLPtime($dbLPtime = null, $comparison = null)
	{
		if (is_array($dbLPtime)) {
			$useMinMax = false;
			if (isset($dbLPtime['min'])) {
				$this->addUsingAlias(CcFilesPeer::LPTIME, $dbLPtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLPtime['max'])) {
				$this->addUsingAlias(CcFilesPeer::LPTIME, $dbLPtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LPTIME, $dbLPtime, $comparison);
	}

	/**
	 * Filter the query on the md5 column
	 * 
	 * @param     string $dbMd5 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbMd5($dbMd5 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbMd5)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbMd5)) {
				$dbMd5 = str_replace('*', '%', $dbMd5);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::MD5, $dbMd5, $comparison);
	}

	/**
	 * Filter the query on the track_title column
	 * 
	 * @param     string $dbTrackTitle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbTrackTitle($dbTrackTitle = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbTrackTitle)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbTrackTitle)) {
				$dbTrackTitle = str_replace('*', '%', $dbTrackTitle);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::TRACK_TITLE, $dbTrackTitle, $comparison);
	}

	/**
	 * Filter the query on the artist_name column
	 * 
	 * @param     string $dbArtistName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbArtistName($dbArtistName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbArtistName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbArtistName)) {
				$dbArtistName = str_replace('*', '%', $dbArtistName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ARTIST_NAME, $dbArtistName, $comparison);
	}

	/**
	 * Filter the query on the bit_rate column
	 * 
	 * @param     int|array $dbBitRate The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbBitRate($dbBitRate = null, $comparison = null)
	{
		if (is_array($dbBitRate)) {
			$useMinMax = false;
			if (isset($dbBitRate['min'])) {
				$this->addUsingAlias(CcFilesPeer::BIT_RATE, $dbBitRate['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBitRate['max'])) {
				$this->addUsingAlias(CcFilesPeer::BIT_RATE, $dbBitRate['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BIT_RATE, $dbBitRate, $comparison);
	}

	/**
	 * Filter the query on the sample_rate column
	 * 
	 * @param     int|array $dbSampleRate The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSampleRate($dbSampleRate = null, $comparison = null)
	{
		if (is_array($dbSampleRate)) {
			$useMinMax = false;
			if (isset($dbSampleRate['min'])) {
				$this->addUsingAlias(CcFilesPeer::SAMPLE_RATE, $dbSampleRate['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbSampleRate['max'])) {
				$this->addUsingAlias(CcFilesPeer::SAMPLE_RATE, $dbSampleRate['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SAMPLE_RATE, $dbSampleRate, $comparison);
	}

	/**
	 * Filter the query on the format column
	 * 
	 * @param     string $dbFormat The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbFormat($dbFormat = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFormat)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFormat)) {
				$dbFormat = str_replace('*', '%', $dbFormat);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FORMAT, $dbFormat, $comparison);
	}

	/**
	 * Filter the query on the length column
	 * 
	 * @param     string $dbLength The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLength($dbLength = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLength)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLength)) {
				$dbLength = str_replace('*', '%', $dbLength);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LENGTH, $dbLength, $comparison);
	}

	/**
	 * Filter the query on the album_title column
	 * 
	 * @param     string $dbAlbumTitle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbAlbumTitle($dbAlbumTitle = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbAlbumTitle)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbAlbumTitle)) {
				$dbAlbumTitle = str_replace('*', '%', $dbAlbumTitle);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ALBUM_TITLE, $dbAlbumTitle, $comparison);
	}

	/**
	 * Filter the query on the genre column
	 * 
	 * @param     string $dbGenre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbGenre($dbGenre = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbGenre)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbGenre)) {
				$dbGenre = str_replace('*', '%', $dbGenre);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::GENRE, $dbGenre, $comparison);
	}

	/**
	 * Filter the query on the comments column
	 * 
	 * @param     string $dbComments The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbComments($dbComments = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbComments)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbComments)) {
				$dbComments = str_replace('*', '%', $dbComments);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::COMMENTS, $dbComments, $comparison);
	}

	/**
	 * Filter the query on the year column
	 * 
	 * @param     string $dbYear The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbYear($dbYear = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbYear)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbYear)) {
				$dbYear = str_replace('*', '%', $dbYear);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::YEAR, $dbYear, $comparison);
	}

	/**
	 * Filter the query on the track_number column
	 * 
	 * @param     int|array $dbTrackNumber The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbTrackNumber($dbTrackNumber = null, $comparison = null)
	{
		if (is_array($dbTrackNumber)) {
			$useMinMax = false;
			if (isset($dbTrackNumber['min'])) {
				$this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $dbTrackNumber['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTrackNumber['max'])) {
				$this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $dbTrackNumber['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $dbTrackNumber, $comparison);
	}

	/**
	 * Filter the query on the channels column
	 * 
	 * @param     int|array $dbChannels The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbChannels($dbChannels = null, $comparison = null)
	{
		if (is_array($dbChannels)) {
			$useMinMax = false;
			if (isset($dbChannels['min'])) {
				$this->addUsingAlias(CcFilesPeer::CHANNELS, $dbChannels['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbChannels['max'])) {
				$this->addUsingAlias(CcFilesPeer::CHANNELS, $dbChannels['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CHANNELS, $dbChannels, $comparison);
	}

	/**
	 * Filter the query on the url column
	 * 
	 * @param     string $dbUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbUrl($dbUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbUrl)) {
				$dbUrl = str_replace('*', '%', $dbUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::URL, $dbUrl, $comparison);
	}

	/**
	 * Filter the query on the bpm column
	 * 
	 * @param     int|array $dbBpm The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbBpm($dbBpm = null, $comparison = null)
	{
		if (is_array($dbBpm)) {
			$useMinMax = false;
			if (isset($dbBpm['min'])) {
				$this->addUsingAlias(CcFilesPeer::BPM, $dbBpm['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbBpm['max'])) {
				$this->addUsingAlias(CcFilesPeer::BPM, $dbBpm['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BPM, $dbBpm, $comparison);
	}

	/**
	 * Filter the query on the rating column
	 * 
	 * @param     string $dbRating The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbRating($dbRating = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbRating)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbRating)) {
				$dbRating = str_replace('*', '%', $dbRating);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RATING, $dbRating, $comparison);
	}

	/**
	 * Filter the query on the encoded_by column
	 * 
	 * @param     string $dbEncodedBy The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbEncodedBy($dbEncodedBy = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbEncodedBy)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbEncodedBy)) {
				$dbEncodedBy = str_replace('*', '%', $dbEncodedBy);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ENCODED_BY, $dbEncodedBy, $comparison);
	}

	/**
	 * Filter the query on the disc_number column
	 * 
	 * @param     string $dbDiscNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbDiscNumber($dbDiscNumber = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbDiscNumber)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbDiscNumber)) {
				$dbDiscNumber = str_replace('*', '%', $dbDiscNumber);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::DISC_NUMBER, $dbDiscNumber, $comparison);
	}

	/**
	 * Filter the query on the mood column
	 * 
	 * @param     string $dbMood The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbMood($dbMood = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbMood)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbMood)) {
				$dbMood = str_replace('*', '%', $dbMood);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::MOOD, $dbMood, $comparison);
	}

	/**
	 * Filter the query on the label column
	 * 
	 * @param     string $dbLabel The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLabel($dbLabel = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLabel)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLabel)) {
				$dbLabel = str_replace('*', '%', $dbLabel);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LABEL, $dbLabel, $comparison);
	}

	/**
	 * Filter the query on the composer column
	 * 
	 * @param     string $dbComposer The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbComposer($dbComposer = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbComposer)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbComposer)) {
				$dbComposer = str_replace('*', '%', $dbComposer);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::COMPOSER, $dbComposer, $comparison);
	}

	/**
	 * Filter the query on the encoder column
	 * 
	 * @param     string $dbEncoder The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbEncoder($dbEncoder = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbEncoder)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbEncoder)) {
				$dbEncoder = str_replace('*', '%', $dbEncoder);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ENCODER, $dbEncoder, $comparison);
	}

	/**
	 * Filter the query on the checksum column
	 * 
	 * @param     string $dbChecksum The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbChecksum($dbChecksum = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbChecksum)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbChecksum)) {
				$dbChecksum = str_replace('*', '%', $dbChecksum);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CHECKSUM, $dbChecksum, $comparison);
	}

	/**
	 * Filter the query on the lyrics column
	 * 
	 * @param     string $dbLyrics The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLyrics($dbLyrics = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLyrics)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLyrics)) {
				$dbLyrics = str_replace('*', '%', $dbLyrics);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LYRICS, $dbLyrics, $comparison);
	}

	/**
	 * Filter the query on the orchestra column
	 * 
	 * @param     string $dbOrchestra The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbOrchestra($dbOrchestra = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbOrchestra)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbOrchestra)) {
				$dbOrchestra = str_replace('*', '%', $dbOrchestra);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORCHESTRA, $dbOrchestra, $comparison);
	}

	/**
	 * Filter the query on the conductor column
	 * 
	 * @param     string $dbConductor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbConductor($dbConductor = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbConductor)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbConductor)) {
				$dbConductor = str_replace('*', '%', $dbConductor);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CONDUCTOR, $dbConductor, $comparison);
	}

	/**
	 * Filter the query on the lyricist column
	 * 
	 * @param     string $dbLyricist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLyricist($dbLyricist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLyricist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLyricist)) {
				$dbLyricist = str_replace('*', '%', $dbLyricist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LYRICIST, $dbLyricist, $comparison);
	}

	/**
	 * Filter the query on the original_lyricist column
	 * 
	 * @param     string $dbOriginalLyricist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbOriginalLyricist($dbOriginalLyricist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbOriginalLyricist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbOriginalLyricist)) {
				$dbOriginalLyricist = str_replace('*', '%', $dbOriginalLyricist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORIGINAL_LYRICIST, $dbOriginalLyricist, $comparison);
	}

	/**
	 * Filter the query on the radio_station_name column
	 * 
	 * @param     string $dbRadioStationName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbRadioStationName($dbRadioStationName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbRadioStationName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbRadioStationName)) {
				$dbRadioStationName = str_replace('*', '%', $dbRadioStationName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RADIO_STATION_NAME, $dbRadioStationName, $comparison);
	}

	/**
	 * Filter the query on the info_url column
	 * 
	 * @param     string $dbInfoUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbInfoUrl($dbInfoUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbInfoUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbInfoUrl)) {
				$dbInfoUrl = str_replace('*', '%', $dbInfoUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::INFO_URL, $dbInfoUrl, $comparison);
	}

	/**
	 * Filter the query on the artist_url column
	 * 
	 * @param     string $dbArtistUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbArtistUrl($dbArtistUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbArtistUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbArtistUrl)) {
				$dbArtistUrl = str_replace('*', '%', $dbArtistUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ARTIST_URL, $dbArtistUrl, $comparison);
	}

	/**
	 * Filter the query on the audio_source_url column
	 * 
	 * @param     string $dbAudioSourceUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbAudioSourceUrl($dbAudioSourceUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbAudioSourceUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbAudioSourceUrl)) {
				$dbAudioSourceUrl = str_replace('*', '%', $dbAudioSourceUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::AUDIO_SOURCE_URL, $dbAudioSourceUrl, $comparison);
	}

	/**
	 * Filter the query on the radio_station_url column
	 * 
	 * @param     string $dbRadioStationUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbRadioStationUrl($dbRadioStationUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbRadioStationUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbRadioStationUrl)) {
				$dbRadioStationUrl = str_replace('*', '%', $dbRadioStationUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RADIO_STATION_URL, $dbRadioStationUrl, $comparison);
	}

	/**
	 * Filter the query on the buy_this_url column
	 * 
	 * @param     string $dbBuyThisUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbBuyThisUrl($dbBuyThisUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbBuyThisUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbBuyThisUrl)) {
				$dbBuyThisUrl = str_replace('*', '%', $dbBuyThisUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BUY_THIS_URL, $dbBuyThisUrl, $comparison);
	}

	/**
	 * Filter the query on the isrc_number column
	 * 
	 * @param     string $dbIsrcNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbIsrcNumber($dbIsrcNumber = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbIsrcNumber)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbIsrcNumber)) {
				$dbIsrcNumber = str_replace('*', '%', $dbIsrcNumber);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ISRC_NUMBER, $dbIsrcNumber, $comparison);
	}

	/**
	 * Filter the query on the catalog_number column
	 * 
	 * @param     string $dbCatalogNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbCatalogNumber($dbCatalogNumber = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCatalogNumber)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCatalogNumber)) {
				$dbCatalogNumber = str_replace('*', '%', $dbCatalogNumber);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CATALOG_NUMBER, $dbCatalogNumber, $comparison);
	}

	/**
	 * Filter the query on the original_artist column
	 * 
	 * @param     string $dbOriginalArtist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbOriginalArtist($dbOriginalArtist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbOriginalArtist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbOriginalArtist)) {
				$dbOriginalArtist = str_replace('*', '%', $dbOriginalArtist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORIGINAL_ARTIST, $dbOriginalArtist, $comparison);
	}

	/**
	 * Filter the query on the copyright column
	 * 
	 * @param     string $dbCopyright The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbCopyright($dbCopyright = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCopyright)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCopyright)) {
				$dbCopyright = str_replace('*', '%', $dbCopyright);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::COPYRIGHT, $dbCopyright, $comparison);
	}

	/**
	 * Filter the query on the report_datetime column
	 * 
	 * @param     string $dbReportDatetime The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbReportDatetime($dbReportDatetime = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbReportDatetime)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbReportDatetime)) {
				$dbReportDatetime = str_replace('*', '%', $dbReportDatetime);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_DATETIME, $dbReportDatetime, $comparison);
	}

	/**
	 * Filter the query on the report_location column
	 * 
	 * @param     string $dbReportLocation The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbReportLocation($dbReportLocation = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbReportLocation)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbReportLocation)) {
				$dbReportLocation = str_replace('*', '%', $dbReportLocation);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_LOCATION, $dbReportLocation, $comparison);
	}

	/**
	 * Filter the query on the report_organization column
	 * 
	 * @param     string $dbReportOrganization The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbReportOrganization($dbReportOrganization = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbReportOrganization)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbReportOrganization)) {
				$dbReportOrganization = str_replace('*', '%', $dbReportOrganization);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_ORGANIZATION, $dbReportOrganization, $comparison);
	}

	/**
	 * Filter the query on the subject column
	 * 
	 * @param     string $dbSubject The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSubject($dbSubject = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbSubject)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbSubject)) {
				$dbSubject = str_replace('*', '%', $dbSubject);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SUBJECT, $dbSubject, $comparison);
	}

	/**
	 * Filter the query on the contributor column
	 * 
	 * @param     string $dbContributor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbContributor($dbContributor = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbContributor)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbContributor)) {
				$dbContributor = str_replace('*', '%', $dbContributor);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CONTRIBUTOR, $dbContributor, $comparison);
	}

	/**
	 * Filter the query on the language column
	 * 
	 * @param     string $dbLanguage The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLanguage($dbLanguage = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLanguage)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLanguage)) {
				$dbLanguage = str_replace('*', '%', $dbLanguage);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LANGUAGE, $dbLanguage, $comparison);
	}

	/**
	 * Filter the query on the file_exists column
	 * 
	 * @param     boolean|string $dbFileExists The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbFileExists($dbFileExists = null, $comparison = null)
	{
		if (is_string($dbFileExists)) {
			$file_exists = in_array(strtolower($dbFileExists), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcFilesPeer::FILE_EXISTS, $dbFileExists, $comparison);
	}

	/**
	 * Filter the query on the soundcloud_id column
	 * 
	 * @param     int|array $dbSoundcloudId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSoundcloudId($dbSoundcloudId = null, $comparison = null)
	{
		if (is_array($dbSoundcloudId)) {
			$useMinMax = false;
			if (isset($dbSoundcloudId['min'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ID, $dbSoundcloudId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbSoundcloudId['max'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ID, $dbSoundcloudId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ID, $dbSoundcloudId, $comparison);
	}

	/**
	 * Filter the query on the soundcloud_error_code column
	 * 
	 * @param     int|array $dbSoundcloudErrorCode The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSoundcloudErrorCode($dbSoundcloudErrorCode = null, $comparison = null)
	{
		if (is_array($dbSoundcloudErrorCode)) {
			$useMinMax = false;
			if (isset($dbSoundcloudErrorCode['min'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ERROR_CODE, $dbSoundcloudErrorCode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbSoundcloudErrorCode['max'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ERROR_CODE, $dbSoundcloudErrorCode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ERROR_CODE, $dbSoundcloudErrorCode, $comparison);
	}

	/**
	 * Filter the query on the soundcloud_error_msg column
	 * 
	 * @param     string $dbSoundcloudErrorMsg The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSoundcloudErrorMsg($dbSoundcloudErrorMsg = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbSoundcloudErrorMsg)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbSoundcloudErrorMsg)) {
				$dbSoundcloudErrorMsg = str_replace('*', '%', $dbSoundcloudErrorMsg);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_ERROR_MSG, $dbSoundcloudErrorMsg, $comparison);
	}

	/**
	 * Filter the query on the soundcloud_link_to_file column
	 * 
	 * @param     string $dbSoundcloudLinkToFile The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSoundcloudLinkToFile($dbSoundcloudLinkToFile = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbSoundcloudLinkToFile)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbSoundcloudLinkToFile)) {
				$dbSoundcloudLinkToFile = str_replace('*', '%', $dbSoundcloudLinkToFile);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_LINK_TO_FILE, $dbSoundcloudLinkToFile, $comparison);
	}

	/**
	 * Filter the query on the soundcloud_upload_time column
	 * 
	 * @param     string|array $dbSoundCloundUploadTime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSoundCloundUploadTime($dbSoundCloundUploadTime = null, $comparison = null)
	{
		if (is_array($dbSoundCloundUploadTime)) {
			$useMinMax = false;
			if (isset($dbSoundCloundUploadTime['min'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_UPLOAD_TIME, $dbSoundCloundUploadTime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbSoundCloundUploadTime['max'])) {
				$this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_UPLOAD_TIME, $dbSoundCloundUploadTime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SOUNDCLOUD_UPLOAD_TIME, $dbSoundCloundUploadTime, $comparison);
	}

	/**
	 * Filter the query on the replay_gain column
	 * 
	 * @param     string|array $dbReplayGain The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbReplayGain($dbReplayGain = null, $comparison = null)
	{
		if (is_array($dbReplayGain)) {
			$useMinMax = false;
			if (isset($dbReplayGain['min'])) {
				$this->addUsingAlias(CcFilesPeer::REPLAY_GAIN, $dbReplayGain['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbReplayGain['max'])) {
				$this->addUsingAlias(CcFilesPeer::REPLAY_GAIN, $dbReplayGain['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPLAY_GAIN, $dbReplayGain, $comparison);
	}

	/**
	 * Filter the query on the owner_id column
	 * 
	 * @param     int|array $dbOwnerId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbOwnerId($dbOwnerId = null, $comparison = null)
	{
		if (is_array($dbOwnerId)) {
			$useMinMax = false;
			if (isset($dbOwnerId['min'])) {
				$this->addUsingAlias(CcFilesPeer::OWNER_ID, $dbOwnerId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbOwnerId['max'])) {
				$this->addUsingAlias(CcFilesPeer::OWNER_ID, $dbOwnerId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::OWNER_ID, $dbOwnerId, $comparison);
	}

	/**
	 * Filter the query on the cuein column
	 * 
	 * @param     string $dbCuein The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbCuein($dbCuein = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCuein)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCuein)) {
				$dbCuein = str_replace('*', '%', $dbCuein);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CUEIN, $dbCuein, $comparison);
	}

	/**
	 * Filter the query on the cueout column
	 * 
	 * @param     string $dbCueout The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbCueout($dbCueout = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCueout)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCueout)) {
				$dbCueout = str_replace('*', '%', $dbCueout);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CUEOUT, $dbCueout, $comparison);
	}

	/**
	 * Filter the query on the silan_check column
	 * 
	 * @param     boolean|string $dbSilanCheck The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbSilanCheck($dbSilanCheck = null, $comparison = null)
	{
		if (is_string($dbSilanCheck)) {
			$silan_check = in_array(strtolower($dbSilanCheck), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcFilesPeer::SILAN_CHECK, $dbSilanCheck, $comparison);
	}

	/**
	 * Filter the query on the hidden column
	 * 
	 * @param     boolean|string $dbHidden The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbHidden($dbHidden = null, $comparison = null)
	{
		if (is_string($dbHidden)) {
			$hidden = in_array(strtolower($dbHidden), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcFilesPeer::HIDDEN, $dbHidden, $comparison);
	}

	/**
	 * Filter the query on the is_scheduled column
	 * 
	 * @param     boolean|string $dbIsScheduled The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbIsScheduled($dbIsScheduled = null, $comparison = null)
	{
		if (is_string($dbIsScheduled)) {
			$is_scheduled = in_array(strtolower($dbIsScheduled), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcFilesPeer::IS_SCHEDULED, $dbIsScheduled, $comparison);
	}

	/**
	 * Filter the query on the is_playlist column
	 * 
	 * @param     boolean|string $dbIsPlaylist The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbIsPlaylist($dbIsPlaylist = null, $comparison = null)
	{
		if (is_string($dbIsPlaylist)) {
			$is_playlist = in_array(strtolower($dbIsPlaylist), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcFilesPeer::IS_PLAYLIST, $dbIsPlaylist, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByFkOwner($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::OWNER_ID, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the FkOwner relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinFkOwner($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('FkOwner');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'FkOwner');
		}
		
		return $this;
	}

	/**
	 * Use the FkOwner relation CcSubjs object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery A secondary query class using the current class as primary query
	 */
	public function useFkOwnerQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinFkOwner($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'FkOwner', 'CcSubjsQuery');
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcSubjsRelatedByDbEditedby($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::EDITEDBY, $ccSubjs->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcSubjsRelatedByDbEditedby($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSubjsRelatedByDbEditedby');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcSubjsRelatedByDbEditedby');
		}
		
		return $this;
	}

	/**
	 * Use the CcSubjsRelatedByDbEditedby relation CcSubjs object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery A secondary query class using the current class as primary query
	 */
	public function useCcSubjsRelatedByDbEditedbyQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSubjsRelatedByDbEditedby($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjsRelatedByDbEditedby', 'CcSubjsQuery');
	}

	/**
	 * Filter the query by a related CcMusicDirs object
	 *
	 * @param     CcMusicDirs $ccMusicDirs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcMusicDirs($ccMusicDirs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::DIRECTORY, $ccMusicDirs->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcMusicDirs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcMusicDirs($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcMusicDirs');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcMusicDirs');
		}
		
		return $this;
	}

	/**
	 * Use the CcMusicDirs relation CcMusicDirs object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcMusicDirsQuery A secondary query class using the current class as primary query
	 */
	public function useCcMusicDirsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcMusicDirs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcMusicDirs', 'CcMusicDirsQuery');
	}

	/**
	 * Filter the query by a related CcShowInstances object
	 *
	 * @param     CcShowInstances $ccShowInstances  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcShowInstances($ccShowInstances, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccShowInstances->getDbRecordedFile(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowInstances relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcShowInstances($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowInstances');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcShowInstances');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowInstances relation CcShowInstances object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowInstancesQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowInstancesQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcShowInstances($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
	}

	/**
	 * Filter the query by a related CcPlaylistcontents object
	 *
	 * @param     CcPlaylistcontents $ccPlaylistcontents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccPlaylistcontents->getDbFileId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylistcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcPlaylistcontents($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlaylistcontents');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcPlaylistcontents');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlaylistcontents relation CcPlaylistcontents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcontentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlaylistcontentsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlaylistcontents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylistcontents', 'CcPlaylistcontentsQuery');
	}

	/**
	 * Filter the query by a related CcBlockcontents object
	 *
	 * @param     CcBlockcontents $ccBlockcontents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcBlockcontents($ccBlockcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccBlockcontents->getDbFileId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlockcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcBlockcontents($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcBlockcontents');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcBlockcontents');
		}
		
		return $this;
	}

	/**
	 * Use the CcBlockcontents relation CcBlockcontents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockcontentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockcontentsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcBlockcontents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlockcontents', 'CcBlockcontentsQuery');
	}

	/**
	 * Filter the query by a related CcSchedule object
	 *
	 * @param     CcSchedule $ccSchedule  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcSchedule($ccSchedule, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccSchedule->getDbFileId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSchedule relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcSchedule($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSchedule');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcSchedule');
		}
		
		return $this;
	}

	/**
	 * Use the CcSchedule relation CcSchedule object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcScheduleQuery A secondary query class using the current class as primary query
	 */
	public function useCcScheduleQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSchedule($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
	}

	/**
	 * Filter the query by a related CcPlayoutHistory object
	 *
	 * @param     CcPlayoutHistory $ccPlayoutHistory  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccPlayoutHistory->getDbFileId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistory relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistory($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistory');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcPlayoutHistory');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlayoutHistory relation CcPlayoutHistory object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlayoutHistory($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcFiles $ccFiles Object to remove from the list of results
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function prune($ccFiles = null)
	{
		if ($ccFiles) {
			$this->addUsingAlias(CcFilesPeer::ID, $ccFiles->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcFilesQuery
