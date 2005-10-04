<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.monkey.php - part of getID3()                        //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getMonkeysAudioHeaderFilepointer(&$fd, &$ThisFileInfo) {
    // based loosely on code from TMonkey by Jurgen Faul
    // jfaul@gmx.de     http://jfaul.de/atl

    $ThisFileInfo['fileformat']            = 'mac';
    $ThisFileInfo['audio']['dataformat']   = 'mac';
    $ThisFileInfo['audio']['bitrate_mode'] = 'vbr';

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $MACheaderData = fread($fd, 40);

    $ThisFileInfo['monkeys_audio']['raw']['header_tag']           =                  substr($MACheaderData, 0, 4);
    $ThisFileInfo['monkeys_audio']['raw']['nVersion']             = LittleEndian2Int(substr($MACheaderData, 4, 2));
    $ThisFileInfo['monkeys_audio']['raw']['nCompressionLevel']    = LittleEndian2Int(substr($MACheaderData, 6, 2));
    $ThisFileInfo['monkeys_audio']['raw']['nFormatFlags']         = LittleEndian2Int(substr($MACheaderData, 8, 2));
    $ThisFileInfo['monkeys_audio']['raw']['nChannels']            = LittleEndian2Int(substr($MACheaderData, 10, 2));
    $ThisFileInfo['monkeys_audio']['raw']['nSampleRate']          = LittleEndian2Int(substr($MACheaderData, 12, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nWAVHeaderBytes']      = LittleEndian2Int(substr($MACheaderData, 16, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nWAVTerminatingBytes'] = LittleEndian2Int(substr($MACheaderData, 20, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nTotalFrames']         = LittleEndian2Int(substr($MACheaderData, 24, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nFinalFrameSamples']   = LittleEndian2Int(substr($MACheaderData, 28, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nPeakLevel']           = LittleEndian2Int(substr($MACheaderData, 32, 4));
    $ThisFileInfo['monkeys_audio']['raw']['nSeekElements']        = LittleEndian2Int(substr($MACheaderData, 38, 2));

    $ThisFileInfo['monkeys_audio']['flags']['8-bit']         = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0001);
    $ThisFileInfo['monkeys_audio']['flags']['crc-32']        = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0002);
    $ThisFileInfo['monkeys_audio']['flags']['peak_level']    = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0004);
    $ThisFileInfo['monkeys_audio']['flags']['24-bit']        = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0008);
    $ThisFileInfo['monkeys_audio']['flags']['seek_elements'] = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0010);
    $ThisFileInfo['monkeys_audio']['flags']['no_wav_header'] = (bool) ($ThisFileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0020);
    $ThisFileInfo['monkeys_audio']['version']                = $ThisFileInfo['monkeys_audio']['raw']['nVersion'] / 1000;
    $ThisFileInfo['monkeys_audio']['compression']            = MonkeyCompressionLevelNameLookup($ThisFileInfo['monkeys_audio']['raw']['nCompressionLevel']);
    $ThisFileInfo['monkeys_audio']['samples_per_frame']      = MonkeySamplesPerFrame($ThisFileInfo['monkeys_audio']['raw']['nVersion'], $ThisFileInfo['monkeys_audio']['raw']['nCompressionLevel']);
    $ThisFileInfo['monkeys_audio']['bits_per_sample']        = ($ThisFileInfo['monkeys_audio']['flags']['24-bit'] ? 24 : ($ThisFileInfo['monkeys_audio']['flags']['8-bit'] ? 8 : 16));
    if ($ThisFileInfo['monkeys_audio']['bits_per_sample'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MAC file: bits_per_sample == zero';
		return false;
    }
    $ThisFileInfo['monkeys_audio']['channels']               = $ThisFileInfo['monkeys_audio']['raw']['nChannels'];
    $ThisFileInfo['audio']['channels']                       = $ThisFileInfo['monkeys_audio']['channels'];
    $ThisFileInfo['monkeys_audio']['sample_rate']            = $ThisFileInfo['monkeys_audio']['raw']['nSampleRate'];
    if ($ThisFileInfo['monkeys_audio']['sample_rate'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MAC file: frequency == zero';
		return false;
    }
    $ThisFileInfo['audio']['sample_rate']                    = $ThisFileInfo['monkeys_audio']['sample_rate'];
    $ThisFileInfo['monkeys_audio']['peak_level']             = $ThisFileInfo['monkeys_audio']['raw']['nPeakLevel'];
    $ThisFileInfo['monkeys_audio']['peak_ratio']             = $ThisFileInfo['monkeys_audio']['peak_level'] / pow(2, $ThisFileInfo['monkeys_audio']['bits_per_sample'] - 1);
    $ThisFileInfo['monkeys_audio']['frames']                 = $ThisFileInfo['monkeys_audio']['raw']['nTotalFrames'];
    $ThisFileInfo['monkeys_audio']['samples']                = (($ThisFileInfo['monkeys_audio']['frames'] - 1) * $ThisFileInfo['monkeys_audio']['samples_per_frame']) + $ThisFileInfo['monkeys_audio']['raw']['nFinalFrameSamples'];
    $ThisFileInfo['monkeys_audio']['playtime']               = $ThisFileInfo['monkeys_audio']['samples'] / $ThisFileInfo['monkeys_audio']['sample_rate'];
    if ($ThisFileInfo['monkeys_audio']['playtime'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MAC file: playtime == zero';
		return false;
    }
    $ThisFileInfo['playtime_seconds']                        = $ThisFileInfo['monkeys_audio']['playtime'];
    $ThisFileInfo['monkeys_audio']['compressed_size']        = $ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset'];
    $ThisFileInfo['monkeys_audio']['uncompressed_size']      = $ThisFileInfo['monkeys_audio']['samples'] * $ThisFileInfo['monkeys_audio']['channels'] * ($ThisFileInfo['monkeys_audio']['bits_per_sample'] / 8);
    if ($ThisFileInfo['monkeys_audio']['uncompressed_size'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MAC file: uncompressed_size == zero';
		return false;
    }
    $ThisFileInfo['monkeys_audio']['compression_ratio']      = $ThisFileInfo['monkeys_audio']['compressed_size'] / ($ThisFileInfo['monkeys_audio']['uncompressed_size'] + $ThisFileInfo['monkeys_audio']['raw']['nWAVHeaderBytes']);
    $ThisFileInfo['monkeys_audio']['bitrate']                = (($ThisFileInfo['monkeys_audio']['samples'] * $ThisFileInfo['monkeys_audio']['channels'] * $ThisFileInfo['monkeys_audio']['bits_per_sample']) / $ThisFileInfo['monkeys_audio']['playtime']) * $ThisFileInfo['monkeys_audio']['compression_ratio'];
    $ThisFileInfo['audio']['bitrate']                           = $ThisFileInfo['monkeys_audio']['bitrate'];

    // add size of MAC header to avdataoffset - MD5data
    $ThisFileInfo['avdataoffset'] += 40;

    $ThisFileInfo['audio']['bits_per_sample'] = $ThisFileInfo['monkeys_audio']['bits_per_sample'];
    $ThisFileInfo['audio']['encoder']         = 'MAC v'.number_format($ThisFileInfo['monkeys_audio']['version'], 2);

    return true;
}

function MonkeyCompressionLevelNameLookup($compressionlevel) {
    static $MonkeyCompressionLevelNameLookup = array();
    if (empty($MonkeyCompressionLevelNameLookup)) {
		$MonkeyCompressionLevelNameLookup[0]     = 'unknown';
		$MonkeyCompressionLevelNameLookup[1000]  = 'fast';
		$MonkeyCompressionLevelNameLookup[2000]  = 'normal';
		$MonkeyCompressionLevelNameLookup[3000]  = 'high';
		$MonkeyCompressionLevelNameLookup[4000]  = 'extra-high';
		$MonkeyCompressionLevelNameLookup[5000]  = 'insane'; // thanks Allan Hansen <ah@artemis.dk> (October 6, 2002)
    }
    return (isset($MonkeyCompressionLevelNameLookup[$compressionlevel]) ? $MonkeyCompressionLevelNameLookup[$compressionlevel] : 'invalid');
}

function MonkeySamplesPerFrame($versionid, $compressionlevel) {
    if ($versionid >= 3950) {
		return 73728 * 4;
    } elseif ($versionid >= 3900) {
		return 73728;
    } elseif (($versionid >= 3800) && ($compressionlevel == 4000)) {
		return 73728;
    } else {
		return 9216;
    }
}

?>