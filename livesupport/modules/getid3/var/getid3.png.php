<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.png.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getPNGHeaderFilepointer(&$fd, &$ThisFileInfo) {
    $ThisFileInfo['fileformat']          = 'png';
    $ThisFileInfo['video']['dataformat'] = 'png';

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $PNGfiledata = fread($fd, FREAD_BUFFER_SIZE);
    $offset = 0;

    $PNGidentifier = substr($PNGfiledata, $offset, 8); // $89 $50 $4E $47 $0D $0A $1A $0A
    $offset += 8;
    if ($PNGidentifier != chr(0x89).chr(0x50).chr(0x4E).chr(0x47).chr(0x0D).chr(0x0A).chr(0x1A).chr(0x0A)) {
		$ThisFileInfo['error'] .= "\n".'First 8 bytes of file ('.PrintHexBytes($PNGidentifier).') did not match expected PNG identifier';
		unset($ThisFileInfo['fileformat']);
		return false;
    }

    while (((ftell($fd) - (strlen($PNGfiledata) - $offset)) < $ThisFileInfo['filesize'])) {
		$chunk['data_length'] = BigEndian2Int(substr($PNGfiledata, $offset, 4));
		$offset += 4;
		while (((strlen($PNGfiledata) - $offset) < ($chunk['data_length'] + 4)) && (ftell($fd) < $ThisFileInfo['filesize'])) {
			$PNGfiledata .= fread($fd, FREAD_BUFFER_SIZE);
		}
		$chunk['type_text']   =               substr($PNGfiledata, $offset, 4);
		$offset += 4;
		$chunk['type_raw']    = BigEndian2Int($chunk['type_text']);
		$chunk['data']        =               substr($PNGfiledata, $offset, $chunk['data_length']);
		$offset += $chunk['data_length'];
		$chunk['crc']         = BigEndian2Int(substr($PNGfiledata, $offset, 4));
		$offset += 4;

		$chunk['flags']['ancilliary']   = (bool) ($chunk['type_raw'] & 0x20000000);
		$chunk['flags']['private']      = (bool) ($chunk['type_raw'] & 0x00200000);
		$chunk['flags']['reserved']     = (bool) ($chunk['type_raw'] & 0x00002000);
		$chunk['flags']['safe_to_copy'] = (bool) ($chunk['type_raw'] & 0x00000020);

		switch ($chunk['type_text']) {

			case 'IHDR': // Image Header
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['width']                     = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  0, 4));
				$ThisFileInfo['png'][$chunk['type_text']]['height']                    = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  4, 4));
				$ThisFileInfo['png'][$chunk['type_text']]['raw']['bit_depth']          = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  8, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['raw']['color_type']         = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  9, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['raw']['compression_method'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 10, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['raw']['filter_method']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 11, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['raw']['interlace_method']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 12, 1));

				$ThisFileInfo['png'][$chunk['type_text']]['compression_method_text']   = PNGcompressionMethodLookup($ThisFileInfo['png'][$chunk['type_text']]['raw']['compression_method']);
				$ThisFileInfo['png'][$chunk['type_text']]['color_type']['palette']     = (bool) ($ThisFileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x01);
				$ThisFileInfo['png'][$chunk['type_text']]['color_type']['true_color']  = (bool) ($ThisFileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x02);
				$ThisFileInfo['png'][$chunk['type_text']]['color_type']['alpha']       = (bool) ($ThisFileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x04);

				$ThisFileInfo['video']['resolution_x'] = $ThisFileInfo['png'][$chunk['type_text']]['width'];
				$ThisFileInfo['video']['resolution_y'] = $ThisFileInfo['png'][$chunk['type_text']]['height'];
				break;


			case 'PLTE': // Palette
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$paletteoffset = 0;
				for ($i = 0; $i <= 255; $i++) {
					//$ThisFileInfo['png'][$chunk['type_text']]['red'][$i]   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					//$ThisFileInfo['png'][$chunk['type_text']]['green'][$i] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					//$ThisFileInfo['png'][$chunk['type_text']]['blue'][$i]  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					$red   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					$green = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					$blue  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1));
					$ThisFileInfo['png'][$chunk['type_text']][$i] = (($red << 16) | ($green << 8) | ($blue));
				}
				break;


			case 'tRNS': // Transparency
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				switch ($ThisFileInfo['png']['IHDR']['raw']['color_type']) {
					case 0:
						$ThisFileInfo['png'][$chunk['type_text']]['transparent_color_gray']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2));
						break;

					case 2:
						$ThisFileInfo['png'][$chunk['type_text']]['transparent_color_red']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2));
						$ThisFileInfo['png'][$chunk['type_text']]['transparent_color_green'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2));
						$ThisFileInfo['png'][$chunk['type_text']]['transparent_color_blue']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2));
						break;

					case 3:
						for ($i = 0; $i < strlen($ThisFileInfo['png'][$chunk['type_text']]['header']['data']); $i++) {
							$ThisFileInfo['png'][$chunk['type_text']]['palette_opacity'][$i] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $i, 1));
						}
						break;

					case 4:
					case 6:
						$ThisFileInfo['error'] .= "\n".'Invalid color_type in tRNS chunk: '.$ThisFileInfo['png']['IHDR']['raw']['color_type'];

					default:
						$ThisFileInfo['warning'] .= "\n".'Unhandled color_type in tRNS chunk: '.$ThisFileInfo['png']['IHDR']['raw']['color_type'];
						break;
				}
				break;


			case 'gAMA': // Image Gamma
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['gamma']  = BigEndian2Int($ThisFileInfo['png'][$chunk['type_text']]['header']['data']) / 100000;
				break;


			case 'cHRM': // Primary Chromaticities
				$ThisFileInfo['png'][$chunk['type_text']]['header']  = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['white_x'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  0, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['white_y'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  4, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['red_y']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  8, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['red_y']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 12, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['green_y'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 16, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['green_y'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 20, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['blue_y']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 24, 4)) / 100000;
				$ThisFileInfo['png'][$chunk['type_text']]['blue_y']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 28, 4)) / 100000;
				break;


			case 'sRGB': // Standard RGB Color Space
				$ThisFileInfo['png'][$chunk['type_text']]['header']                 = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['reindering_intent']      = BigEndian2Int($ThisFileInfo['png'][$chunk['type_text']]['header']['data']);
				$ThisFileInfo['png'][$chunk['type_text']]['reindering_intent_text'] = PNGsRGBintentLookup($ThisFileInfo['png'][$chunk['type_text']]['reindering_intent']);
				break;


			case 'iCCP': // Embedded ICC Profile
				$ThisFileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
				list($profilename, $compressiondata)                                = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['profile_name']            = $profilename;
				$ThisFileInfo['png'][$chunk['type_text']]['compression_method']      = BigEndian2Int(substr($compressiondata, 0, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['compression_profile']     = substr($compressiondata, 1);

				$ThisFileInfo['png'][$chunk['type_text']]['compression_method_text'] = PNGcompressionMethodLookup($ThisFileInfo['png'][$chunk['type_text']]['compression_method']);
				break;


			case 'tEXt': // Textual Data
				$ThisFileInfo['png'][$chunk['type_text']]['header']  = $chunk;
				list($keyword, $text)                               = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['keyword'] = $keyword;
				$ThisFileInfo['png'][$chunk['type_text']]['text']    = $text;

				$ThisFileInfo['png']['comments'][$ThisFileInfo['png'][$chunk['type_text']]['keyword']][] = $ThisFileInfo['png'][$chunk['type_text']]['text'];
				break;


			case 'zTXt': // Compressed Textual Data
				$ThisFileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
				list($keyword, $otherdata)                                          = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['keyword']                 = $keyword;
				$ThisFileInfo['png'][$chunk['type_text']]['compression_method']      = BigEndian2Int(substr($otherdata, 0, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['compressed_text']         = substr($otherdata, 1);
				$ThisFileInfo['png'][$chunk['type_text']]['compression_method_text'] = PNGcompressionMethodLookup($ThisFileInfo['png'][$chunk['type_text']]['compression_method']);
				switch ($ThisFileInfo['png'][$chunk['type_text']]['compression_method']) {
					case 0:
						$ThisFileInfo['png'][$chunk['type_text']]['text']            = gzuncompress($ThisFileInfo['png'][$chunk['type_text']]['compressed_text']);
						break;

					default:
						// unknown compression method
						break;
				}

				if (isset($ThisFileInfo['png'][$chunk['type_text']]['text'])) {
					$ThisFileInfo['png']['comments'][$ThisFileInfo['png'][$chunk['type_text']]['keyword']][] = $ThisFileInfo['png'][$chunk['type_text']]['text'];
				}
				break;


			case 'iTXt': // International Textual Data
				$ThisFileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
				list($keyword, $otherdata)                                          = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['keyword']                 = $keyword;
				$ThisFileInfo['png'][$chunk['type_text']]['compression']             = (bool) BigEndian2Int(substr($otherdata, 0, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['compression_method']      = BigEndian2Int(substr($otherdata, 1, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['compression_method_text'] = PNGcompressionMethodLookup($ThisFileInfo['png'][$chunk['type_text']]['compression_method']);
				list($languagetag, $translatedkeyword, $text)                       = explode(chr(0x00), substr($otherdata, 2), 3);
				$ThisFileInfo['png'][$chunk['type_text']]['language_tag']            = $languagetag;
				$ThisFileInfo['png'][$chunk['type_text']]['translated_keyword']      = utf8_decode($translatedkeyword);

				if ($ThisFileInfo['png'][$chunk['type_text']]['compression']) {

					switch ($ThisFileInfo['png'][$chunk['type_text']]['compression_method']) {
						case 0:
							$ThisFileInfo['png'][$chunk['type_text']]['text']        = utf8_decode(gzuncompress($text));
							break;

						default:
							// unknown compression method
							break;
					}

				} else {

					$ThisFileInfo['png'][$chunk['type_text']]['text']                = utf8_decode($text);

				}

				if (isset($ThisFileInfo['png'][$chunk['type_text']]['text'])) {
					$ThisFileInfo['png']['comments'][$ThisFileInfo['png'][$chunk['type_text']]['keyword']][] = $ThisFileInfo['png'][$chunk['type_text']]['text'];
				}
				break;


			case 'bKGD': // Background Color
				$ThisFileInfo['png'][$chunk['type_text']]['header']                   = $chunk;
				switch ($ThisFileInfo['png']['IHDR']['raw']['color_type']) {
					case 0:
					case 4:
						$ThisFileInfo['png'][$chunk['type_text']]['background_gray']  = BigEndian2Int($ThisFileInfo['png'][$chunk['type_text']]['header']['data']);
						break;

					case 2:
					case 6:
						$ThisFileInfo['png'][$chunk['type_text']]['background_red']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0 * $ThisFileInfo['png']['IHDR']['raw']['bit_depth'], $ThisFileInfo['png']['IHDR']['raw']['bit_depth']));
						$ThisFileInfo['png'][$chunk['type_text']]['background_green'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1 * $ThisFileInfo['png']['IHDR']['raw']['bit_depth'], $ThisFileInfo['png']['IHDR']['raw']['bit_depth']));
						$ThisFileInfo['png'][$chunk['type_text']]['background_blue']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2 * $ThisFileInfo['png']['IHDR']['raw']['bit_depth'], $ThisFileInfo['png']['IHDR']['raw']['bit_depth']));
						break;

					case 3:
						$ThisFileInfo['png'][$chunk['type_text']]['background_index'] = BigEndian2Int($ThisFileInfo['png'][$chunk['type_text']]['header']['data']);
						break;

					default:
						break;
				}
				break;


			case 'pHYs': // Physical Pixel Dimensions
				$ThisFileInfo['png'][$chunk['type_text']]['header']                 = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['pixels_per_unit_x']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 4));
				$ThisFileInfo['png'][$chunk['type_text']]['pixels_per_unit_y']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 4, 4));
				$ThisFileInfo['png'][$chunk['type_text']]['unit_specifier']         = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 8, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['unit']                   = PNGpHYsUnitLookup($ThisFileInfo['png'][$chunk['type_text']]['unit_specifier']);
				break;


			case 'sBIT': // Significant Bits
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				switch ($ThisFileInfo['png']['IHDR']['raw']['color_type']) {
					case 0:
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_gray']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
						break;

					case 2:
					case 3:
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_red']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_green'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_blue']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1));
						break;

					case 4:
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_gray']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_alpha'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1));
						break;

					case 6:
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_red']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_green'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_blue']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1));
						$ThisFileInfo['png'][$chunk['type_text']]['significant_bits_alpha'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 3, 1));
						break;

					default:
						break;
				}
				break;


			case 'sPLT': // Suggested Palette
				$ThisFileInfo['png'][$chunk['type_text']]['header']                           = $chunk;
				list($palettename, $otherdata)                                               = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['palette_name']                     = $palettename;
				$sPLToffset = 0;
				$ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bits']                = BigEndian2Int(substr($otherdata, $sPLToffset, 1));
				$sPLToffset += 1;
				$ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes']               = $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bits'] / 8;
				$paletteCounter = 0;
				while ($sPLToffset < strlen($otherdata)) {
					$ThisFileInfo['png'][$chunk['type_text']]['red'][$paletteCounter]       = BigEndian2Int(substr($otherdata, $sPLToffset, $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes']));
					$sPLToffset += $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
					$ThisFileInfo['png'][$chunk['type_text']]['green'][$paletteCounter]     = BigEndian2Int(substr($otherdata, $sPLToffset, $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes']));
					$sPLToffset += $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
					$ThisFileInfo['png'][$chunk['type_text']]['blue'][$paletteCounter]      = BigEndian2Int(substr($otherdata, $sPLToffset, $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes']));
					$sPLToffset += $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
					$ThisFileInfo['png'][$chunk['type_text']]['alpha'][$paletteCounter]     = BigEndian2Int(substr($otherdata, $sPLToffset, $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes']));
					$sPLToffset += $ThisFileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
					$ThisFileInfo['png'][$chunk['type_text']]['frequency'][$paletteCounter] = BigEndian2Int(substr($otherdata, $sPLToffset, 2));
					$sPLToffset += 2;
					$paletteCounter++;
				}
				break;


			case 'hIST': // Palette Histogram
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$hISTcounter = 0;
				while ($hISTcounter < strlen($ThisFileInfo['png'][$chunk['type_text']]['header']['data'])) {
					$ThisFileInfo['png'][$chunk['type_text']][$hISTcounter] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $hISTcounter / 2, 2));
					$hISTcounter += 2;
				}
				break;


			case 'tIME': // Image Last-Modification Time
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['year']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2));
				$ThisFileInfo['png'][$chunk['type_text']]['month']  = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['day']    = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 3, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['hour']   = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 4, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['minute'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 5, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['second'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 6, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['unix']   = gmmktime($ThisFileInfo['png'][$chunk['type_text']]['hour'], $ThisFileInfo['png'][$chunk['type_text']]['minute'], $ThisFileInfo['png'][$chunk['type_text']]['second'], $ThisFileInfo['png'][$chunk['type_text']]['month'], $ThisFileInfo['png'][$chunk['type_text']]['day'], $ThisFileInfo['png'][$chunk['type_text']]['year']);
				break;


			case 'oFFs': // Image Offset
				$ThisFileInfo['png'][$chunk['type_text']]['header']         = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['position_x']     = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 4), false, true);
				$ThisFileInfo['png'][$chunk['type_text']]['position_y']     = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 4, 4), false, true);
				$ThisFileInfo['png'][$chunk['type_text']]['unit_specifier'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 8, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['unit']           = PNGoFFsUnitLookup($ThisFileInfo['png'][$chunk['type_text']]['unit_specifier']);
				break;


			case 'pCAL': // Calibration Of Pixel Values
				$ThisFileInfo['png'][$chunk['type_text']]['header']             = $chunk;
				list($calibrationname, $otherdata)                             = explode(chr(0x00), $ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2);
				$ThisFileInfo['png'][$chunk['type_text']]['calibration_name']   = $calibrationname;
				$pCALoffset = 0;
				$ThisFileInfo['png'][$chunk['type_text']]['original_zero']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 4), false, true);
				$pCALoffset += 4;
				$ThisFileInfo['png'][$chunk['type_text']]['original_max']       = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 4), false, true);
				$pCALoffset += 4;
				$ThisFileInfo['png'][$chunk['type_text']]['equation_type']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 1));
				$pCALoffset += 1;
				$ThisFileInfo['png'][$chunk['type_text']]['equation_type_text'] = PNGpCALequationTypeLookup($ThisFileInfo['png'][$chunk['type_text']]['equation_type']);
				$ThisFileInfo['png'][$chunk['type_text']]['parameter_count']    = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 1));
				$pCALoffset += 1;
				$ThisFileInfo['png'][$chunk['type_text']]['parameters']         = explode(chr(0x00), substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset));
				break;


			case 'sCAL': // Physical Scale Of Image Subject
				$ThisFileInfo['png'][$chunk['type_text']]['header']         = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']]['unit_specifier'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
				$ThisFileInfo['png'][$chunk['type_text']]['unit']           = PNGsCALUnitLookup($ThisFileInfo['png'][$chunk['type_text']]['unit_specifier']);
				list($pixelwidth, $pixelheight)                            = explode(chr(0x00), substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1));
				$ThisFileInfo['png'][$chunk['type_text']]['pixel_width']    = $pixelwidth;
				$ThisFileInfo['png'][$chunk['type_text']]['pixel_height']   = $pixelheight;
				break;


			case 'gIFg': // GIF Graphic Control Extension
				$gIFgCounter = 0;
				if (isset($ThisFileInfo['png'][$chunk['type_text']]) && is_array($ThisFileInfo['png'][$chunk['type_text']])) {
					$gIFgCounter = count($ThisFileInfo['png'][$chunk['type_text']]);
				}
				$ThisFileInfo['png'][$chunk['type_text']][$gIFgCounter]['header']          = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']][$gIFgCounter]['disposal_method'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1));
				$ThisFileInfo['png'][$chunk['type_text']][$gIFgCounter]['user_input_flag'] = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1));
				$ThisFileInfo['png'][$chunk['type_text']][$gIFgCounter]['delay_time']      = BigEndian2Int(substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 2, 2));
				break;


			case 'gIFx': // GIF Application Extension
				$gIFxCounter = 0;
				if (isset($ThisFileInfo['png'][$chunk['type_text']]) && is_array($ThisFileInfo['png'][$chunk['type_text']])) {
					$gIFxCounter = count($ThisFileInfo['png'][$chunk['type_text']]);
				}
				$ThisFileInfo['png'][$chunk['type_text']][$gIFxCounter]['header']                 = $chunk;
				$ThisFileInfo['png'][$chunk['type_text']][$gIFxCounter]['application_identifier'] = substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  0, 8);
				$ThisFileInfo['png'][$chunk['type_text']][$gIFxCounter]['authentication_code']    = substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'],  8, 3);
				$ThisFileInfo['png'][$chunk['type_text']][$gIFxCounter]['application_data']       = substr($ThisFileInfo['png'][$chunk['type_text']]['header']['data'], 11);
				break;


			case 'IDAT': // Image Data
				$idatinformationfieldindex = 0;
				if (isset($ThisFileInfo['png']['IDAT']) && is_array($ThisFileInfo['png']['IDAT'])) {
					$idatinformationfieldindex = count($ThisFileInfo['png']['IDAT']);
				}
				unset($chunk['data']);
				$ThisFileInfo['png'][$chunk['type_text']][$idatinformationfieldindex]['header'] = $chunk;
				break;


			case 'IEND': // Image Trailer
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				break;


			default:
				//unset($chunk['data']);
				$ThisFileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				$ThisFileInfo['warning'] .= "\n".'Unhandled chunk type: '.$chunk['type_text'];
				break;
		}
    }

    // PNG tags have highest priority
    if (!empty($ThisFileInfo['png']['comments'])) {
		CopyFormatCommentsToRootComments($ThisFileInfo['png']['comments'], $ThisFileInfo, true, true, true);

		// add tag to array of tags
		$ThisFileInfo['tags'][] = 'png';
    }

    return true;
}

function PNGsRGBintentLookup($sRGB) {
    static $PNGsRGBintentLookup = array();
    if (empty($PNGsRGBintentLookup)) {
		$PNGsRGBintentLookup[0] = 'Perceptual';
		$PNGsRGBintentLookup[1] = 'Relative colorimetric';
		$PNGsRGBintentLookup[2] = 'Saturation';
		$PNGsRGBintentLookup[3] = 'Absolute colorimetric';
    }
    return (isset($PNGsRGBintentLookup[$sRGB]) ? $PNGsRGBintentLookup[$sRGB] : 'invalid');
}

function PNGcompressionMethodLookup($compressionmethod) {
    static $PNGcompressionMethodLookup = array();
    if (empty($PNGcompressionMethodLookup)) {
		$PNGcompressionMethodLookup[0] = 'deflate/inflate';
    }
    return (isset($PNGcompressionMethodLookup[$compressionmethod]) ? $PNGcompressionMethodLookup[$compressionmethod] : 'invalid');
}

function PNGpHYsUnitLookup($unitid) {
    static $PNGpHYsUnitLookup = array();
    if (empty($PNGpHYsUnitLookup)) {
		$PNGpHYsUnitLookup[0] = 'unknown';
		$PNGpHYsUnitLookup[1] = 'meter';
    }
    return (isset($PNGpHYsUnitLookup[$unitid]) ? $PNGpHYsUnitLookup[$unitid] : 'invalid');
}

function PNGoFFsUnitLookup($unitid) {
    static $PNGoFFsUnitLookup = array();
    if (empty($PNGoFFsUnitLookup)) {
		$PNGoFFsUnitLookup[0] = 'pixel';
		$PNGoFFsUnitLookup[1] = 'micrometer';
    }
    return (isset($PNGoFFsUnitLookup[$unitid]) ? $PNGoFFsUnitLookup[$unitid] : 'invalid');
}

function PNGpCALequationTypeLookup($equationtype) {
    static $PNGpCALequationTypeLookup = array();
    if (empty($PNGpCALequationTypeLookup)) {
		$PNGpCALequationTypeLookup[0] = 'Linear mapping';
		$PNGpCALequationTypeLookup[1] = 'Base-e exponential mapping';
		$PNGpCALequationTypeLookup[2] = 'Arbitrary-base exponential mapping';
		$PNGpCALequationTypeLookup[3] = 'Hyperbolic mapping';
    }
    return (isset($PNGpCALequationTypeLookup[$equationtype]) ? $PNGpCALequationTypeLookup[$equationtype] : 'invalid');
}

function PNGsCALUnitLookup($unitid) {
    static $PNGsCALUnitLookup = array();
    if (empty($PNGsCALUnitLookup)) {
		$PNGsCALUnitLookup[0] = 'meter';
		$PNGsCALUnitLookup[1] = 'radian';
    }
    return (isset($PNGsCALUnitLookup[$unitid]) ? $PNGsCALUnitLookup[$unitid] : 'invalid');
}

?>