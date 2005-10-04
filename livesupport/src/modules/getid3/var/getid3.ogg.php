<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.ogg.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getOggHeaderFilepointer(&$fd, &$ThisFileInfo) {

    $ThisFileInfo['fileformat'] = 'ogg';

    // Warn about illegal tags - only vorbiscomments are allowed
    if (isset($ThisFileInfo['id3v2'])) {
		$ThisFileInfo['warning'] .= "\n".'Illegal ID3v2 tag present.';
    }
    if (isset($ThisFileInfo['id3v1'])) {
		$ThisFileInfo['warning'] .= "\n".'Illegal ID3v1 tag present.';
    }
    if (isset($ThisFileInfo['ape'])) {
		$ThisFileInfo['warning'] .= "\n".'Illegal APE tag present.';
    }


    // Page 1 - Stream Header

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);

    $oggpageinfo = ParseOggPageHeader($fd);
    $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']] = $oggpageinfo;

    if (ftell($fd) >= FREAD_BUFFER_SIZE) {
		$ThisFileInfo['error'] .= "\n".'Could not find start of Ogg page in the first '.FREAD_BUFFER_SIZE.' bytes (this might not be an Ogg-Vorbis file?)';
		unset($ThisFileInfo['fileformat']);
		unset($ThisFileInfo['ogg']);
		return false;
    }

    $filedata = fread($fd, $oggpageinfo['page_length']);
    $filedataoffset = 0;

    if (substr($filedata, 0, 4) == 'fLaC') {

		$ThisFileInfo['audio']['dataformat']   = 'flac';
		$ThisFileInfo['audio']['bitrate_mode'] = 'vbr';

    } elseif (substr($filedata, 1, 6) == 'vorbis') {

		$ThisFileInfo['audio']['dataformat'] = 'vorbis';

		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['packet_type'] = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
		$filedataoffset += 1;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['stream_type'] = substr($filedata, $filedataoffset, 6); // hard-coded to 'vorbis'
		$filedataoffset += 6;
		$ThisFileInfo['ogg']['bitstreamversion'] = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['numberofchannels'] = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
		$filedataoffset += 1;
		$ThisFileInfo['audio']['channels']                = $ThisFileInfo['ogg']['numberofchannels'];
		$ThisFileInfo['ogg']['samplerate']       = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		if ($ThisFileInfo['ogg']['samplerate'] == 0) {
			$ThisFileInfo['error'] .= "\n".'Corrupt Ogg file: sample rate == zero';
			return false;
		}
		$ThisFileInfo['audio']['sample_rate']               = $ThisFileInfo['ogg']['samplerate'];
		$ThisFileInfo['ogg']['samples']          = 0; // filled in later
		$ThisFileInfo['ogg']['bitrate_average']  = 0; // filled in later
		$ThisFileInfo['ogg']['bitrate_max']      = LittleEndian2Int(substr($filedata,  $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['bitrate_nominal']  = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['bitrate_min']      = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['blocksize_small']  = pow(2,  LittleEndian2Int(substr($filedata, $filedataoffset, 1)) & 0x0F);
		$ThisFileInfo['ogg']['blocksize_large']  = pow(2, (LittleEndian2Int(substr($filedata, $filedataoffset, 1)) & 0xF0) >> 4);
		$ThisFileInfo['ogg']['stop_bit']         = LittleEndian2Int(substr($filedata, $filedataoffset, 1)); // must be 1, marks end of packet

		$ThisFileInfo['audio']['bitrate_mode'] = 'vbr'; // overridden if actually abr
		if ($ThisFileInfo['ogg']['bitrate_max'] == 0xFFFFFFFF) {
			unset($ThisFileInfo['ogg']['bitrate_max']);
			$ThisFileInfo['audio']['bitrate_mode'] = 'abr';
		}
		if ($ThisFileInfo['ogg']['bitrate_nominal'] == 0xFFFFFFFF) {
			unset($ThisFileInfo['ogg']['bitrate_nominal']);
		}
		if ($ThisFileInfo['ogg']['bitrate_min'] == 0xFFFFFFFF) {
			unset($ThisFileInfo['ogg']['bitrate_min']);
			$ThisFileInfo['audio']['bitrate_mode'] = 'abr';
		}

    } elseif (substr($filedata, 0, 8) == 'Speex   ') {

		// http://www.speex.org/manual/node10.html

		$ThisFileInfo['audio']['dataformat']      = 'speex';
		$ThisFileInfo['mime_type']                = 'audio/speex';
		$ThisFileInfo['audio']['bitrate_mode']    = 'abr';

		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['speex_string']           =                  substr($filedata, $filedataoffset, 8); // hard-coded to 'Speex   '
		$filedataoffset += 8;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['speex_version']          =                  substr($filedata, $filedataoffset, 20);
		$filedataoffset += 20;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['speex_version_id']       = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['header_size']            = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['rate']                   = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['mode']                   = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['mode_bitstream_version'] = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['nb_channels']            = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['bitrate']                = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['framesize']              = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['vbr']                    = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['frames_per_packet']      = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['extra_headers']          = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['reserved1']              = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;
		$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['reserved2']              = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
		$filedataoffset += 4;

		$ThisFileInfo['speex']['speex_version'] = trim($ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['speex_version']);
		$ThisFileInfo['speex']['sample_rate']   = $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['rate'];
		$ThisFileInfo['speex']['channels']      = $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['nb_channels'];
		$ThisFileInfo['speex']['vbr']           = (bool) $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['vbr'];
		$ThisFileInfo['speex']['band_type']     = SpeexBandModeLookup($ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['mode']);

		$ThisFileInfo['audio']['sample_rate']   = $ThisFileInfo['speex']['sample_rate'];
		$ThisFileInfo['audio']['channels']      = $ThisFileInfo['speex']['channels'];
		if ($ThisFileInfo['speex']['vbr']) {
			$ThisFileInfo['audio']['bitrate_mode'] = 'vbr';
		}

    } else {

		$ThisFileInfo['error'] .= "\n".'Expecting either "Speex   " or "vorbis" identifier strings, found neither';
		unset($ThisFileInfo['ogg']);
		unset($ThisFileInfo['mime_type']);
		return false;

    }


    // Page 2 - Comment Header

    $oggpageinfo = ParseOggPageHeader($fd);
    $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']] = $oggpageinfo;

    switch ($ThisFileInfo['audio']['dataformat']) {

		case 'vorbis':
			$filedata = fread($fd, $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_length']);
			$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['packet_type'] = LittleEndian2Int(substr($filedata, 0, 1));
			$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['stream_type'] =                  substr($filedata, 1, 6); // hard-coded to 'vorbis'

			ParseVorbisCommentsFilepointer($fd, $ThisFileInfo);
			break;

		case 'flac':
			require_once(GETID3_INCLUDEPATH.'getid3.flac.php');
			if (!FLACparseMETAdata($fd, $ThisFileInfo)) {
				$ThisFileInfo['error'] .= "\n".'Failed to parse FLAC headers';
				return false;
			}
			break;

		case 'speex':
			fseek($fd, $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_length'], SEEK_CUR);
			ParseVorbisCommentsFilepointer($fd, $ThisFileInfo);
			break;

    }



    // Last Page - Number of Samples

    fseek($fd, max($ThisFileInfo['avdataend'] - FREAD_BUFFER_SIZE, 0), SEEK_SET);
    $LastChunkOfOgg = strrev(fread($fd, FREAD_BUFFER_SIZE));
    if ($LastOggSpostion = strpos($LastChunkOfOgg, 'SggO')) {
		fseek($fd, 0 - ($LastOggSpostion + strlen('SggO')), SEEK_END);
		$ThisFileInfo['avdataend'] = ftell($fd);
		$ThisFileInfo['ogg']['pageheader']['eos'] = ParseOggPageHeader($fd);
		$ThisFileInfo['ogg']['samples']   = $ThisFileInfo['ogg']['pageheader']['eos']['pcm_abs_position'];
		if ($ThisFileInfo['ogg']['samples'] == 0) {
			$ThisFileInfo['error'] .= "\n".'Corrupt Ogg file: eos.number of samples == zero';
			return false;
		}
		$ThisFileInfo['ogg']['bitrate_average'] = (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / ($ThisFileInfo['ogg']['samples'] / $ThisFileInfo['audio']['sample_rate']);
    }

    if (isset($ThisFileInfo['ogg']['bitrate_average']) && ($ThisFileInfo['ogg']['bitrate_average'] > 0)) {
		$ThisFileInfo['audio']['bitrate'] = $ThisFileInfo['ogg']['bitrate_average'];
    } elseif (isset($ThisFileInfo['ogg']['bitrate_nominal']) && ($ThisFileInfo['ogg']['bitrate_nominal'] > 0)) {
		$ThisFileInfo['audio']['bitrate'] = $ThisFileInfo['ogg']['bitrate_nominal'];
    } elseif (isset($ThisFileInfo['ogg']['bitrate_min']) && isset($ThisFileInfo['ogg']['bitrate_max'])) {
		$ThisFileInfo['audio']['bitrate'] = ($ThisFileInfo['ogg']['bitrate_min'] + $ThisFileInfo['ogg']['bitrate_max']) / 2;
    }
    if (isset($ThisFileInfo['audio']['bitrate']) && !isset($ThisFileInfo['playtime_seconds'])) {
		if ($ThisFileInfo['audio']['bitrate'] == 0) {
			$ThisFileInfo['error'] .= "\n".'Corrupt Ogg file: bitrate_audio == zero';
			return false;
		}
		$ThisFileInfo['playtime_seconds'] = (float) ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['audio']['bitrate']);
    }

    if (isset($ThisFileInfo['ogg']['vendor'])) {
		$ThisFileInfo['audio']['encoder'] = preg_replace('/^Encoded with /', '', $ThisFileInfo['ogg']['vendor']);
    }

    return true;
}


function ParseOggPageHeader(&$fd) {
    // http://xiph.org/ogg/vorbis/doc/framing.html
    $oggheader['page_start_offset'] = ftell($fd); // where we started from in the file

    $filedata = fread($fd, FREAD_BUFFER_SIZE);
    $filedataoffset = 0;
    while ((substr($filedata, $filedataoffset++, 4) != 'OggS')) {
		if ((ftell($fd) - $oggheader['page_start_offset']) >= FREAD_BUFFER_SIZE) {
			// should be found before here
			return false;
		}
		if (strlen($filedata) < 1024) {
			if (feof($fd) || (($filedata .= fread($fd, FREAD_BUFFER_SIZE)) === false)) {
				// get some more data, unless eof, in which case fail
				return false;
			}
		}
    }
    $filedataoffset += strlen('OggS') - 1; // page, delimited by 'OggS'

    $oggheader['stream_structver']  = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
    $filedataoffset += 1;
    $oggheader['flags_raw']         = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
    $filedataoffset += 1;
    $oggheader['flags']['fresh']    = (bool) ($oggheader['flags_raw'] & 0x01); // fresh packet
    $oggheader['flags']['bos']      = (bool) ($oggheader['flags_raw'] & 0x02); // first page of logical bitstream (bos)
    $oggheader['flags']['eos']      = (bool) ($oggheader['flags_raw'] & 0x04); // last page of logical bitstream (eos)

    $oggheader['pcm_abs_position']  = LittleEndian2Int(substr($filedata, $filedataoffset, 8));
    $filedataoffset += 8;
    $oggheader['stream_serialno']   = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
    $filedataoffset += 4;
    $oggheader['page_seqno']        = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
    $filedataoffset += 4;
    $oggheader['page_checksum']     = LittleEndian2Int(substr($filedata, $filedataoffset, 4));
    $filedataoffset += 4;
    $oggheader['page_segments']     = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
    $filedataoffset += 1;
    $oggheader['page_length'] = 0;
    for ($i = 0; $i < $oggheader['page_segments']; $i++) {
		$oggheader['segment_table'][$i] = LittleEndian2Int(substr($filedata, $filedataoffset, 1));
		$filedataoffset += 1;
		$oggheader['page_length'] += $oggheader['segment_table'][$i];
    }
    $oggheader['header_end_offset'] = $oggheader['page_start_offset'] + $filedataoffset;
    $oggheader['page_end_offset']   = $oggheader['header_end_offset'] + $oggheader['page_length'];
    fseek($fd, $oggheader['header_end_offset'], SEEK_SET);

    return $oggheader;
}


function ParseVorbisCommentsFilepointer(&$fd, &$ThisFileInfo) {

    $OriginalOffset = ftell($fd);
    $CommentStartOffset = $OriginalOffset;
    $commentdataoffset = 0;
    $VorbisCommentPage = 1;

    switch ($ThisFileInfo['audio']['dataformat']) {
		case 'vorbis':
			$CommentStartOffset = $ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage]['page_start_offset'];  // Second Ogg page, after header block
			fseek($fd, $CommentStartOffset, SEEK_SET);
			$commentdataoffset = 27 + $ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage]['page_segments'];
			$commentdata = fread($fd, OggPageSegmentLength($ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage], 1) + $commentdataoffset);

			$commentdataoffset += (strlen('vorbis') + 1);
			break;

		case 'flac':
			fseek($fd, $ThisFileInfo['flac']['VORBIS_COMMENT']['raw']['offset'] + 4, SEEK_SET);
			$commentdata = fread($fd, $ThisFileInfo['flac']['VORBIS_COMMENT']['raw']['block_length']);
			break;

		case 'speex':
			$CommentStartOffset = $ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage]['page_start_offset'];  // Second Ogg page, after header block
			fseek($fd, $CommentStartOffset, SEEK_SET);
			$commentdataoffset = 27 + $ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage]['page_segments'];
			$commentdata = fread($fd, OggPageSegmentLength($ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage], 1) + $commentdataoffset);
			break;

		default:
			return false;
			break;
    }

    $VendorSize = LittleEndian2Int(substr($commentdata, $commentdataoffset, 4));
    $commentdataoffset += 4;

    $ThisFileInfo['ogg']['vendor'] = substr($commentdata, $commentdataoffset, $VendorSize);
    $commentdataoffset += $VendorSize;

    $CommentsCount = LittleEndian2Int(substr($commentdata, $commentdataoffset, 4));
    $commentdataoffset += 4;
    $ThisFileInfo['avdataoffset'] = $CommentStartOffset + $commentdataoffset;

    $basicfields = array('TITLE', 'ARTIST', 'ALBUM', 'TRACKNUMBER', 'GENRE', 'DATE', 'DESCRIPTION', 'COMMENT');
    for ($i = 0; $i < $CommentsCount; $i++) {

		$ThisFileInfo['ogg']['comments_raw'][$i]['dataoffset'] = $CommentStartOffset + $commentdataoffset;

		if (ftell($fd) < ($ThisFileInfo['ogg']['comments_raw'][$i]['dataoffset'] + 4)) {
			$VorbisCommentPage++;

			$oggpageinfo = ParseOggPageHeader($fd);
			$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']] = $oggpageinfo;

			// First, save what we haven't read yet
			$AsYetUnusedData = substr($commentdata, $commentdataoffset);

			// Then take that data off the end
			$commentdata     = substr($commentdata, 0, $commentdataoffset);

			// Add [headerlength] bytes of dummy data for the Ogg Page Header, just to keep absolute offsets correct
			$commentdata .= str_repeat(chr(0), 27 + $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_segments']);
			$commentdataoffset += (27 + $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_segments']);

			// Finally, stick the unused data back on the end
			$commentdata .= $AsYetUnusedData;

			//$commentdata .= fread($fd, $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_length']);
			$commentdata .= fread($fd, OggPageSegmentLength($ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage], 1));

		}
		$ThisFileInfo['ogg']['comments_raw'][$i]['size']       = LittleEndian2Int(substr($commentdata, $commentdataoffset, 4));

		// replace avdataoffset with position just after the last vorbiscomment
		$ThisFileInfo['avdataoffset'] = $ThisFileInfo['ogg']['comments_raw'][$i]['dataoffset'] + $ThisFileInfo['ogg']['comments_raw'][$i]['size'] + 4;

		$commentdataoffset += 4;
		while ((strlen($commentdata) - $commentdataoffset) < $ThisFileInfo['ogg']['comments_raw'][$i]['size']) {
			if (($ThisFileInfo['ogg']['comments_raw'][$i]['size'] > $ThisFileInfo['avdataend']) || ($ThisFileInfo['ogg']['comments_raw'][$i]['size'] < 0)) {
				$ThisFileInfo['error'] .= "\n".'Invalid Ogg comment size (comment #'.$i.', claims to be '.number_format($ThisFileInfo['ogg']['comments_raw'][$i]['size']).' bytes) - aborting reading comments';
				break 2;
			}

			$VorbisCommentPage++;

			$oggpageinfo = ParseOggPageHeader($fd);
			$ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']] = $oggpageinfo;

			// First, save what we haven't read yet
			$AsYetUnusedData = substr($commentdata, $commentdataoffset);

			// Then take that data off the end
			$commentdata     = substr($commentdata, 0, $commentdataoffset);

			// Add [headerlength] bytes of dummy data for the Ogg Page Header, just to keep absolute offsets correct
			$commentdata .= str_repeat(chr(0), 27 + $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_segments']);
			$commentdataoffset += (27 + $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_segments']);

			// Finally, stick the unused data back on the end
			$commentdata .= $AsYetUnusedData;

			//$commentdata .= fread($fd, $ThisFileInfo['ogg']['pageheader'][$oggpageinfo['page_seqno']]['page_length']);
			$commentdata .= fread($fd, OggPageSegmentLength($ThisFileInfo['ogg']['pageheader'][$VorbisCommentPage], 1));

			//$filebaseoffset += $oggpageinfo['header_end_offset'] - $oggpageinfo['page_start_offset'];
		}
		$commentstring = substr($commentdata, $commentdataoffset, $ThisFileInfo['ogg']['comments_raw'][$i]['size']);
		$commentdataoffset += $ThisFileInfo['ogg']['comments_raw'][$i]['size'];

		if (!$commentstring) {

			// no comment?
			$ThisFileInfo['warning'] .= "\n".'Blank Ogg comment ['.$i.']';

		} elseif (strstr($commentstring, '=')) {

			$commentexploded = explode('=', $commentstring, 2);
			$ThisFileInfo['ogg']['comments_raw'][$i]['key']   = strtoupper($commentexploded[0]);
			$ThisFileInfo['ogg']['comments_raw'][$i]['value'] = ($commentexploded[1] ? utf8_decode($commentexploded[1]) : '');
			$ThisFileInfo['ogg']['comments_raw'][$i]['data']  = base64_decode($ThisFileInfo['ogg']['comments_raw'][$i]['value']);

			$ThisFileInfo['ogg']['comments'][strtolower($ThisFileInfo['ogg']['comments_raw'][$i]['key'])][] = $ThisFileInfo['ogg']['comments_raw'][$i]['value'];

			require_once(GETID3_INCLUDEPATH.'getid3.getimagesize.php');
			$imagechunkcheck = GetDataImageSize($ThisFileInfo['ogg']['comments_raw'][$i]['data']);
			$ThisFileInfo['ogg']['comments_raw'][$i]['image_mime'] = image_type_to_mime_type($imagechunkcheck[2]);
			if (!$ThisFileInfo['ogg']['comments_raw'][$i]['image_mime'] || ($ThisFileInfo['ogg']['comments_raw'][$i]['image_mime'] == 'application/octet-stream')) {
				unset($ThisFileInfo['ogg']['comments_raw'][$i]['image_mime']);
				unset($ThisFileInfo['ogg']['comments_raw'][$i]['data']);
			}

		} else {

			$ThisFileInfo['warning'] .= "\n".'[known problem with CDex >= v1.40, < v1.50b7] Invalid Ogg comment name/value pair ['.$i.']: '.$commentstring;

		}
    }


    // Check for presence of vorbiscomments
    if (isset($ThisFileInfo['ogg']['comments'])) {
		$ThisFileInfo['tags'][] = 'vorbiscomment';

		// Yank other comments - vorbiscomments has highest preference
		if (isset($ThisFileInfo['ogg']['comments'])) {
			CopyFormatCommentsToRootComments($ThisFileInfo['ogg']['comments'], $ThisFileInfo, true, true, true);
		}

    }


    // Replay Gain Adjustment
    // http://privatewww.essex.ac.uk/~djmrob/replaygain/
    if (isset($ThisFileInfo['ogg']['comments']) && is_array($ThisFileInfo['ogg']['comments'])) {
		foreach ($ThisFileInfo['ogg']['comments'] as $index => $keyvaluepair) {
			if (isset($keyvaluepair['key'])) {
				switch ($keyvaluepair['key']) {
					case 'RG_AUDIOPHILE':
					case 'REPLAYGAIN_ALBUM_GAIN':
						$ThisFileInfo['replay_gain']['audiophile']['adjustment'] = (double) $keyvaluepair['value'];
						break;

					case 'RG_RADIO':
					case 'REPLAYGAIN_TRACK_GAIN':
						$ThisFileInfo['replay_gain']['radio']['adjustment'] = (double) $keyvaluepair['value'];
						break;

					case 'REPLAYGAIN_ALBUM_PEAK':
						$ThisFileInfo['replay_gain']['audiophile']['peak'] = (double) $keyvaluepair['value'];
						break;

					case 'RG_PEAK':
					case 'REPLAYGAIN_TRACK_PEAK':
						$ThisFileInfo['replay_gain']['radio']['peak'] = (double) $keyvaluepair['value'];
						break;


					default:
						// do nothing
						break;
				}
			}
		}
    }

    fseek($fd, $OriginalOffset, SEEK_SET);

    return true;
}

function SpeexBandModeLookup($mode) {
    static $SpeexBandModeLookup = array();
    if (empty($SpeexBandModeLookup)) {
		$SpeexBandModeLookup[0] = 'narrow';
		$SpeexBandModeLookup[1] = 'wide';
		$SpeexBandModeLookup[2] = 'ultra-wide';
    }
    return (isset($SpeexBandModeLookup[$mode]) ? $SpeexBandModeLookup[$mode] : null);
}

function OggPageSegmentLength($OggInfoArray, $SegmentNumber=1) {
    for ($i = 0; $i < $SegmentNumber; $i++) {
		$segmentlength = 0;
		foreach ($OggInfoArray['segment_table'] as $key => $value) {
			$segmentlength += $value;
			if ($value < 255) {
				break;
			}
		}
    }
    return $segmentlength;
}

?>