<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.iso.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getISOHeaderFilepointer($fd, &$ThisFileInfo) {
    $ThisFileInfo['fileformat'] = 'iso';

    for ($i = 16; $i <= 19; $i++) {
		fseek($fd, 2048 * $i, SEEK_SET);
		$ISOheader = fread($fd, 2048);
		if (substr($ISOheader, 1, 5) == 'CD001') {
			switch (ord($ISOheader{0})) {
				case 1:
					$ThisFileInfo['iso']['primary_volume_descriptor']['offset'] = 2048 * $i;
					ParsePrimaryVolumeDescriptor($ISOheader, $ThisFileInfo);
					break;

				case 2:
					$ThisFileInfo['iso']['supplementary_volume_descriptor']['offset'] = 2048 * $i;
					ParseSupplementaryVolumeDescriptor($ISOheader, $ThisFileInfo);
					break;

				default:
					// skip
					break;
			}
		}
    }

    ParsePathTable($fd, $ThisFileInfo);

    $ThisFileInfo['iso']['files'] = array();
    foreach ($ThisFileInfo['iso']['path_table']['directories'] as $directorynum => $directorydata) {

		$ThisFileInfo['iso']['directories'][$directorynum] = ParseDirectoryRecord($fd, $directorydata, $ThisFileInfo);

    }

    return true;

}


function ParsePrimaryVolumeDescriptor(&$ISOheader, &$ThisFileInfo) {
    // ISO integer values are stored *BOTH* Little-Endian AND Big-Endian format!!
    // ie 12345 == 0x3039  is stored as $39 $30 $30 $39 in a 4-byte field

    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_descriptor_type']         = LittleEndian2Int(substr($ISOheader,    0, 1));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['standard_identifier']            =                  substr($ISOheader,    1, 5);
    if ($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['standard_identifier'] != 'CD001') {
		$ThisFileInfo['error'] .= "\n".'Expected "CD001" at offset ('.($ThisFileInfo['iso']['primary_volume_descriptor']['offset'] + 1).'), found "'.$ThisFileInfo['iso']['primary_volume_descriptor']['raw']['standard_identifier'].'" instead';
		return false;
    }


    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_descriptor_version']      = LittleEndian2Int(substr($ISOheader,    6, 1));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['unused_1']                       =                  substr($ISOheader,    7, 1);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['system_identifier']              =                  substr($ISOheader,    8, 32);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_identifier']              =                  substr($ISOheader,   40, 32);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['unused_2']                       =                  substr($ISOheader,   72, 8);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_space_size']              = LittleEndian2Int(substr($ISOheader,   80, 4));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['unused_3']                       =                  substr($ISOheader,   88, 32);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_set_size']                = LittleEndian2Int(substr($ISOheader,  120, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_sequence_number']         = LittleEndian2Int(substr($ISOheader,  124, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['logical_block_size']             = LittleEndian2Int(substr($ISOheader,  128, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_size']                = LittleEndian2Int(substr($ISOheader,  132, 4));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_l_location']          = LittleEndian2Int(substr($ISOheader,  140, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_l_opt_location']      = LittleEndian2Int(substr($ISOheader,  144, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_m_location']          = LittleEndian2Int(substr($ISOheader,  148, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_m_opt_location']      = LittleEndian2Int(substr($ISOheader,  152, 2));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['root_directory_record']          =                  substr($ISOheader,  156, 34);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_set_identifier']          =                  substr($ISOheader,  190, 128);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['publisher_identifier']           =                  substr($ISOheader,  318, 128);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['data_preparer_identifier']       =                  substr($ISOheader,  446, 128);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['application_identifier']         =                  substr($ISOheader,  574, 128);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['copyright_file_identifier']      =                  substr($ISOheader,  702, 37);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['abstract_file_identifier']       =                  substr($ISOheader,  739, 37);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['bibliographic_file_identifier']  =                  substr($ISOheader,  776, 37);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_creation_date_time']      =                  substr($ISOheader,  813, 17);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_modification_date_time']  =                  substr($ISOheader,  830, 17);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_expiration_date_time']    =                  substr($ISOheader,  847, 17);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_effective_date_time']     =                  substr($ISOheader,  864, 17);
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['file_structure_version']         = LittleEndian2Int(substr($ISOheader,  881, 1));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['unused_4']                       = LittleEndian2Int(substr($ISOheader,  882, 1));
    $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['application_data']               =                  substr($ISOheader,  883, 512);
    //$ThisFileInfo['iso']['primary_volume_descriptor']['raw']['unused_5']                       =                  substr($ISOheader, 1395, 653);

    $ThisFileInfo['iso']['primary_volume_descriptor']['system_identifier']              = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['system_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_identifier']              = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_set_identifier']          = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_set_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['publisher_identifier']           = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['publisher_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['data_preparer_identifier']       = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['data_preparer_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['application_identifier']         = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['application_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['copyright_file_identifier']      = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['copyright_file_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['abstract_file_identifier']       = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['abstract_file_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['bibliographic_file_identifier']  = trim($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['bibliographic_file_identifier']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_creation_date_time']      = ISOtimeText2UNIXtime($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_creation_date_time']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_modification_date_time']  = ISOtimeText2UNIXtime($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_modification_date_time']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_expiration_date_time']    = ISOtimeText2UNIXtime($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_expiration_date_time']);
    $ThisFileInfo['iso']['primary_volume_descriptor']['volume_effective_date_time']     = ISOtimeText2UNIXtime($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_effective_date_time']);

    if (($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_space_size'] * 2048) > $ThisFileInfo['filesize']) {
		$ThisFileInfo['error'] .= "\n".'Volume Space Size ('.($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['volume_space_size'] * 2048).' bytes) is larger than the file size ('.$ThisFileInfo['filesize'].' bytes) (truncated file?)';
    }

    return true;
}


function ParseSupplementaryVolumeDescriptor(&$ISOheader, &$ThisFileInfo) {
    // ISO integer values are stored Both-Endian format!!
    // ie 12345 == 0x3039  is stored as $39 $30 $30 $39 in a 4-byte field

    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_descriptor_type']         = LittleEndian2Int(substr($ISOheader,    0, 1));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['standard_identifier']            =                  substr($ISOheader,    1, 5);
    if ($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['standard_identifier'] != 'CD001') {
		$ThisFileInfo['error'] .= "\n".'Expected "CD001" at offset ('.($ThisFileInfo['iso']['supplementary_volume_descriptor']['offset'] + 1).'), found "'.$ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['standard_identifier'].'" instead';
		return false;
    }


    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_descriptor_version']      = LittleEndian2Int(substr($ISOheader,    6, 1));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['unused_1']                       =                  substr($ISOheader,    7, 1);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['system_identifier']              =                  substr($ISOheader,    8, 32);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_identifier']              =                  substr($ISOheader,   40, 32);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['unused_2']                       =                  substr($ISOheader,   72, 8);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_space_size']              = LittleEndian2Int(substr($ISOheader,   80, 4));
    if ($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_space_size'] == 0) {
		// Supplementary Volume Descriptor not used
//        unset($ThisFileInfo['iso']['supplementary_volume_descriptor']);
//        return false;
    }

    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['unused_3']                       =                  substr($ISOheader,   88, 32);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_set_size']                = LittleEndian2Int(substr($ISOheader,  120, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_sequence_number']         = LittleEndian2Int(substr($ISOheader,  124, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['logical_block_size']             = LittleEndian2Int(substr($ISOheader,  128, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_size']                = LittleEndian2Int(substr($ISOheader,  132, 4));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location']          = LittleEndian2Int(substr($ISOheader,  140, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_l_opt_location']      = LittleEndian2Int(substr($ISOheader,  144, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_m_location']          = LittleEndian2Int(substr($ISOheader,  148, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_m_opt_location']      = LittleEndian2Int(substr($ISOheader,  152, 2));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['root_directory_record']          =                  substr($ISOheader,  156, 34);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_set_identifier']          =                  substr($ISOheader,  190, 128);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['publisher_identifier']           =                  substr($ISOheader,  318, 128);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['data_preparer_identifier']       =                  substr($ISOheader,  446, 128);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['application_identifier']         =                  substr($ISOheader,  574, 128);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['copyright_file_identifier']      =                  substr($ISOheader,  702, 37);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['abstract_file_identifier']       =                  substr($ISOheader,  739, 37);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['bibliographic_file_identifier']  =                  substr($ISOheader,  776, 37);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_creation_date_time']      =                  substr($ISOheader,  813, 17);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_modification_date_time']  =                  substr($ISOheader,  830, 17);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_expiration_date_time']    =                  substr($ISOheader,  847, 17);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_effective_date_time']     =                  substr($ISOheader,  864, 17);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['file_structure_version']         = LittleEndian2Int(substr($ISOheader,  881, 1));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['unused_4']                       = LittleEndian2Int(substr($ISOheader,  882, 1));
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['application_data']               =                  substr($ISOheader,  883, 512);
    //$ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['unused_5']                       =                  substr($ISOheader, 1395, 653);

    $ThisFileInfo['iso']['supplementary_volume_descriptor']['system_identifier']              = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['system_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_identifier']              = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_set_identifier']          = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_set_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['publisher_identifier']           = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['publisher_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['data_preparer_identifier']       = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['data_preparer_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['application_identifier']         = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['application_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['copyright_file_identifier']      = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['copyright_file_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['abstract_file_identifier']       = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['abstract_file_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['bibliographic_file_identifier']  = trim($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['bibliographic_file_identifier']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_creation_date_time']      = ISOtimeText2UNIXtime($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_creation_date_time']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_modification_date_time']  = ISOtimeText2UNIXtime($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_modification_date_time']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_expiration_date_time']    = ISOtimeText2UNIXtime($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_expiration_date_time']);
    $ThisFileInfo['iso']['supplementary_volume_descriptor']['volume_effective_date_time']     = ISOtimeText2UNIXtime($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_effective_date_time']);

    if (($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_space_size'] * $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['logical_block_size']) > $ThisFileInfo['filesize']) {
		$ThisFileInfo['error'] .= "\n".'Volume Space Size ('.($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['volume_space_size'] * $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['logical_block_size']).' bytes) is larger than the file size ('.$ThisFileInfo['filesize'].' bytes) (truncated file?)';
    }

    return true;
}


function ParsePathTable($fd, &$ThisFileInfo) {
    if (!isset($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location']) && !isset($ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_l_location'])) {
		return false;
    }
    if (isset($ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location'])) {
		$PathTableLocation = $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location'];
		$PathTableSize     = $ThisFileInfo['iso']['supplementary_volume_descriptor']['raw']['path_table_size'];
		$TextEncoding      = 255; // Big-Endian Unicode
    } else {
		$PathTableLocation = $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_l_location'];
		$PathTableSize     = $ThisFileInfo['iso']['primary_volume_descriptor']['raw']['path_table_size'];
		$TextEncoding      = 0; // ASCII
    }

    if (($PathTableLocation * 2048) > $ThisFileInfo['filesize']) {
		$ThisFileInfo['error'] .= "\n".'Path Table Location specifies an offset ('.($PathTableLocation * 2048).') beyond the end-of-file ('.$ThisFileInfo['filesize'].')';
		return false;
    }

    $ThisFileInfo['iso']['path_table']['offset'] = $PathTableLocation * 2048;
    fseek($fd, $ThisFileInfo['iso']['path_table']['offset'], SEEK_SET);
    $ThisFileInfo['iso']['path_table']['raw'] = fread($fd, $PathTableSize);

    $offset = 0;
    $pathcounter = 1;
    while ($offset < $PathTableSize) {
		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['length']           = LittleEndian2Int(substr($ThisFileInfo['iso']['path_table']['raw'], $offset, 1));
		$offset += 1;
		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['extended_length']  = LittleEndian2Int(substr($ThisFileInfo['iso']['path_table']['raw'], $offset, 1));
		$offset += 1;
		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['location_logical'] = LittleEndian2Int(substr($ThisFileInfo['iso']['path_table']['raw'], $offset, 4));
		$offset += 4;
		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['parent_directory'] = LittleEndian2Int(substr($ThisFileInfo['iso']['path_table']['raw'], $offset, 2));
		$offset += 2;
		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['name']             =                  substr($ThisFileInfo['iso']['path_table']['raw'], $offset, $ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['length']);
		$offset += $ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['length'] + ($ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['length'] % 2);

		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['name_ascii']       = RoughTranslateUnicodeToASCII($ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['name'], $TextEncoding);

		$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['location_bytes'] = $ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['location_logical'] * 2048;
		if ($pathcounter == 1) {
			$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['full_path'] = '/';
		} else {
			$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['full_path'] = $ThisFileInfo['iso']['path_table']['directories'][$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['parent_directory']]['full_path'].$ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['name_ascii'].'/';
		}
		$FullPathArray[] = $ThisFileInfo['iso']['path_table']['directories'][$pathcounter]['full_path'];

		$pathcounter++;

    }

    return true;
}


function ParseDirectoryRecord(&$fd, $directorydata, &$ThisFileInfo) {
    if (isset($ThisFileInfo['iso']['supplementary_volume_descriptor'])) {
		$TextEncoding = 255; // Big-Endian Unicode
    } else {
		$TextEncoding = 0; // ASCII
    }


    fseek($fd, $directorydata['location_bytes'], SEEK_SET);
    $DirectoryRecordData = fread($fd, 1);

    while (ord($DirectoryRecordData{0}) > 33) {

		$DirectoryRecordData .= fread($fd, ord($DirectoryRecordData{0}) - 1);

		$ThisDirectoryRecord['raw']['length']                    = LittleEndian2Int(substr($DirectoryRecordData,  0, 1));
		$ThisDirectoryRecord['raw']['extended_attribute_length'] = LittleEndian2Int(substr($DirectoryRecordData,  1, 1));
		$ThisDirectoryRecord['raw']['offset_logical']            = LittleEndian2Int(substr($DirectoryRecordData,  2, 4));
		$ThisDirectoryRecord['raw']['filesize']                  = LittleEndian2Int(substr($DirectoryRecordData, 10, 4));
		$ThisDirectoryRecord['raw']['recording_date_time']       =                  substr($DirectoryRecordData, 18, 7);
		$ThisDirectoryRecord['raw']['file_flags']                = LittleEndian2Int(substr($DirectoryRecordData, 25, 1));
		$ThisDirectoryRecord['raw']['file_unit_size']            = LittleEndian2Int(substr($DirectoryRecordData, 26, 1));
		$ThisDirectoryRecord['raw']['interleave_gap_size']       = LittleEndian2Int(substr($DirectoryRecordData, 27, 1));
		$ThisDirectoryRecord['raw']['volume_sequence_number']    = LittleEndian2Int(substr($DirectoryRecordData, 28, 2));
		$ThisDirectoryRecord['raw']['file_identifier_length']    = LittleEndian2Int(substr($DirectoryRecordData, 32, 1));
		$ThisDirectoryRecord['raw']['file_identifier']           =                  substr($DirectoryRecordData, 33, $ThisDirectoryRecord['raw']['file_identifier_length']);

		$ThisDirectoryRecord['file_identifier_ascii']            = RoughTranslateUnicodeToASCII($ThisDirectoryRecord['raw']['file_identifier'], $TextEncoding);

		$ThisDirectoryRecord['filesize']                  = $ThisDirectoryRecord['raw']['filesize'];
		$ThisDirectoryRecord['offset_bytes']              = $ThisDirectoryRecord['raw']['offset_logical'] * 2048;
		$ThisDirectoryRecord['file_flags']['hidden']      = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x01);
		$ThisDirectoryRecord['file_flags']['directory']   = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x02);
		$ThisDirectoryRecord['file_flags']['associated']  = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x04);
		$ThisDirectoryRecord['file_flags']['extended']    = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x08);
		$ThisDirectoryRecord['file_flags']['permissions'] = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x10);
		$ThisDirectoryRecord['file_flags']['multiple']    = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x80);
		$ThisDirectoryRecord['recording_timestamp']       = ISOtime2UNIXtime($ThisDirectoryRecord['raw']['recording_date_time']);

		if ($ThisDirectoryRecord['file_flags']['directory']) {
			$ThisDirectoryRecord['filename'] = $directorydata['full_path'];
		} else {
			$ThisDirectoryRecord['filename'] = $directorydata['full_path'].ISOstripFilenameVersion($ThisDirectoryRecord['file_identifier_ascii']);
			$ThisFileInfo['iso']['files'] = array_merge_clobber($ThisFileInfo['iso']['files'], CreateDeepArray($ThisDirectoryRecord['filename'], '/', $ThisDirectoryRecord['filesize']));
		}

		$DirectoryRecord[] = $ThisDirectoryRecord;
		$DirectoryRecordData = fread($fd, 1);
    }

    return $DirectoryRecord;
}

function ISOstripFilenameVersion($ISOfilename) {
    // convert 'filename.ext;1' to 'filename.ext'
    if (!strstr($ISOfilename, ';')) {
		return $ISOfilename;
    } else {
		return substr($ISOfilename, 0, strpos($ISOfilename, ';'));
    }
}

function ISOtimeText2UNIXtime($ISOtime) {

    $UNIXyear   = (int) substr($ISOtime,  0, 4);
    $UNIXmonth  = (int) substr($ISOtime,  4, 2);
    $UNIXday    = (int) substr($ISOtime,  6, 2);
    $UNIXhour   = (int) substr($ISOtime,  8, 2);
    $UNIXminute = (int) substr($ISOtime, 10, 2);
    $UNIXsecond = (int) substr($ISOtime, 12, 2);

    if (!$UNIXyear) {
		return false;
    }
    return mktime($UNIXhour, $UNIXminute, $UNIXsecond, $UNIXmonth, $UNIXday, $UNIXyear);
}

function ISOtime2UNIXtime($ISOtime) {
    // Represented by seven bytes:
    // 1: Number of years since 1900
    // 2: Month of the year from 1 to 12
    // 3: Day of the Month from 1 to 31
    // 4: Hour of the day from 0 to 23
    // 5: Minute of the hour from 0 to 59
    // 6: second of the minute from 0 to 59
    // 7: Offset from Greenwich Mean Time in number of 15 minute intervals from -48 (West) to +52 (East)

    $UNIXyear   = ord($ISOtime{0}) + 1900;
    $UNIXmonth  = ord($ISOtime{1});
    $UNIXday    = ord($ISOtime{2});
    $UNIXhour   = ord($ISOtime{3});
    $UNIXminute = ord($ISOtime{4});
    $UNIXsecond = ord($ISOtime{5});
    $GMToffset  = TwosCompliment2Decimal(ord($ISOtime{5}));

    return mktime($UNIXhour, $UNIXminute, $UNIXsecond, $UNIXmonth, $UNIXday, $UNIXyear);
}


?>