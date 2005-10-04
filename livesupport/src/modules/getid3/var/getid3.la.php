<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.la.php - part of getID3()                            //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getLAHeaderFilepointer(&$fd, &$ThisFileInfo) {
    $offset = 0;
    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $rawdata = fread($fd, FREAD_BUFFER_SIZE);

    switch (substr($rawdata, $offset, 4)) {
		case 'LA02':
		case 'LA03':
			$ThisFileInfo['fileformat']              = 'la';
			$ThisFileInfo['audio']['dataformat']        = 'la';
			$ThisFileInfo['la']['version_major']     = (int) substr($rawdata, $offset + 2, 1);
			$ThisFileInfo['la']['version_minor']     = (int) substr($rawdata, $offset + 3, 1);
			$ThisFileInfo['la']['version']           = (float) $ThisFileInfo['la']['version_major'] + ($ThisFileInfo['la']['version_minor'] / 10);
			$offset += 4;

			$ThisFileInfo['la']['uncompressed_size'] = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;
			if ($ThisFileInfo['la']['uncompressed_size'] == 0) {
				$ThisFileInfo['error'] .= "\n".'Corrupt LA file: uncompressed_size == zero';
				return false;
			}

			$WAVEchunk = substr($rawdata, $offset, 4);
			if ($WAVEchunk !== 'WAVE') {
				$ThisFileInfo['error'] .= "\n".'Expected "WAVE" ('.PrintHexBytes('WAVE').') at offset '.$offset.', found "'.$WAVEchunk.'" ('.PrintHexBytes($WAVEchunk).') instead.';
				return false;
			}
			$offset += 4;

			$ThisFileInfo['la']['format_size']       = 24;
			if ($ThisFileInfo['la']['version'] > 0.2) {

				$ThisFileInfo['la']['format_size']   = LittleEndian2Int(substr($rawdata, $offset, 4));
				$ThisFileInfo['la']['header_size']   = 49 + $ThisFileInfo['la']['format_size'] - 24;
				$offset += 4;

			} else {

				// version two didn't support additional data blocks
				$ThisFileInfo['la']['header_size']   = 41;

			}

			$fmt_chunk = substr($rawdata, $offset, 4);
			if ($fmt_chunk !== 'fmt ') {
				$ThisFileInfo['error'] .= "\n".'Expected "fmt " ('.PrintHexBytes('fmt ').') at offset '.$offset.', found "'.$fmt_chunk.'" ('.PrintHexBytes($fmt_chunk).') instead.';
				return false;
			}
			$offset += 4;
			$fmt_size = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;

			$ThisFileInfo['la']['format_raw']        = LittleEndian2Int(substr($rawdata, $offset, 2));
			$offset += 2;

			$ThisFileInfo['la']['channels']          = LittleEndian2Int(substr($rawdata, $offset, 2));
			$offset += 2;
			if ($ThisFileInfo['la']['channels'] == 0) {
				$ThisFileInfo['error'] .= "\n".'Corrupt LA file: channels == zero';
					return false;
			}

			$ThisFileInfo['la']['sample_rate']       = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;
			if ($ThisFileInfo['la']['sample_rate'] == 0) {
				$ThisFileInfo['error'] .= "\n".'Corrupt LA file: sample_rate == zero';
					return false;
			}

			$ThisFileInfo['la']['bytes_per_second']  = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;
			$ThisFileInfo['la']['bytes_per_sample']  = LittleEndian2Int(substr($rawdata, $offset, 2));
			$offset += 2;
			$ThisFileInfo['la']['bits_per_sample']   = LittleEndian2Int(substr($rawdata, $offset, 2));
			$offset += 2;

			$ThisFileInfo['la']['samples']           = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;

			$ThisFileInfo['la']['seekable']          = (bool) LittleEndian2Int(substr($rawdata, $offset, 1));
			$offset += 1;

			$ThisFileInfo['la']['original_crc']      = LittleEndian2Int(substr($rawdata, $offset, 4));
			$offset += 4;

			require_once(GETID3_INCLUDEPATH.'getid3.riff.php');
			$ThisFileInfo['la']['codec']             = RIFFwFormatTagLookup($ThisFileInfo['la']['format_raw']);
			$ThisFileInfo['la']['compression_ratio'] = (float) (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) / $ThisFileInfo['la']['uncompressed_size']);
			$ThisFileInfo['playtime_seconds']        = (float) ($ThisFileInfo['la']['samples'] / $ThisFileInfo['la']['sample_rate']) / $ThisFileInfo['la']['channels'];
			if ($ThisFileInfo['playtime_seconds'] == 0) {
				$ThisFileInfo['error'] .= "\n".'Corrupt LA file: playtime_seconds == zero';
				return false;
			}

			// add size of file header to avdataoffset - calc bitrate correctly + MD5 data
			$ThisFileInfo['avdataoffset'] += $ThisFileInfo['la']['header_size'];

			$ThisFileInfo['audio']['bitrate']         = ($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8 / $ThisFileInfo['playtime_seconds'];
			$ThisFileInfo['audio']['codec']           = $ThisFileInfo['la']['codec'];
			$ThisFileInfo['audio']['bits_per_sample'] = $ThisFileInfo['la']['bits_per_sample'];
			break;

		default:
			if (substr($rawdata, $offset, 2) == 'LA') {
				$ThisFileInfo['error'] .= "\n".'This version of getID3() (v'.GETID3VERSION.') doesn\'t support LA version '.substr($rawdata, $offset + 2, 1).'.'.substr($rawdata, $offset + 3, 1).' which this appears to be - check http://getid3.sourceforge.net for updates.';
			} else {
				$ThisFileInfo['error'] .= "\n".'Not a LA (Lossless-Audio) file';
			}
			return false;
			break;
    }

    $ThisFileInfo['audio']['channels']    = $ThisFileInfo['la']['channels'];
    $ThisFileInfo['audio']['sample_rate'] = (int) $ThisFileInfo['la']['sample_rate'];
    $ThisFileInfo['audio']['encoder']     = (string) $ThisFileInfo['la']['version'];

    return true;
}

?>