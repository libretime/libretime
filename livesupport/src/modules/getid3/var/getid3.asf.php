<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.asf.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

$GUIDarray = KnownGUIDs();
foreach ($GUIDarray as $GUIDname => $hexstringvalue) {
    // initialize all GUID constants
    define($GUIDname, GUIDtoBytestring($hexstringvalue));
}

function getASFHeaderFilepointer(&$fd, &$ThisFileInfo) {
    // ASF structure:
    // * Header Object [required]
    //   * File Properties Object [required]   (global file attributes)
    //   * Stream Properties Object [required] (defines media stream & characteristics)
    //   * Header Extension Object [required]  (additional functionality)
    //   * Content Description Object          (bibliographic information)
    //   * Script Command Object               (commands for during playback)
    //   * Marker Object                       (named jumped points within the file)
    // * Data Object [required]
    //   * Data Packets
    // * Index Object

    // Header Object: (mandatory, one only)
    // Field Name                   Field Type   Size (bits)
    // Object ID                    GUID         128             // GUID for header object - ASF_Header_Object
    // Object Size                  QWORD        64              // size of header object, including 30 bytes of Header Object header
    // Number of Header Objects     DWORD        32              // number of objects in header object
    // Reserved1                    BYTE         8               // hardcoded: 0x01
    // Reserved2                    BYTE         8               // hardcoded: 0x02

    $ThisFileInfo['fileformat']   = 'asf';

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $HeaderObjectData = fread($fd, 30);

    $ThisFileInfo['asf']['header_object']['objectid']      = substr($HeaderObjectData, 0, 16);
    $ThisFileInfo['asf']['header_object']['objectid_guid'] = BytestringToGUID($ThisFileInfo['asf']['header_object']['objectid']);
    if ($ThisFileInfo['asf']['header_object']['objectid'] != ASF_Header_Object) {
		$ThisFileInfo['warning'] .= "\n".'ASF header GUID {'.BytestringToGUID($ThisFileInfo['asf']['header_object']['objectid']).'} does not match expected "ASF_Header_Object" GUID {'.BytestringToGUID(ASF_Header_Object).'}';
		//return false;
		break;
    }
    $ThisFileInfo['asf']['header_object']['objectsize']    = LittleEndian2Int(substr($HeaderObjectData, 16, 8));
    $ThisFileInfo['asf']['header_object']['headerobjects'] = LittleEndian2Int(substr($HeaderObjectData, 24, 4));
    $ThisFileInfo['asf']['header_object']['reserved1']     = LittleEndian2Int(substr($HeaderObjectData, 28, 1));
    $ThisFileInfo['asf']['header_object']['reserved2']     = LittleEndian2Int(substr($HeaderObjectData, 29, 1));

    //$ASFHeaderData  = $HeaderObjectData;
    $ASFHeaderData = fread($fd, $ThisFileInfo['asf']['header_object']['objectsize'] - 30);
    //$offset = 30;
    $offset = 0;

    for ($HeaderObjectsCounter = 0; $HeaderObjectsCounter < $ThisFileInfo['asf']['header_object']['headerobjects']; $HeaderObjectsCounter++) {
		$NextObjectGUID     = substr($ASFHeaderData, $offset, 16);
		$offset += 16;
		$NextObjectGUIDtext = BytestringToGUID($NextObjectGUID);
		$NextObjectSize = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
		$offset += 8;
		switch ($NextObjectGUID) {

			case ASF_File_Properties_Object:
				// File Properties Object: (mandatory, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for file properties object - ASF_File_Properties_Object
				// Object Size                  QWORD        64              // size of file properties object, including 104 bytes of File Properties Object header
				// File ID                      GUID         128             // unique ID - identical to File ID in Data Object
				// File Size                    QWORD        64              // entire file in bytes. Invalid if Broadcast Flag == 1
				// Creation Date                QWORD        64              // date & time of file creation. Maybe invalid if Broadcast Flag == 1
				// Data Packets Count           QWORD        64              // number of data packets in Data Object. Invalid if Broadcast Flag == 1
				// Play Duration                QWORD        64              // playtime, in 100-nanosecond units. Invalid if Broadcast Flag == 1
				// Send Duration                QWORD        64              // time needed to send file, in 100-nanosecond units. Players can ignore this value. Invalid if Broadcast Flag == 1
				// Preroll                      QWORD        64              // time to buffer data before starting to play file, in 1-millisecond units. If <> 0, PlayDuration and PresentationTime have been offset by this amount
				// Flags                        DWORD        32              //
				// * Broadcast Flag             bits         1  (0x01)       // file is currently being written, some header values are invalid
				// * Seekable Flag              bits         1  (0x02)       // is file seekable
				// * Reserved                   bits         30 (0xFFFFFFFC) // reserved - set to zero
				// Minimum Data Packet Size     DWORD        32              // in bytes. should be same as Maximum Data Packet Size. Invalid if Broadcast Flag == 1
				// Maximum Data Packet Size     DWORD        32              // in bytes. should be same as Minimum Data Packet Size. Invalid if Broadcast Flag == 1
				// Maximum Bitrate              DWORD        32              // maximum instantaneous bitrate in bits per second for entire file, including all data streams and ASF overhead

				$ThisFileInfo['asf']['file_properties_object']['objectid']           = $NextObjectGUID;
				$ThisFileInfo['asf']['file_properties_object']['objectid_guid']      = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['file_properties_object']['objectsize']         = $NextObjectSize;
				$ThisFileInfo['asf']['file_properties_object']['fileid']             = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['file_properties_object']['fileid_guid']        = BytestringToGUID($ThisFileInfo['asf']['file_properties_object']['fileid']);
				$ThisFileInfo['asf']['file_properties_object']['filesize']           = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['file_properties_object']['creation_date']      = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$ThisFileInfo['asf']['file_properties_object']['creation_date_unix'] = FILETIMEtoUNIXtime($ThisFileInfo['asf']['file_properties_object']['creation_date']);
				$offset += 8;
				$ThisFileInfo['asf']['file_properties_object']['data_packets']       = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['file_properties_object']['play_duration']      = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['file_properties_object']['send_duration']      = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['file_properties_object']['preroll']            = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['playtime_seconds'] = ($ThisFileInfo['asf']['file_properties_object']['play_duration'] / 10000000) - ($ThisFileInfo['asf']['file_properties_object']['preroll'] / 1000);
				$ThisFileInfo['asf']['file_properties_object']['flags_raw']          = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['file_properties_object']['flags']['broadcast'] = (bool) ($ThisFileInfo['asf']['file_properties_object']['flags_raw'] & 0x0001);
				$ThisFileInfo['asf']['file_properties_object']['flags']['seekable']  = (bool) ($ThisFileInfo['asf']['file_properties_object']['flags_raw'] & 0x0002);

				$ThisFileInfo['asf']['file_properties_object']['min_packet_size']    = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['file_properties_object']['max_packet_size']    = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['file_properties_object']['max_bitrate']        = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['bitrate']                                             = $ThisFileInfo['asf']['file_properties_object']['max_bitrate'];
				break;

			case ASF_Stream_Properties_Object:
				// Stream Properties Object: (mandatory, one per media stream)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for stream properties object - ASF_Stream_Properties_Object
				// Object Size                  QWORD        64              // size of stream properties object, including 78 bytes of Stream Properties Object header
				// Stream Type                  GUID         128             // ASF_Audio_Media, ASF_Video_Media or ASF_Command_Media
				// Error Correction Type        GUID         128             // ASF_Audio_Spread for audio-only streams, ASF_No_Error_Correction for other stream types
				// Time Offset                  QWORD        64              // 100-nanosecond units. typically zero. added to all timestamps of samples in the stream
				// Type-Specific Data Length    DWORD        32              // number of bytes for Type-Specific Data field
				// Error Correction Data Length DWORD        32              // number of bytes for Error Correction Data field
				// Flags                        WORD         16              //
				// * Stream Number              bits         7 (0x007F)      // number of this stream.  1 <= valid <= 127
				// * Reserved                   bits         8 (0x7F80)      // reserved - set to zero
				// * Encrypted Content Flag     bits         1 (0x8000)      // stream contents encrypted if set
				// Reserved                     DWORD        32              // reserved - set to zero
				// Type-Specific Data           BYTESTREAM   variable        // type-specific format data, depending on value of Stream Type
				// Error Correction Data        BYTESTREAM   variable        // error-correction-specific format data, depending on value of Error Correct Type

				// There is one ASF_Stream_Properties_Object for each stream (audio, video) but the
				// stream number isn't known until halfway through decoding the structure, hence it
				// it is decoded to a temporary variable and then stuck in the appropriate index later

				$StreamPropertiesObjectData['objectid']           = $NextObjectGUID;
				$StreamPropertiesObjectData['objectid_guid']      = $NextObjectGUIDtext;
				$StreamPropertiesObjectData['objectsize']         = $NextObjectSize;
				$StreamPropertiesObjectData['stream_type']        = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$StreamPropertiesObjectData['stream_type_guid']   = BytestringToGUID($StreamPropertiesObjectData['stream_type']);
				$StreamPropertiesObjectData['error_correct_type'] = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$StreamPropertiesObjectData['error_correct_guid'] = BytestringToGUID($StreamPropertiesObjectData['error_correct_type']);
				$StreamPropertiesObjectData['time_offset']        = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
				$offset += 8;
				$StreamPropertiesObjectData['type_data_length']   = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$StreamPropertiesObjectData['error_data_length']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$StreamPropertiesObjectData['flags_raw']          = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$StreamPropertiesObjectStreamNumber               = $StreamPropertiesObjectData['flags_raw'] & 0x007F;
				$StreamPropertiesObjectData['flags']['encrypted'] = (bool) ($StreamPropertiesObjectData['flags_raw'] & 0x8000);

				$offset += 4; // reserved - DWORD
				$StreamPropertiesObjectData['type_specific_data'] = substr($ASFHeaderData, $offset, $StreamPropertiesObjectData['type_data_length']);
				$offset += $StreamPropertiesObjectData['type_data_length'];
				$StreamPropertiesObjectData['error_correct_data'] = substr($ASFHeaderData, $offset, $StreamPropertiesObjectData['error_data_length']);
				$offset += $StreamPropertiesObjectData['error_data_length'];

				switch ($StreamPropertiesObjectData['stream_type']) {

					case ASF_Audio_Media:
						if (empty($ThisFileInfo['audio']['bitrate_mode'])) {
							$ThisFileInfo['audio']['bitrate_mode'] = 'cbr';
						}

						require_once(GETID3_INCLUDEPATH.'getid3.riff.php');
						$audiodata = RIFFparseWAVEFORMATex(substr($StreamPropertiesObjectData['type_specific_data'], 0, 16));
						unset($audiodata['raw']);
						$ThisFileInfo['audio'] = array_merge_noclobber($audiodata, $ThisFileInfo['audio']);
						break;

					case ASF_Video_Media:
						if (empty($ThisFileInfo['video']['bitrate_mode'])) {
							$ThisFileInfo['video']['bitrate_mode'] = 'cbr';
						}
						break;

					case ASF_Command_Media:
					default:
						// do nothing
						break;

				}

				$ThisFileInfo['asf']['stream_properties_object'][$StreamPropertiesObjectStreamNumber] = $StreamPropertiesObjectData;
				unset($StreamPropertiesObjectData); // clear for next stream, if any
				break;

			case ASF_Header_Extension_Object:
				// Header Extension Object: (mandatory, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Header Extension object - ASF_Header_Extension_Object
				// Object Size                  QWORD        64              // size of Header Extension object, including 46 bytes of Header Extension Object header
				// Reserved Field 1             GUID         128             // hardcoded: ASF_Reserved_1
				// Reserved Field 2             WORD         16              // hardcoded: 0x00000006
				// Header Extension Data Size   DWORD        32              // in bytes. valid: 0, or > 24. equals object size minus 46
				// Header Extension Data        BYTESTREAM   variable        // array of zero or more extended header objects

				$ThisFileInfo['asf']['header_extension_object']['objectid']            = $NextObjectGUID;
				$ThisFileInfo['asf']['header_extension_object']['objectid_guid']       = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['header_extension_object']['objectsize']          = $NextObjectSize;
				$ThisFileInfo['asf']['header_extension_object']['reserved_1']          = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['header_extension_object']['reserved_1_guid']     = BytestringToGUID($ThisFileInfo['asf']['header_extension_object']['reserved_1']);
				if ($ThisFileInfo['asf']['header_extension_object']['reserved_1'] != ASF_Reserved_1) {
					$ThisFileInfo['warning'] .= "\n".'header_extension_object.reserved_1 GUID ('.BytestringToGUID($ThisFileInfo['asf']['header_extension_object']['reserved_1']).') does not match expected "ASF_Reserved_1" GUID ('.BytestringToGUID(ASF_Reserved_1).')';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['header_extension_object']['reserved_2']          = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				if ($ThisFileInfo['asf']['header_extension_object']['reserved_2'] != 6) {
					$ThisFileInfo['warning'] .= "\n".'header_extension_object.reserved_2 ('.PrintHexBytes($ThisFileInfo['asf']['header_extension_object']['reserved_2']).') does not match expected value of "6"';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['header_extension_object']['extension_data_size'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['header_extension_object']['extension_data']      = LittleEndian2Int(substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['header_extension_object']['extension_data_size']));
				$offset += $ThisFileInfo['asf']['header_extension_object']['extension_data_size'];
				break;

			case ASF_Codec_List_Object:
				// Codec List Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Codec List object - ASF_Codec_List_Object
				// Object Size                  QWORD        64              // size of Codec List object, including 44 bytes of Codec List Object header
				// Reserved                     GUID         128             // hardcoded: 86D15241-311D-11D0-A3A4-00A0C90348F6
				// Codec Entries Count          DWORD        32              // number of entries in Codec Entries array
				// Codec Entries                array of:    variable        //
				// * Type                       WORD         16              // 0x0001 = Video Codec, 0x0002 = Audio Codec, 0xFFFF = Unknown Codec
				// * Codec Name Length          WORD         16              // number of Unicode characters stored in the Codec Name field
				// * Codec Name                 WCHAR        variable        // array of Unicode characters - name of codec used to create the content
				// * Codec Description Length   WORD         16              // number of Unicode characters stored in the Codec Description field
				// * Codec Description          WCHAR        variable        // array of Unicode characters - description of format used to create the content
				// * Codec Information Length   WORD         16              // number of Unicode characters stored in the Codec Information field
				// * Codec Information          BYTESTREAM   variable        // opaque array of information bytes about the codec used to create the content

				$ThisFileInfo['asf']['codec_list']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['codec_list']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['codec_list']['objectsize']                = $NextObjectSize;
				$ThisFileInfo['asf']['codec_list']['reserved']                  = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['codec_list']['reserved_guid']             = BytestringToGUID($ThisFileInfo['asf']['codec_list']['reserved']);
				if ($ThisFileInfo['asf']['codec_list']['reserved'] != GUIDtoBytestring('86D15241-311D-11D0-A3A4-00A0C90348F6')) {
					$ThisFileInfo['warning'] .= "\n".'codec_list_object.reserved GUID {'.BytestringToGUID($ThisFileInfo['asf']['codec_list']['reserved']).'} does not match expected "ASF_Reserved_1" GUID {86D15241-311D-11D0-A3A4-00A0C90348F6}';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['codec_list']['codec_entries_count']       = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				for ($CodecEntryCounter = 0; $CodecEntryCounter < $ThisFileInfo['asf']['codec_list']['codec_entries_count']; $CodecEntryCounter++) {
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['type_raw'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['type']     = ASFCodecListObjectTypeLookup($ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['type_raw']);

					$CodecNameLength = LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
					$offset += 2;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['name']       = substr($ASFHeaderData, $offset, $CodecNameLength);
					$offset += $CodecNameLength;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['name_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['name'], 2);

					$CodecDescriptionLength = LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
					$offset += 2;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['description']       = substr($ASFHeaderData, $offset, $CodecDescriptionLength);
					$offset += $CodecDescriptionLength;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['description_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['description'], 2);

					$CodecInformationLength = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['information'] = substr($ASFHeaderData, $offset, $CodecInformationLength);
					$offset += $CodecInformationLength;

					if ($ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['type_raw'] == 2) {
						// audio codec
						$ThisFileInfo['audio']['codec'] = $ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['name_ascii'];
						list($AudioCodecBitrate, $AudioCodecFrequency, $AudioCodecChannels) = explode(',', $ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['description_ascii']);

						if (!isset($ThisFileInfo['audio']['bitrate']) && strstr($AudioCodecBitrate, 'kbps')) {
							$ThisFileInfo['audio']['bitrate'] = (int) (trim(str_replace('kbps', '', $AudioCodecBitrate)) * 1000);
						}
						if (!isset($ThisFileInfo['video']['bitrate']) && isset($ThisFileInfo['audio']['bitrate']) && isset($ThisFileInfo['asf']['file_properties_object']['max_bitrate']) && ($ThisFileInfo['asf']['codec_list']['codec_entries_count'] > 1)) {
							$ThisFileInfo['video']['bitrate'] = $ThisFileInfo['asf']['file_properties_object']['max_bitrate'] - $ThisFileInfo['audio']['bitrate'];
						}

						$AudioCodecFrequency = (int) trim(str_replace('kHz', '', $AudioCodecFrequency));
						switch ($AudioCodecFrequency) {
							case 8:
								$ThisFileInfo['audio']['sample_rate'] = 8000;
								break;

							case 11:
								$ThisFileInfo['audio']['sample_rate'] = 11025;
								break;

							case 16:
								$ThisFileInfo['audio']['sample_rate'] = 16000;
								break;

							case 22:
								$ThisFileInfo['audio']['sample_rate'] = 22050;
								break;

							case 32:
								$ThisFileInfo['audio']['sample_rate'] = 32000;
								break;

							case 44:
								$ThisFileInfo['audio']['sample_rate'] = 44100;
								break;

							case 48:
								$ThisFileInfo['audio']['sample_rate'] = 48000;
								break;

							default:
								$ThisFileInfo['error'] .= "\n".'unknown frequency: '.$ThisFileInfo['asf']['codec_list']['codec_entries'][$CodecEntryCounter]['description_ascii'];
								return false;
								break;
						}

						if (!isset($ThisFileInfo['audio']['channels'])) {
							if (strstr($AudioCodecChannels, 'stereo')) {
								$ThisFileInfo['audio']['channels'] = 2;
							} elseif (strstr($AudioCodecChannels, 'mono')) {
								$ThisFileInfo['audio']['channels'] = 1;
							}
						}
					}
				}
				break;

			case ASF_Script_Command_Object:
				// Script Command Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Script Command object - ASF_Script_Command_Object
				// Object Size                  QWORD        64              // size of Script Command object, including 44 bytes of Script Command Object header
				// Reserved                     GUID         128             // hardcoded: 4B1ACBE3-100B-11D0-A39B-00A0C90348F6
				// Commands Count               WORD         16              // number of Commands structures in the Script Commands Objects
				// Command Types Count          WORD         16              // number of Command Types structures in the Script Commands Objects
				// Command Types                array of:    variable        //
				// * Command Type Name Length   WORD         16              // number of Unicode characters for Command Type Name
				// * Command Type Name          WCHAR        variable        // array of Unicode characters - name of a type of command
				// Commands                     array of:    variable        //
				// * Presentation Time          DWORD        32              // presentation time of that command, in milliseconds
				// * Type Index                 WORD         16              // type of this command, as a zero-based index into the array of Command Types of this object
				// * Command Name Length        WORD         16              // number of Unicode characters for Command Name
				// * Command Name               WCHAR        variable        // array of Unicode characters - name of this command

				$ThisFileInfo['asf']['script_command_object']['objectid']             = $NextObjectGUID;
				$ThisFileInfo['asf']['script_command_object']['objectid_guid']        = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['script_command_object']['objectsize']           = $NextObjectSize;
				$ThisFileInfo['asf']['script_command_object']['reserved']             = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['script_command_object']['reserved_guid']        = BytestringToGUID($ThisFileInfo['asf']['script_command_object']['reserved']);
				if ($ThisFileInfo['asf']['script_command_object']['reserved'] != GUIDtoBytestring('4B1ACBE3-100B-11D0-A39B-00A0C90348F6')) {
					$ThisFileInfo['warning'] .= "\n".'script_command_object.reserved GUID {'.BytestringToGUID($ThisFileInfo['asf']['script_command_object']['reserved']).'} does not match expected "ASF_Reserved_1" GUID {4B1ACBE3-100B-11D0-A39B-00A0C90348F6}';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['script_command_object']['commands_count']       = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['script_command_object']['command_types_count']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				for ($CommandTypesCounter = 0; $CommandTypesCounter < $ThisFileInfo['asf']['script_command_object']['command_types_count']; $CommandTypesCounter++) {
					$CommandTypeNameLength = LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
					$offset += 2;
					$ThisFileInfo['asf']['script_command_object']['command_types'][$CommandTypesCounter]['name'] = substr($ASFHeaderData, $offset, $CommandTypeNameLength);
					$offset += $CommandTypeNameLength;
					$ThisFileInfo['asf']['script_command_object']['command_types'][$CommandTypesCounter]['name_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['script_command_object']['command_types'][$CommandTypesCounter]['name'], 2);
				}
				for ($CommandsCounter = 0; $CommandsCounter < $ThisFileInfo['asf']['script_command_object']['commands_count']; $CommandsCounter++) {
					$ThisFileInfo['asf']['script_command_object']['commands'][$CommandsCounter]['presentation_time']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$ThisFileInfo['asf']['script_command_object']['commands'][$CommandsCounter]['type_index']         = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;

					$CommandTypeNameLength = LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
					$offset += 2;
					$ThisFileInfo['asf']['script_command_object']['commands'][$CommandsCounter]['name'] = substr($ASFHeaderData, $offset, $CommandTypeNameLength);
					$offset += $CommandTypeNameLength;
					$ThisFileInfo['asf']['script_command_object']['commands'][$CommandsCounter]['name_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['script_command_object']['commands'][$CommandsCounter]['name'], 2);
				}
				break;

			case ASF_Marker_Object:
				// Marker Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Marker object - ASF_Marker_Object
				// Object Size                  QWORD        64              // size of Marker object, including 48 bytes of Marker Object header
				// Reserved                     GUID         128             // hardcoded: 4CFEDB20-75F6-11CF-9C0F-00A0C90349CB
				// Markers Count                DWORD        32              // number of Marker structures in Marker Object
				// Reserved                     WORD         16              // hardcoded: 0x0000
				// Name Length                  WORD         16              // number of bytes in the Name field
				// Name                         WCHAR        variable        // name of the Marker Object
				// Markers                      array of:    variable        //
				// * Offset                     QWORD        64              // byte offset into Data Object
				// * Presentation Time          QWORD        64              // in 100-nanosecond units
				// * Entry Length               WORD         16              // length in bytes of (Send Time + Flags + Marker Description Length + Marker Description + Padding)
				// * Send Time                  DWORD        32              // in milliseconds
				// * Flags                      DWORD        32              // hardcoded: 0x00000000
				// * Marker Description Length  DWORD        32              // number of bytes in Marker Description field
				// * Marker Description         WCHAR        variable        // array of Unicode characters - description of marker entry
				// * Padding                    BYTESTREAM   variable        // optional padding bytes

				$ThisFileInfo['asf']['marker_object']['objectid']             = $NextObjectGUID;
				$ThisFileInfo['asf']['marker_object']['objectid_guid']        = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['marker_object']['objectsize']           = $NextObjectSize;
				$ThisFileInfo['asf']['marker_object']['reserved']             = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['marker_object']['reserved_guid']        = BytestringToGUID($ThisFileInfo['asf']['marker_object']['reserved']);
				if ($ThisFileInfo['asf']['marker_object']['reserved'] != GUIDtoBytestring('4CFEDB20-75F6-11CF-9C0F-00A0C90349CB')) {
					$ThisFileInfo['warning'] .= "\n".'marker_object.reserved GUID {'.BytestringToGUID($ThisFileInfo['asf']['marker_object']['reserved_1']).'} does not match expected "ASF_Reserved_1" GUID {4CFEDB20-75F6-11CF-9C0F-00A0C90349CB}';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['marker_object']['markers_count'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['marker_object']['reserved_2'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				if ($ThisFileInfo['asf']['marker_object']['reserved_2'] != 0) {
					$ThisFileInfo['warning'] .= "\n".'marker_object.reserved_2 ('.PrintHexBytes($ThisFileInfo['asf']['marker_object']['reserved_2']).') does not match expected value of "0"';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['marker_object']['name_length'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['marker_object']['name'] = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['marker_object']['name_length']);
				$offset += $ThisFileInfo['asf']['marker_object']['name_length'];
				$ThisFileInfo['asf']['marker_object']['name_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['marker_object']['name'], 2);
				for ($MarkersCounter = 0; $MarkersCounter < $ThisFileInfo['asf']['marker_object']['markers_count']; $MarkersCounter++) {
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['offset']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['presentation_time']         = LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['entry_length']              = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['send_time']                 = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['flags']                     = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description_length'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description']        = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description_length']);
					$offset += $ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description_length'];
					$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description_ascii']  = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description'], 2);
					$PaddingLength = $ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['entry_length'] - 4 -  4 - 4 - $ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['marker_description_length'];
					if ($PaddingLength > 0) {
						$ThisFileInfo['asf']['marker_object']['markers'][$MarkersCounter]['padding']               = substr($ASFHeaderData, $offset, $PaddingLength);
						$offset += $PaddingLength;
					}
				}
				break;

			case ASF_Bitrate_Mutual_Exclusion_Object:
				// Bitrate Mutual Exclusion Object: (optional)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Bitrate Mutual Exclusion object - ASF_Bitrate_Mutual_Exclusion_Object
				// Object Size                  QWORD        64              // size of Bitrate Mutual Exclusion object, including 42 bytes of Bitrate Mutual Exclusion Object header
				// Exlusion Type                GUID         128             // nature of mutual exclusion relationship. one of: (ASF_Mutex_Bitrate, ASF_Mutex_Unknown)
				// Stream Numbers Count         WORD         16              // number of video streams
				// Stream Numbers               WORD         variable        // array of mutually exclusive video stream numbers. 1 <= valid <= 127

				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['objectid']             = $NextObjectGUID;
				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['objectid_guid']        = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['objectsize']           = $NextObjectSize;
				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved']             = substr($ASFHeaderData, $offset, 16);
				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved_guid']        = BytestringToGUID($ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved']);
				$offset += 16;
				if (($ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] != ASF_Mutex_Bitrate) && ($ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] != ASF_Mutex_Unknown)) {
					$ThisFileInfo['warning'] .= "\n".'bitrate_mutual_exclusion_object.reserved GUID {'.BytestringToGUID($ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['reserved']).'} does not match expected "ASF_Mutex_Bitrate" GUID {'.BytestringToGUID(ASF_Mutex_Bitrate).'} or  "ASF_Mutex_Unknown" GUID {'.BytestringToGUID(ASF_Mutex_Unknown).'}';
					//return false;
					break;
				}
				$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['stream_numbers_count'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				for ($StreamNumberCounter = 0; $StreamNumberCounter < $ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['markers_count']; $StreamNumberCounter++) {
					$ThisFileInfo['asf']['bitrate_mutual_exclusion_object']['stream_numbers'][$StreamNumberCounter] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
				}
				break;

			case ASF_Error_Correction_Object:
				// Error Correction Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Error Correction object - ASF_Error_Correction_Object
				// Object Size                  QWORD        64              // size of Error Correction object, including 44 bytes of Error Correction Object header
				// Error Correction Type        GUID         128             // type of error correction. one of: (ASF_No_Error_Correction, ASF_Audio_Spread)
				// Error Correction Data Length DWORD        32              // number of bytes in Error Correction Data field
				// Error Correction Data        BYTESTREAM   variable        // structure depends on value of Error Correction Type field

				$ThisFileInfo['asf']['error_correction_object']['objectid']              = $NextObjectGUID;
				$ThisFileInfo['asf']['error_correction_object']['objectid_guid']         = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['error_correction_object']['objectsize']            = $NextObjectSize;
				$ThisFileInfo['asf']['error_correction_object']['error_correction_type'] = substr($ASFHeaderData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['error_correction_object']['error_correction_guid'] = BytestringToGUID($ThisFileInfo['asf']['error_correction_object']['error_correction_type']);
				$ThisFileInfo['asf']['error_correction_object']['error_correction_data_length'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
				$offset += 4;
				switch ($ThisFileInfo['asf']['error_correction_object']['error_correction_type']) {
					case ASF_No_Error_Correction:
						// should be no data, but just in case there is, skip to the end of the field
						$offset += $ThisFileInfo['asf']['error_correction_object']['error_correction_data_length'];
						break;

					case ASF_Audio_Spread:
						// Field Name                   Field Type   Size (bits)
						// Span                         BYTE         8               // number of packets over which audio will be spread.
						// Virtual Packet Length        WORD         16              // size of largest audio payload found in audio stream
						// Virtual Chunk Length         WORD         16              // size of largest audio payload found in audio stream
						// Silence Data Length          WORD         16              // number of bytes in Silence Data field
						// Silence Data                 BYTESTREAM   variable        // hardcoded: 0x00 * (Silence Data Length) bytes

						$ThisFileInfo['asf']['error_correction_object']['span']                  = LittleEndian2Int(substr($ASFHeaderData, $offset, 1));
						$offset += 1;
						$ThisFileInfo['asf']['error_correction_object']['virtual_packet_length'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$ThisFileInfo['asf']['error_correction_object']['virtual_chunk_length']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$ThisFileInfo['asf']['error_correction_object']['silence_data_length']   = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$ThisFileInfo['asf']['error_correction_object']['silence_data']          = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['error_correction_object']['silence_data_length']);
						$offset += $ThisFileInfo['asf']['error_correction_object']['silence_data_length'];
						break;

					default:
						$ThisFileInfo['warning'] .= "\n".'error_correction_object.error_correction_type GUID {'.BytestringToGUID($ThisFileInfo['asf']['error_correction_object']['reserved']).'} does not match expected "ASF_No_Error_Correction" GUID {'.BytestringToGUID(ASF_No_Error_Correction).'} or  "ASF_Audio_Spread" GUID {'.BytestringToGUID(ASF_Audio_Spread).'}';
						//return false;
						break;
				}

				break;

			case ASF_Content_Description_Object:
				// Content Description Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Content Description object - ASF_Content_Description_Object
				// Object Size                  QWORD        64              // size of Content Description object, including 34 bytes of Content Description Object header
				// Title Length                 WORD         16              // number of bytes in Title field
				// Author Length                WORD         16              // number of bytes in Author field
				// Copyright Length             WORD         16              // number of bytes in Copyright field
				// Description Length           WORD         16              // number of bytes in Description field
				// Rating Length                WORD         16              // number of bytes in Rating field
				// Title                        WCHAR        16              // array of Unicode characters - Title
				// Author                       WCHAR        16              // array of Unicode characters - Author
				// Copyright                    WCHAR        16              // array of Unicode characters - Copyright
				// Description                  WCHAR        16              // array of Unicode characters - Description
				// Rating                       WCHAR        16              // array of Unicode characters - Rating

				$ThisFileInfo['asf']['content_description']['objectid']              = $NextObjectGUID;
				$ThisFileInfo['asf']['content_description']['objectid_guid']         = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['content_description']['objectsize']            = $NextObjectSize;
				$ThisFileInfo['asf']['content_description']['title_length']          = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['content_description']['author_length']         = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['content_description']['copyright_length']      = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['content_description']['description_length']    = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['content_description']['rating_length']         = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['content_description']['title']                 = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['content_description']['title_length']);
				$offset += $ThisFileInfo['asf']['content_description']['title_length'];
				$ThisFileInfo['asf']['content_description']['title_ascii']           = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['content_description']['title'], 2);
				$ThisFileInfo['asf']['content_description']['author']                = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['content_description']['author_length']);
				$offset += $ThisFileInfo['asf']['content_description']['author_length'];
				$ThisFileInfo['asf']['content_description']['author_ascii']          = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['content_description']['author'], 2);
				$ThisFileInfo['asf']['content_description']['copyright']             = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['content_description']['copyright_length']);
				$offset += $ThisFileInfo['asf']['content_description']['copyright_length'];
				$ThisFileInfo['asf']['content_description']['copyright_ascii']       = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['content_description']['copyright'], 2);
				$ThisFileInfo['asf']['content_description']['description']           = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['content_description']['description_length']);
				$offset += $ThisFileInfo['asf']['content_description']['description_length'];
				$ThisFileInfo['asf']['content_description']['description_ascii']     = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['content_description']['description'], 2);
				$ThisFileInfo['asf']['content_description']['rating']                = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['content_description']['rating_length']);
				$offset += $ThisFileInfo['asf']['content_description']['rating_length'];
				$ThisFileInfo['asf']['content_description']['rating_ascii']          = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['content_description']['rating'], 2);

				foreach (array('title', 'author', 'copyright', 'description', 'rating') as $keytocopy) {
					if (!empty($ThisFileInfo['asf']['content_description'][$keytocopy.'_ascii'])) {
						$ThisFileInfo['asf']['comments']["$keytocopy"] = $ThisFileInfo['asf']['content_description'][$keytocopy.'_ascii'];
					}
				}

				// ASF tags have highest priority
				if (!empty($ThisFileInfo['asf']['comments'])) {
					CopyFormatCommentsToRootComments($ThisFileInfo['asf']['comments'], $ThisFileInfo, true, true, true);
				}

				// add tag to array of tags
				$ThisFileInfo['tags'][] = 'asf';

				break;

			case ASF_Extended_Content_Description_Object:
				// Extended Content Description Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Extended Content Description object - ASF_Extended_Content_Description_Object
				// Object Size                  QWORD        64              // size of ExtendedContent Description object, including 26 bytes of Extended Content Description Object header
				// Content Descriptors Count    WORD         16              // number of entries in Content Descriptors list
				// Content Descriptors          array of:    variable        //
				// * Descriptor Name Length     WORD         16              // size in bytes of Descriptor Name field
				// * Descriptor Name            WCHAR        variable        // array of Unicode characters - Descriptor Name
				// * Descriptor Value Data Type WORD         16              // Lookup array:
																			// 0x0000 = Unicode String (variable length)
																			// 0x0001 = BYTE array     (variable length)
																			// 0x0002 = BOOL           (DWORD, 32 bits)
																			// 0x0003 = DWORD          (DWORD, 32 bits)
																			// 0x0004 = QWORD          (QWORD, 64 bits)
																			// 0x0005 = WORD           (WORD,  16 bits)
				// * Descriptor Value Length    WORD         16              // number of bytes stored in Descriptor Value field
				// * Descriptor Value           variable     variable        // value for Content Descriptor

				$ThisFileInfo['asf']['extended_content_description']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['extended_content_description']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['extended_content_description']['objectsize']                = $NextObjectSize;
				$ThisFileInfo['asf']['extended_content_description']['content_descriptors_count'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				for ($ExtendedContentDescriptorsCounter = 0; $ExtendedContentDescriptorsCounter < $ThisFileInfo['asf']['extended_content_description']['content_descriptors_count']; $ExtendedContentDescriptorsCounter++) {
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name_length']  = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name']         = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name_length']);
					$offset += $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name_length'];
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name_ascii']   = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name'], 2);
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_type']   = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_length'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value']        = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_length']);
					$offset += $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_length'];
					switch ($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_type']) {
						case 0x0000: // Unicode string
							$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value'], 2);
							break;

						case 0x0001: // BYTE array
							// do nothing
							break;

						case 0x0002: // BOOL
							$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value'] = (bool) LittleEndian2Int($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value']);
							break;

						case 0x0003: // DWORD
						case 0x0004: // QWORD
						case 0x0005: // WORD
							$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value'] = LittleEndian2Int($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value']);
							break;

						default:
							$ThisFileInfo['warning'] .= "\n".'extended_content_description.content_descriptors.'.$ExtendedContentDescriptorsCounter.'.value_type is invalid ('.$ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_type'].')';
							//return false;
							break;
					}

					switch ($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['name_ascii']) {
						case 'WM/AlbumTitle':
							$ThisFileInfo['asf']['comments']['album'] = $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_ascii'];
							break;

						case 'WM/Genre':
							$ThisFileInfo['asf']['comments']['genre'] = $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_ascii'];
							require_once(GETID3_INCLUDEPATH.'getid3.id3.php');
							$CleanedGenre = LookupGenre(LookupGenre($ThisFileInfo['asf']['comments']['genre'], true)); // convert to standard GenreID and back to standard spelling/capitalization
							if ($CleanedGenre != $ThisFileInfo['asf']['comments']['genre']) {
								$ThisFileInfo['asf']['comments']['genre'] = $CleanedGenre;
							}
							break;

						case 'WM/TrackNumber':
							$ThisFileInfo['asf']['comments']['track'] = $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value'];
							break;

						case 'WM/Track':
							if (empty($ThisFileInfo['asf']['comments']['track'])) {
								$ThisFileInfo['asf']['comments']['track'] = 1 + $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value'];
							}
							break;

						case 'WM/Year':
							$ThisFileInfo['asf']['comments']['year'] = $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value_ascii'];
							break;

						case 'IsVBR':
							if ($ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value']) {
								$ThisFileInfo['audio']['bitrate_mode'] = 'vbr';
								$ThisFileInfo['video']['bitrate_mode'] = 'vbr';
							}
							break;

						case 'ID3':
							if ($tempfilehandle = tmpfile()) {
								require_once(GETID3_INCLUDEPATH.'getid3.id3v2.php');
								$tempThisfileInfo = array();
								fwrite($tempfilehandle, $ThisFileInfo['asf']['extended_content_description']['content_descriptors'][$ExtendedContentDescriptorsCounter]['value']);
								getID3v2Filepointer($tempfilehandle, $tempThisfileInfo);
								fclose($tempfilehandle);
								$ThisFileInfo['id3v2'] = $tempThisfileInfo['id3v2'];
							}
							break;

						default:
							// do nothing
							break;
					}

					// ASF tags have highest priority
					if (!empty($ThisFileInfo['asf']['comments'])) {
						CopyFormatCommentsToRootComments($ThisFileInfo['asf']['comments'], $ThisFileInfo, true, true, true);
					}

				}
				break;

			case ASF_Stream_Bitrate_Properties_Object:
				// Stream Bitrate Properties Object: (optional, one only)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Stream Bitrate Properties object - ASF_Stream_Bitrate_Properties_Object
				// Object Size                  QWORD        64              // size of Extended Content Description object, including 26 bytes of Stream Bitrate Properties Object header
				// Bitrate Records Count        WORD         16              // number of records in Bitrate Records
				// Bitrate Records              array of:    variable        //
				// * Flags                      WORD         16              //
				// * * Stream Number            bits         7  (0x007F)     // number of this stream
				// * * Reserved                 bits         9  (0xFF80)     // hardcoded: 0
				// * Average Bitrate            DWORD        32              // in bits per second

				$ThisFileInfo['asf']['stream_bitrate_properties']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['stream_bitrate_properties']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['stream_bitrate_properties']['objectsize']                = $NextObjectSize;
				$ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records_count']     = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
				$offset += 2;
				for ($BitrateRecordsCounter = 0; $BitrateRecordsCounter < $ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records_count']; $BitrateRecordsCounter++) {
					$ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['flags_raw'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['flags']['stream_number'] = $ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['flags_raw'] & 0x007F;
					$ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['bitrate'] = LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
				}
				break;

			case ASF_Padding_Object:
				// Padding Object: (optional)
				// Field Name                   Field Type   Size (bits)
				// Object ID                    GUID         128             // GUID for Padding object - ASF_Padding_Object
				// Object Size                  QWORD        64              // size of Padding object, including 24 bytes of Stream Bitrate Properties Object header
				// Padding Data                 BYTESTREAM   variable        // ignore
				$ThisFileInfo['asf']['padding_object']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['padding_object']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['padding_object']['objectsize']                = $NextObjectSize;
				$ThisFileInfo['asf']['padding_object']['padding_length']            = $ThisFileInfo['asf']['padding_object']['objectsize'] - 16 - 8;
				$ThisFileInfo['asf']['padding_object']['padding']                   = substr($ASFHeaderData, $offset, $ThisFileInfo['asf']['padding_object']['padding_length']);
				break;

			default:
				// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
				if (GUIDname($NextObjectGUIDtext)) {
					$ThisFileInfo['warning'] .= "\n".'unhandled GUID "'.GUIDname($NextObjectGUIDtext).'" {'.$NextObjectGUIDtext.'} in ASF header at offset '.($offset - 16 - 8);
				} else {
					$ThisFileInfo['warning'] .= "\n".'unknown GUID {'.$NextObjectGUIDtext.'} in ASF header at offset '.($offset - 16 - 8);
				}
				$offset += ($NextObjectSize - 16 - 8);
				break;
		}
    }
    if (isset($ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records_count'])) {
		$ASFbitrateAudio = 0;
		$ASFbitrateVideo = 0;
		for ($BitrateRecordsCounter = 0; $BitrateRecordsCounter < $ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records_count']; $BitrateRecordsCounter++) {
			if (isset($ThisFileInfo['asf']['codec_list']['codec_entries'][$BitrateRecordsCounter])) {
				switch ($ThisFileInfo['asf']['codec_list']['codec_entries'][$BitrateRecordsCounter]['type_raw']) {
					case 1:
						$ASFbitrateVideo += $ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['bitrate'];
						break;

					case 2:
						$ASFbitrateAudio += $ThisFileInfo['asf']['stream_bitrate_properties']['bitrate_records'][$BitrateRecordsCounter]['bitrate'];
						break;

					default:
						// do nothing
						break;
				}
			}
		}
		if ($ASFbitrateAudio > 0) {
			$ThisFileInfo['audio']['bitrate']     = $ASFbitrateAudio;
		}
		if ($ASFbitrateVideo > 0) {
			$ThisFileInfo['video']['bitrate']     = $ASFbitrateVideo;
		}
    }
    if (isset($ThisFileInfo['asf']['stream_properties_object']) && is_array($ThisFileInfo['asf']['stream_properties_object'])) {
		require_once(GETID3_INCLUDEPATH.'getid3.riff.php');
		foreach ($ThisFileInfo['asf']['stream_properties_object'] as $streamnumber => $streamdata) {
			switch ($streamdata['stream_type']) {
				case ASF_Audio_Media:
					// Field Name                   Field Type   Size (bits)
					// Codec ID / Format Tag        WORD         16              // unique ID of audio codec - defined as wFormatTag field of WAVEFORMATEX structure
					// Number of Channels           WORD         16              // number of channels of audio - defined as nChannels field of WAVEFORMATEX structure
					// Samples Per Second           DWORD        32              // in Hertz - defined as nSamplesPerSec field of WAVEFORMATEX structure
					// Average number of Bytes/sec  DWORD        32              // bytes/sec of audio stream  - defined as nAvgBytesPerSec field of WAVEFORMATEX structure
					// Block Alignment              WORD         16              // block size in bytes of audio codec - defined as nBlockAlign field of WAVEFORMATEX structure
					// Bits per sample              WORD         16              // bits per sample of mono data. set to zero for variable bitrate codecs. defined as wBitsPerSample field of WAVEFORMATEX structure
					// Codec Specific Data Size     WORD         16              // size in bytes of Codec Specific Data buffer - defined as cbSize field of WAVEFORMATEX structure
					// Codec Specific Data          BYTESTREAM   variable        // array of codec-specific data bytes


					$audiomediaoffset = 0;

					require_once(GETID3_INCLUDEPATH.'getid3.riff.php');
					$ThisFileInfo['asf']['audio_media'][$streamnumber] = RIFFparseWAVEFORMATex(substr($streamdata['type_specific_data'], $audiomediaoffset, 16));
					$audiomediaoffset += 16;

					if (!isset($ThisFileInfo['audio']['bitrate'])) {
						$ThisFileInfo['audio']['bitrate']                                      = $ThisFileInfo['asf']['audio_media'][$streamnumber]['bytes_sec'] * 8;
					}
					$ThisFileInfo['asf']['audio_media'][$streamnumber]['codec_data_size'] = LittleEndian2Int(substr($streamdata['type_specific_data'], $audiomediaoffset, 2));
					$audiomediaoffset += 2;
					$ThisFileInfo['asf']['audio_media'][$streamnumber]['codec_data']      = substr($streamdata['type_specific_data'], $audiomediaoffset, $ThisFileInfo['asf']['audio_media'][$streamnumber]['codec_data_size']);
					$audiomediaoffset += $ThisFileInfo['asf']['audio_media'][$streamnumber]['codec_data_size'];
					break;

				case ASF_Video_Media:
					// Field Name                   Field Type   Size (bits)
					// Encoded Image Width          DWORD        32              // width of image in pixels
					// Encoded Image Height         DWORD        32              // height of image in pixels
					// Reserved Flags               BYTE         8               // hardcoded: 0x02
					// Format Data Size             WORD         16              // size of Format Data field in bytes
					// Format Data                  array of:    variable        //
					// * Format Data Size           DWORD        32              // number of bytes in Format Data field, in bytes - defined as biSize field of BITMAPINFOHEADER structure
					// * Image Width                LONG         32              // width of encoded image in pixels - defined as biWidth field of BITMAPINFOHEADER structure
					// * Image Height               LONG         32              // height of encoded image in pixels - defined as biHeight field of BITMAPINFOHEADER structure
					// * Reserved                   WORD         16              // hardcoded: 0x0001 - defined as biPlanes field of BITMAPINFOHEADER structure
					// * Bits Per Pixel Count       WORD         16              // bits per pixel - defined as biBitCount field of BITMAPINFOHEADER structure
					// * Compression ID             FOURCC       32              // fourcc of video codec - defined as biCompression field of BITMAPINFOHEADER structure
					// * Image Size                 DWORD        32              // image size in bytes - defined as biSizeImage field of BITMAPINFOHEADER structure
					// * Horizontal Pixels / Meter  DWORD        32              // horizontal resolution of target device in pixels per meter - defined as biXPelsPerMeter field of BITMAPINFOHEADER structure
					// * Vertical Pixels / Meter    DWORD        32              // vertical resolution of target device in pixels per meter - defined as biYPelsPerMeter field of BITMAPINFOHEADER structure
					// * Colors Used Count          DWORD        32              // number of color indexes in the color table that are actually used - defined as biClrUsed field of BITMAPINFOHEADER structure
					// * Important Colors Count     DWORD        32              // number of color index required for displaying bitmap. if zero, all colors are required. defined as biClrImportant field of BITMAPINFOHEADER structure
					// * Codec Specific Data        BYTESTREAM   variable        // array of codec-specific data bytes

					$videomediaoffset = 0;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['image_width']                     = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['image_height']                    = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['flags']                           = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 1));
					$videomediaoffset += 1;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data_size']                = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
					$videomediaoffset += 2;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['format_data_size'] = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['image_width']      = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['image_height']     = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['reserved']         = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
					$videomediaoffset += 2;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['bits_per_pixel']   = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
					$videomediaoffset += 2;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['codec_fourcc']     = substr($streamdata['type_specific_data'], $videomediaoffset, 4);
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['image_size']       = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['horizontal_pels']  = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['vertical_pels']    = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['colors_used']      = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['colors_important'] = LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
					$videomediaoffset += 4;
					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['codec_data']       = substr($streamdata['type_specific_data'], $videomediaoffset);


					$ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['codec'] = RIFFfourccLookup($ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['codec_fourcc']);
					$ThisFileInfo['video']['codec']                                            = $ThisFileInfo['asf']['video_media'][$streamnumber]['format_data']['codec'];
					$ThisFileInfo['video']['resolution_x']                                     = $ThisFileInfo['asf']['video_media'][$streamnumber]['image_width'];
					$ThisFileInfo['video']['resolution_y']                                     = $ThisFileInfo['asf']['video_media'][$streamnumber]['image_height'];
					break;

				default:
					break;
			}
		}
    }

    while (ftell($fd) < $ThisFileInfo['avdataend']) {
		$NextObjectDataHeader = fread($fd, 24);
		$offset = 0;
		$NextObjectGUID = substr($NextObjectDataHeader, 0, 16);
		$offset += 16;
		$NextObjectGUIDtext = BytestringToGUID($NextObjectGUID);
		$NextObjectSize = LittleEndian2Int(substr($NextObjectDataHeader, $offset, 8));
		$offset += 8;

		switch ($NextObjectGUID) {
			case ASF_Data_Object:
				// Data Object: (mandatory, one only)
				// Field Name                       Field Type   Size (bits)
				// Object ID                        GUID         128             // GUID for Data object - ASF_Data_Object
				// Object Size                      QWORD        64              // size of Data object, including 50 bytes of Data Object header. may be 0 if FilePropertiesObject.BroadcastFlag == 1
				// File ID                          GUID         128             // unique identifier. identical to File ID field in Header Object
				// Total Data Packets               QWORD        64              // number of Data Packet entries in Data Object. invalid if FilePropertiesObject.BroadcastFlag == 1
				// Reserved                         WORD         16              // hardcoded: 0x0101

				$DataObjectData = $NextObjectDataHeader.fread($fd, 50 - 24);
				$offset = 24;

				$ThisFileInfo['asf']['data_object']['objectid']           = $NextObjectGUID;
				$ThisFileInfo['asf']['data_object']['objectid_guid']      = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['data_object']['objectsize']         = $NextObjectSize;

				$ThisFileInfo['asf']['data_object']['fileid']             = substr($DataObjectData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['data_object']['fileid_guid']        = BytestringToGUID($ThisFileInfo['asf']['data_object']['fileid']);
				$ThisFileInfo['asf']['data_object']['total_data_packets'] = LittleEndian2Int(substr($DataObjectData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['data_object']['reserved']           = LittleEndian2Int(substr($DataObjectData, $offset, 2));
				$offset += 2;
				if ($ThisFileInfo['asf']['data_object']['reserved'] != 0x0101) {
					$ThisFileInfo['warning'] .= "\n".'data_object.reserved ('.PrintHexBytes($ThisFileInfo['asf']['data_object']['reserved']).') does not match expected value of "0x0101"';
					//return false;
					break;
				}

				// Data Packets                     array of:    variable        //
				// * Error Correction Flags         BYTE         8               //
				// * * Error Correction Data Length bits         4               // if Error Correction Length Type == 00, size of Error Correction Data in bytes, else hardcoded: 0000
				// * * Opaque Data Present          bits         1               //
				// * * Error Correction Length Type bits         2               // number of bits for size of the error correction data. hardcoded: 00
				// * * Error Correction Present     bits         1               // If set, use Opaque Data Packet structure, else use Payload structure
				// * Error Correction Data

				$ThisFileInfo['avdataoffset'] = ftell($fd);
				fseek($fd, ($ThisFileInfo['asf']['data_object']['objectsize'] - 50), SEEK_CUR); // skip actual audio/video data
				$ThisFileInfo['avdataend'] = ftell($fd);
				break;

			case ASF_Simple_Index_Object:
				// Simple Index Object: (optional, recommended, one per video stream)
				// Field Name                       Field Type   Size (bits)
				// Object ID                        GUID         128             // GUID for Simple Index object - ASF_Data_Object
				// Object Size                      QWORD        64              // size of Simple Index object, including 56 bytes of Simple Index Object header
				// File ID                          GUID         128             // unique identifier. may be zero or identical to File ID field in Data Object and Header Object
				// Index Entry Time Interval        QWORD        64              // interval between index entries in 100-nanosecond units
				// Maximum Packet Count             DWORD        32              // maximum packet count for all index entries
				// Index Entries Count              DWORD        32              // number of Index Entries structures
				// Index Entries                    array of:    variable        //
				// * Packet Number                  DWORD        32              // number of the Data Packet associated with this index entry
				// * Packet Count                   WORD         16              // number of Data Packets to sent at this index entry

				$SimpleIndexObjectData = $NextObjectDataHeader.fread($fd, 56 - 24);
				$offset = 24;

				$ThisFileInfo['asf']['simple_index_object']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['simple_index_object']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['simple_index_object']['objectsize']                = $NextObjectSize;

				$ThisFileInfo['asf']['simple_index_object']['fileid']                    =                  substr($SimpleIndexObjectData, $offset, 16);
				$offset += 16;
				$ThisFileInfo['asf']['simple_index_object']['fileid_guid']               = BytestringToGUID($ThisFileInfo['asf']['simple_index_object']['fileid']);
				$ThisFileInfo['asf']['simple_index_object']['index_entry_time_interval'] = LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 8));
				$offset += 8;
				$ThisFileInfo['asf']['simple_index_object']['maximum_packet_count']      = LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['simple_index_object']['index_entries_count']       = LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 4));
				$offset += 4;

				$IndexEntriesData = $SimpleIndexObjectData.fread($fd, 6 * $ThisFileInfo['asf']['simple_index_object']['index_entries_count']);
				for ($IndexEntriesCounter = 0; $IndexEntriesCounter < $ThisFileInfo['asf']['simple_index_object']['index_entries_count']; $IndexEntriesCounter++) {
					$ThisFileInfo['asf']['simple_index_object']['index_entries'][$IndexEntriesCounter]['packet_number'] = LittleEndian2Int(substr($IndexEntriesData, $offset, 4));
					$offset += 4;
					$ThisFileInfo['asf']['simple_index_object']['index_entries'][$IndexEntriesCounter]['packet_count']  = LittleEndian2Int(substr($IndexEntriesData, $offset, 4));
					$offset += 2;
				}

				break;

			case ASF_Index_Object:
				// 6.2 ASF top-level Index Object (optional but recommended when appropriate, 0 or 1)
				// Field Name                       Field Type   Size (bits)
				// Object ID                        GUID         128             // GUID for the Index Object - ASF_Index_Object
				// Object Size                      QWORD        64              // Specifies the size, in bytes, of the Index Object, including at least 34 bytes of Index Object header
				// Index Entry Time Interval        DWORD        32              // Specifies the time interval between each index entry in ms.
				// Index Specifiers Count           WORD         16              // Specifies the number of Index Specifiers structures in this Index Object.
				// Index Blocks Count               DWORD        32              // Specifies the number of Index Blocks structures in this Index Object.

				// Index Entry Time Interval        DWORD        32              // Specifies the time interval between index entries in milliseconds.  This value cannot be 0.
				// Index Specifiers Count           WORD         16              // Specifies the number of entries in the Index Specifiers list.  Valid values are 1 and greater.
				// Index Specifiers                 array of:    varies          //
				// * Stream Number                  WORD         16              // Specifies the stream number that the Index Specifiers refer to. Valid values are between 1 and 127.
				// * Index Type                     WORD         16              // Specifies Index Type values as follows:
																				//   1 = Nearest Past Data Packet - indexes point to the data packet whose presentation time is closest to the index entry time.
																				//   2 = Nearest Past Media Object - indexes point to the closest data packet containing an entire object or first fragment of an object.
																				//   3 = Nearest Past Cleanpoint. - indexes point to the closest data packet containing an entire object (or first fragment of an object) that has the Cleanpoint Flag set.
																				//   Nearest Past Cleanpoint is the most common type of index.
				// Index Entry Count                DWORD        32              // Specifies the number of Index Entries in the block.
				// * Block Positions                QWORD        varies          // Specifies a list of byte offsets of the beginnings of the blocks relative to the beginning of the first Data Packet (i.e., the beginning of the Data Object + 50 bytes). The number of entries in this list is specified by the value of the Index Specifiers Count field. The order of those byte offsets is tied to the order in which Index Specifiers are listed.
				// * Index Entries                  array of:    varies          //
				// * * Offsets                      DWORD        varies          // An offset value of 0xffffffff indicates an invalid offset value

				$ASFIndexObjectData = $NextObjectDataHeader.fread($fd, 34 - 24);
				$offset = 24;

				$ThisFileInfo['asf']['asf_index_object']['objectid']                  = $NextObjectGUID;
				$ThisFileInfo['asf']['asf_index_object']['objectid_guid']             = $NextObjectGUIDtext;
				$ThisFileInfo['asf']['asf_index_object']['objectsize']                = $NextObjectSize;

				$ThisFileInfo['asf']['asf_index_object']['entry_time_interval']       = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
				$offset += 4;
				$ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']    = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
				$offset += 2;
				$ThisFileInfo['asf']['asf_index_object']['index_blocks_count']        = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
				$offset += 4;

				$ASFIndexObjectData .= fread($fd, 4 * $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']);
				for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']; $IndexSpecifiersCounter++) {
					$IndexSpecifierStreamNumber = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['asf_index_object']['index_specifiers'][$IndexSpecifiersCounter]['stream_number']   = $IndexSpecifierStreamNumber;
					$ThisFileInfo['asf']['asf_index_object']['index_specifiers'][$IndexSpecifiersCounter]['index_type']      = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
					$offset += 2;
					$ThisFileInfo['asf']['asf_index_object']['index_specifiers'][$IndexSpecifiersCounter]['index_type_text'] = ASFIndexObjectIndexTypeLookup($ThisFileInfo['asf']['asf_index_object']['index_specifiers'][$IndexSpecifiersCounter]['index_type']);
				}

				$ASFIndexObjectData .= fread($fd, 4);
				$ThisFileInfo['asf']['asf_index_object']['index_entry_count'] = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
				$offset += 4;

				$ASFIndexObjectData .= fread($fd, 8 * $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']);
				for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']; $IndexSpecifiersCounter++) {
					$ThisFileInfo['asf']['asf_index_object']['block_positions'][$IndexSpecifiersCounter] = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 8));
					$offset += 8;
				}

				$ASFIndexObjectData .= fread($fd, 4 * $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count'] * $ThisFileInfo['asf']['asf_index_object']['index_entry_count']);
				for ($IndexEntryCounter = 0; $IndexEntryCounter < $ThisFileInfo['asf']['asf_index_object']['index_entry_count']; $IndexEntryCounter++) {
					for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $ThisFileInfo['asf']['asf_index_object']['index_specifiers_count']; $IndexSpecifiersCounter++) {
						$ThisFileInfo['asf']['asf_index_object']['offsets'][$IndexSpecifiersCounter][$IndexEntryCounter] = LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
						$offset += 4;
					}
				}
				break;


			default:
				// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
				if (GUIDname($NextObjectGUIDtext)) {
					$ThisFileInfo['warning'] .= "\n".'unhandled GUID "'.GUIDname($NextObjectGUIDtext).'" {'.$NextObjectGUIDtext.'} in ASF body at offset '.($offset - 16 - 8);
				} else {
					$ThisFileInfo['warning'] .= "\n".'unknown GUID {'.$NextObjectGUIDtext.'} in ASF body at offset '.(ftell($fd) - 16 - 8);
				}
				fseek($fd, ($NextObjectSize - 16 - 8), SEEK_CUR);
				break;
		}
    }

    if (isset($ThisFileInfo['asf']['codec_list']['codec_entries']) && is_array($ThisFileInfo['asf']['codec_list']['codec_entries'])) {
		foreach ($ThisFileInfo['asf']['codec_list']['codec_entries'] as $streamnumber => $streamdata) {
			switch ($streamdata['information']) {
				case 'WMV1':
				case 'WMV2':
				case 'WMV3':
					$ThisFileInfo['video']['dataformat'] = 'wmv';
					$ThisFileInfo['mime_type']        = 'video/x-ms-wmv';
					break;

				case 'MP42':
				case 'MP43':
				case 'MP4S':
				case 'mp4s':
					$ThisFileInfo['video']['dataformat'] = 'asf';
					$ThisFileInfo['mime_type']        = 'video/x-ms-asf';
					break;

				default:
					switch ($streamdata['type_raw']) {
						case 1:
							if (strstr($streamdata['name_ascii'], 'Windows Media')) {
								$ThisFileInfo['video']['dataformat'] = 'wmv';
								if ($ThisFileInfo['mime_type'] == 'video/x-ms-asf') {
									$ThisFileInfo['mime_type'] = 'video/x-ms-wmv';
								}
							}
							break;

						case 2:
							if (strstr($streamdata['name_ascii'], 'Windows Media')) {
								$ThisFileInfo['audio']['dataformat'] = 'wma';
								if ($ThisFileInfo['mime_type'] == 'video/x-ms-asf') {
									$ThisFileInfo['mime_type'] = 'audio/x-ms-wma';
								}
							}
							break;

					}
					break;
			}
		}
    }

    if (!empty($ThisFileInfo['audio']) && empty($ThisFileInfo['audio']['dataformat'])) {
		$ThisFileInfo['audio']['dataformat'] = 'asf';
    }
    if (!empty($ThisFileInfo['video']) && empty($ThisFileInfo['video']['dataformat'])) {
		$ThisFileInfo['video']['dataformat'] = 'asf';
    }

    if (isset($ThisFileInfo['asf']['codec_list']['codec_entries'])) {
		foreach ($ThisFileInfo['asf']['codec_list']['codec_entries'] as $streamnumber => $streamdata) {
			switch ($streamdata['type_raw']) {

				case 1: // video
					$ThisFileInfo['video']['encoder'] = $ThisFileInfo['asf']['codec_list']['codec_entries'][$streamnumber]['name_ascii'];
					break;

				case 2: // audio
					$ThisFileInfo['audio']['encoder'] = $ThisFileInfo['asf']['codec_list']['codec_entries'][$streamnumber]['name_ascii'];
					$ThisFileInfo['audio']['codec']   = $ThisFileInfo['audio']['encoder'];
					break;

				default:
					$ThisFileInfo['warning'] .= "\n".'Unknown streamtype: [codec_list][codec_entries]['.$streamnumber.'][type_raw] == '.$streamdata['type_raw'];
					break;

			}
		}
    }

    return true;
}

function ASFCodecListObjectTypeLookup($CodecListType) {
    static $ASFCodecListObjectTypeLookup = array();
    if (empty($ASFCodecListObjectTypeLookup)) {
		$ASFCodecListObjectTypeLookup[0x0001] = 'Video Codec';
		$ASFCodecListObjectTypeLookup[0x0002] = 'Audio Codec';
		$ASFCodecListObjectTypeLookup[0xFFFF] = 'Unknown Codec';
    }

    return (isset($ASFCodecListObjectTypeLookup[$CodecListType]) ? $ASFCodecListObjectTypeLookup[$CodecListType] : 'Invalid Codec Type');
}

function KnownGUIDs() {
    static $GUIDarray = array();
    if (empty($GUIDarray)) {
		$GUIDarray['ASF_Extended_Stream_Properties_Object']   = '14E6A5CB-C672-4332-8399-A96952065B5A';
		$GUIDarray['ASF_Padding_Object']                      = '1806D474-CADF-4509-A4BA-9AABCB96AAE8';
		$GUIDarray['ASF_Payload_Ext_Syst_Pixel_Aspect_Ratio'] = '1B1EE554-F9EA-4BC8-821A-376B74E4C4B8';
		$GUIDarray['ASF_Script_Command_Object']               = '1EFB1A30-0B62-11D0-A39B-00A0C90348F6';
		$GUIDarray['ASF_No_Error_Correction']                 = '20FB5700-5B55-11CF-A8FD-00805F5C442B';
		$GUIDarray['ASF_Content_Branding_Object']             = '2211B3FA-BD23-11D2-B4B7-00A0C955FC6E';
		$GUIDarray['ASF_Content_Encryption_Object']           = '2211B3FB-BD23-11D2-B4B7-00A0C955FC6E';
		$GUIDarray['ASF_Digital_Signature_Object']            = '2211B3FC-BD23-11D2-B4B7-00A0C955FC6E';
		$GUIDarray['ASF_Extended_Content_Encryption_Object']  = '298AE614-2622-4C17-B935-DAE07EE9289C';
		$GUIDarray['ASF_Simple_Index_Object']                 = '33000890-E5B1-11CF-89F4-00A0C90349CB';
		$GUIDarray['ASF_Degradable_JPEG_Media']               = '35907DE0-E415-11CF-A917-00805F5C442B';
		$GUIDarray['ASF_Payload_Extension_System_Timecode']   = '399595EC-8667-4E2D-8FDB-98814CE76C1E';
		$GUIDarray['ASF_Binary_Media']                        = '3AFB65E2-47EF-40F2-AC2C-70A90D71D343';
		$GUIDarray['ASF_Timecode_Index_Object']               = '3CB73FD0-0C4A-4803-953D-EDF7B6228F0C';
		$GUIDarray['ASF_Metadata_Library_Object']             = '44231C94-9498-49D1-A141-1D134E457054';
		$GUIDarray['ASF_Reserved_3']                          = '4B1ACBE3-100B-11D0-A39B-00A0C90348F6';
		$GUIDarray['ASF_Reserved_4']                          = '4CFEDB20-75F6-11CF-9C0F-00A0C90349CB';
		$GUIDarray['ASF_Command_Media']                       = '59DACFC0-59E6-11D0-A3AC-00A0C90348F6';
		$GUIDarray['ASF_Header_Extension_Object']             = '5FBF03B5-A92E-11CF-8EE3-00C00C205365';
		$GUIDarray['ASF_Media_Object_Index_Parameters_Obj']   = '6B203BAD-3F11-4E84-ACA8-D7613DE2CFA7';
		$GUIDarray['ASF_Header_Object']                       = '75B22630-668E-11CF-A6D9-00AA0062CE6C';
		$GUIDarray['ASF_Content_Description_Object']          = '75B22633-668E-11CF-A6D9-00AA0062CE6C';
		$GUIDarray['ASF_Error_Correction_Object']             = '75B22635-668E-11CF-A6D9-00AA0062CE6C';
		$GUIDarray['ASF_Data_Object']                         = '75B22636-668E-11CF-A6D9-00AA0062CE6C';
		$GUIDarray['ASF_Web_Stream_Media_Subtype']            = '776257D4-C627-41CB-8F81-7AC7FF1C40CC';
		$GUIDarray['ASF_Stream_Bitrate_Properties_Object']    = '7BF875CE-468D-11D1-8D82-006097C9A2B2';
		$GUIDarray['ASF_Language_List_Object']                = '7C4346A9-EFE0-4BFC-B229-393EDE415C85';
		$GUIDarray['ASF_Codec_List_Object']                   = '86D15240-311D-11D0-A3A4-00A0C90348F6';
		$GUIDarray['ASF_Reserved_2']                          = '86D15241-311D-11D0-A3A4-00A0C90348F6';
		$GUIDarray['ASF_File_Properties_Object']              = '8CABDCA1-A947-11CF-8EE4-00C00C205365';
		$GUIDarray['ASF_File_Transfer_Media']                 = '91BD222C-F21C-497A-8B6D-5AA86BFC0185';
		$GUIDarray['ASF_Old_RTP_Extension_Data']              = '96800C63-4C94-11D1-837B-0080C7A37F95';
		$GUIDarray['ASF_Advanced_Mutual_Exclusion_Object']    = 'A08649CF-4775-4670-8A16-6E35357566CD';
		$GUIDarray['ASF_Bandwidth_Sharing_Object']            = 'A69609E6-517B-11D2-B6AF-00C04FD908E9';
		$GUIDarray['ASF_Reserved_1']                          = 'ABD3D211-A9BA-11cf-8EE6-00C00C205365';
		$GUIDarray['ASF_Bandwidth_Sharing_Exclusive']         = 'AF6060AA-5197-11D2-B6AF-00C04FD908E9';
		$GUIDarray['ASF_Bandwidth_Sharing_Partial']           = 'AF6060AB-5197-11D2-B6AF-00C04FD908E9';
		$GUIDarray['ASF_JFIF_Media']                          = 'B61BE100-5B4E-11CF-A8FD-00805F5C442B';
		$GUIDarray['ASF_Stream_Properties_Object']            = 'B7DC0791-A9B7-11CF-8EE6-00C00C205365';
		$GUIDarray['ASF_Video_Media']                         = 'BC19EFC0-5B4D-11CF-A8FD-00805F5C442B';
		$GUIDarray['ASF_Audio_Spread']                        = 'BFC3CD50-618F-11CF-8BB2-00AA00B4E220';
		$GUIDarray['ASF_Metadata_Object']                     = 'C5F8CBEA-5BAF-4877-8467-AA8C44FA4CCA';
		$GUIDarray['ASF_Payload_Ext_Syst_Sample_Duration']    = 'C6BD9450-867F-4907-83A3-C77921B733AD';
		$GUIDarray['ASF_Group_Mutual_Exclusion_Object']       = 'D1465A40-5A79-4338-B71B-E36B8FD6C249';
		$GUIDarray['ASF_Extended_Content_Description_Object'] = 'D2D0A440-E307-11D2-97F0-00A0C95EA850';
		$GUIDarray['ASF_Stream_Prioritization_Object']        = 'D4FED15B-88D3-454F-81F0-ED5C45999E24';
		$GUIDarray['ASF_Payload_Ext_System_Content_Type']     = 'D590DC20-07BC-436C-9CF7-F3BBFBF1A4DC';
		$GUIDarray['ASF_Old_File_Properties_Object']          = 'D6E229D0-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_ASF_Header_Object']               = 'D6E229D1-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_ASF_Data_Object']                 = 'D6E229D2-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Index_Object']                        = 'D6E229D3-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Stream_Properties_Object']        = 'D6E229D4-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Content_Description_Object']      = 'D6E229D5-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Script_Command_Object']           = 'D6E229D6-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Marker_Object']                   = 'D6E229D7-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Component_Download_Object']       = 'D6E229D8-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Stream_Group_Object']             = 'D6E229D9-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Scalable_Object']                 = 'D6E229DA-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Prioritization_Object']           = 'D6E229DB-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Bitrate_Mutual_Exclusion_Object']     = 'D6E229DC-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Inter_Media_Dependency_Object']   = 'D6E229DD-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Rating_Object']                   = 'D6E229DE-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Index_Parameters_Object']             = 'D6E229DF-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Color_Table_Object']              = 'D6E229E0-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Language_List_Object']            = 'D6E229E1-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Audio_Media']                     = 'D6E229E2-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Video_Media']                     = 'D6E229E3-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Image_Media']                     = 'D6E229E4-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Timecode_Media']                  = 'D6E229E5-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Text_Media']                      = 'D6E229E6-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_MIDI_Media']                      = 'D6E229E7-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Command_Media']                   = 'D6E229E8-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_No_Error_Concealment']            = 'D6E229EA-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Scrambled_Audio']                 = 'D6E229EB-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_No_Color_Table']                  = 'D6E229EC-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_SMPTE_Time']                      = 'D6E229ED-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_ASCII_Text']                      = 'D6E229EE-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Unicode_Text']                    = 'D6E229EF-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_HTML_Text']                       = 'D6E229F0-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_URL_Command']                     = 'D6E229F1-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Filename_Command']                = 'D6E229F2-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_ACM_Codec']                       = 'D6E229F3-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_VCM_Codec']                       = 'D6E229F4-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_QuickTime_Codec']                 = 'D6E229F5-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_DirectShow_Transform_Filter']     = 'D6E229F6-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_DirectShow_Rendering_Filter']     = 'D6E229F7-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_No_Enhancement']                  = 'D6E229F8-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Unknown_Enhancement_Type']        = 'D6E229F9-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Temporal_Enhancement']            = 'D6E229FA-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Spatial_Enhancement']             = 'D6E229FB-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Quality_Enhancement']             = 'D6E229FC-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Number_of_Channels_Enhancement']  = 'D6E229FD-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Frequency_Response_Enhancement']  = 'D6E229FE-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Media_Object']                    = 'D6E229FF-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Mutex_Language']                      = 'D6E22A00-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Mutex_Bitrate']                       = 'D6E22A01-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Mutex_Unknown']                       = 'D6E22A02-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_ASF_Placeholder_Object']          = 'D6E22A0E-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Old_Data_Unit_Extension_Object']      = 'D6E22A0F-35DA-11D1-9034-00A0C90349BE';
		$GUIDarray['ASF_Web_Stream_Format']                   = 'DA1E6B13-8359-4050-B398-388E965BF00C';
		$GUIDarray['ASF_Payload_Ext_System_File_Name']        = 'E165EC0E-19ED-45D7-B4A7-25CBD1E28E9B';
		$GUIDarray['ASF_Marker_Object']                       = 'F487CD01-A951-11CF-8EE6-00C00C205365';
		$GUIDarray['ASF_Timecode_Index_Parameters_Object']    = 'F55E496D-9797-4B5D-8C8B-604DFE9BFB24';
		$GUIDarray['ASF_Audio_Media']                         = 'F8699E40-5B4D-11CF-A8FD-00805F5C442B';
		$GUIDarray['ASF_Media_Object_Index_Object']           = 'FEB103F8-12AD-4C64-840F-2A1D2F7AD48C';
		$GUIDarray['ASF_Alt_Extended_Content_Encryption_Obj'] = 'FF889EF1-ADEE-40DA-9E71-98704BB928CE';
    }
    return $GUIDarray;
}

function GUIDname($GUIDstring) {
    static $GUIDarray = array();
    if (empty($GUIDarray)) {
		$GUIDarray = KnownGUIDs();
    }
    return array_search($GUIDstring, $GUIDarray);
}

function ASFIndexObjectIndexTypeLookup($id) {
    static $ASFIndexObjectIndexTypeLookup = array();
    if (empty($ASFIndexObjectIndexTypeLookup)) {
		$ASFIndexObjectIndexTypeLookup[1] = 'Nearest Past Data Packet';
		$ASFIndexObjectIndexTypeLookup[2] = 'Nearest Past Media Object';
		$ASFIndexObjectIndexTypeLookup[3] = 'Nearest Past Cleanpoint';
    }
    return (isset($ASFIndexObjectIndexTypeLookup[$id]) ? $ASFIndexObjectIndexTypeLookup[$id] : 'invalid');
}

?>