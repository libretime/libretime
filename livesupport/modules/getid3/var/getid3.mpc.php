<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.mpc.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getMPCHeaderFilepointer(&$fd, &$ThisFileInfo) {
    // http://www.uni-jena.de/~pfk/mpp/sv8/header.html

    $ThisFileInfo['fileformat']               = 'mpc';
    $ThisFileInfo['audio']['dataformat']      = 'mpc';
    $ThisFileInfo['audio']['bitrate_mode']    = 'vbr';
    $ThisFileInfo['audio']['channels']        = 2;  // the format appears to be hardcoded for stereo only

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);

    $ThisFileInfo['mpc']['header']['size']                   = 30;
    $MPCheaderData = fread($fd, $ThisFileInfo['mpc']['header']['size']);
    $offset = 0;

    $ThisFileInfo['mpc']['header']['raw']['preamble']        =                  substr($MPCheaderData, $offset, 3); // should be 'MP+'
    $offset += 3;
    $StreamVersionByte                                      = LittleEndian2Int(substr($MPCheaderData, $offset, 1));
    $offset += 1;
    $ThisFileInfo['mpc']['header']['stream_major_version']   = ($StreamVersionByte & 0x0F);
    $ThisFileInfo['mpc']['header']['stream_minor_version']   = ($StreamVersionByte & 0xF0) >> 4;
    $ThisFileInfo['mpc']['header']['frame_count']            = LittleEndian2Int(substr($MPCheaderData, $offset, 4));
    $offset += 4;

    switch ($ThisFileInfo['mpc']['header']['stream_major_version']) {
		case 7:
			//$ThisFileInfo['fileformat'] = 'SV7';
			break;

		default:
			$ThisFileInfo['error'] .= "\n".'Only MPEGplus/Musepack SV7 supported';
			return false;
    }

    $FlagsByte1                                             = LittleEndian2Int(substr($MPCheaderData, $offset, 4));
    $offset += 4;
    $ThisFileInfo['mpc']['header']['intensity_stereo']       = (bool) (($FlagsByte1 & 0x80000000) >> 31);
    $ThisFileInfo['mpc']['header']['mid_side_stereo']        = (bool) (($FlagsByte1 & 0x40000000) >> 30);
    $ThisFileInfo['mpc']['header']['max_subband']            = ($FlagsByte1 & 0x3F000000) >> 24;
    $ThisFileInfo['mpc']['header']['raw']['profile']         = ($FlagsByte1 & 0x00F00000) >> 20;
    $ThisFileInfo['mpc']['header']['begin_loud']             = (bool) (($FlagsByte1 & 0x00080000) >> 19);
    $ThisFileInfo['mpc']['header']['end_loud']               = (bool) (($FlagsByte1 & 0x00040000) >> 18);
    $ThisFileInfo['mpc']['header']['raw']['sample_rate']     = ($FlagsByte1 & 0x00030000) >> 16;
    $ThisFileInfo['mpc']['header']['max_level']              = ($FlagsByte1 & 0x0000FFFF);

    $ThisFileInfo['mpc']['header']['raw']['title_peak']      = LittleEndian2Int(substr($MPCheaderData, $offset, 2));
    $offset += 2;
    $ThisFileInfo['mpc']['header']['raw']['title_gain']      = LittleEndian2Int(substr($MPCheaderData, $offset, 2), true);
    $offset += 2;

    $ThisFileInfo['mpc']['header']['raw']['album_peak']      = LittleEndian2Int(substr($MPCheaderData, $offset, 2));
    $offset += 2;
    $ThisFileInfo['mpc']['header']['raw']['album_gain']      = LittleEndian2Int(substr($MPCheaderData, $offset, 2), true);
    $offset += 2;

    $FlagsByte2                                              = LittleEndian2Int(substr($MPCheaderData, $offset, 4));
    $offset += 4;
    $ThisFileInfo['mpc']['header']['true_gapless']           = (bool) (($FlagsByte2 & 0x80000000) >> 31);
    $ThisFileInfo['mpc']['header']['last_frame_length']      = ($FlagsByte2 & 0x7FF00000) >> 20;


    $offset += 3;  // unused?
    $ThisFileInfo['mpc']['header']['raw']['encoder_version'] = LittleEndian2Int(substr($MPCheaderData, $offset, 1));
    $offset += 1;

    $ThisFileInfo['mpc']['header']['profile']                = MPCprofileNameLookup($ThisFileInfo['mpc']['header']['raw']['profile']);
    $ThisFileInfo['mpc']['header']['sample_rate']            = MPCfrequencyLookup($ThisFileInfo['mpc']['header']['raw']['sample_rate']);
    if ($ThisFileInfo['mpc']['header']['sample_rate'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MPC file: frequency == zero';
		return false;
    }
    $ThisFileInfo['audio']['sample_rate']                    = $ThisFileInfo['mpc']['header']['sample_rate'];
    $ThisFileInfo['mpc']['header']['samples']                = ((($ThisFileInfo['mpc']['header']['frame_count'] - 1) * 1152) + $ThisFileInfo['mpc']['header']['last_frame_length']) * $ThisFileInfo['audio']['channels'];

    $ThisFileInfo['playtime_seconds']                        = ($ThisFileInfo['mpc']['header']['samples'] / $ThisFileInfo['audio']['channels']) / $ThisFileInfo['audio']['sample_rate'];
    if ($ThisFileInfo['playtime_seconds'] == 0) {
		$ThisFileInfo['error'] .= "\n".'Corrupt MPC file: playtime_seconds == zero';
		return false;
    }

    // add size of file header to avdataoffset - calc bitrate correctly + MD5 data
    $ThisFileInfo['avdataoffset'] += $ThisFileInfo['mpc']['header']['size'];

    $ThisFileInfo['audio']['bitrate'] = (($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['playtime_seconds'];

    $ThisFileInfo['mpc']['header']['title_peak']             = $ThisFileInfo['mpc']['header']['raw']['title_peak'];
    $ThisFileInfo['mpc']['header']['title_peak_db']          = MPCpeakDBLookup($ThisFileInfo['mpc']['header']['title_peak']);
    $ThisFileInfo['mpc']['header']['title_gain_db']          = $ThisFileInfo['mpc']['header']['raw']['title_gain'] / 100;
    $ThisFileInfo['mpc']['header']['album_peak']             = $ThisFileInfo['mpc']['header']['raw']['album_peak'];
    $ThisFileInfo['mpc']['header']['album_peak_db']          = MPCpeakDBLookup($ThisFileInfo['mpc']['header']['album_peak']);
    $ThisFileInfo['mpc']['header']['album_gain_db']          = $ThisFileInfo['mpc']['header']['raw']['album_gain'] / 100;;
    $ThisFileInfo['mpc']['header']['encoder_version']        = MPCencoderVersionLookup($ThisFileInfo['mpc']['header']['raw']['encoder_version']);

    if ($ThisFileInfo['mpc']['header']['title_peak_db']) {
		$ThisFileInfo['replay_gain']['radio']['peak']            = $ThisFileInfo['mpc']['header']['title_peak'];
		$ThisFileInfo['replay_gain']['radio']['adjustment']      = $ThisFileInfo['mpc']['header']['title_gain_db'];
    } else {
		$ThisFileInfo['replay_gain']['radio']['peak']            = CastAsInt(round($ThisFileInfo['mpc']['header']['max_level'] * 1.18)); // why? I don't know - see mppdec.c
		$ThisFileInfo['replay_gain']['radio']['adjustment']      = 0;
    }
    if ($ThisFileInfo['mpc']['header']['album_peak_db']) {
		$ThisFileInfo['replay_gain']['audiophile']['peak']       = $ThisFileInfo['mpc']['header']['album_peak'];
		$ThisFileInfo['replay_gain']['audiophile']['adjustment'] = $ThisFileInfo['mpc']['header']['album_gain_db'];
    }

    $ThisFileInfo['audio']['encoder'] = $ThisFileInfo['mpc']['header']['encoder_version'].', SV'.$ThisFileInfo['mpc']['header']['stream_major_version'].'.'.$ThisFileInfo['mpc']['header']['stream_minor_version'];

    return true;
}

function MPCprofileNameLookup($profileid) {
    static $MPCprofileNameLookup = array();
    if (empty($MPCprofileNameLookup)) {
		$MPCprofileNameLookup[0]  = 'no profile';
		$MPCprofileNameLookup[1]  = 'Experimental';
		$MPCprofileNameLookup[2]  = 'unused';
		$MPCprofileNameLookup[3]  = 'unused';
		$MPCprofileNameLookup[4]  = 'unused';
		$MPCprofileNameLookup[5]  = 'below Telephone (q = 0.0)';
		$MPCprofileNameLookup[6]  = 'below Telephone (q = 1.0)';
		$MPCprofileNameLookup[7]  = 'Telephone (q = 2.0)';
		$MPCprofileNameLookup[8]  = 'Thumb (q = 3.0)';
		$MPCprofileNameLookup[9]  = 'Radio (q = 4.0)';
		$MPCprofileNameLookup[10] = 'Standard (q = 5.0)';
		$MPCprofileNameLookup[11] = 'Extreme (q = 6.0)';
		$MPCprofileNameLookup[12] = 'Insane (q = 7.0)';
		$MPCprofileNameLookup[13] = 'BrainDead (q = 8.0)';
		$MPCprofileNameLookup[14] = 'above BrainDead (q = 9.0)';
		$MPCprofileNameLookup[15] = 'above BrainDead (q = 10.0)';
    }
    return (isset($MPCprofileNameLookup[$profileid]) ? $MPCprofileNameLookup[$profileid] : 'invalid');
}

function MPCfrequencyLookup($frequencyid) {
    static $MPCfrequencyLookup = array();
    if (empty($MPCfrequencyLookup)) {
		$MPCfrequencyLookup[0] = 44100;
		$MPCfrequencyLookup[1] = 48000;
		$MPCfrequencyLookup[2] = 37800;
		$MPCfrequencyLookup[3] = 32000;
    }
    return (isset($MPCfrequencyLookup[$frequencyid]) ? $MPCfrequencyLookup[$frequencyid] : 'invalid');
}

function MPCpeakDBLookup($intvalue) {
    if ($intvalue > 0) {
		return ((log10($intvalue) / log10(2)) - 15) * 6;
    }
    return false;
}

function MPCencoderVersionLookup($encoderversion) {
    //Encoder version * 100  (106 = 1.06)
    //EncoderVersion % 10 == 0        Release (1.0)
    //EncoderVersion %  2 == 0        Beta (1.06)
    //EncoderVersion %  2 == 1        Alpha (1.05a...z)

    if (($encoderversion % 10) == 0) {
		// release version
		return number_format($encoderversion / 100, 2);
    } elseif (($encoderversion % 2) == 0) {
		// beta version
		return number_format($encoderversion / 100, 2).' beta';
    } else {
		// alpha version
		return number_format($encoderversion / 100, 2).' alpha';
    }
}

?>