<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.id3v2.php - part of getID3()                         //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getID3v2Filepointer($fd, &$ThisFileInfo) {
    //    Overall tag structure:
    //        +-----------------------------+
    //        |      Header (10 bytes)      |
    //        +-----------------------------+
    //        |       Extended Header       |
    //        | (variable length, OPTIONAL) |
    //        +-----------------------------+
    //        |   Frames (variable length)  |
    //        +-----------------------------+
    //        |           Padding           |
    //        | (variable length, OPTIONAL) |
    //        +-----------------------------+
    //        | Footer (10 bytes, OPTIONAL) |
    //        +-----------------------------+

    //    Header
    //        ID3v2/file identifier      "ID3"
    //        ID3v2 version              $04 00
    //        ID3v2 flags                (%ab000000 in v2.2, %abc00000 in v2.3, %abcd0000 in v2.4.x)
    //        ID3v2 size             4 * %0xxxxxxx

    require_once(GETID3_INCLUDEPATH.'getid3.frames.php'); // ID3v2FrameProcessing()

    rewind($fd);
    $header = fread ($fd, 10);
    if (substr($header, 0, 3) == 'ID3') {
		$ThisFileInfo['id3v2']['header'] = true;
		$ThisFileInfo['id3v2']['majorversion'] = ord($header{3});
		$ThisFileInfo['id3v2']['minorversion'] = ord($header{4});
    }

    if (isset($ThisFileInfo['id3v2']['header']) && ($ThisFileInfo['id3v2']['majorversion'] <= 4)) { // this script probably won't correctly parse ID3v2.5.x and above.

		$id3_flags = BigEndian2Bin($header{5});
		switch ($ThisFileInfo['id3v2']['majorversion']) {
			case 2:
				// %ab000000 in v2.2
				$ThisFileInfo['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$ThisFileInfo['id3v2']['flags']['compression'] = $id3_flags{1}; // b - Compression
				break;

			case 3:
				// %abc00000 in v2.3
				$ThisFileInfo['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$ThisFileInfo['id3v2']['flags']['exthead']     = $id3_flags{1}; // b - Extended header
				$ThisFileInfo['id3v2']['flags']['experim']     = $id3_flags{2}; // c - Experimental indicator
				break;

			case 4:
				// %abcd0000 in v2.4
				$ThisFileInfo['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$ThisFileInfo['id3v2']['flags']['exthead']     = $id3_flags{1}; // b - Extended header
				$ThisFileInfo['id3v2']['flags']['experim']     = $id3_flags{2}; // c - Experimental indicator
				$ThisFileInfo['id3v2']['flags']['isfooter']    = $id3_flags{3}; // d - Footer present
				break;
		}

		$ThisFileInfo['id3v2']['headerlength'] = BigEndian2Int(substr($header, 6, 4), 1) + ID3v2HeaderLength($ThisFileInfo['id3v2']['majorversion']);

//    Extended Header
		if (isset($ThisFileInfo['id3v2']['flags']['exthead']) && $ThisFileInfo['id3v2']['flags']['exthead']) {
//            Extended header size   4 * %0xxxxxxx
//            Number of flag bytes       $01
//            Extended Flags             $xx
//            Where the 'Extended header size' is the size of the whole extended header, stored as a 32 bit synchsafe integer.
			$extheader = fread ($fd, 4);
			$ThisFileInfo['id3v2']['extheaderlength'] = BigEndian2Int($extheader, 1);

//            The extended flags field, with its size described by 'number of flag  bytes', is defined as:
//                %0bcd0000
//            b - Tag is an update
//                Flag data length       $00
//            c - CRC data present
//                Flag data length       $05
//                Total frame CRC    5 * %0xxxxxxx
//            d - Tag restrictions
//                Flag data length       $01
			$extheaderflagbytes = fread ($fd, 1);
			$extheaderflags     = fread ($fd, $extheaderflagbytes);
			$id3_exthead_flags = BigEndian2Bin(substr($header, 5, 1));
			$ThisFileInfo['id3v2']['exthead_flags']['update']       = substr($id3_exthead_flags, 1, 1);
			$ThisFileInfo['id3v2']['exthead_flags']['CRC']          = substr($id3_exthead_flags, 2, 1);
			if ($ThisFileInfo['id3v2']['exthead_flags']['CRC']) {
				$extheaderrawCRC = fread ($fd, 5);
				$ThisFileInfo['id3v2']['exthead_flags']['CRC'] = BigEndian2Int($extheaderrawCRC, 1);
			}
			$ThisFileInfo['id3v2']['exthead_flags']['restrictions'] = substr($id3_exthead_flags, 3, 1);
			if ($ThisFileInfo['id3v2']['exthead_flags']['restrictions']) {
				// Restrictions           %ppqrrstt
				$extheaderrawrestrictions = fread ($fd, 1);
				$ThisFileInfo['id3v2']['exthead_flags']['restrictions_tagsize']  = (bindec('11000000') & ord($extheaderrawrestrictions)) >> 6; // p - Tag size restrictions
				$ThisFileInfo['id3v2']['exthead_flags']['restrictions_textenc']  = (bindec('00100000') & ord($extheaderrawrestrictions)) >> 5; // q - Text encoding restrictions
				$ThisFileInfo['id3v2']['exthead_flags']['restrictions_textsize'] = (bindec('00011000') & ord($extheaderrawrestrictions)) >> 3; // r - Text fields size restrictions
				$ThisFileInfo['id3v2']['exthead_flags']['restrictions_imgenc']   = (bindec('00000100') & ord($extheaderrawrestrictions)) >> 2; // s - Image encoding restrictions
				$ThisFileInfo['id3v2']['exthead_flags']['restrictions_imgsize']  = (bindec('00000011') & ord($extheaderrawrestrictions)) >> 0; // t - Image size restrictions
			}
		} // end extended header

//    Frames

//        All ID3v2 frames consists of one frame header followed by one or more
//        fields containing the actual information. The header is always 10
//        bytes and laid out as follows:
//
//        Frame ID      $xx xx xx xx  (four characters)
//        Size      4 * %0xxxxxxx
//        Flags         $xx xx

		$sizeofframes = $ThisFileInfo['id3v2']['headerlength'] - ID3v2HeaderLength($ThisFileInfo['id3v2']['majorversion']);
		if (isset($ThisFileInfo['id3v2']['extheaderlength'])) {
			$sizeofframes -= $ThisFileInfo['id3v2']['extheaderlength'];
		}
		if (isset($ThisFileInfo['id3v2']['flags']['isfooter']) && $ThisFileInfo['id3v2']['flags']['isfooter']) {
			$sizeofframes -= 10; // footer takes last 10 bytes of ID3v2 header, after frame data, before audio
		}
		if ($sizeofframes > 0) {
			$framedata = fread($fd, $sizeofframes); // read all frames from file into $framedata variable

			//    if entire frame data is unsynched, de-unsynch it now (ID3v2.3.x)
			if (isset($ThisFileInfo['id3v2']['flags']['unsynch']) && $ThisFileInfo['id3v2']['flags']['unsynch'] && ($ThisFileInfo['id3v2']['majorversion'] <= 3)) {
				$framedata = DeUnSynchronise($framedata);
			}
			//        [in ID3v2.4.0] Unsynchronisation [S:6.1] is done on frame level, instead
			//        of on tag level, making it easier to skip frames, increasing the streamability
			//        of the tag. The unsynchronisation flag in the header [S:3.1] indicates that
			//        there exists an unsynchronised frame, while the new unsynchronisation flag in
			//        the frame header [S:4.1.2] indicates unsynchronisation.

			$framedataoffset = 10; // how many bytes into the stream - start from after the 10-byte header
			while (isset($framedata) && (strlen($framedata) > 0)) { // cycle through until no more frame data is left to parse
				if ($ThisFileInfo['id3v2']['majorversion'] == 2) {
					// Frame ID  $xx xx xx (three characters)
					// Size      $xx xx xx (24-bit integer)
					// Flags     $xx xx

					$frame_header = substr($framedata, 0, 6); // take next 6 bytes for header
					$framedata    = substr($framedata, 6);    // and leave the rest in $framedata
					$frame_name   = substr($frame_header, 0, 3);
					$frame_size   = BigEndian2Int(substr($frame_header, 3, 3), 0);
					$frame_flags  = ''; // not used for anything, just to avoid E_NOTICEs

				} elseif ($ThisFileInfo['id3v2']['majorversion'] > 2) {

					// Frame ID  $xx xx xx xx (four characters)
					// Size      $xx xx xx xx (32-bit integer in v2.3, 28-bit synchsafe in v2.4+)
					// Flags     $xx xx

					$frame_header = substr($framedata, 0, 10); // take next 10 bytes for header
					$framedata    = substr($framedata, 10);    // and leave the rest in $framedata

					$frame_name = substr($frame_header, 0, 4);
					if ($ThisFileInfo['id3v2']['majorversion'] == 3) {
						$frame_size = BigEndian2Int(substr($frame_header, 4, 4), 0); // 32-bit integer
					} else { // ID3v2.4+
						$frame_size = BigEndian2Int(substr($frame_header, 4, 4), 1); // 32-bit synchsafe integer (28-bit value)
					}

					if ($frame_size < (strlen($framedata) + 4)) {
						$nextFrameID = substr($framedata, $frame_size, 4);
						if (IsValidID3v2FrameName($nextFrameID, $ThisFileInfo['id3v2']['majorversion'])) {
							// next frame is OK
						} elseif (($frame_name == chr(0).'MP3') || ($frame_name == chr(0).chr(0).'MP') || ($frame_name == ' MP3') || ($frame_name == 'MP3e')) {
							// MP3ext known broken frames - "ok" for the purposes of this test
						} elseif (($ThisFileInfo['id3v2']['majorversion'] == 4) && (IsValidID3v2FrameName(substr($framedata, BigEndian2Int(substr($frame_header, 4, 4), 0), 4), 3))) {
							$ThisFileInfo['warning'] .= "\n".'ID3v2 tag written as ID3v2.4, but with non-synchsafe integers (ID3v2.3 style). Older versions of Helium2 (www.helium2.com) is a known culprit of this. Tag has been parsed as ID3v2.3';
							$ThisFileInfo['id3v2']['majorversion'] = 3;
							$frame_size = BigEndian2Int(substr($frame_header, 4, 4), 0); // 32-bit integer
						}
					}


					$frame_flags = BigEndian2Bin(substr($frame_header, 8, 2));
				}

				if ((($ThisFileInfo['id3v2']['majorversion'] == 2) && ($frame_name == chr(0).chr(0).chr(0))) || ($frame_name == chr(0).chr(0).chr(0).chr(0))) {
					// padding encountered

					$ThisFileInfo['id3v2']['padding']['start']  = $framedataoffset;
					$ThisFileInfo['id3v2']['padding']['length'] = strlen($framedata);
					$ThisFileInfo['id3v2']['padding']['valid']  = true;
					for ($i = 0; $i < $ThisFileInfo['id3v2']['padding']['length']; $i++) {
						if (substr($framedata, $i, 1) != chr(0)) {
							$ThisFileInfo['id3v2']['padding']['valid'] = false;
							$ThisFileInfo['id3v2']['padding']['errorpos'] = $ThisFileInfo['id3v2']['padding']['start'] + $i;
							break;
						}
					}
					break; // skip rest of ID3v2 header
				}

				if ($frame_name == 'COM ') {
					$ThisFileInfo['warning'] .= "\n".'error parsing "'.$frame_name.'" ('.$framedataoffset.' bytes into the ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].' tag). (ERROR: !IsValidID3v2FrameName("'.str_replace(chr(0), ' ', $frame_name).'", '.$ThisFileInfo['id3v2']['majorversion'].'))). [Note: this particular error has been known to happen with tags edited by iTunes (versions "X v2.0.3", "v3.0.1" are known-guilty, probably others too)]';
					$frame_name = 'COMM';
				}
				if (($frame_size <= strlen($framedata)) && (IsValidID3v2FrameName($frame_name, $ThisFileInfo['id3v2']['majorversion']))) {

					$ThisFileInfo['id3v2']["$frame_name"]['data']       = substr($framedata, 0, $frame_size);
					$ThisFileInfo['id3v2']["$frame_name"]['datalength'] = CastAsInt($frame_size);
					$ThisFileInfo['id3v2']["$frame_name"]['dataoffset'] = $framedataoffset;
					$framedata = substr($framedata, $frame_size);

					// in getid3.frames.php - this function does all the FrameID-level parsing
					ID3v2FrameProcessing($frame_name, $frame_flags, $ThisFileInfo);

				} else { // invalid frame length or FrameID

					if ($frame_size <= strlen($framedata)) {

						if (IsValidID3v2FrameName(substr($framedata, $frame_size, 4), $ThisFileInfo['id3v2']['majorversion'])) {

							// next frame is valid, just skip the current frame
							$framedata = substr($framedata, $frame_size);
							$InvalidFrameMessageType = 'warning';
							$InvalidFrameMessageText = ' Next frame is valid, skipping current frame.';

						} else {

							// next frame is invalid too, abort processing
							unset($framedata);
							$InvalidFrameMessageType = 'error';
							$InvalidFrameMessageText = ' Next frame is also invalid, aborting processing.';

						}

					} elseif ($frame_size == strlen($framedata)) {

						// this is the last frame, just skip
						$InvalidFrameMessageType = 'warning';
						$InvalidFrameMessageText = ' This was the last frame.';

					} else {

						// next frame is invalid too, abort processing
						unset($framedata);
						$InvalidFrameMessageType = 'error';
						$InvalidFrameMessageText = ' Invalid frame size, aborting.';

					}
					if (!IsValidID3v2FrameName($frame_name, $ThisFileInfo['id3v2']['majorversion'])) {

						switch ($frame_name) {
							case chr(0).chr(0).'MP':
							case chr(0).'MP3':
							case ' MP3':
							case 'MP3e':
							case chr(0).'MP':
							case ' MP':
							case 'MP3':
								$InvalidFrameMessageType = 'warning';
								$ThisFileInfo["$InvalidFrameMessageType"] .= "\n".'error parsing "'.$frame_name.'" ('.$framedataoffset.' bytes into the ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].' tag). (ERROR: !IsValidID3v2FrameName("'.str_replace(chr(0), ' ', $frame_name).'", '.$ThisFileInfo['id3v2']['majorversion'].'))). [Note: this particular error has been known to happen with tags edited by "MP3ext (www.mutschler.de/mp3ext/)"]';
								break;

							default:
								$ThisFileInfo["$InvalidFrameMessageType"] .= "\n".'error parsing "'.$frame_name.'" ('.$framedataoffset.' bytes into the ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].' tag). (ERROR: !IsValidID3v2FrameName("'.str_replace(chr(0), ' ', $frame_name).'", '.$ThisFileInfo['id3v2']['majorversion'].'))).';
								break;
						}

					} elseif ($frame_size > strlen($framedata)){

						$ThisFileInfo["$InvalidFrameMessageType"] .= "\n".'error parsing "'.$frame_name.'" ('.$framedataoffset.' bytes into the ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].' tag). (ERROR: $frame_size ('.$frame_size.') > strlen($framedata) ('.strlen($framedata).')).';

					} else {

						$ThisFileInfo["$InvalidFrameMessageType"] .= "\n".'error parsing "'.$frame_name.'" ('.$framedataoffset.' bytes into the ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].' tag).';

					}
					$ThisFileInfo["$InvalidFrameMessageType"] .= $InvalidFrameMessageText;

				}
				$framedataoffset += ($frame_size + ID3v2HeaderLength($ThisFileInfo['id3v2']['majorversion']));

			}

		}

//    Footer

    //    The footer is a copy of the header, but with a different identifier.
    //        ID3v2 identifier           "3DI"
    //        ID3v2 version              $04 00
    //        ID3v2 flags                %abcd0000
    //        ID3v2 size             4 * %0xxxxxxx

		if (isset($ThisFileInfo['id3v2']['flags']['isfooter']) && $ThisFileInfo['id3v2']['flags']['isfooter']) {
			$footer = fread ($fd, 10);
			if (substr($footer, 0, 3) == '3DI') {
				$ThisFileInfo['id3v2']['footer'] = true;
				$ThisFileInfo['id3v2']['majorversion_footer'] = ord(substr($footer, 3, 1));
				$ThisFileInfo['id3v2']['minorversion_footer'] = ord(substr($footer, 4, 1));
			}
			if ($ThisFileInfo['id3v2']['majorversion_footer'] <= 4) {
				$id3_flags = BigEndian2Bin(substr($footer, 5, 1));
				$ThisFileInfo['id3v2']['flags']['unsynch_footer']  = substr($id3_flags, 0, 1);
				$ThisFileInfo['id3v2']['flags']['extfoot_footer']  = substr($id3_flags, 1, 1);
				$ThisFileInfo['id3v2']['flags']['experim_footer']  = substr($id3_flags, 2, 1);
				$ThisFileInfo['id3v2']['flags']['isfooter_footer'] = substr($id3_flags, 3, 1);

				$ThisFileInfo['id3v2']['footerlength'] = BigEndian2Int(substr($footer, 6, 4), 1);
			}
		} // end footer


		if (isset($ThisFileInfo['id3v2']['comments']['genre'])) {
			foreach ($ThisFileInfo['id3v2']['comments']['genre'] as $key => $value) {
				unset($ThisFileInfo['id3v2']['comments']['genre'][$key]);
				$ThisFileInfo['id3v2']['comments'] = array_merge_noclobber($ThisFileInfo['id3v2']['comments'], ParseID3v2GenreString($value));
			}
		}

		if (isset($ThisFileInfo['id3v2']['comments']['track'])) {
			foreach ($ThisFileInfo['id3v2']['comments']['track'] as $key => $value) {
				if (strstr($value, '/')) {
					list($ThisFileInfo['id3v2']['comments']['track'][$key], $ThisFileInfo['id3v2']['comments']['totaltracks'][$key]) = explode('/', $ThisFileInfo['id3v2']['comments']['track'][$key]);
				}
				// Convert track number to integer (ID3v2 track numbers could be returned as a
				// string ('03' for example) - this will ensure all track numbers are integers
				$ThisFileInfo['id3v2']['comments']['track'][$key] = intval($ThisFileInfo['id3v2']['comments']['track'][$key]);
			}
		}


    } else { // MajorVersion is > 4, or no ID3v2 header present

		if (isset($ThisFileInfo['id3v2']['header'])) { // MajorVersion is > 4
			$ThisFileInfo['error'] .= "\n".'this script only parses up to ID3v2.4.x - this tag is ID3v2.'.$ThisFileInfo['id3v2']['majorversion'].'.'.$ThisFileInfo['id3v2']['minorversion'];
		} else {
			// no ID3v2 header present - this is fine, just don't process anything.
		}
    }

    return true;
}


function ParseID3v2GenreString($genrestring) {
    // Parse genres into arrays of genreName and genreID
    // ID3v2.2.x, ID3v2.3.x: '(21)' or '(4)Eurodisco' or '(51)(39)' or '(55)((I think...)'
    // ID3v2.4.x: '21' $00 'Eurodisco' $00

    require_once(GETID3_INCLUDEPATH.'getid3.id3.php');
    $returnarray = null;
    if (strpos($genrestring, chr(0)) !== false) {
		$unprocessed = trim($genrestring); // trailing nulls will cause an infinite loop.
		$genrestring = '';
		while (strpos($unprocessed, chr(0)) !== false) {
			// convert null-seperated v2.4-format into v2.3 ()-seperated format
			$endpos = strpos($unprocessed, chr(0));
			$genrestring .= '('.substr($unprocessed, 0, $endpos).')';
			$unprocessed = substr($unprocessed, $endpos + 1);
		}
		unset($unprocessed);
    }
    while (strpos($genrestring, '(') !== false) {
		$startpos = strpos($genrestring, '(');
		$endpos   = strpos($genrestring, ')');
		if (substr($genrestring, $startpos + 1, 1) == '(') {
			$genrestring = substr($genrestring, 0, $startpos).substr($genrestring, $startpos + 1);
			$endpos--;
		}
		$element     = substr($genrestring, $startpos + 1, $endpos - ($startpos + 1));
		$genrestring = substr($genrestring, 0, $startpos).substr($genrestring, $endpos + 1);
		if (LookupGenre($element) !== '') { // $element is a valid genre id/abbreviation
			if (!is_array($returnarray['genre']) || !in_array(LookupGenre($element), $returnarray['genre'])) { // avoid duplicate entires
				if (($element == 'CR') && ($element == 'RX')) {
					$returnarray['genreid'][] = $element;
				} else {
					$returnarray['genreid'][] = (int) $element;
				}
				$returnarray['genre'][]   = LookupGenre($element);
			}
		} else {
			if (!is_array($returnarray['genre']) || !in_array($element, $returnarray['genre'])) { // avoid duplicate entires
				$returnarray['genreid'][] = '';
				$returnarray['genre'][]   = $element;
			}
		}
    }
    if ($genrestring) {
		if (!is_array($returnarray['genre']) || !in_array($genrestring, $returnarray['genre'])) { // avoid duplicate entires
			$returnarray['genreid'][] = '';
			$returnarray['genre'][]   = $genrestring;
		}
    }

    return $returnarray;
}

?>