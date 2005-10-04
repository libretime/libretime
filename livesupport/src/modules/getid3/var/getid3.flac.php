<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.flac.php - part of getID3()                          //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getFLACHeaderFilepointer(&$fd, &$ThisFileInfo) {
    // http://flac.sourceforge.net/format.html

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $StreamMarker = fread($fd, 4);
    if ($StreamMarker != 'fLaC') {
		$ThisFileInfo['error'] .= "\n".'Invalid stream_marker - expected "fLaC", found "'.$StreamMarker.'"';;
		return false;
    }
    $ThisFileInfo['fileformat']            = 'flac';
    $ThisFileInfo['audio']['dataformat']   = 'flac';
    $ThisFileInfo['audio']['bitrate_mode'] = 'vbr';

    return FLACparseMETAdata($fd, $ThisFileInfo);
}


function FLACparseMETAdata(&$fd, &$ThisFileInfo) {

    do {
		$METAdataBlockOffset          = ftell($fd);
		$METAdataBlockHeader          = fread($fd, 4);
		$METAdataLastBlockFlag        = (bool) (BigEndian2Int(substr($METAdataBlockHeader, 0, 1)) & 0x80);
		$METAdataBlockType            = BigEndian2Int(substr($METAdataBlockHeader, 0, 1)) & 0x7F;
		$METAdataBlockLength          = BigEndian2Int(substr($METAdataBlockHeader, 1, 3));
		$METAdataBlockTypeText        = FLACmetaBlockTypeLookup($METAdataBlockType);

		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['offset']          = $METAdataBlockOffset;
		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['last_meta_block'] = $METAdataLastBlockFlag;
		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_type']      = $METAdataBlockType;
		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_type_text'] = $METAdataBlockTypeText;
		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_length']    = $METAdataBlockLength;
		$ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data']      = fread($fd, $METAdataBlockLength);
		$ThisFileInfo['avdataoffset'] = ftell($fd);

		switch ($METAdataBlockTypeText) {

			case 'STREAMINFO':
				if (!FLACparseSTREAMINFO($ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data'], $ThisFileInfo)) {
					return false;
				}
				break;

			case 'PADDING':
				// ignore
				break;

			case 'APPLICATION':
				if (!FLACparseAPPLICATION($ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data'], $ThisFileInfo)) {
					return false;
				}
				break;

			case 'SEEKTABLE':
				if (!FLACparseSEEKTABLE($ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data'], $ThisFileInfo)) {
					return false;
				}
				break;

			case 'VORBIS_COMMENT':
				require_once(GETID3_INCLUDEPATH.'getid3.ogg.php');
				//ParseVorbisComments($ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data'], $ThisFileInfo, $METAdataBlockOffset, $fd);

				$OldOffset = ftell($fd);
				fseek($fd, 0 - $METAdataBlockLength, SEEK_CUR);
				ParseVorbisCommentsFilepointer($fd, $ThisFileInfo);
				fseek($fd, $OldOffset, SEEK_SET);
				break;

			case 'CUESHEET':
				if (!FLACparseCUESHEET($ThisFileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data'], $ThisFileInfo)) {
					return false;
				}
				break;

			default:
				$ThisFileInfo['warning'] .= "\n".'Unhandled METADATA_BLOCK_HEADER.BLOCK_TYPE ('.$METAdataBlockType.') at offset '.$METAdataBlockOffset;
				break;
		}

    } while ($METAdataLastBlockFlag === false);


    if (isset($ThisFileInfo['flac']['STREAMINFO'])) {
		$ThisFileInfo['flac']['compressed_audio_bytes']   = $ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset'];
		$ThisFileInfo['flac']['uncompressed_audio_bytes'] = $ThisFileInfo['flac']['STREAMINFO']['samples_stream'] * $ThisFileInfo['flac']['STREAMINFO']['channels'] * ($ThisFileInfo['flac']['STREAMINFO']['bits_per_sample'] / 8);
		if ($ThisFileInfo['flac']['uncompressed_audio_bytes'] == 0) {
			$ThisFileInfo['error'] .= "\n".'Corrupt FLAC file: uncompressed_audio_bytes == zero';
			return false;
		}
		$ThisFileInfo['flac']['compression_ratio']        = $ThisFileInfo['flac']['compressed_audio_bytes'] / $ThisFileInfo['flac']['uncompressed_audio_bytes'];
    }

    // set md5_data - built into flac 0.5+
    if (isset($ThisFileInfo['flac']['STREAMINFO']['audio_signature'])) {

		if ($ThisFileInfo['flac']['STREAMINFO']['audio_signature'] === str_repeat(chr(0), 16)) {

			$ThisFileInfo['warning'] .= "\n".'FLAC STREAMINFO.audio_signature is null (known issue with libOggFLAC), using calculated md5_data';

		} else {

			$ThisFileInfo['md5_data'] = '';
			$md5 = $ThisFileInfo['flac']['STREAMINFO']['audio_signature'];
			for ($i = 0; $i < strlen($md5); $i++) {
				$ThisFileInfo['md5_data'] .= str_pad(dechex(ord($md5[$i])), 2, '00', STR_PAD_LEFT);
			}
			if (!preg_match('/^[0-9a-f]{32}$/', $ThisFileInfo['md5_data'])) {
				unset($ThisFileInfo['md5_data']);
			}

		}

    }

    $ThisFileInfo['audio']['bits_per_sample'] = $ThisFileInfo['flac']['STREAMINFO']['bits_per_sample'];
    if (!empty($ThisFileInfo['ogg']['vendor'])) {
		$ThisFileInfo['audio']['encoder'] = $ThisFileInfo['ogg']['vendor'];
    }

    return true;
}

function FLACmetaBlockTypeLookup($blocktype) {
    static $FLACmetaBlockTypeLookup = array();
    if (empty($FLACmetaBlockTypeLookup)) {
		$FLACmetaBlockTypeLookup[0] = 'STREAMINFO';
		$FLACmetaBlockTypeLookup[1] = 'PADDING';
		$FLACmetaBlockTypeLookup[2] = 'APPLICATION';
		$FLACmetaBlockTypeLookup[3] = 'SEEKTABLE';
		$FLACmetaBlockTypeLookup[4] = 'VORBIS_COMMENT';
		$FLACmetaBlockTypeLookup[5] = 'CUESHEET';
    }
    return (isset($FLACmetaBlockTypeLookup[$blocktype]) ? $FLACmetaBlockTypeLookup[$blocktype] : 'reserved');
}

function FLACapplicationIDLookup($applicationid) {
    static $FLACapplicationIDLookup = array();
    if (empty($FLACapplicationIDLookup)) {
		// http://flac.sourceforge.net/id.html
		$FLACapplicationIDLookup[0x46746F6C] = 'flac-tools';      // 'Ftol'
		$FLACapplicationIDLookup[0x46746F6C] = 'Sound Font FLAC'; // 'SFFL'
    }
    return (isset($FLACapplicationIDLookup[$applicationid]) ? $FLACapplicationIDLookup[$applicationid] : 'reserved');
}

function FLACparseSTREAMINFO($METAdataBlockData, &$ThisFileInfo) {
    $offset = 0;
    $ThisFileInfo['flac']['STREAMINFO']['min_block_size']  = BigEndian2Int(substr($METAdataBlockData, $offset, 2));
    $offset += 2;
    $ThisFileInfo['flac']['STREAMINFO']['max_block_size']  = BigEndian2Int(substr($METAdataBlockData, $offset, 2));
    $offset += 2;
    $ThisFileInfo['flac']['STREAMINFO']['min_frame_size']  = BigEndian2Int(substr($METAdataBlockData, $offset, 3));
    $offset += 3;
    $ThisFileInfo['flac']['STREAMINFO']['max_frame_size']  = BigEndian2Int(substr($METAdataBlockData, $offset, 3));
    $offset += 3;

    $SampleRateChannelsSampleBitsStreamSamples             = BigEndian2Bin(substr($METAdataBlockData, $offset, 8));
    $ThisFileInfo['flac']['STREAMINFO']['sample_rate']     = Bin2Dec(substr($SampleRateChannelsSampleBitsStreamSamples,  0, 20));
    $ThisFileInfo['flac']['STREAMINFO']['channels']        = Bin2Dec(substr($SampleRateChannelsSampleBitsStreamSamples, 20,  3)) + 1;
    $ThisFileInfo['flac']['STREAMINFO']['bits_per_sample'] = Bin2Dec(substr($SampleRateChannelsSampleBitsStreamSamples, 23,  5)) + 1;
    $ThisFileInfo['flac']['STREAMINFO']['samples_stream']  = Bin2Dec(substr($SampleRateChannelsSampleBitsStreamSamples, 28, 36));
    $offset += 8;

    $ThisFileInfo['flac']['STREAMINFO']['audio_signature'] =               substr($METAdataBlockData, $offset, 16);
    $offset += 16;

    if (!empty($ThisFileInfo['flac']['STREAMINFO']['sample_rate'])) {

		$ThisFileInfo['audio']['bitrate_mode']     = 'vbr';
		$ThisFileInfo['audio']['sample_rate']      = $ThisFileInfo['flac']['STREAMINFO']['sample_rate'];
		$ThisFileInfo['audio']['channels']         = $ThisFileInfo['flac']['STREAMINFO']['channels'];
		$ThisFileInfo['audio']['bits_per_sample']  = $ThisFileInfo['flac']['STREAMINFO']['bits_per_sample'];
		$ThisFileInfo['playtime_seconds']          = $ThisFileInfo['flac']['STREAMINFO']['samples_stream'] / $ThisFileInfo['flac']['STREAMINFO']['sample_rate'];
		$ThisFileInfo['audio']['bitrate']          = (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['playtime_seconds'];

    } else {

		$ThisFileInfo['error'] .= "\n".'Corrupt METAdata block: STREAMINFO';
		return false;

    }
    return true;
}


function FLACparseAPPLICATION($METAdataBlockData, &$ThisFileInfo) {
    $offset = 0;
	$ApplicationID = BigEndian2Int(substr($METAdataBlockData, $offset, 4));
	$offset += 4;
	$ThisFileInfo['flac']['APPLICATION'][$ApplicationID]['name'] = FLACapplicationIDLookup($ApplicationID);
	$ThisFileInfo['flac']['APPLICATION'][$ApplicationID]['data'] = substr($METAdataBlockData, $offset);
	$offset = $METAdataBlockLength;

	return true;
}


function FLACparseSEEKTABLE($METAdataBlockData, &$ThisFileInfo) {
    $offset = 0;
    $METAdataBlockLength = strlen($METAdataBlockData);
    $placeholderpattern = str_repeat(chr(0xFF), 8);
	while ($offset < $METAdataBlockLength) {
		$SampleNumberString = substr($METAdataBlockData, $offset, 8);
		$offset += 8;
		if ($SampleNumberString == $placeholderpattern) {

			// placeholder point
			safe_inc($ThisFileInfo['flac']['SEEKTABLE']['placeholders']);
			$offset += 10;

		} else {

			$SampleNumber                                                = BigEndian2Int($SampleNumberString);
			$ThisFileInfo['flac']['SEEKTABLE'][$SampleNumber]['offset']  = BigEndian2Int(substr($METAdataBlockData, $offset, 8));
			$offset += 8;
			$ThisFileInfo['flac']['SEEKTABLE'][$SampleNumber]['samples'] = BigEndian2Int(substr($METAdataBlockData, $offset, 2));
			$offset += 2;

		}
	}
	return true;
}

function FLACparseCUESHEET($METAdataBlockData, &$ThisFileInfo) {
    $offset = 0;
	$ThisFileInfo['flac']['CUESHEET']['media_catalog_number'] =          trim(substr($METAdataBlockData, $offset, 128), "\0");
	$offset += 128;
	$ThisFileInfo['flac']['CUESHEET']['lead_in_samples']      = BigEndian2Int(substr($METAdataBlockData, $offset, 8));
	$offset += 8;
	$ThisFileInfo['flac']['CUESHEET']['flags']['is_cd']       = (bool) (BigEndian2Int(substr($METAdataBlockData, $offset, 1)) & 0x80);
	$offset += 1;

	$offset += 258; // reserved

	$ThisFileInfo['flac']['CUESHEET']['number_tracks']        = BigEndian2Int(substr($METAdataBlockData, $offset, 1));
	$offset += 1;

	for ($track = 0; $track < $ThisFileInfo['flac']['CUESHEET']['number_tracks']; $track++) {
		$TrackSampleOffset = BigEndian2Int(substr($METAdataBlockData, $offset, 8));
		$offset += 8;
		$TrackNumber       = BigEndian2Int(substr($METAdataBlockData, $offset, 1));
		$offset += 1;

		$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['sample_offset']         = $TrackSampleOffset;

		$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['isrc']                  =               substr($METAdataBlockData, $offset, 12);
		$offset += 12;

		$TrackFlagsRaw                                                                     = BigEndian2Int(substr($METAdataBlockData, $offset, 1));
		$offset += 1;
		$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['flags']['is_audio']     = (bool) ($TrackFlagsRaw & 0x80);
		$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['flags']['pre_emphasis'] = (bool) ($TrackFlagsRaw & 0x40);

		$offset += 13; // reserved

		$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['index_points']          = BigEndian2Int(substr($METAdataBlockData, $offset, 1));
		$offset += 1;

		for ($index = 0; $index < $ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['index_points']; $index++) {
			$IndexSampleOffset = BigEndian2Int(substr($METAdataBlockData, $offset, 8));
			$offset += 8;
			$IndexNumber       = BigEndian2Int(substr($METAdataBlockData, $offset, 8));
			$offset += 1;

			$offset += 3; // reserved

			$ThisFileInfo['flac']['CUESHEET']['tracks'][$TrackNumber]['indexes'][$IndexNumber] = $IndexSampleOffset;
		}
	}
	return true;
}

?>