<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.vqf.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getVQFHeaderFilepointer(&$fd, &$ThisFileInfo) {
    // based loosely on code from TTwinVQ by Jurgen Faul
    // jfaul@gmx.de     http://jfaul.de/atl

    $ThisFileInfo['fileformat']               = 'vqf';
    $ThisFileInfo['audio']['dataformat']      = 'vqf';
    $ThisFileInfo['audio']['bitrate_mode']    = 'cbr';
    $HasVQFTags = false;

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $VQFheaderData = fread($fd, 16);

    $offset = 0;
    $ThisFileInfo['vqf']['raw']['header_tag']     =               substr($VQFheaderData, $offset, 4);
    $offset += 4;
    $ThisFileInfo['vqf']['raw']['version']        =               substr($VQFheaderData, $offset, 8);
    $offset += 8;
    $ThisFileInfo['vqf']['raw']['size']           = BigEndian2Int(substr($VQFheaderData, $offset, 4));
    $offset += 4;

    while (ftell($fd) < $ThisFileInfo['avdataend']) {

		$ChunkBaseOffset = ftell($fd);
		$chunkoffset = 0;
		$ChunkData = fread($fd, 8);
		$ChunkName = substr($ChunkData, $chunkoffset, 4);
		if ($ChunkName == 'DATA') {
			$ThisFileInfo['avdataoffset'] = $ChunkBaseOffset;
			break;
		}
		$chunkoffset += 4;
		$ChunkSize = BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
		$chunkoffset += 4;
		if ($ChunkSize > ($ThisFileInfo['avdataend'] - ftell($fd))) {
			$ThisFileInfo['error'] .= "\n".'Invalid chunk size ('.$ChunkSize.') for chunk "'.$ChunkName.'" at offset '.$ChunkBaseOffset;
			break;
		}
		$ChunkData .= fread($fd, $ChunkSize);

		switch ($ChunkName) {
			case 'COMM':
				$ThisFileInfo['vqf']["$ChunkName"]['channel_mode']   = BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
				$chunkoffset += 4;
				$ThisFileInfo['vqf']["$ChunkName"]['bitrate']        = BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
				$chunkoffset += 4;
				$ThisFileInfo['vqf']["$ChunkName"]['sample_rate']    = BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
				$chunkoffset += 4;
				$ThisFileInfo['vqf']["$ChunkName"]['security_level'] = BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
				$chunkoffset += 4;

				$ThisFileInfo['audio']['channels']                            = $ThisFileInfo['vqf']["$ChunkName"]['channel_mode'] + 1;
				$ThisFileInfo['audio']['sample_rate']                           = VQFchannelFrequencyLookup($ThisFileInfo['vqf']["$ChunkName"]['sample_rate']);
				$ThisFileInfo['audio']['bitrate']                       = $ThisFileInfo['vqf']["$ChunkName"]['bitrate'] * 1000;

				if ($ThisFileInfo['audio']['bitrate'] == 0) {
					$ThisFileInfo['error'] .= 'Corrupt VQF file: bitrate_audio == zero';
						return false;
				}
				break;

			case 'NAME':
			case 'AUTH':
			case '(c) ':
			case 'FILE':
			case 'COMT':
			case 'ALBM':
				$HasVQFTags = true;
				$ThisFileInfo['vqf']['comments'][VQFcommentNiceNameLookup($ChunkName)][] = trim(substr($ChunkData, 8));
				break;

			case 'DSIZ':
				$ThisFileInfo['vqf']['DSIZ'] = BigEndian2Int(substr($ChunkData, 8, 4));
				break;

			default:
				$ThisFileInfo['warning'] .= "\n".'Unhandled chunk type "'.$ChunkName.'" at offset '.$ChunkBaseOffset;
				break;
		}
    }

    $ThisFileInfo['playtime_seconds'] = (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['audio']['bitrate'];

	if (isset($ThisFileInfo['vqf']['DSIZ']) && (($ThisFileInfo['vqf']['DSIZ'] != ($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset'] - strlen('DATA'))))) {
		switch ($ThisFileInfo['vqf']['DSIZ']) {
			case 0:
			case 1:
				$ThisFileInfo['warning'] .= "\n".'Invalid DSIZ value "'.$ThisFileInfo['vqf']['DSIZ'].'". This is known to happen with VQF files encoded by Ahead Nero, and seems to be its way of saying this is TwinVQF v'.($ThisFileInfo['vqf']['DSIZ'] + 1).'.0';
				$ThisFileInfo['audio']['encoder'] = 'Ahead Nero';
				break;

			default:
				$ThisFileInfo['warning'] .= "\n".'Probable corrupted file - should be '.$ThisFileInfo['vqf']['DSIZ'].' bytes, actually '.($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset'] - strlen('DATA'));
				break;
		}
	}

    // Any VQF tags present?
    if ($HasVQFTags) {

		// add tag to array of tags
		$ThisFileInfo['tags'][] = 'vqf';

		// Yank other comments - VQF highest preference
		CopyFormatCommentsToRootComments($ThisFileInfo['vqf']['comments'], $ThisFileInfo, true, true, true);
    }

    return true;
}

function VQFchannelFrequencyLookup($frequencyid) {
    static $VQFchannelFrequencyLookup = array();
    if (empty($VQFchannelFrequencyLookup)) {
		$VQFchannelFrequencyLookup[11] = 11025;
		$VQFchannelFrequencyLookup[22] = 22050;
		$VQFchannelFrequencyLookup[44] = 44100;
    }
    return (isset($VQFchannelFrequencyLookup[$frequencyid]) ? $VQFchannelFrequencyLookup[$frequencyid] : $frequencyid * 1000);
}

function VQFcommentNiceNameLookup($shortname) {
    static $VQFcommentNiceNameLookup = array();
    if (empty($VQFcommentNiceNameLookup)) {
		$VQFcommentNiceNameLookup['NAME'] = 'title';
		$VQFcommentNiceNameLookup['AUTH'] = 'artist';
		$VQFcommentNiceNameLookup['(c) '] = 'copyright';
		$VQFcommentNiceNameLookup['FILE'] = 'filename';
		$VQFcommentNiceNameLookup['COMT'] = 'comment';
		$VQFcommentNiceNameLookup['ALBM'] = 'album';
    }
    return (isset($VQFcommentNiceNameLookup["$shortname"]) ? $VQFcommentNiceNameLookup["$shortname"] : $shortname);
}

?>