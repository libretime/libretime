<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.zip.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getZIPHeaderFilepointer(&$fd, &$ThisFileInfo) {

    $ThisFileInfo['fileformat'] = 'zip';
    $ThisFileInfo['zip']['files'] = array();

    $ThisFileInfo['zip']['compressed_size']   = 0;
    $ThisFileInfo['zip']['uncompressed_size'] = 0;
    $ThisFileInfo['zip']['entries_count']     = 0;

    $EOCDsearchData    = '';
    $EOCDsearchCounter = 0;
    while ($EOCDsearchCounter++ < 512) {

		fseek($fd, -128 * $EOCDsearchCounter, SEEK_END);
		$EOCDsearchData = fread($fd, 128).$EOCDsearchData;

		if (strstr($EOCDsearchData, 'PK'.chr(5).chr(6))) {

			$EOCDposition = strpos($EOCDsearchData, 'PK'.chr(5).chr(6));
			fseek($fd, (-128 * $EOCDsearchCounter) + $EOCDposition, SEEK_END);
			$ThisFileInfo['zip']['end_central_directory'] = ZIPparseEndOfCentralDirectory($fd);

			fseek($fd, $ThisFileInfo['zip']['end_central_directory']['directory_offset'], SEEK_SET);
			$ThisFileInfo['zip']['entries_count']     = 0;
			while ($centraldirectoryentry = ZIPparseCentralDirectory($fd)) {
				$ThisFileInfo['zip']['central_directory'][] = $centraldirectoryentry;
				$ThisFileInfo['zip']['entries_count']++;
				$ThisFileInfo['zip']['compressed_size']   += $centraldirectoryentry['compressed_size'];
				$ThisFileInfo['zip']['uncompressed_size'] += $centraldirectoryentry['uncompressed_size'];

				if ($centraldirectoryentry['uncompressed_size'] > 0) {
					$ThisFileInfo['zip']['files'] = array_merge_clobber($ThisFileInfo['zip']['files'], CreateDeepArray($centraldirectoryentry['filename'], '/', $centraldirectoryentry['uncompressed_size']));
				}
			}

			if ($ThisFileInfo['zip']['entries_count'] == 0) {
				$ThisFileInfo['error'] .= "\n".'No Central Directory entries found (truncated file?)';
				return false;
			}

			if (isset($ThisFileInfo['zip']['end_central_directory']['comment'])) {
				$ThisFileInfo['zip']['comments']['comment'] = $ThisFileInfo['zip']['end_central_directory']['comment'];

				// ZIP tags have highest priority
				if (!empty($ThisFileInfo['zip']['comments'])) {
					CopyFormatCommentsToRootComments($ThisFileInfo['zip']['comments'], $ThisFileInfo, true, true, true);
				}
				// add tag to array of tags
				$ThisFileInfo['tags'][] = 'zip';
			}

			if (isset($ThisFileInfo['zip']['central_directory'][0]['compression_method'])) {
				$ThisFileInfo['zip']['compression_method'] = $ThisFileInfo['zip']['central_directory'][0]['compression_method'];
			}
			if (isset($ThisFileInfo['zip']['central_directory'][0]['flags']['compression_speed'])) {
				$ThisFileInfo['zip']['compression_speed']  = $ThisFileInfo['zip']['central_directory'][0]['flags']['compression_speed'];
			}
			if (isset($ThisFileInfo['zip']['compression_method']) && ($ThisFileInfo['zip']['compression_method'] == 'store') && !isset($ThisFileInfo['zip']['compression_speed'])) {
				$ThisFileInfo['zip']['compression_speed']  = 'store';
			}

			return true;

		}

    }

    if (getZIPentriesFilepointer($fd, $ThisFileInfo)) {

		// central directory couldn't be found and/or parsed
		// scan through actual file data entries, recover as much as possible from probable trucated file
		if ($ThisFileInfo['zip']['compressed_size'] > ($ThisFileInfo['filesize'] - 46 - 22)) {
			$ThisFileInfo['error'] .= "\n".'Warning: Truncated file! - Total compressed file sizes ('.$ThisFileInfo['zip']['compressed_size'].' bytes) is greater than filesize minus Central Directory and End Of Central Directory structures ('.($ThisFileInfo['filesize'] - 46 - 22).' bytes)';
		}
		$ThisFileInfo['error'] .= "\n".'Cannot find End Of Central Directory - returned list of files in [zip][entries] array may not be complete';
		return true;

    } else {

		unset($ThisFileInfo['zip']);
		$ThisFileInfo['fileformat'] = '';
		$ThisFileInfo['error'] .= "\n".'Cannot find End Of Central Directory (truncated file?)';
		return false;

    }
}


function getZIPHeaderFilepointerTopDown(&$fd, &$ThisFileInfo) {
    $ThisFileInfo['fileformat'] = 'zip';

    $ThisFileInfo['zip']['compressed_size']   = 0;
    $ThisFileInfo['zip']['uncompressed_size'] = 0;
    $ThisFileInfo['zip']['entries_count']     = 0;

    rewind($fd);
    while ($fileentry = ZIPparseLocalFileHeader($fd)) {
		$ThisFileInfo['zip']['entries'][] = $fileentry;
		$ThisFileInfo['zip']['entries_count']++;
    }
    if ($ThisFileInfo['zip']['entries_count'] == 0) {
		$ThisFileInfo['error'] .= "\n".'No Local File Header entries found';
		return false;
    }

    $ThisFileInfo['zip']['entries_count']     = 0;
    while ($centraldirectoryentry = ZIPparseCentralDirectory($fd)) {
		$ThisFileInfo['zip']['central_directory'][] = $centraldirectoryentry;
		$ThisFileInfo['zip']['entries_count']++;
		$ThisFileInfo['zip']['compressed_size']   += $centraldirectoryentry['compressed_size'];
		$ThisFileInfo['zip']['uncompressed_size'] += $centraldirectoryentry['uncompressed_size'];
    }
    if ($ThisFileInfo['zip']['entries_count'] == 0) {
		$ThisFileInfo['error'] .= "\n".'No Central Directory entries found (truncated file?)';
		return false;
    }

    if ($EOCD = ZIPparseEndOfCentralDirectory($fd)) {
		$ThisFileInfo['zip']['end_central_directory'] = $EOCD;
    } else {
		$ThisFileInfo['error'] .= "\n".'No End Of Central Directory entry found (truncated file?)';
		return false;
    }

    if (isset($ThisFileInfo['zip']['end_central_directory']['comment'])) {
		$ThisFileInfo['zip']['comments']['comment'] = $ThisFileInfo['zip']['end_central_directory']['comment'];

		// ZIP tags have highest priority
		if (!empty($ThisFileInfo['zip']['comments'])) {
			CopyFormatCommentsToRootComments($ThisFileInfo['zip']['comments'], $ThisFileInfo, true, true, true);
		}
		// add tag to array of tags
		$ThisFileInfo['tags'][] = 'zip';
    }

    return true;
}


function getZIPentriesFilepointer(&$fd, &$ThisFileInfo) {
    $ThisFileInfo['zip']['compressed_size']   = 0;
    $ThisFileInfo['zip']['uncompressed_size'] = 0;
    $ThisFileInfo['zip']['entries_count']     = 0;

    rewind($fd);
    while ($fileentry = ZIPparseLocalFileHeader($fd)) {
		$ThisFileInfo['zip']['entries'][] = $fileentry;
		$ThisFileInfo['zip']['entries_count']++;
		$ThisFileInfo['zip']['compressed_size']   += $fileentry['compressed_size'];
		$ThisFileInfo['zip']['uncompressed_size'] += $fileentry['uncompressed_size'];
    }
    if ($ThisFileInfo['zip']['entries_count'] == 0) {
		$ThisFileInfo['error'] .= "\n".'No Local File Header entries found';
		return false;
    }

    return true;
}


function ZIPparseLocalFileHeader(&$fd) {
    $LocalFileHeader['offset'] = ftell($fd);

    $ZIPlocalFileHeader = fread($fd, 30);

    $LocalFileHeader['raw']['signature']          = LittleEndian2Int(substr($ZIPlocalFileHeader,  0, 4));
    if ($LocalFileHeader['raw']['signature'] != 0x04034B50) {
		// invalid Local File Header Signature
		fseek($fd, $LocalFileHeader['offset'], SEEK_SET); // seek back to where filepointer originally was so it can be handled properly
		return false;
    }
    $LocalFileHeader['raw']['extract_version']    = LittleEndian2Int(substr($ZIPlocalFileHeader,  4, 2));
    $LocalFileHeader['raw']['general_flags']      = LittleEndian2Int(substr($ZIPlocalFileHeader,  6, 2));
    $LocalFileHeader['raw']['compression_method'] = LittleEndian2Int(substr($ZIPlocalFileHeader,  8, 2));
    $LocalFileHeader['raw']['last_mod_file_time'] = LittleEndian2Int(substr($ZIPlocalFileHeader, 10, 2));
    $LocalFileHeader['raw']['last_mod_file_date'] = LittleEndian2Int(substr($ZIPlocalFileHeader, 12, 2));
    $LocalFileHeader['raw']['crc_32']             = LittleEndian2Int(substr($ZIPlocalFileHeader, 14, 4));
    $LocalFileHeader['raw']['compressed_size']    = LittleEndian2Int(substr($ZIPlocalFileHeader, 18, 4));
    $LocalFileHeader['raw']['uncompressed_size']  = LittleEndian2Int(substr($ZIPlocalFileHeader, 22, 4));
    $LocalFileHeader['raw']['filename_length']    = LittleEndian2Int(substr($ZIPlocalFileHeader, 26, 2));
    $LocalFileHeader['raw']['extra_field_length'] = LittleEndian2Int(substr($ZIPlocalFileHeader, 28, 2));

    $LocalFileHeader['extract_version']           = sprintf('%1.1f', $LocalFileHeader['raw']['extract_version'] / 10);
    $LocalFileHeader['host_os']                   = ZIPversionOSLookup(($LocalFileHeader['raw']['extract_version'] & 0xFF00) >> 8);
    $LocalFileHeader['compression_method']        = ZIPcompressionMethodLookup($LocalFileHeader['raw']['compression_method']);
    $LocalFileHeader['compressed_size']           = $LocalFileHeader['raw']['compressed_size'];
    $LocalFileHeader['uncompressed_size']         = $LocalFileHeader['raw']['uncompressed_size'];
    $LocalFileHeader['flags']                     = ZIPparseGeneralPurposeFlags($LocalFileHeader['raw']['general_flags'], $LocalFileHeader['raw']['compression_method']);
    $LocalFileHeader['last_modified_timestamp']   = DOStime2UNIXtime($LocalFileHeader['raw']['last_mod_file_date'], $LocalFileHeader['raw']['last_mod_file_time']);

    $FilenameExtrafieldLength = $LocalFileHeader['raw']['filename_length'] + $LocalFileHeader['raw']['extra_field_length'];
    if ($FilenameExtrafieldLength > 0) {
		$ZIPlocalFileHeader .= fread($fd, $FilenameExtrafieldLength);

		if ($LocalFileHeader['raw']['filename_length'] > 0) {
			$LocalFileHeader['filename']                    = substr($ZIPlocalFileHeader, 30, $LocalFileHeader['raw']['filename_length']);
		}
		if ($LocalFileHeader['raw']['extra_field_length'] > 0) {
			$LocalFileHeader['raw']['extra_field_data'] = substr($ZIPlocalFileHeader, 30 + $LocalFileHeader['raw']['filename_length'], $LocalFileHeader['raw']['extra_field_length']);
		}
    }

    //$LocalFileHeader['compressed_data'] = fread($fd, $LocalFileHeader['raw']['compressed_size']);
    fseek($fd, $LocalFileHeader['raw']['compressed_size'], SEEK_CUR);

    if ($LocalFileHeader['flags']['data_descriptor_used']) {
		$DataDescriptor = fread($fd, 12);
		$LocalFileHeader['data_descriptor']['crc_32']            = LittleEndian2Int(substr($DataDescriptor,  0, 4));
		$LocalFileHeader['data_descriptor']['compressed_size']   = LittleEndian2Int(substr($DataDescriptor,  4, 4));
		$LocalFileHeader['data_descriptor']['uncompressed_size'] = LittleEndian2Int(substr($DataDescriptor,  8, 4));
    }

    return $LocalFileHeader;
}


function ZIPparseCentralDirectory(&$fd) {
    $CentralDirectory['offset'] = ftell($fd);

    $ZIPcentralDirectory = fread($fd, 46);

    $CentralDirectory['raw']['signature']            = LittleEndian2Int(substr($ZIPcentralDirectory,  0, 4));
    if ($CentralDirectory['raw']['signature'] != 0x02014B50) {
		// invalid Central Directory Signature
		fseek($fd, $CentralDirectory['offset'], SEEK_SET); // seek back to where filepointer originally was so it can be handled properly
		return false;
    }
    $CentralDirectory['raw']['create_version']       = LittleEndian2Int(substr($ZIPcentralDirectory,  4, 2));
    $CentralDirectory['raw']['extract_version']      = LittleEndian2Int(substr($ZIPcentralDirectory,  6, 2));
    $CentralDirectory['raw']['general_flags']        = LittleEndian2Int(substr($ZIPcentralDirectory,  8, 2));
    $CentralDirectory['raw']['compression_method']   = LittleEndian2Int(substr($ZIPcentralDirectory, 10, 2));
    $CentralDirectory['raw']['last_mod_file_time']   = LittleEndian2Int(substr($ZIPcentralDirectory, 12, 2));
    $CentralDirectory['raw']['last_mod_file_date']   = LittleEndian2Int(substr($ZIPcentralDirectory, 14, 2));
    $CentralDirectory['raw']['crc_32']               = LittleEndian2Int(substr($ZIPcentralDirectory, 16, 4));
    $CentralDirectory['raw']['compressed_size']      = LittleEndian2Int(substr($ZIPcentralDirectory, 20, 4));
    $CentralDirectory['raw']['uncompressed_size']    = LittleEndian2Int(substr($ZIPcentralDirectory, 24, 4));
    $CentralDirectory['raw']['filename_length']      = LittleEndian2Int(substr($ZIPcentralDirectory, 28, 2));
    $CentralDirectory['raw']['extra_field_length']   = LittleEndian2Int(substr($ZIPcentralDirectory, 30, 2));
    $CentralDirectory['raw']['file_comment_length']  = LittleEndian2Int(substr($ZIPcentralDirectory, 32, 2));
    $CentralDirectory['raw']['disk_number_start']    = LittleEndian2Int(substr($ZIPcentralDirectory, 34, 2));
    $CentralDirectory['raw']['internal_file_attrib'] = LittleEndian2Int(substr($ZIPcentralDirectory, 36, 2));
    $CentralDirectory['raw']['external_file_attrib'] = LittleEndian2Int(substr($ZIPcentralDirectory, 38, 4));
    $CentralDirectory['raw']['local_header_offset']  = LittleEndian2Int(substr($ZIPcentralDirectory, 42, 4));

    $CentralDirectory['entry_offset']              = $CentralDirectory['raw']['local_header_offset'];
    $CentralDirectory['create_version']            = sprintf('%1.1f', $CentralDirectory['raw']['create_version'] / 10);
    $CentralDirectory['extract_version']           = sprintf('%1.1f', $CentralDirectory['raw']['extract_version'] / 10);
    $CentralDirectory['host_os']                   = ZIPversionOSLookup(($CentralDirectory['raw']['extract_version'] & 0xFF00) >> 8);
    $CentralDirectory['compression_method']        = ZIPcompressionMethodLookup($CentralDirectory['raw']['compression_method']);
    $CentralDirectory['compressed_size']           = $CentralDirectory['raw']['compressed_size'];
    $CentralDirectory['uncompressed_size']         = $CentralDirectory['raw']['uncompressed_size'];
    $CentralDirectory['flags']                     = ZIPparseGeneralPurposeFlags($CentralDirectory['raw']['general_flags'], $CentralDirectory['raw']['compression_method']);
    $CentralDirectory['last_modified_timestamp']   = DOStime2UNIXtime($CentralDirectory['raw']['last_mod_file_date'], $CentralDirectory['raw']['last_mod_file_time']);

    $FilenameExtrafieldCommentLength = $CentralDirectory['raw']['filename_length'] + $CentralDirectory['raw']['extra_field_length'] + $CentralDirectory['raw']['file_comment_length'];
    if ($FilenameExtrafieldCommentLength > 0) {
		$FilenameExtrafieldComment = fread($fd, $FilenameExtrafieldCommentLength);

		if ($CentralDirectory['raw']['filename_length'] > 0) {
			$CentralDirectory['filename']                  = substr($FilenameExtrafieldComment, 0, $CentralDirectory['raw']['filename_length']);
		}
		if ($CentralDirectory['raw']['extra_field_length'] > 0) {
			$CentralDirectory['raw']['extra_field_data']   = substr($FilenameExtrafieldComment, $CentralDirectory['raw']['filename_length'], $CentralDirectory['raw']['extra_field_length']);
		}
		if ($CentralDirectory['raw']['file_comment_length'] > 0) {
			$CentralDirectory['file_comment']              = substr($FilenameExtrafieldComment, $CentralDirectory['raw']['filename_length'] + $CentralDirectory['raw']['extra_field_length'], $CentralDirectory['raw']['file_comment_length']);
		}
    }

    return $CentralDirectory;
}

function ZIPparseEndOfCentralDirectory(&$fd) {
    $EndOfCentralDirectory['offset'] = ftell($fd);

    $ZIPendOfCentralDirectory = fread($fd, 22);

    $EndOfCentralDirectory['signature']                   = LittleEndian2Int(substr($ZIPendOfCentralDirectory,  0, 4));
    if ($EndOfCentralDirectory['signature'] != 0x06054B50) {
		// invalid End Of Central Directory Signature
		fseek($fd, $EndOfCentralDirectory['offset'], SEEK_SET); // seek back to where filepointer originally was so it can be handled properly
		return false;
    }
    $EndOfCentralDirectory['disk_number_current']         = LittleEndian2Int(substr($ZIPendOfCentralDirectory,  4, 2));
    $EndOfCentralDirectory['disk_number_start_directory'] = LittleEndian2Int(substr($ZIPendOfCentralDirectory,  6, 2));
    $EndOfCentralDirectory['directory_entries_this_disk'] = LittleEndian2Int(substr($ZIPendOfCentralDirectory,  8, 2));
    $EndOfCentralDirectory['directory_entries_total']     = LittleEndian2Int(substr($ZIPendOfCentralDirectory, 10, 2));
    $EndOfCentralDirectory['directory_size']              = LittleEndian2Int(substr($ZIPendOfCentralDirectory, 12, 4));
    $EndOfCentralDirectory['directory_offset']            = LittleEndian2Int(substr($ZIPendOfCentralDirectory, 16, 4));
    $EndOfCentralDirectory['comment_length']              = LittleEndian2Int(substr($ZIPendOfCentralDirectory, 20, 2));

    if ($EndOfCentralDirectory['comment_length'] > 0) {
		$EndOfCentralDirectory['comment']                 = fread($fd, $EndOfCentralDirectory['comment_length']);
    }

    return $EndOfCentralDirectory;
}


function ZIPparseGeneralPurposeFlags($flagbytes, $compressionmethod) {
    $ParsedFlags['encrypted'] = (bool) ($flagbytes & 0x0001);

    switch ($compressionmethod) {
		case 6:
			$ParsedFlags['dictionary_size']    = (($flagbytes & 0x0002) ? 8192 : 4096);
			$ParsedFlags['shannon_fano_trees'] = (($flagbytes & 0x0004) ? 3    : 2);
			break;

		case 8:
		case 9:
			switch (($flagbytes & 0x0006) >> 1) {
				case 0:
					$ParsedFlags['compression_speed'] = 'normal';
					break;
				case 1:
					$ParsedFlags['compression_speed'] = 'maximum';
					break;
				case 2:
					$ParsedFlags['compression_speed'] = 'fast';
					break;
				case 3:
					$ParsedFlags['compression_speed'] = 'superfast';
					break;
			}
			break;
    }
    $ParsedFlags['data_descriptor_used']       = (bool) ($flagbytes & 0x0008);

    return $ParsedFlags;
}


function ZIPversionOSLookup($index) {
    static $ZIPversionOSLookup = array();
    if (empty($ZIPversionOSLookup)) {
		$ZIPversionOSLookup[0]  = 'MS-DOS and OS/2 (FAT / VFAT / FAT32 file systems)';
		$ZIPversionOSLookup[1]  = 'Amiga';
		$ZIPversionOSLookup[2]  = 'OpenVMS';
		$ZIPversionOSLookup[3]  = 'Unix';
		$ZIPversionOSLookup[4]  = 'VM/CMS';
		$ZIPversionOSLookup[5]  = 'Atari ST';
		$ZIPversionOSLookup[6]  = 'OS/2 H.P.F.S.';
		$ZIPversionOSLookup[7]  = 'Macintosh';
		$ZIPversionOSLookup[8]  = 'Z-System';
		$ZIPversionOSLookup[9]  = 'CP/M';
		$ZIPversionOSLookup[10] = 'Windows NTFS';
		$ZIPversionOSLookup[11] = 'MVS';
		$ZIPversionOSLookup[12] = 'VSE';
		$ZIPversionOSLookup[13] = 'Acorn Risc';
		$ZIPversionOSLookup[14] = 'VFAT';
		$ZIPversionOSLookup[15] = 'Alternate MVS';
		$ZIPversionOSLookup[16] = 'BeOS';
		$ZIPversionOSLookup[17] = 'Tandem';
    }

    return (isset($ZIPversionOSLookup[$index]) ? $ZIPversionOSLookup[$index] : '[unknown]');
}

function ZIPcompressionMethodLookup($index) {
    static $ZIPcompressionMethodLookup = array();
    if (empty($ZIPcompressionMethodLookup)) {
		$ZIPcompressionMethodLookup[0]  = 'store';
		$ZIPcompressionMethodLookup[1]  = 'shrink';
		$ZIPcompressionMethodLookup[2]  = 'reduce-1';
		$ZIPcompressionMethodLookup[3]  = 'reduce-2';
		$ZIPcompressionMethodLookup[4]  = 'reduce-3';
		$ZIPcompressionMethodLookup[5]  = 'reduce-4';
		$ZIPcompressionMethodLookup[6]  = 'implode';
		$ZIPcompressionMethodLookup[7]  = 'tokenize';
		$ZIPcompressionMethodLookup[8]  = 'deflate';
		$ZIPcompressionMethodLookup[9]  = 'deflate64';
		$ZIPcompressionMethodLookup[10] = 'PKWARE Date Compression Library Imploding';
    }

    return (isset($ZIPcompressionMethodLookup[$index]) ? $ZIPcompressionMethodLookup[$index] : '[unknown]');
}

?>