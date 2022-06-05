<?php


/**
 * Base class that represents a query for the 'cc_files' table.
 *
 *
 *
 * @method CcFilesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcFilesQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcFilesQuery orderByDbMime($order = Criteria::ASC) Order by the mime column
 * @method CcFilesQuery orderByDbFtype($order = Criteria::ASC) Order by the ftype column
 * @method CcFilesQuery orderByDbFilepath($order = Criteria::ASC) Order by the filepath column
 * @method CcFilesQuery orderByDbImportStatus($order = Criteria::ASC) Order by the import_status column
 * @method CcFilesQuery orderByDbCurrentlyaccessing($order = Criteria::ASC) Order by the currentlyaccessing column
 * @method CcFilesQuery orderByDbEditedby($order = Criteria::ASC) Order by the editedby column
 * @method CcFilesQuery orderByDbMtime($order = Criteria::ASC) Order by the mtime column
 * @method CcFilesQuery orderByDbUtime($order = Criteria::ASC) Order by the utime column
 * @method CcFilesQuery orderByDbLPtime($order = Criteria::ASC) Order by the lptime column
 * @method CcFilesQuery orderByDbMd5($order = Criteria::ASC) Order by the md5 column
 * @method CcFilesQuery orderByDbTrackTitle($order = Criteria::ASC) Order by the track_title column
 * @method CcFilesQuery orderByDbArtistName($order = Criteria::ASC) Order by the artist_name column
 * @method CcFilesQuery orderByDbBitRate($order = Criteria::ASC) Order by the bit_rate column
 * @method CcFilesQuery orderByDbSampleRate($order = Criteria::ASC) Order by the sample_rate column
 * @method CcFilesQuery orderByDbFormat($order = Criteria::ASC) Order by the format column
 * @method CcFilesQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method CcFilesQuery orderByDbAlbumTitle($order = Criteria::ASC) Order by the album_title column
 * @method CcFilesQuery orderByDbGenre($order = Criteria::ASC) Order by the genre column
 * @method CcFilesQuery orderByDbComments($order = Criteria::ASC) Order by the comments column
 * @method CcFilesQuery orderByDbYear($order = Criteria::ASC) Order by the year column
 * @method CcFilesQuery orderByDbTrackNumber($order = Criteria::ASC) Order by the track_number column
 * @method CcFilesQuery orderByDbChannels($order = Criteria::ASC) Order by the channels column
 * @method CcFilesQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method CcFilesQuery orderByDbBpm($order = Criteria::ASC) Order by the bpm column
 * @method CcFilesQuery orderByDbRating($order = Criteria::ASC) Order by the rating column
 * @method CcFilesQuery orderByDbEncodedBy($order = Criteria::ASC) Order by the encoded_by column
 * @method CcFilesQuery orderByDbDiscNumber($order = Criteria::ASC) Order by the disc_number column
 * @method CcFilesQuery orderByDbMood($order = Criteria::ASC) Order by the mood column
 * @method CcFilesQuery orderByDbLabel($order = Criteria::ASC) Order by the label column
 * @method CcFilesQuery orderByDbComposer($order = Criteria::ASC) Order by the composer column
 * @method CcFilesQuery orderByDbEncoder($order = Criteria::ASC) Order by the encoder column
 * @method CcFilesQuery orderByDbChecksum($order = Criteria::ASC) Order by the checksum column
 * @method CcFilesQuery orderByDbLyrics($order = Criteria::ASC) Order by the lyrics column
 * @method CcFilesQuery orderByDbOrchestra($order = Criteria::ASC) Order by the orchestra column
 * @method CcFilesQuery orderByDbConductor($order = Criteria::ASC) Order by the conductor column
 * @method CcFilesQuery orderByDbLyricist($order = Criteria::ASC) Order by the lyricist column
 * @method CcFilesQuery orderByDbOriginalLyricist($order = Criteria::ASC) Order by the original_lyricist column
 * @method CcFilesQuery orderByDbRadioStationName($order = Criteria::ASC) Order by the radio_station_name column
 * @method CcFilesQuery orderByDbInfoUrl($order = Criteria::ASC) Order by the info_url column
 * @method CcFilesQuery orderByDbArtistUrl($order = Criteria::ASC) Order by the artist_url column
 * @method CcFilesQuery orderByDbAudioSourceUrl($order = Criteria::ASC) Order by the audio_source_url column
 * @method CcFilesQuery orderByDbRadioStationUrl($order = Criteria::ASC) Order by the radio_station_url column
 * @method CcFilesQuery orderByDbBuyThisUrl($order = Criteria::ASC) Order by the buy_this_url column
 * @method CcFilesQuery orderByDbIsrcNumber($order = Criteria::ASC) Order by the isrc_number column
 * @method CcFilesQuery orderByDbCatalogNumber($order = Criteria::ASC) Order by the catalog_number column
 * @method CcFilesQuery orderByDbOriginalArtist($order = Criteria::ASC) Order by the original_artist column
 * @method CcFilesQuery orderByDbCopyright($order = Criteria::ASC) Order by the copyright column
 * @method CcFilesQuery orderByDbReportDatetime($order = Criteria::ASC) Order by the report_datetime column
 * @method CcFilesQuery orderByDbReportLocation($order = Criteria::ASC) Order by the report_location column
 * @method CcFilesQuery orderByDbReportOrganization($order = Criteria::ASC) Order by the report_organization column
 * @method CcFilesQuery orderByDbSubject($order = Criteria::ASC) Order by the subject column
 * @method CcFilesQuery orderByDbContributor($order = Criteria::ASC) Order by the contributor column
 * @method CcFilesQuery orderByDbLanguage($order = Criteria::ASC) Order by the language column
 * @method CcFilesQuery orderByDbFileExists($order = Criteria::ASC) Order by the file_exists column
 * @method CcFilesQuery orderByDbReplayGain($order = Criteria::ASC) Order by the replay_gain column
 * @method CcFilesQuery orderByDbOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method CcFilesQuery orderByDbCuein($order = Criteria::ASC) Order by the cuein column
 * @method CcFilesQuery orderByDbCueout($order = Criteria::ASC) Order by the cueout column
 * @method CcFilesQuery orderByDbSilanCheck($order = Criteria::ASC) Order by the silan_check column
 * @method CcFilesQuery orderByDbHidden($order = Criteria::ASC) Order by the hidden column
 * @method CcFilesQuery orderByDbIsScheduled($order = Criteria::ASC) Order by the is_scheduled column
 * @method CcFilesQuery orderByDbIsPlaylist($order = Criteria::ASC) Order by the is_playlist column
 * @method CcFilesQuery orderByDbFilesize($order = Criteria::ASC) Order by the filesize column
 * @method CcFilesQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcFilesQuery orderByDbArtwork($order = Criteria::ASC) Order by the artwork column
 * @method CcFilesQuery orderByDbTrackType($order = Criteria::ASC) Order by the track_type column
 *
 * @method CcFilesQuery groupByDbId() Group by the id column
 * @method CcFilesQuery groupByDbName() Group by the name column
 * @method CcFilesQuery groupByDbMime() Group by the mime column
 * @method CcFilesQuery groupByDbFtype() Group by the ftype column
 * @method CcFilesQuery groupByDbFilepath() Group by the filepath column
 * @method CcFilesQuery groupByDbImportStatus() Group by the import_status column
 * @method CcFilesQuery groupByDbCurrentlyaccessing() Group by the currentlyaccessing column
 * @method CcFilesQuery groupByDbEditedby() Group by the editedby column
 * @method CcFilesQuery groupByDbMtime() Group by the mtime column
 * @method CcFilesQuery groupByDbUtime() Group by the utime column
 * @method CcFilesQuery groupByDbLPtime() Group by the lptime column
 * @method CcFilesQuery groupByDbMd5() Group by the md5 column
 * @method CcFilesQuery groupByDbTrackTitle() Group by the track_title column
 * @method CcFilesQuery groupByDbArtistName() Group by the artist_name column
 * @method CcFilesQuery groupByDbBitRate() Group by the bit_rate column
 * @method CcFilesQuery groupByDbSampleRate() Group by the sample_rate column
 * @method CcFilesQuery groupByDbFormat() Group by the format column
 * @method CcFilesQuery groupByDbLength() Group by the length column
 * @method CcFilesQuery groupByDbAlbumTitle() Group by the album_title column
 * @method CcFilesQuery groupByDbGenre() Group by the genre column
 * @method CcFilesQuery groupByDbComments() Group by the comments column
 * @method CcFilesQuery groupByDbYear() Group by the year column
 * @method CcFilesQuery groupByDbTrackNumber() Group by the track_number column
 * @method CcFilesQuery groupByDbChannels() Group by the channels column
 * @method CcFilesQuery groupByDbUrl() Group by the url column
 * @method CcFilesQuery groupByDbBpm() Group by the bpm column
 * @method CcFilesQuery groupByDbRating() Group by the rating column
 * @method CcFilesQuery groupByDbEncodedBy() Group by the encoded_by column
 * @method CcFilesQuery groupByDbDiscNumber() Group by the disc_number column
 * @method CcFilesQuery groupByDbMood() Group by the mood column
 * @method CcFilesQuery groupByDbLabel() Group by the label column
 * @method CcFilesQuery groupByDbComposer() Group by the composer column
 * @method CcFilesQuery groupByDbEncoder() Group by the encoder column
 * @method CcFilesQuery groupByDbChecksum() Group by the checksum column
 * @method CcFilesQuery groupByDbLyrics() Group by the lyrics column
 * @method CcFilesQuery groupByDbOrchestra() Group by the orchestra column
 * @method CcFilesQuery groupByDbConductor() Group by the conductor column
 * @method CcFilesQuery groupByDbLyricist() Group by the lyricist column
 * @method CcFilesQuery groupByDbOriginalLyricist() Group by the original_lyricist column
 * @method CcFilesQuery groupByDbRadioStationName() Group by the radio_station_name column
 * @method CcFilesQuery groupByDbInfoUrl() Group by the info_url column
 * @method CcFilesQuery groupByDbArtistUrl() Group by the artist_url column
 * @method CcFilesQuery groupByDbAudioSourceUrl() Group by the audio_source_url column
 * @method CcFilesQuery groupByDbRadioStationUrl() Group by the radio_station_url column
 * @method CcFilesQuery groupByDbBuyThisUrl() Group by the buy_this_url column
 * @method CcFilesQuery groupByDbIsrcNumber() Group by the isrc_number column
 * @method CcFilesQuery groupByDbCatalogNumber() Group by the catalog_number column
 * @method CcFilesQuery groupByDbOriginalArtist() Group by the original_artist column
 * @method CcFilesQuery groupByDbCopyright() Group by the copyright column
 * @method CcFilesQuery groupByDbReportDatetime() Group by the report_datetime column
 * @method CcFilesQuery groupByDbReportLocation() Group by the report_location column
 * @method CcFilesQuery groupByDbReportOrganization() Group by the report_organization column
 * @method CcFilesQuery groupByDbSubject() Group by the subject column
 * @method CcFilesQuery groupByDbContributor() Group by the contributor column
 * @method CcFilesQuery groupByDbLanguage() Group by the language column
 * @method CcFilesQuery groupByDbFileExists() Group by the file_exists column
 * @method CcFilesQuery groupByDbReplayGain() Group by the replay_gain column
 * @method CcFilesQuery groupByDbOwnerId() Group by the owner_id column
 * @method CcFilesQuery groupByDbCuein() Group by the cuein column
 * @method CcFilesQuery groupByDbCueout() Group by the cueout column
 * @method CcFilesQuery groupByDbSilanCheck() Group by the silan_check column
 * @method CcFilesQuery groupByDbHidden() Group by the hidden column
 * @method CcFilesQuery groupByDbIsScheduled() Group by the is_scheduled column
 * @method CcFilesQuery groupByDbIsPlaylist() Group by the is_playlist column
 * @method CcFilesQuery groupByDbFilesize() Group by the filesize column
 * @method CcFilesQuery groupByDbDescription() Group by the description column
 * @method CcFilesQuery groupByDbArtwork() Group by the artwork column
 * @method CcFilesQuery groupByDbTrackType() Group by the track_type column
 *
 * @method CcFilesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcFilesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcFilesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcFilesQuery leftJoinFkOwner($relationAlias = null) Adds a LEFT JOIN clause to the query using the FkOwner relation
 * @method CcFilesQuery rightJoinFkOwner($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FkOwner relation
 * @method CcFilesQuery innerJoinFkOwner($relationAlias = null) Adds a INNER JOIN clause to the query using the FkOwner relation
 *
 * @method CcFilesQuery leftJoinCcSubjsRelatedByDbEditedby($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 * @method CcFilesQuery rightJoinCcSubjsRelatedByDbEditedby($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 * @method CcFilesQuery innerJoinCcSubjsRelatedByDbEditedby($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
 *
 * @method CcFilesQuery leftJoinCloudFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the CloudFile relation
 * @method CcFilesQuery rightJoinCloudFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CloudFile relation
 * @method CcFilesQuery innerJoinCloudFile($relationAlias = null) Adds a INNER JOIN clause to the query using the CloudFile relation
 *
 * @method CcFilesQuery leftJoinCcShowInstances($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method CcFilesQuery rightJoinCcShowInstances($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method CcFilesQuery innerJoinCcShowInstances($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method CcFilesQuery leftJoinCcPlaylistcontents($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcFilesQuery rightJoinCcPlaylistcontents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method CcFilesQuery innerJoinCcPlaylistcontents($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method CcFilesQuery leftJoinCcBlockcontents($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlockcontents relation
 * @method CcFilesQuery rightJoinCcBlockcontents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlockcontents relation
 * @method CcFilesQuery innerJoinCcBlockcontents($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlockcontents relation
 *
 * @method CcFilesQuery leftJoinCcSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method CcFilesQuery rightJoinCcSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method CcFilesQuery innerJoinCcSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method CcFilesQuery leftJoinCcPlayoutHistory($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcFilesQuery rightJoinCcPlayoutHistory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcFilesQuery innerJoinCcPlayoutHistory($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method CcFilesQuery leftJoinThirdPartyTrackReferences($relationAlias = null) Adds a LEFT JOIN clause to the query using the ThirdPartyTrackReferences relation
 * @method CcFilesQuery rightJoinThirdPartyTrackReferences($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ThirdPartyTrackReferences relation
 * @method CcFilesQuery innerJoinThirdPartyTrackReferences($relationAlias = null) Adds a INNER JOIN clause to the query using the ThirdPartyTrackReferences relation
 *
 * @method CcFilesQuery leftJoinPodcastEpisodes($relationAlias = null) Adds a LEFT JOIN clause to the query using the PodcastEpisodes relation
 * @method CcFilesQuery rightJoinPodcastEpisodes($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PodcastEpisodes relation
 * @method CcFilesQuery innerJoinPodcastEpisodes($relationAlias = null) Adds a INNER JOIN clause to the query using the PodcastEpisodes relation
 *
 * @method CcFiles findOne(PropelPDO $con = null) Return the first CcFiles matching the query
 * @method CcFiles findOneOrCreate(PropelPDO $con = null) Return the first CcFiles matching the query, or a new CcFiles object populated from the query conditions when no match is found
 *
 * @method CcFiles findOneByDbName(string $name) Return the first CcFiles filtered by the name column
 * @method CcFiles findOneByDbMime(string $mime) Return the first CcFiles filtered by the mime column
 * @method CcFiles findOneByDbFtype(string $ftype) Return the first CcFiles filtered by the ftype column
 * @method CcFiles findOneByDbFilepath(string $filepath) Return the first CcFiles filtered by the filepath column
 * @method CcFiles findOneByDbImportStatus(int $import_status) Return the first CcFiles filtered by the import_status column
 * @method CcFiles findOneByDbCurrentlyaccessing(int $currentlyaccessing) Return the first CcFiles filtered by the currentlyaccessing column
 * @method CcFiles findOneByDbEditedby(int $editedby) Return the first CcFiles filtered by the editedby column
 * @method CcFiles findOneByDbMtime(string $mtime) Return the first CcFiles filtered by the mtime column
 * @method CcFiles findOneByDbUtime(string $utime) Return the first CcFiles filtered by the utime column
 * @method CcFiles findOneByDbLPtime(string $lptime) Return the first CcFiles filtered by the lptime column
 * @method CcFiles findOneByDbMd5(string $md5) Return the first CcFiles filtered by the md5 column
 * @method CcFiles findOneByDbTrackTitle(string $track_title) Return the first CcFiles filtered by the track_title column
 * @method CcFiles findOneByDbArtistName(string $artist_name) Return the first CcFiles filtered by the artist_name column
 * @method CcFiles findOneByDbBitRate(int $bit_rate) Return the first CcFiles filtered by the bit_rate column
 * @method CcFiles findOneByDbSampleRate(int $sample_rate) Return the first CcFiles filtered by the sample_rate column
 * @method CcFiles findOneByDbFormat(string $format) Return the first CcFiles filtered by the format column
 * @method CcFiles findOneByDbLength(string $length) Return the first CcFiles filtered by the length column
 * @method CcFiles findOneByDbAlbumTitle(string $album_title) Return the first CcFiles filtered by the album_title column
 * @method CcFiles findOneByDbGenre(string $genre) Return the first CcFiles filtered by the genre column
 * @method CcFiles findOneByDbComments(string $comments) Return the first CcFiles filtered by the comments column
 * @method CcFiles findOneByDbYear(string $year) Return the first CcFiles filtered by the year column
 * @method CcFiles findOneByDbTrackNumber(int $track_number) Return the first CcFiles filtered by the track_number column
 * @method CcFiles findOneByDbChannels(int $channels) Return the first CcFiles filtered by the channels column
 * @method CcFiles findOneByDbUrl(string $url) Return the first CcFiles filtered by the url column
 * @method CcFiles findOneByDbBpm(int $bpm) Return the first CcFiles filtered by the bpm column
 * @method CcFiles findOneByDbRating(string $rating) Return the first CcFiles filtered by the rating column
 * @method CcFiles findOneByDbEncodedBy(string $encoded_by) Return the first CcFiles filtered by the encoded_by column
 * @method CcFiles findOneByDbDiscNumber(string $disc_number) Return the first CcFiles filtered by the disc_number column
 * @method CcFiles findOneByDbMood(string $mood) Return the first CcFiles filtered by the mood column
 * @method CcFiles findOneByDbLabel(string $label) Return the first CcFiles filtered by the label column
 * @method CcFiles findOneByDbComposer(string $composer) Return the first CcFiles filtered by the composer column
 * @method CcFiles findOneByDbEncoder(string $encoder) Return the first CcFiles filtered by the encoder column
 * @method CcFiles findOneByDbChecksum(string $checksum) Return the first CcFiles filtered by the checksum column
 * @method CcFiles findOneByDbLyrics(string $lyrics) Return the first CcFiles filtered by the lyrics column
 * @method CcFiles findOneByDbOrchestra(string $orchestra) Return the first CcFiles filtered by the orchestra column
 * @method CcFiles findOneByDbConductor(string $conductor) Return the first CcFiles filtered by the conductor column
 * @method CcFiles findOneByDbLyricist(string $lyricist) Return the first CcFiles filtered by the lyricist column
 * @method CcFiles findOneByDbOriginalLyricist(string $original_lyricist) Return the first CcFiles filtered by the original_lyricist column
 * @method CcFiles findOneByDbRadioStationName(string $radio_station_name) Return the first CcFiles filtered by the radio_station_name column
 * @method CcFiles findOneByDbInfoUrl(string $info_url) Return the first CcFiles filtered by the info_url column
 * @method CcFiles findOneByDbArtistUrl(string $artist_url) Return the first CcFiles filtered by the artist_url column
 * @method CcFiles findOneByDbAudioSourceUrl(string $audio_source_url) Return the first CcFiles filtered by the audio_source_url column
 * @method CcFiles findOneByDbRadioStationUrl(string $radio_station_url) Return the first CcFiles filtered by the radio_station_url column
 * @method CcFiles findOneByDbBuyThisUrl(string $buy_this_url) Return the first CcFiles filtered by the buy_this_url column
 * @method CcFiles findOneByDbIsrcNumber(string $isrc_number) Return the first CcFiles filtered by the isrc_number column
 * @method CcFiles findOneByDbCatalogNumber(string $catalog_number) Return the first CcFiles filtered by the catalog_number column
 * @method CcFiles findOneByDbOriginalArtist(string $original_artist) Return the first CcFiles filtered by the original_artist column
 * @method CcFiles findOneByDbCopyright(string $copyright) Return the first CcFiles filtered by the copyright column
 * @method CcFiles findOneByDbReportDatetime(string $report_datetime) Return the first CcFiles filtered by the report_datetime column
 * @method CcFiles findOneByDbReportLocation(string $report_location) Return the first CcFiles filtered by the report_location column
 * @method CcFiles findOneByDbReportOrganization(string $report_organization) Return the first CcFiles filtered by the report_organization column
 * @method CcFiles findOneByDbSubject(string $subject) Return the first CcFiles filtered by the subject column
 * @method CcFiles findOneByDbContributor(string $contributor) Return the first CcFiles filtered by the contributor column
 * @method CcFiles findOneByDbLanguage(string $language) Return the first CcFiles filtered by the language column
 * @method CcFiles findOneByDbFileExists(boolean $file_exists) Return the first CcFiles filtered by the file_exists column
 * @method CcFiles findOneByDbReplayGain(string $replay_gain) Return the first CcFiles filtered by the replay_gain column
 * @method CcFiles findOneByDbOwnerId(int $owner_id) Return the first CcFiles filtered by the owner_id column
 * @method CcFiles findOneByDbCuein(string $cuein) Return the first CcFiles filtered by the cuein column
 * @method CcFiles findOneByDbCueout(string $cueout) Return the first CcFiles filtered by the cueout column
 * @method CcFiles findOneByDbSilanCheck(boolean $silan_check) Return the first CcFiles filtered by the silan_check column
 * @method CcFiles findOneByDbHidden(boolean $hidden) Return the first CcFiles filtered by the hidden column
 * @method CcFiles findOneByDbIsScheduled(boolean $is_scheduled) Return the first CcFiles filtered by the is_scheduled column
 * @method CcFiles findOneByDbIsPlaylist(boolean $is_playlist) Return the first CcFiles filtered by the is_playlist column
 * @method CcFiles findOneByDbFilesize(int $filesize) Return the first CcFiles filtered by the filesize column
 * @method CcFiles findOneByDbDescription(string $description) Return the first CcFiles filtered by the description column
 * @method CcFiles findOneByDbArtwork(string $artwork) Return the first CcFiles filtered by the artwork column
 * @method CcFiles findOneByDbTrackType(string $track_type) Return the first CcFiles filtered by the track_type column
 *
 * @method array findByDbId(int $id) Return CcFiles objects filtered by the id column
 * @method array findByDbName(string $name) Return CcFiles objects filtered by the name column
 * @method array findByDbMime(string $mime) Return CcFiles objects filtered by the mime column
 * @method array findByDbFtype(string $ftype) Return CcFiles objects filtered by the ftype column
 * @method array findByDbFilepath(string $filepath) Return CcFiles objects filtered by the filepath column
 * @method array findByDbImportStatus(int $import_status) Return CcFiles objects filtered by the import_status column
 * @method array findByDbCurrentlyaccessing(int $currentlyaccessing) Return CcFiles objects filtered by the currentlyaccessing column
 * @method array findByDbEditedby(int $editedby) Return CcFiles objects filtered by the editedby column
 * @method array findByDbMtime(string $mtime) Return CcFiles objects filtered by the mtime column
 * @method array findByDbUtime(string $utime) Return CcFiles objects filtered by the utime column
 * @method array findByDbLPtime(string $lptime) Return CcFiles objects filtered by the lptime column
 * @method array findByDbMd5(string $md5) Return CcFiles objects filtered by the md5 column
 * @method array findByDbTrackTitle(string $track_title) Return CcFiles objects filtered by the track_title column
 * @method array findByDbArtistName(string $artist_name) Return CcFiles objects filtered by the artist_name column
 * @method array findByDbBitRate(int $bit_rate) Return CcFiles objects filtered by the bit_rate column
 * @method array findByDbSampleRate(int $sample_rate) Return CcFiles objects filtered by the sample_rate column
 * @method array findByDbFormat(string $format) Return CcFiles objects filtered by the format column
 * @method array findByDbLength(string $length) Return CcFiles objects filtered by the length column
 * @method array findByDbAlbumTitle(string $album_title) Return CcFiles objects filtered by the album_title column
 * @method array findByDbGenre(string $genre) Return CcFiles objects filtered by the genre column
 * @method array findByDbComments(string $comments) Return CcFiles objects filtered by the comments column
 * @method array findByDbYear(string $year) Return CcFiles objects filtered by the year column
 * @method array findByDbTrackNumber(int $track_number) Return CcFiles objects filtered by the track_number column
 * @method array findByDbChannels(int $channels) Return CcFiles objects filtered by the channels column
 * @method array findByDbUrl(string $url) Return CcFiles objects filtered by the url column
 * @method array findByDbBpm(int $bpm) Return CcFiles objects filtered by the bpm column
 * @method array findByDbRating(string $rating) Return CcFiles objects filtered by the rating column
 * @method array findByDbEncodedBy(string $encoded_by) Return CcFiles objects filtered by the encoded_by column
 * @method array findByDbDiscNumber(string $disc_number) Return CcFiles objects filtered by the disc_number column
 * @method array findByDbMood(string $mood) Return CcFiles objects filtered by the mood column
 * @method array findByDbLabel(string $label) Return CcFiles objects filtered by the label column
 * @method array findByDbComposer(string $composer) Return CcFiles objects filtered by the composer column
 * @method array findByDbEncoder(string $encoder) Return CcFiles objects filtered by the encoder column
 * @method array findByDbChecksum(string $checksum) Return CcFiles objects filtered by the checksum column
 * @method array findByDbLyrics(string $lyrics) Return CcFiles objects filtered by the lyrics column
 * @method array findByDbOrchestra(string $orchestra) Return CcFiles objects filtered by the orchestra column
 * @method array findByDbConductor(string $conductor) Return CcFiles objects filtered by the conductor column
 * @method array findByDbLyricist(string $lyricist) Return CcFiles objects filtered by the lyricist column
 * @method array findByDbOriginalLyricist(string $original_lyricist) Return CcFiles objects filtered by the original_lyricist column
 * @method array findByDbRadioStationName(string $radio_station_name) Return CcFiles objects filtered by the radio_station_name column
 * @method array findByDbInfoUrl(string $info_url) Return CcFiles objects filtered by the info_url column
 * @method array findByDbArtistUrl(string $artist_url) Return CcFiles objects filtered by the artist_url column
 * @method array findByDbAudioSourceUrl(string $audio_source_url) Return CcFiles objects filtered by the audio_source_url column
 * @method array findByDbRadioStationUrl(string $radio_station_url) Return CcFiles objects filtered by the radio_station_url column
 * @method array findByDbBuyThisUrl(string $buy_this_url) Return CcFiles objects filtered by the buy_this_url column
 * @method array findByDbIsrcNumber(string $isrc_number) Return CcFiles objects filtered by the isrc_number column
 * @method array findByDbCatalogNumber(string $catalog_number) Return CcFiles objects filtered by the catalog_number column
 * @method array findByDbOriginalArtist(string $original_artist) Return CcFiles objects filtered by the original_artist column
 * @method array findByDbCopyright(string $copyright) Return CcFiles objects filtered by the copyright column
 * @method array findByDbReportDatetime(string $report_datetime) Return CcFiles objects filtered by the report_datetime column
 * @method array findByDbReportLocation(string $report_location) Return CcFiles objects filtered by the report_location column
 * @method array findByDbReportOrganization(string $report_organization) Return CcFiles objects filtered by the report_organization column
 * @method array findByDbSubject(string $subject) Return CcFiles objects filtered by the subject column
 * @method array findByDbContributor(string $contributor) Return CcFiles objects filtered by the contributor column
 * @method array findByDbLanguage(string $language) Return CcFiles objects filtered by the language column
 * @method array findByDbFileExists(boolean $file_exists) Return CcFiles objects filtered by the file_exists column
 * @method array findByDbReplayGain(string $replay_gain) Return CcFiles objects filtered by the replay_gain column
 * @method array findByDbOwnerId(int $owner_id) Return CcFiles objects filtered by the owner_id column
 * @method array findByDbCuein(string $cuein) Return CcFiles objects filtered by the cuein column
 * @method array findByDbCueout(string $cueout) Return CcFiles objects filtered by the cueout column
 * @method array findByDbSilanCheck(boolean $silan_check) Return CcFiles objects filtered by the silan_check column
 * @method array findByDbHidden(boolean $hidden) Return CcFiles objects filtered by the hidden column
 * @method array findByDbIsScheduled(boolean $is_scheduled) Return CcFiles objects filtered by the is_scheduled column
 * @method array findByDbIsPlaylist(boolean $is_playlist) Return CcFiles objects filtered by the is_playlist column
 * @method array findByDbFilesize(int $filesize) Return CcFiles objects filtered by the filesize column
 * @method array findByDbDescription(string $description) Return CcFiles objects filtered by the description column
 * @method array findByDbArtwork(string $artwork) Return CcFiles objects filtered by the artwork column
 * @method array findByDbTrackType(string $track_type) Return CcFiles objects filtered by the track_type column
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
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'CcFiles';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcFilesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcFilesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcFilesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcFilesQuery) {
            return $criteria;
        }
        $query = new CcFilesQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CcFiles|CcFiles[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcFilesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CcFiles A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CcFiles A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "mime", "ftype", "filepath", "import_status", "currentlyaccessing", "editedby", "mtime", "utime", "lptime", "md5", "track_title", "artist_name", "bit_rate", "sample_rate", "format", "length", "album_title", "genre", "comments", "year", "track_number", "channels", "url", "bpm", "rating", "encoded_by", "disc_number", "mood", "label", "composer", "encoder", "checksum", "lyrics", "orchestra", "conductor", "lyricist", "original_lyricist", "radio_station_name", "info_url", "artist_url", "audio_source_url", "radio_station_url", "buy_this_url", "isrc_number", "catalog_number", "original_artist", "copyright", "report_datetime", "report_location", "report_organization", "subject", "contributor", "language", "file_exists", "replay_gain", "owner_id", "cuein", "cueout", "silan_check", "hidden", "is_scheduled", "is_playlist", "filesize", "description", "artwork", "track_type" FROM "cc_files" WHERE "id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CcFiles();
            $obj->hydrate($row);
            CcFilesPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return CcFiles|CcFiles[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|CcFiles[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcFilesPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbId(1234); // WHERE id = 1234
     * $query->filterByDbId(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterByDbId(array('min' => 12)); // WHERE id >= 12
     * $query->filterByDbId(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcFilesPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcFilesPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByDbName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbMime('fooValue');   // WHERE mime = 'fooValue'
     * $query->filterByDbMime('%fooValue%'); // WHERE mime LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbMime The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbFtype('fooValue');   // WHERE ftype = 'fooValue'
     * $query->filterByDbFtype('%fooValue%'); // WHERE ftype LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbFtype The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Filter the query on the filepath column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFilepath('fooValue');   // WHERE filepath = 'fooValue'
     * $query->filterByDbFilepath('%fooValue%'); // WHERE filepath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbFilepath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Filter the query on the import_status column
     *
     * Example usage:
     * <code>
     * $query->filterByDbImportStatus(1234); // WHERE import_status = 1234
     * $query->filterByDbImportStatus(array(12, 34)); // WHERE import_status IN (12, 34)
     * $query->filterByDbImportStatus(array('min' => 12)); // WHERE import_status >= 12
     * $query->filterByDbImportStatus(array('max' => 12)); // WHERE import_status <= 12
     * </code>
     *
     * @param     mixed $dbImportStatus The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbImportStatus($dbImportStatus = null, $comparison = null)
    {
        if (is_array($dbImportStatus)) {
            $useMinMax = false;
            if (isset($dbImportStatus['min'])) {
                $this->addUsingAlias(CcFilesPeer::IMPORT_STATUS, $dbImportStatus['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbImportStatus['max'])) {
                $this->addUsingAlias(CcFilesPeer::IMPORT_STATUS, $dbImportStatus['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::IMPORT_STATUS, $dbImportStatus, $comparison);
    }

    /**
     * Filter the query on the currentlyaccessing column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCurrentlyaccessing(1234); // WHERE currentlyaccessing = 1234
     * $query->filterByDbCurrentlyaccessing(array(12, 34)); // WHERE currentlyaccessing IN (12, 34)
     * $query->filterByDbCurrentlyaccessing(array('min' => 12)); // WHERE currentlyaccessing >= 12
     * $query->filterByDbCurrentlyaccessing(array('max' => 12)); // WHERE currentlyaccessing <= 12
     * </code>
     *
     * @param     mixed $dbCurrentlyaccessing The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbEditedby(1234); // WHERE editedby = 1234
     * $query->filterByDbEditedby(array(12, 34)); // WHERE editedby IN (12, 34)
     * $query->filterByDbEditedby(array('min' => 12)); // WHERE editedby >= 12
     * $query->filterByDbEditedby(array('max' => 12)); // WHERE editedby <= 12
     * </code>
     *
     * @see       filterByCcSubjsRelatedByDbEditedby()
     *
     * @param     mixed $dbEditedby The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbMtime('2011-03-14'); // WHERE mtime = '2011-03-14'
     * $query->filterByDbMtime('now'); // WHERE mtime = '2011-03-14'
     * $query->filterByDbMtime(array('max' => 'yesterday')); // WHERE mtime < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbMtime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbUtime('2011-03-14'); // WHERE utime = '2011-03-14'
     * $query->filterByDbUtime('now'); // WHERE utime = '2011-03-14'
     * $query->filterByDbUtime(array('max' => 'yesterday')); // WHERE utime < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbUtime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLPtime('2011-03-14'); // WHERE lptime = '2011-03-14'
     * $query->filterByDbLPtime('now'); // WHERE lptime = '2011-03-14'
     * $query->filterByDbLPtime(array('max' => 'yesterday')); // WHERE lptime < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbLPtime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbMd5('fooValue');   // WHERE md5 = 'fooValue'
     * $query->filterByDbMd5('%fooValue%'); // WHERE md5 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbMd5 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbTrackTitle('fooValue');   // WHERE track_title = 'fooValue'
     * $query->filterByDbTrackTitle('%fooValue%'); // WHERE track_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTrackTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbArtistName('fooValue');   // WHERE artist_name = 'fooValue'
     * $query->filterByDbArtistName('%fooValue%'); // WHERE artist_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbArtistName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbBitRate(1234); // WHERE bit_rate = 1234
     * $query->filterByDbBitRate(array(12, 34)); // WHERE bit_rate IN (12, 34)
     * $query->filterByDbBitRate(array('min' => 12)); // WHERE bit_rate >= 12
     * $query->filterByDbBitRate(array('max' => 12)); // WHERE bit_rate <= 12
     * </code>
     *
     * @param     mixed $dbBitRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbSampleRate(1234); // WHERE sample_rate = 1234
     * $query->filterByDbSampleRate(array(12, 34)); // WHERE sample_rate IN (12, 34)
     * $query->filterByDbSampleRate(array('min' => 12)); // WHERE sample_rate >= 12
     * $query->filterByDbSampleRate(array('max' => 12)); // WHERE sample_rate <= 12
     * </code>
     *
     * @param     mixed $dbSampleRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbFormat('fooValue');   // WHERE format = 'fooValue'
     * $query->filterByDbFormat('%fooValue%'); // WHERE format LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbFormat The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLength('fooValue');   // WHERE length = 'fooValue'
     * $query->filterByDbLength('%fooValue%'); // WHERE length LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLength The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbAlbumTitle('fooValue');   // WHERE album_title = 'fooValue'
     * $query->filterByDbAlbumTitle('%fooValue%'); // WHERE album_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbAlbumTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbGenre('fooValue');   // WHERE genre = 'fooValue'
     * $query->filterByDbGenre('%fooValue%'); // WHERE genre LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbGenre The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbComments('fooValue');   // WHERE comments = 'fooValue'
     * $query->filterByDbComments('%fooValue%'); // WHERE comments LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbComments The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbYear('fooValue');   // WHERE year = 'fooValue'
     * $query->filterByDbYear('%fooValue%'); // WHERE year LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbYear The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbTrackNumber(1234); // WHERE track_number = 1234
     * $query->filterByDbTrackNumber(array(12, 34)); // WHERE track_number IN (12, 34)
     * $query->filterByDbTrackNumber(array('min' => 12)); // WHERE track_number >= 12
     * $query->filterByDbTrackNumber(array('max' => 12)); // WHERE track_number <= 12
     * </code>
     *
     * @param     mixed $dbTrackNumber The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbChannels(1234); // WHERE channels = 1234
     * $query->filterByDbChannels(array(12, 34)); // WHERE channels IN (12, 34)
     * $query->filterByDbChannels(array('min' => 12)); // WHERE channels >= 12
     * $query->filterByDbChannels(array('max' => 12)); // WHERE channels <= 12
     * </code>
     *
     * @param     mixed $dbChannels The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByDbUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbBpm(1234); // WHERE bpm = 1234
     * $query->filterByDbBpm(array(12, 34)); // WHERE bpm IN (12, 34)
     * $query->filterByDbBpm(array('min' => 12)); // WHERE bpm >= 12
     * $query->filterByDbBpm(array('max' => 12)); // WHERE bpm <= 12
     * </code>
     *
     * @param     mixed $dbBpm The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbRating('fooValue');   // WHERE rating = 'fooValue'
     * $query->filterByDbRating('%fooValue%'); // WHERE rating LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbRating The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbEncodedBy('fooValue');   // WHERE encoded_by = 'fooValue'
     * $query->filterByDbEncodedBy('%fooValue%'); // WHERE encoded_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEncodedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbDiscNumber('fooValue');   // WHERE disc_number = 'fooValue'
     * $query->filterByDbDiscNumber('%fooValue%'); // WHERE disc_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDiscNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbMood('fooValue');   // WHERE mood = 'fooValue'
     * $query->filterByDbMood('%fooValue%'); // WHERE mood LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbMood The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByDbLabel('%fooValue%'); // WHERE label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLabel The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbComposer('fooValue');   // WHERE composer = 'fooValue'
     * $query->filterByDbComposer('%fooValue%'); // WHERE composer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbComposer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbEncoder('fooValue');   // WHERE encoder = 'fooValue'
     * $query->filterByDbEncoder('%fooValue%'); // WHERE encoder LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEncoder The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbChecksum('fooValue');   // WHERE checksum = 'fooValue'
     * $query->filterByDbChecksum('%fooValue%'); // WHERE checksum LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbChecksum The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLyrics('fooValue');   // WHERE lyrics = 'fooValue'
     * $query->filterByDbLyrics('%fooValue%'); // WHERE lyrics LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLyrics The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbOrchestra('fooValue');   // WHERE orchestra = 'fooValue'
     * $query->filterByDbOrchestra('%fooValue%'); // WHERE orchestra LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbOrchestra The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbConductor('fooValue');   // WHERE conductor = 'fooValue'
     * $query->filterByDbConductor('%fooValue%'); // WHERE conductor LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbConductor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLyricist('fooValue');   // WHERE lyricist = 'fooValue'
     * $query->filterByDbLyricist('%fooValue%'); // WHERE lyricist LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLyricist The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbOriginalLyricist('fooValue');   // WHERE original_lyricist = 'fooValue'
     * $query->filterByDbOriginalLyricist('%fooValue%'); // WHERE original_lyricist LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbOriginalLyricist The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbRadioStationName('fooValue');   // WHERE radio_station_name = 'fooValue'
     * $query->filterByDbRadioStationName('%fooValue%'); // WHERE radio_station_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbRadioStationName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbInfoUrl('fooValue');   // WHERE info_url = 'fooValue'
     * $query->filterByDbInfoUrl('%fooValue%'); // WHERE info_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbInfoUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbArtistUrl('fooValue');   // WHERE artist_url = 'fooValue'
     * $query->filterByDbArtistUrl('%fooValue%'); // WHERE artist_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbArtistUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbAudioSourceUrl('fooValue');   // WHERE audio_source_url = 'fooValue'
     * $query->filterByDbAudioSourceUrl('%fooValue%'); // WHERE audio_source_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbAudioSourceUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbRadioStationUrl('fooValue');   // WHERE radio_station_url = 'fooValue'
     * $query->filterByDbRadioStationUrl('%fooValue%'); // WHERE radio_station_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbRadioStationUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbBuyThisUrl('fooValue');   // WHERE buy_this_url = 'fooValue'
     * $query->filterByDbBuyThisUrl('%fooValue%'); // WHERE buy_this_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbBuyThisUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbIsrcNumber('fooValue');   // WHERE isrc_number = 'fooValue'
     * $query->filterByDbIsrcNumber('%fooValue%'); // WHERE isrc_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbIsrcNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbCatalogNumber('fooValue');   // WHERE catalog_number = 'fooValue'
     * $query->filterByDbCatalogNumber('%fooValue%'); // WHERE catalog_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCatalogNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbOriginalArtist('fooValue');   // WHERE original_artist = 'fooValue'
     * $query->filterByDbOriginalArtist('%fooValue%'); // WHERE original_artist LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbOriginalArtist The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbCopyright('fooValue');   // WHERE copyright = 'fooValue'
     * $query->filterByDbCopyright('%fooValue%'); // WHERE copyright LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCopyright The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbReportDatetime('fooValue');   // WHERE report_datetime = 'fooValue'
     * $query->filterByDbReportDatetime('%fooValue%'); // WHERE report_datetime LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbReportDatetime The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbReportLocation('fooValue');   // WHERE report_location = 'fooValue'
     * $query->filterByDbReportLocation('%fooValue%'); // WHERE report_location LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbReportLocation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbReportOrganization('fooValue');   // WHERE report_organization = 'fooValue'
     * $query->filterByDbReportOrganization('%fooValue%'); // WHERE report_organization LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbReportOrganization The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbSubject('fooValue');   // WHERE subject = 'fooValue'
     * $query->filterByDbSubject('%fooValue%'); // WHERE subject LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbSubject The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbContributor('fooValue');   // WHERE contributor = 'fooValue'
     * $query->filterByDbContributor('%fooValue%'); // WHERE contributor LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbContributor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbLanguage('fooValue');   // WHERE language = 'fooValue'
     * $query->filterByDbLanguage('%fooValue%'); // WHERE language LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLanguage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbFileExists(true); // WHERE file_exists = true
     * $query->filterByDbFileExists('yes'); // WHERE file_exists = true
     * </code>
     *
     * @param     boolean|string $dbFileExists The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbFileExists($dbFileExists = null, $comparison = null)
    {
        if (is_string($dbFileExists)) {
            $dbFileExists = in_array(strtolower($dbFileExists), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcFilesPeer::FILE_EXISTS, $dbFileExists, $comparison);
    }

    /**
     * Filter the query on the replay_gain column
     *
     * Example usage:
     * <code>
     * $query->filterByDbReplayGain(1234); // WHERE replay_gain = 1234
     * $query->filterByDbReplayGain(array(12, 34)); // WHERE replay_gain IN (12, 34)
     * $query->filterByDbReplayGain(array('min' => 12)); // WHERE replay_gain >= 12
     * $query->filterByDbReplayGain(array('max' => 12)); // WHERE replay_gain <= 12
     * </code>
     *
     * @param     mixed $dbReplayGain The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbOwnerId(1234); // WHERE owner_id = 1234
     * $query->filterByDbOwnerId(array(12, 34)); // WHERE owner_id IN (12, 34)
     * $query->filterByDbOwnerId(array('min' => 12)); // WHERE owner_id >= 12
     * $query->filterByDbOwnerId(array('max' => 12)); // WHERE owner_id <= 12
     * </code>
     *
     * @see       filterByFkOwner()
     *
     * @param     mixed $dbOwnerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbCuein('fooValue');   // WHERE cuein = 'fooValue'
     * $query->filterByDbCuein('%fooValue%'); // WHERE cuein LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCuein The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbCueout('fooValue');   // WHERE cueout = 'fooValue'
     * $query->filterByDbCueout('%fooValue%'); // WHERE cueout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCueout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
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
     * Example usage:
     * <code>
     * $query->filterByDbSilanCheck(true); // WHERE silan_check = true
     * $query->filterByDbSilanCheck('yes'); // WHERE silan_check = true
     * </code>
     *
     * @param     boolean|string $dbSilanCheck The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbSilanCheck($dbSilanCheck = null, $comparison = null)
    {
        if (is_string($dbSilanCheck)) {
            $dbSilanCheck = in_array(strtolower($dbSilanCheck), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcFilesPeer::SILAN_CHECK, $dbSilanCheck, $comparison);
    }

    /**
     * Filter the query on the hidden column
     *
     * Example usage:
     * <code>
     * $query->filterByDbHidden(true); // WHERE hidden = true
     * $query->filterByDbHidden('yes'); // WHERE hidden = true
     * </code>
     *
     * @param     boolean|string $dbHidden The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbHidden($dbHidden = null, $comparison = null)
    {
        if (is_string($dbHidden)) {
            $dbHidden = in_array(strtolower($dbHidden), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcFilesPeer::HIDDEN, $dbHidden, $comparison);
    }

    /**
     * Filter the query on the is_scheduled column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsScheduled(true); // WHERE is_scheduled = true
     * $query->filterByDbIsScheduled('yes'); // WHERE is_scheduled = true
     * </code>
     *
     * @param     boolean|string $dbIsScheduled The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbIsScheduled($dbIsScheduled = null, $comparison = null)
    {
        if (is_string($dbIsScheduled)) {
            $dbIsScheduled = in_array(strtolower($dbIsScheduled), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcFilesPeer::IS_SCHEDULED, $dbIsScheduled, $comparison);
    }

    /**
     * Filter the query on the is_playlist column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsPlaylist(true); // WHERE is_playlist = true
     * $query->filterByDbIsPlaylist('yes'); // WHERE is_playlist = true
     * </code>
     *
     * @param     boolean|string $dbIsPlaylist The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbIsPlaylist($dbIsPlaylist = null, $comparison = null)
    {
        if (is_string($dbIsPlaylist)) {
            $dbIsPlaylist = in_array(strtolower($dbIsPlaylist), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcFilesPeer::IS_PLAYLIST, $dbIsPlaylist, $comparison);
    }

    /**
     * Filter the query on the filesize column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFilesize(1234); // WHERE filesize = 1234
     * $query->filterByDbFilesize(array(12, 34)); // WHERE filesize IN (12, 34)
     * $query->filterByDbFilesize(array('min' => 12)); // WHERE filesize >= 12
     * $query->filterByDbFilesize(array('max' => 12)); // WHERE filesize <= 12
     * </code>
     *
     * @param     mixed $dbFilesize The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbFilesize($dbFilesize = null, $comparison = null)
    {
        if (is_array($dbFilesize)) {
            $useMinMax = false;
            if (isset($dbFilesize['min'])) {
                $this->addUsingAlias(CcFilesPeer::FILESIZE, $dbFilesize['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFilesize['max'])) {
                $this->addUsingAlias(CcFilesPeer::FILESIZE, $dbFilesize['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::FILESIZE, $dbFilesize, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDbDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbDescription($dbDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDescription)) {
                $dbDescription = str_replace('*', '%', $dbDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the artwork column
     *
     * Example usage:
     * <code>
     * $query->filterByDbArtwork('fooValue');   // WHERE artwork = 'fooValue'
     * $query->filterByDbArtwork('%fooValue%'); // WHERE artwork LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbArtwork The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbArtwork($dbArtwork = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbArtwork)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbArtwork)) {
                $dbArtwork = str_replace('*', '%', $dbArtwork);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::ARTWORK, $dbArtwork, $comparison);
    }

    /**
     * Filter the query on the track_type column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTrackType('fooValue');   // WHERE track_type = 'fooValue'
     * $query->filterByDbTrackType('%fooValue%'); // WHERE track_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTrackType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function filterByDbTrackType($dbTrackType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTrackType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTrackType)) {
                $dbTrackType = str_replace('*', '%', $dbTrackType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcFilesPeer::TRACK_TYPE, $dbTrackType, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFkOwner($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcFilesPeer::OWNER_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcFilesPeer::OWNER_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByFkOwner() only accepts arguments of type CcSubjs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FkOwner relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinFkOwner($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useFkOwnerQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFkOwner($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FkOwner', 'CcSubjsQuery');
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjsRelatedByDbEditedby($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(CcFilesPeer::EDITEDBY, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcFilesPeer::EDITEDBY, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcSubjsRelatedByDbEditedby() only accepts arguments of type CcSubjs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSubjsRelatedByDbEditedby relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcSubjsRelatedByDbEditedby($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsRelatedByDbEditedbyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSubjsRelatedByDbEditedby($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjsRelatedByDbEditedby', 'CcSubjsQuery');
    }

    /**
     * Filter the query by a related CloudFile object
     *
     * @param   CloudFile|PropelObjectCollection $cloudFile  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCloudFile($cloudFile, $comparison = null)
    {
        if ($cloudFile instanceof CloudFile) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $cloudFile->getCcFileId(), $comparison);
        } elseif ($cloudFile instanceof PropelObjectCollection) {
            return $this
                ->useCloudFileQuery()
                ->filterByPrimaryKeys($cloudFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCloudFile() only accepts arguments of type CloudFile or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CloudFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCloudFile($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CloudFile');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CloudFile');
        }

        return $this;
    }

    /**
     * Use the CloudFile relation CloudFile object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CloudFileQuery A secondary query class using the current class as primary query
     */
    public function useCloudFileQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCloudFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CloudFile', 'CloudFileQuery');
    }

    /**
     * Filter the query by a related CcShowInstances object
     *
     * @param   CcShowInstances|PropelObjectCollection $ccShowInstances  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowInstances($ccShowInstances, $comparison = null)
    {
        if ($ccShowInstances instanceof CcShowInstances) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $ccShowInstances->getDbRecordedFile(), $comparison);
        } elseif ($ccShowInstances instanceof PropelObjectCollection) {
            return $this
                ->useCcShowInstancesQuery()
                ->filterByPrimaryKeys($ccShowInstances->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowInstances() only accepts arguments of type CcShowInstances or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowInstances relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcShowInstances($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcShowInstancesQuery A secondary query class using the current class as primary query
     */
    public function useCcShowInstancesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowInstances($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
    }

    /**
     * Filter the query by a related CcPlaylistcontents object
     *
     * @param   CcPlaylistcontents|PropelObjectCollection $ccPlaylistcontents  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
    {
        if ($ccPlaylistcontents instanceof CcPlaylistcontents) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $ccPlaylistcontents->getDbFileId(), $comparison);
        } elseif ($ccPlaylistcontents instanceof PropelObjectCollection) {
            return $this
                ->useCcPlaylistcontentsQuery()
                ->filterByPrimaryKeys($ccPlaylistcontents->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlaylistcontents() only accepts arguments of type CcPlaylistcontents or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylistcontents relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcPlaylistcontents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcPlaylistcontentsQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistcontentsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylistcontents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylistcontents', 'CcPlaylistcontentsQuery');
    }

    /**
     * Filter the query by a related CcBlockcontents object
     *
     * @param   CcBlockcontents|PropelObjectCollection $ccBlockcontents  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlockcontents($ccBlockcontents, $comparison = null)
    {
        if ($ccBlockcontents instanceof CcBlockcontents) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $ccBlockcontents->getDbFileId(), $comparison);
        } elseif ($ccBlockcontents instanceof PropelObjectCollection) {
            return $this
                ->useCcBlockcontentsQuery()
                ->filterByPrimaryKeys($ccBlockcontents->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcBlockcontents() only accepts arguments of type CcBlockcontents or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcBlockcontents relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcBlockcontents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcBlockcontentsQuery A secondary query class using the current class as primary query
     */
    public function useCcBlockcontentsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcBlockcontents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlockcontents', 'CcBlockcontentsQuery');
    }

    /**
     * Filter the query by a related CcSchedule object
     *
     * @param   CcSchedule|PropelObjectCollection $ccSchedule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSchedule($ccSchedule, $comparison = null)
    {
        if ($ccSchedule instanceof CcSchedule) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $ccSchedule->getDbFileId(), $comparison);
        } elseif ($ccSchedule instanceof PropelObjectCollection) {
            return $this
                ->useCcScheduleQuery()
                ->filterByPrimaryKeys($ccSchedule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcSchedule() only accepts arguments of type CcSchedule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSchedule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcSchedule($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcScheduleQuery A secondary query class using the current class as primary query
     */
    public function useCcScheduleQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
    }

    /**
     * Filter the query by a related CcPlayoutHistory object
     *
     * @param   CcPlayoutHistory|PropelObjectCollection $ccPlayoutHistory  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
    {
        if ($ccPlayoutHistory instanceof CcPlayoutHistory) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $ccPlayoutHistory->getDbFileId(), $comparison);
        } elseif ($ccPlayoutHistory instanceof PropelObjectCollection) {
            return $this
                ->useCcPlayoutHistoryQuery()
                ->filterByPrimaryKeys($ccPlayoutHistory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlayoutHistory() only accepts arguments of type CcPlayoutHistory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
        if ($relationAlias) {
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
     * @return   CcPlayoutHistoryQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlayoutHistory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
    }

    /**
     * Filter the query by a related ThirdPartyTrackReferences object
     *
     * @param   ThirdPartyTrackReferences|PropelObjectCollection $thirdPartyTrackReferences  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByThirdPartyTrackReferences($thirdPartyTrackReferences, $comparison = null)
    {
        if ($thirdPartyTrackReferences instanceof ThirdPartyTrackReferences) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $thirdPartyTrackReferences->getDbFileId(), $comparison);
        } elseif ($thirdPartyTrackReferences instanceof PropelObjectCollection) {
            return $this
                ->useThirdPartyTrackReferencesQuery()
                ->filterByPrimaryKeys($thirdPartyTrackReferences->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByThirdPartyTrackReferences() only accepts arguments of type ThirdPartyTrackReferences or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ThirdPartyTrackReferences relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinThirdPartyTrackReferences($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ThirdPartyTrackReferences');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'ThirdPartyTrackReferences');
        }

        return $this;
    }

    /**
     * Use the ThirdPartyTrackReferences relation ThirdPartyTrackReferences object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   ThirdPartyTrackReferencesQuery A secondary query class using the current class as primary query
     */
    public function useThirdPartyTrackReferencesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinThirdPartyTrackReferences($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ThirdPartyTrackReferences', 'ThirdPartyTrackReferencesQuery');
    }

    /**
     * Filter the query by a related PodcastEpisodes object
     *
     * @param   PodcastEpisodes|PropelObjectCollection $podcastEpisodes  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcFilesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcastEpisodes($podcastEpisodes, $comparison = null)
    {
        if ($podcastEpisodes instanceof PodcastEpisodes) {
            return $this
                ->addUsingAlias(CcFilesPeer::ID, $podcastEpisodes->getDbFileId(), $comparison);
        } elseif ($podcastEpisodes instanceof PropelObjectCollection) {
            return $this
                ->usePodcastEpisodesQuery()
                ->filterByPrimaryKeys($podcastEpisodes->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPodcastEpisodes() only accepts arguments of type PodcastEpisodes or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PodcastEpisodes relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function joinPodcastEpisodes($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PodcastEpisodes');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PodcastEpisodes');
        }

        return $this;
    }

    /**
     * Use the PodcastEpisodes relation PodcastEpisodes object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PodcastEpisodesQuery A secondary query class using the current class as primary query
     */
    public function usePodcastEpisodesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPodcastEpisodes($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PodcastEpisodes', 'PodcastEpisodesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcFiles $ccFiles Object to remove from the list of results
     *
     * @return CcFilesQuery The current query, for fluid interface
     */
    public function prune($ccFiles = null)
    {
        if ($ccFiles) {
            $this->addUsingAlias(CcFilesPeer::ID, $ccFiles->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
