<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// Requires: PHP 4.1.0 (or higher)                             //
//           GD  <1.6 for GIF and JPEG functions               //
//           GD >=1.6 for PNG and JPEG functions               //
//           GD >=2.0 for BMP display function                 //
//                                                             //
// Please see getid3.readme.txt for more information           //
//                                                             //
/////////////////////////////////////////////////////////////////

// Defines
define('GETID3VERSION', '1.6.0');
define('FREAD_BUFFER_SIZE', 16384); // number of bytes to read in at once

// Get base path of getID3()
$includedfilepaths = get_included_files();
foreach ($includedfilepaths as $key => $val) {
    if (basename($val) == 'getid3.php') {
		if (substr(php_uname(), 0, 7) == 'Windows') {
			define('GETID3_INCLUDEPATH', str_replace('\\', '/', dirname($val)).'/');
		} else {
			define('GETID3_INCLUDEPATH', dirname($val).'/');
		}
    }
}
if (!defined('GETID3_INCLUDEPATH')) {
    define('GETID3_INCLUDEPATH', '');
}


function GetAllFileInfo($filename, $assumedFormat='', $MD5file=false, $MD5data=false) {
    require_once(GETID3_INCLUDEPATH.'getid3.functions.php'); // Function library

    $ThisFileInfo['getID3version']       = GETID3VERSION;
    $ThisFileInfo['fileformat']          = '';            // filled in later
    $ThisFileInfo['audio']['dataformat'] = '';            // filled in later, unset if not used
    $ThisFileInfo['video']['dataformat'] = '';            // filled in later, unset if not used
    $ThisFileInfo['tags']                = array();       // filled in later, unset if not used
    $ThisFileInfo['error']               = '';            // filled in later, unset if not used
    $ThisFileInfo['warning']             = '';            // filled in later, unset if not used
    $ThisFileInfo['exist']               = false;

    if (eregi('^(ht|f)tp://', $filename)) {

		// remote file
		$ThisFileInfo['filename'] = $filename;
		$ThisFileInfo['error'] .= "\n".'Remote files are not supported in this version of getID3() - please copy the file locally first';

    } else {

		// local file

		$ThisFileInfo['filename']     = basename($filename);
		$ThisFileInfo['filepath']     = str_replace('\\', '/', realpath(dirname($filename)));
		$ThisFileInfo['filenamepath'] = $ThisFileInfo['filepath'].'/'.$ThisFileInfo['filename'];
		ob_start();
		if ($localfilepointer = fopen($filename, 'rb')) {

			$ThisFileInfo['exist'] = true;

			//$ThisFileInfo['filesize'] = filesize($ThisFileInfo['filenamepath']);
			// PHP doesn't support integers larger than 31-bit (~2GB)
			// filesize() simply returns (filesize % (pow(2, 32)), no matter the actual filesize
			// ftell() returns 0 if seeking to the end is beyond the range of unsigned integer
			fseek($localfilepointer, 0, SEEK_END);
			$ThisFileInfo['filesize'] = ftell($localfilepointer);
			ob_end_clean();
			if ($ThisFileInfo['filesize'] == 0) {
				if (filesize($ThisFileInfo['filenamepath']) != 0) {

					unset($ThisFileInfo['filesize']);
					$ThisFileInfo['error'] .= "\n".'File is most likely larger than 2GB and is not supported by PHP';

				}

				// remove unneeded/meaningless keys
				CleanUpGetAllMP3info($ThisFileInfo);

				// close & remove local filepointer
				CloseRemoveFilepointer($localfilepointer);
				return $ThisFileInfo;

			}


		} else {

			$ThisFileInfo['error'] .= "\n".'Error opening file: '.trim(strip_tags(ob_get_contents()));
			ob_end_clean();

			// remove unneeded/meaningless keys
			CleanUpGetAllMP3info($ThisFileInfo);

			// close & remove local filepointer
			CloseRemoveFilepointer($localfilepointer);
			return $ThisFileInfo;

		}

    }

    // Initialize avdataoffset and avdataend
    $ThisFileInfo['avdataoffset'] = 0;
    $ThisFileInfo['avdataend']    = $ThisFileInfo['filesize'];

    // Handle APE tags
    HandleAPETag($localfilepointer, $ThisFileInfo);

    rewind($localfilepointer);
    //$formattest = fread($localfilepointer, 16);  // 16 bytes is sufficient for any format except ISO CD-image
    $formattest = fread($localfilepointer, 32774); // (ISO needs at least 32774 bytes)

    // Handle ID3v2 tag
    if (substr($formattest, 0, 3) == 'ID3') {
		HandleID3v2Tag($localfilepointer, $ThisFileInfo);
		rewind($localfilepointer);
		fseek($localfilepointer, $ThisFileInfo['avdataoffset'], SEEK_SET);
		$formattest = fread($localfilepointer, 32774); // (ISO9660 needs at least 32774 bytes)
    }

    // Handle ID3v1 tags
    HandleID3v1Tag($localfilepointer, $ThisFileInfo);

    // Nothing-but-tags check
    if (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) > 0) {

		if ($DeterminedFormat = GetFileFormat($formattest)) {

			// break if ID3/APE tags found on illegal format
			if (!$DeterminedFormat['allowtags'] && ($ThisFileInfo['avdataoffset'] > 0) && ($ThisFileInfo['avdataend'] != $ThisFileInfo['filesize'])) {
				$ThisFileInfo['error'] .= "\n".'Illegal ID3 and/or APE tag found on non multimedia file.';
				break;
			}

			// set mime type
			$ThisFileInfo['mime_type'] = $DeterminedFormat['mimetype'];

			// supported format signature pattern detected
			require_once(GETID3_INCLUDEPATH.$DeterminedFormat['include']);

			switch ($DeterminedFormat['format']) {
				//case 'midi':
				//    if ($assumedFormat === false) {
				//        // do not parse all MIDI tracks - much faster
				//        getMIDIHeaderFilepointer($localfilepointer, $ThisFileInfo, false);
				//    } else {
				//        getMIDIHeaderFilepointer($localfilepointer, $ThisFileInfo);
				//    }
				//    break;

				//case 'aac':
				//    if (!getAACADIFheaderFilepointer($localfilepointer, $ThisFileInfo)) {
				//        $dummy = $ThisFileInfo;
				//        unset($dummy['error']);
				//        if (getAACADTSheaderFilepointer($localfilepointer, $dummy)) {
				//            $ThisFileInfo = $dummy;
				//        }
				//    }
				//    break;

				default:
					$VariableFunctionName = $DeterminedFormat['function'];
					$VariableFunctionName($localfilepointer, $ThisFileInfo);
					break;
			}

		} elseif ((($assumedFormat == 'mp3') || (($assumedFormat == '') && ((substr($formattest, 0, 3) == 'ID3') || (substr(BigEndian2Bin(substr($formattest, 0, 2)), 0, 11) == '11111111111'))))) {

			// assume AAC-ADTS format
			require_once(GETID3_INCLUDEPATH.'getid3.aac.php');
			$dummy = $ThisFileInfo;
			if (getAACADTSheaderFilepointer($localfilepointer, $dummy)) {

				$ThisFileInfo = $dummy;

			} else {

				// it's not AAC-ADTS format, probably MP3
				require_once(GETID3_INCLUDEPATH.'getid3.mp3.php');
				getMP3headerFilepointer($localfilepointer, $ThisFileInfo);

			}

		} else {

			// unknown format, do nothing

		}

    }

    if (isset($ThisFileInfo['fileformat'])) {

		// Calculate combined bitrate - audio + video
		$CombinedBitrate  = 0;
		$CombinedBitrate += (isset($ThisFileInfo['audio']['bitrate']) ? $ThisFileInfo['audio']['bitrate'] : 0);
		$CombinedBitrate += (isset($ThisFileInfo['video']['bitrate']) ? $ThisFileInfo['video']['bitrate'] : 0);
		if (($CombinedBitrate > 0) && !isset($ThisFileInfo['bitrate'])) {
			$ThisFileInfo['bitrate'] = $CombinedBitrate;
		}

		// Set playtime string
		if (!empty($ThisFileInfo['playtime_seconds']) && empty($ThisFileInfo['playtime_string'])) {
			$ThisFileInfo['playtime_string'] = PlaytimeString($ThisFileInfo['playtime_seconds']);
		}

		if (!empty($ThisFileInfo['audio']['channels'])) {
			switch ($ThisFileInfo['audio']['channels']) {
				case 1:
					$ThisFileInfo['audio']['channelmode'] = 'mono';
					break;

				case 2:
					$ThisFileInfo['audio']['channelmode'] = 'stereo';
					break;

				default:
					// unknown?
					break;
			}
		}
    }

    // Get the MD5 hash of the entire file
    if ($MD5file && empty($ThisFileInfo['md5_file'])) {
		$ThisFileInfo['md5_file'] = md5_file($filename);
    }

    // Get the MD5 hash of the audio/video portion of the file
    // (without ID3/APE/Lyrics3/etc header/footer tags
    if ($MD5data && empty($ThisFileInfo['md5_data'])) {
		getMD5data($ThisFileInfo);
	}

    // return tags data in alphabetical order, without duplicates
    $ThisFileInfo['tags'] = array_unique($ThisFileInfo['tags']);
    sort($ThisFileInfo['tags']);

    // remove unneeded/meaningless keys
    CleanUpGetAllMP3info($ThisFileInfo);

    // close & remove local filepointer
    CloseRemoveFilepointer($localfilepointer);

    return $ThisFileInfo;
}


function CleanUpGetAllMP3info(&$ThisFileInfo) {
    if (empty($ThisFileInfo['fileformat'])) {
		// remove meaningless entries from unknown-format files
		unset($ThisFileInfo['fileformat']);
		unset($ThisFileInfo['audio']['dataformat']);
		unset($ThisFileInfo['video']['dataformat']);
		unset($ThisFileInfo['avdataoffset']);
		unset($ThisFileInfo['avdataend']);
    }

    $PotentialKeysToRemove = array('dataformat', 'bitrate_mode');
    foreach (array('audio', 'video') as $key1) {
		$RemoveThisKey = true;
		if (count($ThisFileInfo[$key1]) > count($PotentialKeysToRemove)) {
			$RemoveThisKey = false;
		} else {
			foreach ($PotentialKeysToRemove as $key2) {
				if (!in_array($key2, $PotentialKeysToRemove)) {
					$RemoveThisKey = false;
					break;
				}
			}
		}
		if ($RemoveThisKey) {
			unset($ThisFileInfo[$key1]);
		}
    }

    $PotentialKeysToRemove = array('comments', 'error', 'warning', 'audio', 'video');
    foreach ($PotentialKeysToRemove as $keyname) {
		if (empty($ThisFileInfo["$keyname"])) {
			unset($ThisFileInfo["$keyname"]);
		}
    }

    return true;
}

function CloseRemoveFilepointer(&$filepointer) {
    if (isset($localfilepointer) && is_resource($localfilepointer) && (get_resource_type($localfilepointer) == 'file')) {
		fclose($localfilepointer);
		if (isset($localfilepointer)) {
			unset($localfilepointer);
		}
    }
    return true;
}


function CopyFormatCommentsToRootComments($commentsarray, &$ThisFileInfo, $KeysToCopy=array('title'=>'title', 'artist'=>'artist', 'album'=>'album', 'year'=>'year', 'genre'=>'genre', 'comment'=>'comment', 'track'=>'track'), $ReplaceAll=false, $Append=false) {
    if ($ReplaceAll) {
		$ThisFileInfo['comments'] = array();
    }

    if (!is_array($KeysToCopy)) {
		$KeysToCopy = array();
		foreach (array_keys($commentsarray) as $key => $value) {
			$KeysToCopy[$value] = $value;
		}
    }
    foreach ($KeysToCopy as $FromKey => $ToKey) {
		$ToKey = strtolower($ToKey);
		if (!empty($commentsarray["$FromKey"])) {
			if (is_array($commentsarray["$FromKey"])) {
				foreach ($commentsarray["$FromKey"] as $CommentArrayValue) {
					if ((empty($ThisFileInfo['comments']["$ToKey"])) || ($Append && !in_array($CommentArrayValue, $ThisFileInfo['comments']["$ToKey"], false))) {
						$ThisFileInfo['comments']["$ToKey"][] = $CommentArrayValue;
					}
				}
			} else {
				if ((empty($ThisFileInfo['comments']["$ToKey"])) || ($Append && !in_array($commentsarray["$FromKey"], $ThisFileInfo['comments']["$ToKey"], false))) {
					$ThisFileInfo['comments']["$ToKey"][] = $commentsarray["$FromKey"];
				}
			}
		}
    }
    return true;
}

function GetFileFormat(&$filedata) {
    // this function will determine the format of a file based on usually
    // the first 2-4 bytes of the file (8 bytes for PNG, 16 bytes for JPG,
    // and in the case of ISO CD image, 6 bytes offset 32kb from the start
    // of the file).

    // Array containing information about all supported formats
    static $format_info = array();
    if (empty($format_info)) {
		$format_info = array(

			// Format:  <internal name> => array(<regular expression to id file>, <include-file>, <function-to-call>, <allow-id/ape-tags>, <MIME type>)


			// Audio formats

			// AAC  - audio       - Advanced Audio Coding (AAC) - ADIF format
			'aac'  => array('^ADIF',  'getid3.aac.php', 'getAACADIFheaderFilepointer', true, 'application/octet-stream'),

			// AIFF - audio       - Audio Interchange File Format (AIFF)
			//'aiff' => array('^FORM', 'getid3.aiff.php', 'getAIFFHeaderFilepointer', true, 'audio/x-aiff'),

			// FLAC - audio       - Free Lossless Audio Codec
			'flac' => array('^fLaC', 'getid3.flac.php', 'getFLACHeaderFilepointer', true, 'audio/x-flac'),

			// LA   - audio       - Lossless Audio (LA)
			'la'   => array('^LA0[23]', 'getid3.la.php', 'getLAHeaderFilepointer', true, 'application/octet-stream'),

			// MIDI - audio       - MIDI (Musical Instrument Digital Interface)
			'midi' => array('^MThd', 'getid3.midi.php', 'getMIDIHeaderFilepointer', true, 'audio/midi'),

			// MAC  - audio       - Monkey's Audio Compressor
			'mac'  => array('^MAC ', 'getid3.monkey.php', 'getMonkeysAudioHeaderFilepointer', true, 'application/octet-stream'),

			// MPC  - audio       - Musepack / MPEGplus
			'mpc'  => array('^MP\+', 'getid3.mpc.php', 'getMPCHeaderFilepointer', true, 'application/octet-stream'),

			// Ogg  - audio       - Ogg Vorbis
			'ogg'  => array('^OggS', 'getid3.ogg.php', 'getOggHeaderFilepointer', true, 'application/x-ogg'),

			// VQF  - audio       - transform-domain weighted interleave Vector Quantization Format (VQF)
			'vqf'  => array('^TWIN', 'getid3.vqf.php', 'getVQFHeaderFilepointer', true, 'application/octet-stream'),


			// Audio-Video formats

			// ASF  - audio/video - Advanced Streaming Format, Windows Media Video, Windows Media Audio
			'asf'  => array('^\x30\x26\xB2\x75\x8E\x66\xCF\x11\xA6\xD9\x00\xAA\x00\x62\xCE\x6C', 'getid3.asf.php', 'getASFHeaderFilepointer', true, 'video/x-ms-asf'),

			// RIFF - audio/video - Resource Interchange File Format (RIFF) / WAV / AVI / CD-audio / SDSS = renamed variant used by SmartSound QuickTracks (www.smartsound.com)
			'riff' => array('^(RIFF|SDSS)', 'getid3.riff.php', 'getRIFFHeaderFilepointer', true, 'audio/x-wave'),

			// Real - audio/video - RealAudio, RealVideo
			'real' => array('^\.RMF', 'getid3.real.php', 'getRealHeaderFilepointer', true, 'audio/x-realaudio'),

			// NSV  - audio/video - Nullsoft Streaming Video (NSV)
			'nsv'  => array('^NSV[sf]', 'getid3.nsv.php', 'getNSVHeaderFilepointer', true, 'application/octet-stream'),

			// MPEG - audio/video - MPEG (Moving Pictures Experts Group)
			'mpeg' => array('^\x00\x00\x01\xBA', 'getid3.mpeg.php', 'getMPEGHeaderFilepointer', true, 'video/mpeg'),

			// QT   - audio/video - Quicktime
			'quicktime' => array('^.{4}(cmov|free|ftyp|mdat|moov|pnot|skip|wide)', 'getid3.quicktime.php', 'getQuicktimeHeaderFilepointer', true, 'video/quicktime'),


			// Still-Image formats

			// BMP  - still image - Bitmap (Windows, OS/2; uncompressed, RLE8, RLE4)
			'bmp'  => array('^BM', 'getid3.bmp.php', 'getBMPHeaderFilepointer', false, 'image/bmp'),

			// GIF  - still image - Graphics Interchange Format
			'gif'  => array('^GIF', 'getid3.gif.php', 'getGIFHeaderFilepointer', false, 'image/gif'),

			// JPEG - still image - Joint Photographic Experts Group (JPEG)
			'jpg'  => array('^\xFF\xD8\xFF', 'getid3.jpg.php', 'getJPGHeaderFilepointer', false, 'image/jpg'),

			// PNG  - still image - Portable Network Graphics (PNG)
			'png'  => array('^\x89\x50\x4E\x47\x0D\x0A\x1A\x0A', 'getid3.png.php', 'getPNGHeaderFilepointer', false, 'image/png'),


			// Data formats

			// EXE  - data        - EXEcutable program (EXE, COM)
			//'exe'  => array('^MZ', 'getid3.exe.php', 'getEXEHeaderFilepointer', false, 'application/octet-stream'),

			// ISO  - data        - International Standards Organization (ISO) CD-ROM Image
			'iso'  => array('^.{32769}CD001', 'getid3.iso.php', 'getISOHeaderFilepointer', false, 'application/octet-stream'),

			// RAR  - data        - RAR compressed data
			//'rar'  => array('^Rar\!', 'getid3.rar.php', 'getRARHeaderFilepointer', false, 'application/octet-stream'),

			// ZIP  - data        - ZIP compressed data
			'zip'  => array('^PK\x03\x04', 'getid3.zip.php', 'getZIPHeaderFilepointer', false, 'application/zip'),

		);
    }

    // Identify file format - loop through $format_info and detect with reg expr
    foreach ($format_info as $format_name => $info) {
		// Using preg_match() instead of ereg() - much faster
		// The /s switch on preg_match() forces preg_match() NOT to treat
		// newline (0x0A) characters as special chars but do a binary match
		if (preg_match('/'.$info[0].'/s', $filedata)) {
			// Extract information
			$FormatData['format']    = $format_name;
			//$FormatData['pattern']  = $info[0];
			$FormatData['include']   = $info[1];
			$FormatData['function']  = $info[2];
			$FormatData['allowtags'] = $info[3];
			$FormatData['mimetype']  = $info[4];

			return $FormatData;

		}
    }
    return false;
}


function HandleAPETag(&$fd, &$ThisFileInfo) {
    require_once(GETID3_INCLUDEPATH.'getid3.ape.php');
    getAPEtagFilepointer($fd, $ThisFileInfo);

    if (isset($ThisFileInfo['ape']['header']['raw']['tagsize'])) {

		$ThisFileInfo['avdataend'] -= $ThisFileInfo['ape']['header']['raw']['tagsize'] + 32;

		// APE tags has second highest priority
		CopyFormatCommentsToRootComments($ThisFileInfo['ape']['comments'], $ThisFileInfo, true, true, true);

		// add tag to array of tags
		$ThisFileInfo['tags'][] = 'ape';

    }

    return true;
}



function HandleID3v1Tag(&$fd, &$ThisFileInfo) {
    fseek($fd, (0 - 128 - 9 - 6), SEEK_END); // end - ID3v1 - LYRICSEND - [Lyrics3size]
    $lyrics3_id3v1 = fread($fd, (128 + 9 + 6));
    $lyrics3lsz    = substr($lyrics3_id3v1,  0,   6); // Lyrics3size
    $lyrics3end    = substr($lyrics3_id3v1,  6,   9); // LYRICSEND or LYRICS200
    $id3v1tag      = substr($lyrics3_id3v1, 15, 128); // ID3v1

    if ($lyrics3end == 'LYRICSEND') {
		// Lyrics3 v1 and ID3v1

		$lyrics3size = 5100;
		$ThisFileInfo['avdataend'] -= $lyrics3size;
		require_once(GETID3_INCLUDEPATH.'getid3.lyrics3.php');
		getLyrics3Filepointer($ThisFileInfo, $fd, 0 - 128 - $lyrics3size, 1, $lyrics3size);

    } elseif ($lyrics3end == 'LYRICS200') {
		// Lyrics3 v2 and ID3v1

		$lyrics3size = $lyrics3lsz + 6 + strlen('LYRICS200'); // LSZ = lyrics + 'LYRICSBEGIN'; add 6-byte size field; add 'LYRICS200'
		$ThisFileInfo['avdataend'] -= $lyrics3size;
		require_once(GETID3_INCLUDEPATH.'getid3.lyrics3.php');
		getLyrics3Filepointer($ThisFileInfo, $fd, -128 - $lyrics3size, 2, $lyrics3size);

    } elseif (substr($lyrics3_id3v1, strlen($lyrics3_id3v1) - 1 - 9, 9) == 'LYRICSEND') {
		// Lyrics3 v1, no ID3v1 (I think according to Lyrics3 specs there MUST be ID3v1, but just in case :)

		$lyrics3size = 5100;
		$ThisFileInfo['avdataend'] -= $lyrics3size;
		require_once(GETID3_INCLUDEPATH.'getid3.lyrics3.php');
		getLyrics3Filepointer($ThisFileInfo, $fd, 0 - $lyrics3size, 1, $lyrics3size);

    } elseif (substr($lyrics3_id3v1, strlen($lyrics3_id3v1) - 1 - 9, 9) == 'LYRICS200') {
		// Lyrics3 v2, no ID3v1 (I think according to Lyrics3 specs there MUST be ID3v1, but just in case :)

		$lyrics3size = $lyrics3lsz + 6 + strlen('LYRICS200'); // LSZ = lyrics + 'LYRICSBEGIN'; add 6-byte size field; add 'LYRICS200'
		$ThisFileInfo['avdataend'] -= $lyrics3size;
		require_once(GETID3_INCLUDEPATH.'getid3.lyrics3.php');
		getLyrics3Filepointer($ThisFileInfo, $fd, 0 - $lyrics3size, 2, $lyrics3size);
    }

    if (substr($id3v1tag, 0, 3) == 'TAG') {
		$ThisFileInfo['avdataend'] -= 128;

		require_once(GETID3_INCLUDEPATH.'getid3.id3v1.php');
		getID3v1Filepointer($fd, $ThisFileInfo);

		// Do not change fileformat if already set
		if (empty($ThisFileInfo['fileformat'])) {
			$ThisFileInfo['fileformat'] = 'id3';
		}

		$ThisFileInfo['tags'][] = 'id3v1';

		// ID3v1 has lowest preference. We add if $ThisFileInfo[comments] is empty - this will override empty tags of higher preference, or add comments to root if not already present
		if (isset($ThisFileInfo['id3v1'])) {
			CopyFormatCommentsToRootComments($ThisFileInfo['id3v1'], $ThisFileInfo, true, false, false);
		}
    }

    return true;
}



function HandleID3v2Tag(&$fd, &$ThisFileInfo) {
    require_once(GETID3_INCLUDEPATH.'getid3.id3v2.php');
    getID3v2Filepointer($fd, $ThisFileInfo);

    // Tag present
    if (isset($ThisFileInfo['id3v2']['header'])) {

		// Do not change fileformat if already set
		if (empty($ThisFileInfo['fileformat'])) {
			$ThisFileInfo['fileformat'] = 'id3';
		}

		// Set avdataoffset
		$ThisFileInfo['avdataoffset'] = $ThisFileInfo['id3v2']['headerlength'];
		if (isset($ThisFileInfo['id3v2']['footer'])) {
			$ThisFileInfo['avdataoffset'] += 10;
		}

		$ThisFileInfo['tags'][] = 'id3v2';

		if (isset($ThisFileInfo['id3v2']['comments'])) {
			CopyFormatCommentsToRootComments($ThisFileInfo['id3v2']['comments'], $ThisFileInfo, true, false, false);
		}

    }

    return true;
}

function getMD5data(&$ThisFileInfo) {

	if (($ThisFileInfo['fileformat'] == 'ogg') && (@$ThisFileInfo['audio']['dataformat'] == 'vorbis')) {

		// We cannot get an identical md5_data value for Ogg files where the comments
		// span more than 1 Ogg page (compared to the same audio data with smaller
		// comments) using the normal getID3() method of MD5'ing the data between the
		// end of the comments and the end of the file (minus any trailing tags),
		// because the page sequence numbers of the pages that the audio data is on
		// do not match. Under normal circumstances, where comments are smaller than
		// the nominal 4-8kB page size, then this is not a problem, but if there are
		// very large comments, the only way around it is to strip off the comment
		// tags with vorbiscomment and MD5 that file.
		// This procedure must be applied to ALL Ogg files, not just the ones with
		// comments larger than 1 page, because the below method simply MD5's the
		// whole file with the comments stripped, not just the portion after the
		// comments block (which is the standard getID3() method.

		// The above-mentioned problem of comments spanning multiple pages and changing
		// page sequence numbers likely happens for OggSpeex and OggFLAC as well, but
		// currently vorbiscomment only works on OggVorbis files.

		if ((bool) ini_get('safe_mode')) {

			$ThisFileInfo['warning'] .= "\n".'Failed making system call to vorbiscomment.exe - md5_data is incorrect - error returned: PHP running in Safe Mode (backtick operator not available)';
			$ThisFileInfo['md5_data'] = false;

		} else {

			// Prevent user from aborting script
			$old_abort = ignore_user_abort(true);

			// Create empty file
			$empty = tempnam('/tmp', 'getID3');
			touch($empty);


			// Use vorbiscomment to make temp file without comments
			$temp = tempnam('/tmp', 'getID3');
			$file = $ThisFileInfo['filenamepath'];

			if (substr(php_uname(), 0, 7) == 'Windows') {

				if (file_exists(GETID3_INCLUDEPATH.'vorbiscomment.exe')) {

					$VorbisCommentError = `vorbiscomment.exe -w -c "$empty" "$file" "$temp"`;

				} else {

					$VorbisCommentError = 'vorbiscomment.exe not found in '.GETID3_INCLUDEPATH;

				}

			} else {

				$VorbisCommentError = `vorbiscomment -w -c "$empty" "$file" "$temp" 2>&1`;

			}

			if (!empty($VorbisCommentError)) {

				$ThisFileInfo['warning'] .= "\n".'Failed making system call to vorbiscomment(.exe) - md5_data will be incorrect. If vorbiscomment is unavailable, please download from http://www.vorbis.com/download.psp and put in the getID3() directory. Error returned: '.$VorbisCommentError;
				$ThisFileInfo['md5_data'] = false;

			} else {

				// Get md5 value of newly created file
				$ThisFileInfo['md5_data'] = md5_file($temp);

			}

			// Clean up
			unlink($empty);
			unlink($temp);

			// Reset abort setting
			ignore_user_abort($old_abort);

		}

	} else {

		if (!empty($ThisFileInfo['avdataoffset']) || (isset($ThisFileInfo['avdataend']) && ($ThisFileInfo['avdataend'] < $ThisFileInfo['filesize']))) {
			$ThisFileInfo['md5_data'] = md5_data($ThisFileInfo['filenamepath'], $ThisFileInfo['avdataoffset'], $ThisFileInfo['avdataend']);
		} else {
			if (empty($ThisFileInfo['md5_file'])) {
				$ThisFileInfo['md5_file'] = md5_file($ThisFileInfo['filenamepath']);
			}
			$ThisFileInfo['md5_data'] = $ThisFileInfo['md5_file'];
		}

	}
    return true;
}


function PoweredBygetID3($string='<BR><HR NOSHADE><DIV STYLE="font-size: 8pt; font-face: sans-serif;">Powered by <A HREF="http://getid3.sourceforge.net" TARGET="_blank"><B>getID3() v<!--GETID3VER--></B><BR>http://getid3.sourceforge.net</A></DIV>') {
    return str_replace('<!--GETID3VER-->', GETID3VERSION, $string);
}

?>