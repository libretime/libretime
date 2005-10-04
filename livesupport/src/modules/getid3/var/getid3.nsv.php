<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.nsv.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getNSVHeaderFilepointer(&$fd, &$ThisFileInfo) {
    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $NSVheader = fread($fd, 4);

    switch ($NSVheader) {
		case 'NSVs':
			if (getNSVsHeaderFilepointer($fd, $ThisFileInfo, 0)) {
				$ThisFileInfo['fileformat']       = 'nsv';
				$ThisFileInfo['audio']['dataformat'] = 'nsv';
				$ThisFileInfo['video']['dataformat'] = 'nsv';
			}
			break;

		case 'NSVf':
			if (getNSVfHeaderFilepointer($fd, $ThisFileInfo, 0)) {
				$ThisFileInfo['fileformat'] = 'nsv';
				$ThisFileInfo['audio']['dataformat'] = 'nsv';
				$ThisFileInfo['video']['dataformat'] = 'nsv';
				getNSVsHeaderFilepointer($fd, $ThisFileInfo, $ThisFileInfo['nsv']['NSVf']['header_length']);
			}
			break;

		default:
			$ThisFileInfo['error'] .= "\n".'unknown NSV file header ('.$NSVheader.')';
			return false;
			break;
    }

    if (!isset($ThisFileInfo['nsv']['NSVf'])) {
		$ThisFileInfo['warning'] .= "\n".'NSVf header not present - cannot calculate playtime or bitrate';
    }

    return true;
}

function getNSVsHeaderFilepointer(&$fd, &$ThisFileInfo, $fileoffset) {
    fseek($fd, $fileoffset, SEEK_SET);
    $NSVsheader = fread($fd, 28);
    $offset = 0;

    $ThisFileInfo['nsv']['NSVs']['identifier']      =                  substr($NSVsheader, $offset, 4);
    $offset += 4;

    if ($ThisFileInfo['nsv']['NSVs']['identifier'] != 'NSVs') {
		$ThisFileInfo['error'] .= "\n".'expected "NSVs" at offset ('.$fileoffset.'), found "'.$ThisFileInfo['nsv']['NSVs']['identifier'].'" instead';
		unset($ThisFileInfo['nsv']['NSVs']);
		return false;
    }

    $ThisFileInfo['nsv']['NSVs']['offset']          = $fileoffset;

    $ThisFileInfo['nsv']['NSVs']['video_codec']     =                  substr($NSVsheader, $offset, 4);
    $offset += 4;
    $ThisFileInfo['nsv']['NSVs']['audio_codec']     =                  substr($NSVsheader, $offset, 4);
    $offset += 4;
    $ThisFileInfo['nsv']['NSVs']['resolution_x']    = LittleEndian2Int(substr($NSVsheader, $offset, 2));
    $offset += 2;
    $ThisFileInfo['nsv']['NSVs']['resolution_y']    = LittleEndian2Int(substr($NSVsheader, $offset, 2));
    $offset += 2;

    $ThisFileInfo['nsv']['NSVs']['framerate_index'] = LittleEndian2Int(substr($NSVsheader, $offset, 2));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown1b']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown1c']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown1d']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown2a']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown2b']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown2c']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['nsv']['NSVs']['unknown2d']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
    $offset += 1;

    switch ($ThisFileInfo['nsv']['NSVs']['audio_codec']) {
		case 'PCM ':
			$ThisFileInfo['nsv']['NSVs']['bits_channel']    = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			$ThisFileInfo['nsv']['NSVs']['channels']        = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			$ThisFileInfo['nsv']['NSVs']['sample_rate']     = LittleEndian2Int(substr($NSVsheader, $offset, 2));
			$offset += 2;

			$ThisFileInfo['audio']['sample_rate']                      = $ThisFileInfo['nsv']['NSVs']['sample_rate'];
			break;

		case 'MP3 ':
		case 'NONE':
		default:
			$ThisFileInfo['nsv']['NSVs']['unknown3a']       = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			$ThisFileInfo['nsv']['NSVs']['unknown3b']       = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			$ThisFileInfo['nsv']['NSVs']['unknown3c']       = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			$ThisFileInfo['nsv']['NSVs']['unknown3d']       = LittleEndian2Int(substr($NSVsheader, $offset, 1));
			$offset += 1;
			break;
    }

    $ThisFileInfo['video']['resolution_x']     = $ThisFileInfo['nsv']['NSVs']['resolution_x'];
    $ThisFileInfo['video']['resolution_y']     = $ThisFileInfo['nsv']['NSVs']['resolution_y'];
    $ThisFileInfo['nsv']['NSVs']['frame_rate'] = NSVframerateLookup($ThisFileInfo['nsv']['NSVs']['framerate_index']);
    $ThisFileInfo['video']['frame_rate']                = $ThisFileInfo['nsv']['NSVs']['frame_rate'];

    return true;
}

function getNSVfHeaderFilepointer(&$fd, &$ThisFileInfo, $fileoffset, $getTOCoffsets=false) {
    fseek($fd, $fileoffset, SEEK_SET);
    $NSVfheader = fread($fd, 28);
    $offset = 0;

    $ThisFileInfo['nsv']['NSVf']['identifier']    =                  substr($NSVfheader, $offset, 4);
    $offset += 4;

    if ($ThisFileInfo['nsv']['NSVf']['identifier'] != 'NSVf') {
		$ThisFileInfo['error'] .= "\n".'expected "NSVf" at offset ('.$fileoffset.'), found "'.$ThisFileInfo['nsv']['NSVf']['identifier'].'" instead';
		unset($ThisFileInfo['nsv']['NSVf']);
		return false;
    }

    $ThisFileInfo['nsv']['NSVs']['offset']        = $fileoffset;

    $ThisFileInfo['nsv']['NSVf']['header_length'] = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;
    $ThisFileInfo['nsv']['NSVf']['file_size']     = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;

    if ($ThisFileInfo['nsv']['NSVf']['file_size'] > $ThisFileInfo['avdataend']) {
		$ThisFileInfo['warning'] .= "\n".'truncated file - NSVf header indicates '.$ThisFileInfo['nsv']['NSVf']['file_size'].' bytes, file actually '.$ThisFileInfo['avdataend'].' bytes';
    }

    $ThisFileInfo['nsv']['NSVf']['playtime_ms']   = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;
    $ThisFileInfo['nsv']['NSVf']['meta_size']     = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;
    $ThisFileInfo['nsv']['NSVf']['TOC_entries_1'] = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;
    $ThisFileInfo['nsv']['NSVf']['TOC_entries_2'] = LittleEndian2Int(substr($NSVfheader, $offset, 4));
    $offset += 4;

    if ($ThisFileInfo['nsv']['NSVf']['playtime_ms'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt NSV file: NSVf.playtime_ms == zero';
		return false;
    }

    $NSVfheader .= fread($fd, $ThisFileInfo['nsv']['NSVf']['meta_size'] + (4 * $ThisFileInfo['nsv']['NSVf']['TOC_entries_1']) + (4 * $ThisFileInfo['nsv']['NSVf']['TOC_entries_2']));
    $NSVfheaderlength = strlen($NSVfheader);
    $ThisFileInfo['nsv']['NSVf']['metadata']      =                  substr($NSVfheader, $offset, $ThisFileInfo['nsv']['NSVf']['meta_size']);
    $offset += $ThisFileInfo['nsv']['NSVf']['meta_size'];

    if ($getTOCoffsets) {
		$TOCcounter = 0;
		while ($TOCcounter < $ThisFileInfo['nsv']['NSVf']['TOC_entries_1']) {
			if ($TOCcounter < $ThisFileInfo['nsv']['NSVf']['TOC_entries_1']) {
				$ThisFileInfo['nsv']['NSVf']['TOC_1'][$TOCcounter] = LittleEndian2Int(substr($NSVfheader, $offset, 4));
				$offset += 4;
				$TOCcounter++;
			}
		}
    }

    if (trim($ThisFileInfo['nsv']['NSVf']['metadata']) != '') {
		$CommentPairArray = explode('` ', $ThisFileInfo['nsv']['NSVf']['metadata']);
		foreach ($CommentPairArray as $CommentPair) {
			if (strstr($CommentPair, '=`')) {
				list($key, $value) = explode('=`', $CommentPair, 2);
				$ThisFileInfo['nsv']['comments'][strtolower($key)] = str_replace('`', '', $value);
			} elseif (strstr($CommentPair, '='.chr(1))) {
				list($key, $value) = explode('='.chr(1), $CommentPair, 2);
				$ThisFileInfo['nsv']['comments'][strtolower($key)] = str_replace(chr(1), '', $value);
			}
		}
    }
    // NSV tags have highest priority
    if (!empty($ThisFileInfo['nsv']['comments'])) {
		CopyFormatCommentsToRootComments($ThisFileInfo['nsv']['comments'], $ThisFileInfo, true, true, true);
    }
    // add tag to array of tags
    $ThisFileInfo['tags'][] = 'nsv';

    $ThisFileInfo['playtime_seconds'] = $ThisFileInfo['nsv']['NSVf']['playtime_ms'] / 1000;
    $ThisFileInfo['bitrate']          = ($ThisFileInfo['nsv']['NSVf']['file_size'] * 8) / $ThisFileInfo['playtime_seconds'];

    return true;
}


function NSVframerateLookup($framerateindex) {
    if ($framerateindex <= 127) {
		return (float) $framerateindex;
    }

    static $NSVframerateLookup = array();
    if (empty($NSVframerateLookup)) {
		$NSVframerateLookup[129] = (float) 29.970;
		$NSVframerateLookup[131] = (float) 23.976;
		$NSVframerateLookup[133] = (float) 14.985;
		$NSVframerateLookup[197] = (float) 59.940;
		$NSVframerateLookup[199] = (float) 47.952;
    }
    return (isset($NSVframerateLookup[$framerateindex]) ? $NSVframerateLookup[$framerateindex] : false);
}

?>