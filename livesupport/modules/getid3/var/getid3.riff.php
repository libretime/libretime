<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.riff.php - part of getID3()                          //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getRIFFHeaderFilepointer(&$fd, &$ThisFileInfo) {
    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $RIFFheader = fread($fd, 12);
    switch (substr($RIFFheader, 0, 4)) {
		case 'RIFF':
		case 'SDSS':  // SDSS is identical to RIFF, just renamed. Used by SmartSound QuickTracks (www.smartsound.com)
			$ThisFileInfo['fileformat']   = 'riff';
			$ThisFileInfo['RIFF'][substr($RIFFheader, 8, 4)] = ParseRIFF($fd, $ThisFileInfo['avdataoffset'] + 12, $ThisFileInfo['avdataoffset'] + LittleEndian2Int(substr($RIFFheader, 4, 4)), $ThisFileInfo);
			break;

		default:
			$ThisFileInfo['error'] .= "\n".'Cannot parse RIFF (this is maybe not a RIFF / WAV / AVI file?)';
			unset($ThisFileInfo['fileformat']);
			return false;
			break;
    }

    $streamindex = 0;
    $arraykeys = array_keys($ThisFileInfo['RIFF']);
    switch ($arraykeys[0]) {
		case 'WAVE':
			$ThisFileInfo['audio']['bitrate_mode'] = 'cbr';
			$ThisFileInfo['audio']['dataformat']   = 'wav';
			if (isset($ThisFileInfo['RIFF']['WAVE']['fmt '][0]['data'])) {

				$ThisFileInfo['RIFF']['audio'][$streamindex] = RIFFparseWAVEFORMATex($ThisFileInfo['RIFF']['WAVE']['fmt '][0]['data']);

				if ($ThisFileInfo['RIFF']['audio'][$streamindex] == 0) {
					$ThisFileInfo['error'] .= 'Corrupt RIFF file: bitrate_audio == zero';
					return false;
				}

				$ThisFileInfo['RIFF']['raw']['fmt '] = $ThisFileInfo['RIFF']['audio'][$streamindex]['raw'];
				unset($ThisFileInfo['RIFF']['audio'][$streamindex]['raw']);
				$ThisFileInfo['audio'] = array_merge_noclobber($ThisFileInfo['audio'], $ThisFileInfo['RIFF']['audio'][$streamindex]);

				$ThisFileInfo['audio']['bitrate'] = $ThisFileInfo['RIFF']['audio'][$streamindex]['bitrate'];

				$ThisFileInfo['playtime_seconds'] = (float) ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['audio']['bitrate']);

				if (isset($ThisFileInfo['RIFF']['WAVE']['data'][0]['offset']) && isset($ThisFileInfo['RIFF']['raw']['fmt ']['wFormatTag'])) {
					switch ($ThisFileInfo['RIFF']['raw']['fmt ']['wFormatTag']) {

						case 85: // LAME ACM
							require_once(GETID3_INCLUDEPATH.'getid3.mp3.php');
							getOnlyMPEGaudioInfo($fd, $ThisFileInfo, $ThisFileInfo['RIFF']['WAVE']['data'][0]['offset'], false);
							$ThisFileInfo['audio']['dataformat'] = 'mp3';
							if (isset($ThisFileInfo['mpeg']['audio'])) {
								$ThisFileInfo['audio']['sample_rate']  = $ThisFileInfo['mpeg']['audio']['sample_rate'];
								$ThisFileInfo['audio']['channels']     = $ThisFileInfo['mpeg']['audio']['channels'];
								$ThisFileInfo['audio']['bitrate']      = $ThisFileInfo['mpeg']['audio']['bitrate'] * 1000;
								$ThisFileInfo['bitrate']               = $ThisFileInfo['audio']['bitrate'];
								$ThisFileInfo['audio']['bitrate_mode'] = strtolower($ThisFileInfo['mpeg']['audio']['bitratemode']);
							}
							break;

						default:
							// do nothing
							break;

					}
				}
			}
			if (isset($ThisFileInfo['RIFF']['WAVE']['rgad'][0]['data'])) {
				require_once(GETID3_INCLUDEPATH.'getid3.rgad.php');

				$rgadData = $ThisFileInfo['RIFF']['WAVE']['rgad'][0]['data'];
				$ThisFileInfo['RIFF']['raw']['rgad']['fPeakAmplitude']      = LittleEndian2Float(substr($rgadData, 0, 4));
				$ThisFileInfo['RIFF']['raw']['rgad']['nRadioRgAdjust']      = LittleEndian2Int(substr($rgadData, 4, 2));
				$ThisFileInfo['RIFF']['raw']['rgad']['nAudiophileRgAdjust'] = LittleEndian2Int(substr($rgadData, 6, 2));
				$nRadioRgAdjustBitstring      = str_pad(Dec2Bin($ThisFileInfo['RIFF']['raw']['rgad']['nRadioRgAdjust']), 16, '0', STR_PAD_LEFT);
				$nAudiophileRgAdjustBitstring = str_pad(Dec2Bin($ThisFileInfo['RIFF']['raw']['rgad']['nAudiophileRgAdjust']), 16, '0', STR_PAD_LEFT);
				$ThisFileInfo['RIFF']['raw']['rgad']['radio']['name']       = Bin2Dec(substr($nRadioRgAdjustBitstring, 0, 3));
				$ThisFileInfo['RIFF']['raw']['rgad']['radio']['originator'] = Bin2Dec(substr($nRadioRgAdjustBitstring, 3, 3));
				$ThisFileInfo['RIFF']['raw']['rgad']['radio']['signbit']    = Bin2Dec(substr($nRadioRgAdjustBitstring, 6, 1));
				$ThisFileInfo['RIFF']['raw']['rgad']['radio']['adjustment'] = Bin2Dec(substr($nRadioRgAdjustBitstring, 7, 9));
				$ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['name']       = Bin2Dec(substr($nAudiophileRgAdjustBitstring, 0, 3));
				$ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['originator'] = Bin2Dec(substr($nAudiophileRgAdjustBitstring, 3, 3));
				$ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['signbit']    = Bin2Dec(substr($nAudiophileRgAdjustBitstring, 6, 1));
				$ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['adjustment'] = Bin2Dec(substr($nAudiophileRgAdjustBitstring, 7, 9));

				$ThisFileInfo['RIFF']['rgad']['peakamplitude'] = $ThisFileInfo['RIFF']['raw']['rgad']['fPeakAmplitude'];
				if (($ThisFileInfo['RIFF']['raw']['rgad']['radio']['name'] != 0) && ($ThisFileInfo['RIFF']['raw']['rgad']['radio']['originator'] != 0)) {
					$ThisFileInfo['RIFF']['rgad']['radio']['name']            = RGADnameLookup($ThisFileInfo['RIFF']['raw']['rgad']['radio']['name']);
					$ThisFileInfo['RIFF']['rgad']['radio']['originator']      = RGADoriginatorLookup($ThisFileInfo['RIFF']['raw']['rgad']['radio']['originator']);
					$ThisFileInfo['RIFF']['rgad']['radio']['adjustment']      = RGADadjustmentLookup($ThisFileInfo['RIFF']['raw']['rgad']['radio']['adjustment'], $ThisFileInfo['RIFF']['raw']['rgad']['radio']['signbit']);
				}
				if (($ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['name'] != 0) && ($ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['originator'] != 0)) {
					$ThisFileInfo['RIFF']['rgad']['audiophile']['name']       = RGADnameLookup($ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['name']);
					$ThisFileInfo['RIFF']['rgad']['audiophile']['originator'] = RGADoriginatorLookup($ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['originator']);
					$ThisFileInfo['RIFF']['rgad']['audiophile']['adjustment'] = RGADadjustmentLookup($ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['adjustment'], $ThisFileInfo['RIFF']['raw']['rgad']['audiophile']['signbit']);
				}
			}
			if (isset($ThisFileInfo['RIFF']['WAVE']['fact'][0]['data'])) {
				$ThisFileInfo['RIFF']['raw']['fact']['NumberOfSamples'] = LittleEndian2Int(substr($ThisFileInfo['RIFF']['WAVE']['fact'][0]['data'], 0, 4));

				if (isset($ThisFileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec']) && ($ThisFileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'] > 0)) {
					$ThisFileInfo['playtime_seconds'] = (float) $ThisFileInfo['RIFF']['raw']['fact']['NumberOfSamples'] / $ThisFileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'];
				}

				if (isset($ThisFileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec']) && $ThisFileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec']) {
					$ThisFileInfo['audio']['bitrate'] = CastAsInt($ThisFileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] * 8);
				}
			}

			if (!isset($ThisFileInfo['audio']['bitrate']) && isset($ThisFileInfo['RIFF']['audio'][$streamindex]['bitrate'])) {
				$ThisFileInfo['audio']['bitrate']    = $ThisFileInfo['RIFF']['audio'][$streamindex]['bitrate'];
				$ThisFileInfo['playtime_seconds'] = (float) ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) * 8) / $ThisFileInfo['audio']['bitrate']);
			}
			break;

		case 'AVI ':
			$ThisFileInfo['video']['bitrate_mode'] = 'cbr';
			$ThisFileInfo['video']['dataformat']   = 'avi';
			$ThisFileInfo['mime_type']  = 'video/avi';
			if (isset($ThisFileInfo['RIFF']['AVI ']['hdrl']['avih'][$streamindex]['data'])) {
				$avihData = $ThisFileInfo['RIFF']['AVI ']['hdrl']['avih'][$streamindex]['data'];
				$ThisFileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame']    = LittleEndian2Int(substr($avihData,  0, 4)); // frame display rate (or 0L)
				if ($ThisFileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'] == 0) {
					$ThisFileInfo['error'] .= 'Corrupt RIFF file: avih.dwMicroSecPerFrame == zero';
					return false;
				}
				$ThisFileInfo['RIFF']['raw']['avih']['dwMaxBytesPerSec']      = LittleEndian2Int(substr($avihData,  4, 4)); // max. transfer rate
				$ThisFileInfo['RIFF']['raw']['avih']['dwPaddingGranularity']  = LittleEndian2Int(substr($avihData,  8, 4)); // pad to multiples of this size; normally 2K.
				$ThisFileInfo['RIFF']['raw']['avih']['dwFlags']               = LittleEndian2Int(substr($avihData, 12, 4)); // the ever-present flags
				$ThisFileInfo['RIFF']['raw']['avih']['dwTotalFrames']         = LittleEndian2Int(substr($avihData, 16, 4)); // # frames in file
				$ThisFileInfo['RIFF']['raw']['avih']['dwInitialFrames']       = LittleEndian2Int(substr($avihData, 20, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwStreams']             = LittleEndian2Int(substr($avihData, 24, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwSuggestedBufferSize'] = LittleEndian2Int(substr($avihData, 28, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwWidth']               = LittleEndian2Int(substr($avihData, 32, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwHeight']              = LittleEndian2Int(substr($avihData, 36, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwScale']               = LittleEndian2Int(substr($avihData, 40, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwRate']                = LittleEndian2Int(substr($avihData, 44, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwStart']               = LittleEndian2Int(substr($avihData, 48, 4));
				$ThisFileInfo['RIFF']['raw']['avih']['dwLength']              = LittleEndian2Int(substr($avihData, 52, 4));

				$ThisFileInfo['RIFF']['raw']['avih']['flags']['hasindex']     = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000010);
				$ThisFileInfo['RIFF']['raw']['avih']['flags']['mustuseindex'] = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000020);
				$ThisFileInfo['RIFF']['raw']['avih']['flags']['interleaved']  = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000100);
				$ThisFileInfo['RIFF']['raw']['avih']['flags']['trustcktype']  = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000800);
				$ThisFileInfo['RIFF']['raw']['avih']['flags']['capturedfile'] = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00010000);
				$ThisFileInfo['RIFF']['raw']['avih']['flags']['copyrighted']  = (bool) ($ThisFileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00020010);


				$ThisFileInfo['RIFF']['video'][$streamindex]['frame_width']  = $ThisFileInfo['RIFF']['raw']['avih']['dwWidth'];
				$ThisFileInfo['RIFF']['video'][$streamindex]['frame_height'] = $ThisFileInfo['RIFF']['raw']['avih']['dwHeight'];
				$ThisFileInfo['RIFF']['video'][$streamindex]['frame_rate']   = round(1000000 / $ThisFileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'], 3);
				if (!isset($ThisFileInfo['video']['resolution_x'])) {
					$ThisFileInfo['video']['resolution_x'] = $ThisFileInfo['RIFF']['video'][$streamindex]['frame_width'];
				}
				if (!isset($ThisFileInfo['video']['resolution_y'])) {
					$ThisFileInfo['video']['resolution_y'] = $ThisFileInfo['RIFF']['video'][$streamindex]['frame_height'];
				}
				$ThisFileInfo['video']['frame_rate'] = $ThisFileInfo['RIFF']['video'][$streamindex]['frame_rate'];
			}
			if (isset($ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'][0]['data'])) {
				if (is_array($ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'])) {
					for ($i = 0; $i < count($ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strh']); $i++) {
						if (isset($ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'][$i]['data'])) {
							$strhData = $ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'][$i]['data'];
							$strhfccType = substr($strhData,  0, 4);

							if (isset($ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strf'][$i]['data'])) {
								$strfData = $ThisFileInfo['RIFF']['AVI ']['hdrl']['strl']['strf'][$i]['data'];
								switch ($strhfccType) {
									case 'auds':
										$ThisFileInfo['audio']['bitrate_mode'] = 'cbr';
										$ThisFileInfo['audio']['dataformat']   = 'wav';
										if (isset($ThisFileInfo['RIFF']['audio']) && is_array($ThisFileInfo['RIFF']['audio'])) {
											$streamindex = count($ThisFileInfo['RIFF']['audio']);
										}

										$ThisFileInfo['RIFF']['audio'][$streamindex] = RIFFparseWAVEFORMATex($strfData);
										$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex] = $ThisFileInfo['RIFF']['audio'][$streamindex]['raw'];
										unset($ThisFileInfo['RIFF']['audio'][$streamindex]['raw']);

										$ThisFileInfo['audio'] = array_merge_noclobber($ThisFileInfo['audio'], $ThisFileInfo['RIFF']['audio'][$streamindex]);

										switch ($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['wFormatTag']) {
											case 85:
												$ThisFileInfo['audio']['dataformat'] = 'mp3';
												break;

											case 8192:
												$ThisFileInfo['audio']['dataformat'] = 'ac3';
												break;

											default:
												$ThisFileInfo['audio']['dataformat'] = 'wav';
												break;
										}

										break;


									case 'iavs':
									case 'vids':
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['fccType']               =                  substr($strhData,  0, 4);  // same as $strhfccType;
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['fccHandler']            =                  substr($strhData,  4, 4);
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwFlags']               = LittleEndian2Int(substr($strhData,  8, 4)); // Contains AVITF_* flags
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['wPriority']             = LittleEndian2Int(substr($strhData, 12, 2));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['wLanguage']             = LittleEndian2Int(substr($strhData, 14, 2));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwInitialFrames']       = LittleEndian2Int(substr($strhData, 16, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwScale']               = LittleEndian2Int(substr($strhData, 20, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwRate']                = LittleEndian2Int(substr($strhData, 24, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwStart']               = LittleEndian2Int(substr($strhData, 28, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwLength']              = LittleEndian2Int(substr($strhData, 32, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwSuggestedBufferSize'] = LittleEndian2Int(substr($strhData, 36, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwQuality']             = LittleEndian2Int(substr($strhData, 40, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['dwSampleSize']          = LittleEndian2Int(substr($strhData, 44, 4));
										$ThisFileInfo['RIFF']['raw']['strh'][$i]['rcFrame']               = LittleEndian2Int(substr($strhData, 48, 4));

										$ThisFileInfo['RIFF']['video'][$streamindex]['codec'] = RIFFfourccLookup($ThisFileInfo['RIFF']['raw']['strh'][$i]['fccHandler']);
										if (!$ThisFileInfo['RIFF']['video'][$streamindex]['codec'] && isset($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc']) && RIFFfourccLookup($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc'])) {
											$ThisFileInfo['RIFF']['video'][$streamindex]['codec'] = RIFFfourccLookup($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc']);
										}
										$ThisFileInfo['video']['codec'] = $ThisFileInfo['RIFF']['video'][$streamindex]['codec'];

										switch ($strhfccType) {
											case 'vids':
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biSize']          = LittleEndian2Int(substr($strfData,  0, 4)); // number of bytes required by the BITMAPINFOHEADER structure
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biWidth']         = LittleEndian2Int(substr($strfData,  4, 4)); // width of the bitmap in pixels
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biHeight']        = LittleEndian2Int(substr($strfData,  8, 4)); // height of the bitmap in pixels. If biHeight is positive, the bitmap is a 'bottom-up' DIB and its origin is the lower left corner. If biHeight is negative, the bitmap is a 'top-down' DIB and its origin is the upper left corner
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biPlanes']        = LittleEndian2Int(substr($strfData, 12, 2)); // number of color planes on the target device. In most cases this value must be set to 1
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biBitCount']      = LittleEndian2Int(substr($strfData, 14, 2)); // Specifies the number of bits per pixels
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc']          = substr($strfData, 16, 4);                   //
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biSizeImage']     = LittleEndian2Int(substr($strfData, 20, 4)); // size of the bitmap data section of the image (the actual pixel data, excluding BITMAPINFOHEADER and RGBQUAD structures)
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biXPelsPerMeter'] = LittleEndian2Int(substr($strfData, 24, 4)); // horizontal resolution, in pixels per metre, of the target device
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biYPelsPerMeter'] = LittleEndian2Int(substr($strfData, 28, 4)); // vertical resolution, in pixels per metre, of the target device
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biClrUsed']       = LittleEndian2Int(substr($strfData, 32, 4)); // actual number of color indices in the color table used by the bitmap. If this value is zero, the bitmap uses the maximum number of colors corresponding to the value of the biBitCount member for the compression mode specified by biCompression
												$ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['biClrImportant']  = LittleEndian2Int(substr($strfData, 36, 4)); // number of color indices that are considered important for displaying the bitmap. If this value is zero, all colors are important

												if ($ThisFileInfo['RIFF']['video'][$streamindex]['codec'] == 'DV') {
													$ThisFileInfo['RIFF']['video'][$streamindex]['dv_type'] = 2;
												}
												break;

											case 'iavs':
												$ThisFileInfo['RIFF']['video'][$streamindex]['dv_type'] = 1;
												break;
										}
										break;

									default:
										$ThisFileInfo['warning'] .= "\n".'Unhandled fccType for stream ('.$i.'): "'.$strhfccType.'"';
										break;

								}
							}
						}
						if (isset($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc']) && RIFFfourccLookup($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc'])) {
							$ThisFileInfo['RIFF']['video'][$streamindex]['codec'] = RIFFfourccLookup($ThisFileInfo['RIFF']['raw']['strf']["$strhfccType"][$streamindex]['fourcc']);
							$ThisFileInfo['video']['codec'] = $ThisFileInfo['RIFF']['video'][$streamindex]['codec'];
						}

					}
				}
			}
			break;

		case 'CDDA':
			$ThisFileInfo['audio']['bitrate_mode'] = 'cbr';
			$ThisFileInfo['audio']['dataformat']   = 'cda';
			unset($ThisFileInfo['mime_type']);

			if (isset($ThisFileInfo['RIFF']['CDDA']['fmt '][0]['data'])) {
				$fmtData = $ThisFileInfo['RIFF']['CDDA']['fmt '][0]['data'];
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['unknown1']           = LittleEndian2Int(substr($fmtData,  0, 2));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['track_num']          = LittleEndian2Int(substr($fmtData,  2, 2));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['disc_id']            = LittleEndian2Int(substr($fmtData,  4, 4));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['start_offset_frame'] = LittleEndian2Int(substr($fmtData,  8, 4));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['playtime_frames']    = LittleEndian2Int(substr($fmtData, 12, 4));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['unknown6']           = LittleEndian2Int(substr($fmtData, 16, 4));
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['unknown7']           = LittleEndian2Int(substr($fmtData, 20, 4));

				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['start_offset_seconds'] = (float) $ThisFileInfo['RIFF']['CDDA']['fmt '][0]['start_offset_frame'] / 75;
				$ThisFileInfo['RIFF']['CDDA']['fmt '][0]['playtime_seconds']     = (float) $ThisFileInfo['RIFF']['CDDA']['fmt '][0]['playtime_frames'] / 75;
				$ThisFileInfo['comments']['track']                               = $ThisFileInfo['RIFF']['CDDA']['fmt '][0]['track_num'];
				$ThisFileInfo['playtime_seconds']                                = $ThisFileInfo['RIFF']['CDDA']['fmt '][0]['playtime_seconds'];

				// hardcoded data for CD-audio
				$ThisFileInfo['audio']['sample_rate']     = 44100;
				$ThisFileInfo['audio']['channels']        = 2;
				$ThisFileInfo['audio']['bits_per_sample'] = 16;
				$ThisFileInfo['audio']['bitrate']         = $ThisFileInfo['audio']['sample_rate'] * $ThisFileInfo['audio']['channels'] * $ThisFileInfo['audio']['bits_per_sample'];
				$ThisFileInfo['audio']['bitrate_mode']    = 'cbr';
			}
			break;


		default:
			unset($ThisFileInfo['fileformat']);
			break;
    }

    if (isset($ThisFileInfo['RIFF']['WAVE']['DISP']) && is_array($ThisFileInfo['RIFF']['WAVE']['DISP'])) {
		$ThisFileInfo['tags'][] = 'riff';
		$ThisFileInfo['RIFF']['comments']['title'][] = trim(substr($ThisFileInfo['RIFF']['WAVE']['DISP'][count($ThisFileInfo['RIFF']['WAVE']['DISP']) - 1]['data'], 4));
    }
    if (isset($ThisFileInfo['RIFF']['WAVE']['INFO']) && is_array($ThisFileInfo['RIFF']['WAVE']['INFO'])) {
		$ThisFileInfo['tags'][] = 'riff';
		$RIFFinfoKeyLookup = array('IART'=>'artist', 'IGNR'=>'genre', 'ICMT'=>'comment', 'ICOP'=>'copyright', 'IENG'=>'engineers', 'IKEY'=>'keywords', 'IMED'=>'orignalmedium', 'INAM'=>'name', 'ISRC'=>'sourcesupplier', 'ITCH'=>'digitizer', 'ISBJ'=>'subject', 'ISRF'=>'digitizationsource');
		foreach ($RIFFinfoKeyLookup as $key => $value) {
			foreach ($ThisFileInfo['RIFF']['WAVE']['INFO']["$key"] as $commentid => $commentdata) {
				if (trim($commentdata['data']) != '') {
					$ThisFileInfo['RIFF']['comments']["$value"][] = trim($commentdata['data']);
				}
			}
		}
    }
    if (!empty($ThisFileInfo['RIFF']['comments'])) {
		CopyFormatCommentsToRootComments($ThisFileInfo['RIFF']['comments'], $ThisFileInfo, true, true, true);
    }

    if (!isset($ThisFileInfo['playtime_seconds'])) {
		$ThisFileInfo['playtime_seconds'] = 0;
    }
    if (isset($ThisFileInfo['RIFF']['raw']['avih']['dwTotalFrames']) && isset($ThisFileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'])) {
		$ThisFileInfo['playtime_seconds'] = $ThisFileInfo['RIFF']['raw']['avih']['dwTotalFrames'] * ($ThisFileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'] / 1000000);
    }

    if ($ThisFileInfo['playtime_seconds'] > 0) {

		if (isset($ThisFileInfo['RIFF']['audio']) && isset($ThisFileInfo['RIFF']['video'])) {

			if (!isset($ThisFileInfo['bitrate'])) {
				$ThisFileInfo['bitrate'] = ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) / $ThisFileInfo['playtime_seconds']) * 8);
			}

		} elseif (isset($ThisFileInfo['RIFF']['audio']) && !isset($ThisFileInfo['RIFF']['video'])) {

			if (!isset($ThisFileInfo['audio']['bitrate'])) {
				$ThisFileInfo['audio']['bitrate'] = ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) / $ThisFileInfo['playtime_seconds']) * 8);
			}

		} elseif (!isset($ThisFileInfo['RIFF']['audio']) && isset($ThisFileInfo['RIFF']['video'])) {

			if (!isset($ThisFileInfo['video']['bitrate'])) {
				$ThisFileInfo['video']['bitrate'] = ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) / $ThisFileInfo['playtime_seconds']) * 8);
			}

		}

    }


    if (isset($ThisFileInfo['RIFF']['video']) && isset($ThisFileInfo['audio']['bitrate']) && ($ThisFileInfo['audio']['bitrate'] > 0) && ($ThisFileInfo['playtime_seconds'] > 0)) {
		$ThisFileInfo['audio']['bitrate'] = 0;
		$ThisFileInfo['video']['bitrate'] = ((($ThisFileInfo['avdataend'] - $ThisFileInfo['avdataoffset']) / $ThisFileInfo['playtime_seconds']) * 8);
		foreach ($ThisFileInfo['RIFF']['audio'] as $channelnumber => $audioinfoarray) {
			$ThisFileInfo['video']['bitrate'] -= $audioinfoarray['bitrate'];
			$ThisFileInfo['audio']['bitrate'] += $audioinfoarray['bitrate'];
		}
		if ($ThisFileInfo['video']['bitrate'] <= 0) {
			unset($ThisFileInfo['video']['bitrate']);
		}
		if ($ThisFileInfo['audio']['bitrate'] <= 0) {
			unset($ThisFileInfo['audio']['bitrate']);
		}
    }

    if (!empty($ThisFileInfo['RIFF']['raw']['fmt ']['nBitsPerSample'])) {
		$ThisFileInfo['audio']['bits_per_sample'] = $ThisFileInfo['RIFF']['raw']['fmt ']['nBitsPerSample'];
    }

    // Skip RIFF header
    $ThisFileInfo['avdataoffset'] += 44;

    return true;
}


function ParseRIFF(&$fd, $startoffset, $maxoffset, &$ThisFileInfo) {
    $RIFFchunk = false;

    fseek($fd, $startoffset, SEEK_SET);

    while (ftell($fd) < $maxoffset) {
		$chunkname = fread($fd, 4);
		$chunksize = LittleEndian2Int(fread($fd, 4));
		if (($chunksize % 2) != 0) {
			// all structures are packed on word boundaries
			$chunksize++;
		}

		switch ($chunkname) {

			case 'LIST':
				$listname = fread($fd, 4);
				switch ($listname) {
					case 'movi':
					case 'rec ':
						// skip over
						$RIFFchunk["$listname"]['offset'] = ftell($fd) - 4;
						$RIFFchunk["$listname"]['size']   = $chunksize;
						fseek($fd, $chunksize - 4, SEEK_CUR);

						$ThisFileInfo['avdataoffset'] = max($ThisFileInfo['avdataoffset'], $RIFFchunk["$listname"]['offset']);
						$ThisFileInfo['avdataend']    = max($ThisFileInfo['avdataend'],    $RIFFchunk["$listname"]['offset'] + $RIFFchunk["$listname"]['size']);
						break;

					default:
						if (!isset($RIFFchunk["$listname"])) {
							$RIFFchunk["$listname"] = array();
						}
						$RIFFchunk["$listname"] = array_merge_recursive($RIFFchunk["$listname"], ParseRIFF($fd, ftell($fd), ftell($fd) + $chunksize - 4, $ThisFileInfo));
						break;
				}
				break;

			default:
				$thisindex = 0;
				if (isset($RIFFchunk["$chunkname"]) && is_array($RIFFchunk["$chunkname"])) {
					$thisindex = count($RIFFchunk["$chunkname"]);
				}
				$RIFFchunk["$chunkname"][$thisindex]['offset'] = ftell($fd) - 8;
				$RIFFchunk["$chunkname"][$thisindex]['size']   = $chunksize;
				if ($chunksize <= 2048) {
					$RIFFchunk["$chunkname"][$thisindex]['data'] = fread($fd, $chunksize);
				} else {
					fseek($fd, $chunksize, SEEK_CUR);
				}

				break;

		}

    }

    return $RIFFchunk;
}


function RIFFparseWAVEFORMATex($WaveFormatExData) {
    $WaveFormatEx['raw']['wFormatTag']      = LittleEndian2Int(substr($WaveFormatExData,  0, 2));
    $WaveFormatEx['raw']['nChannels']       = LittleEndian2Int(substr($WaveFormatExData,  2, 2));
    $WaveFormatEx['raw']['nSamplesPerSec']  = LittleEndian2Int(substr($WaveFormatExData,  4, 4));
    $WaveFormatEx['raw']['nAvgBytesPerSec'] = LittleEndian2Int(substr($WaveFormatExData,  8, 4));
    $WaveFormatEx['raw']['nBlockAlign']     = LittleEndian2Int(substr($WaveFormatExData, 12, 2));
    $WaveFormatEx['raw']['nBitsPerSample']  = LittleEndian2Int(substr($WaveFormatExData, 14, 2));

    $WaveFormatEx['codec']           = RIFFwFormatTagLookup($WaveFormatEx['raw']['wFormatTag']);
    $WaveFormatEx['channels']        = $WaveFormatEx['raw']['nChannels'];
    $WaveFormatEx['sample_rate']     = $WaveFormatEx['raw']['nSamplesPerSec'];
    $WaveFormatEx['bitrate']         = $WaveFormatEx['raw']['nAvgBytesPerSec'] * 8;
    $WaveFormatEx['bits_per_sample'] = $WaveFormatEx['raw']['nBitsPerSample'];

    return $WaveFormatEx;
}


function RIFFwFormatTagLookup($wFormatTag) {
    static $RIFFwFormatTagLookup = array();
    if (empty($RIFFwFormatTagLookup)) {
		$RIFFwFormatTagLookup[0x0000] = 'Microsoft Unknown Wave Format';
		$RIFFwFormatTagLookup[0x0001] = 'Microsoft Pulse Code Modulation (PCM)';
		$RIFFwFormatTagLookup[0x0002] = 'Microsoft ADPCM';
		$RIFFwFormatTagLookup[0x0003] = 'IEEE Float';
		$RIFFwFormatTagLookup[0x0004] = 'Compaq Computer VSELP';
		$RIFFwFormatTagLookup[0x0005] = 'IBM CVSD';
		$RIFFwFormatTagLookup[0x0006] = 'Microsoft A-Law';
		$RIFFwFormatTagLookup[0x0007] = 'Microsoft mu-Law';
		$RIFFwFormatTagLookup[0x0010] = 'OKI ADPCM';
		$RIFFwFormatTagLookup[0x0011] = 'Intel DVI/IMA ADPCM';
		$RIFFwFormatTagLookup[0x0012] = 'Videologic MediaSpace ADPCM';
		$RIFFwFormatTagLookup[0x0013] = 'Sierra Semiconductor ADPCM';
		$RIFFwFormatTagLookup[0x0014] = 'Antex Electronics G.723 ADPCM';
		$RIFFwFormatTagLookup[0x0015] = 'DSP Solutions DigiSTD';
		$RIFFwFormatTagLookup[0x0016] = 'DSP Solutions DigiFIX';
		$RIFFwFormatTagLookup[0x0017] = 'Dialogic OKI ADPCM';
		$RIFFwFormatTagLookup[0x0018] = 'MediaVision ADPCM';
		$RIFFwFormatTagLookup[0x0019] = 'Hewlett-Packard CU';
		$RIFFwFormatTagLookup[0x0020] = 'Yamaha ADPCM';
		$RIFFwFormatTagLookup[0x0021] = 'Speech Compression Sonarc';
		$RIFFwFormatTagLookup[0x0022] = 'DSP Group TrueSpeech';
		$RIFFwFormatTagLookup[0x0023] = 'Echo Speech EchoSC1';
		$RIFFwFormatTagLookup[0x0024] = 'Audiofile AF36';
		$RIFFwFormatTagLookup[0x0025] = 'Audio Processing Technology APTX';
		$RIFFwFormatTagLookup[0x0026] = 'AudioFile AF10';
		$RIFFwFormatTagLookup[0x0027] = 'Prosody 1612';
		$RIFFwFormatTagLookup[0x0028] = 'LRC';
		$RIFFwFormatTagLookup[0x0030] = 'Dolby AC2';
		$RIFFwFormatTagLookup[0x0031] = 'Microsoft GSM 6.10';
		$RIFFwFormatTagLookup[0x0032] = 'MSNAudio';
		$RIFFwFormatTagLookup[0x0033] = 'Antex Electronics ADPCME';
		$RIFFwFormatTagLookup[0x0034] = 'Control Resources VQLPC';
		$RIFFwFormatTagLookup[0x0035] = 'DSP Solutions DigiREAL';
		$RIFFwFormatTagLookup[0x0036] = 'DSP Solutions DigiADPCM';
		$RIFFwFormatTagLookup[0x0037] = 'Control Resources CR10';
		$RIFFwFormatTagLookup[0x0038] = 'Natural MicroSystems VBXADPCM';
		$RIFFwFormatTagLookup[0x0039] = 'Crystal Semiconductor IMA ADPCM';
		$RIFFwFormatTagLookup[0x003A] = 'EchoSC3';
		$RIFFwFormatTagLookup[0x003B] = 'Rockwell ADPCM';
		$RIFFwFormatTagLookup[0x003C] = 'Rockwell Digit LK';
		$RIFFwFormatTagLookup[0x003D] = 'Xebec';
		$RIFFwFormatTagLookup[0x0040] = 'Antex Electronics G.721 ADPCM';
		$RIFFwFormatTagLookup[0x0041] = 'G.728 CELP';
		$RIFFwFormatTagLookup[0x0042] = 'MSG723';
		$RIFFwFormatTagLookup[0x0050] = 'Microsoft MPEG';
		$RIFFwFormatTagLookup[0x0052] = 'RT24';
		$RIFFwFormatTagLookup[0x0053] = 'PAC';
		$RIFFwFormatTagLookup[0x0055] = 'MPEG Layer 3';
		$RIFFwFormatTagLookup[0x0059] = 'Lucent G.723';
		$RIFFwFormatTagLookup[0x0060] = 'Cirrus';
		$RIFFwFormatTagLookup[0x0061] = 'ESPCM';
		$RIFFwFormatTagLookup[0x0062] = 'Voxware';
		$RIFFwFormatTagLookup[0x0063] = 'Canopus Atrac';
		$RIFFwFormatTagLookup[0x0064] = 'G.726 ADPCM';
		$RIFFwFormatTagLookup[0x0065] = 'G.722 ADPCM';
		$RIFFwFormatTagLookup[0x0066] = 'DSAT';
		$RIFFwFormatTagLookup[0x0067] = 'DSAT Display';
		$RIFFwFormatTagLookup[0x0069] = 'Voxware Byte Aligned';
		$RIFFwFormatTagLookup[0x0070] = 'Voxware AC8';
		$RIFFwFormatTagLookup[0x0071] = 'Voxware AC10';
		$RIFFwFormatTagLookup[0x0072] = 'Voxware AC16';
		$RIFFwFormatTagLookup[0x0073] = 'Voxware AC20';
		$RIFFwFormatTagLookup[0x0074] = 'Voxware MetaVoice';
		$RIFFwFormatTagLookup[0x0075] = 'Voxware MetaSound';
		$RIFFwFormatTagLookup[0x0076] = 'Voxware RT29HW';
		$RIFFwFormatTagLookup[0x0077] = 'Voxware VR12';
		$RIFFwFormatTagLookup[0x0078] = 'Voxware VR18';
		$RIFFwFormatTagLookup[0x0079] = 'Voxware TQ40';
		$RIFFwFormatTagLookup[0x0080] = 'Softsound';
		$RIFFwFormatTagLookup[0x0081] = 'Voxware TQ60';
		$RIFFwFormatTagLookup[0x0082] = 'MSRT24';
		$RIFFwFormatTagLookup[0x0083] = 'G.729A';
		$RIFFwFormatTagLookup[0x0084] = 'MVI MV12';
		$RIFFwFormatTagLookup[0x0085] = 'DF G.726';
		$RIFFwFormatTagLookup[0x0086] = 'DF GSM610';
		$RIFFwFormatTagLookup[0x0088] = 'ISIAudio';
		$RIFFwFormatTagLookup[0x0089] = 'Onlive';
		$RIFFwFormatTagLookup[0x0091] = 'SBC24';
		$RIFFwFormatTagLookup[0x0092] = 'Dolby AC3 SPDIF';
		$RIFFwFormatTagLookup[0x0097] = 'ZyXEL ADPCM';
		$RIFFwFormatTagLookup[0x0098] = 'Philips LPCBB';
		$RIFFwFormatTagLookup[0x0099] = 'Packed';
		$RIFFwFormatTagLookup[0x0100] = 'Rhetorex ADPCM';
		$RIFFwFormatTagLookup[0x0101] = 'IBM mu-law';
		$RIFFwFormatTagLookup[0x0102] = 'IBM A-law';
		$RIFFwFormatTagLookup[0x0103] = 'IBM AVC Adaptive Differential Pulse Code Modulation (ADPCM)';
		$RIFFwFormatTagLookup[0x0111] = 'Vivo G.723';
		$RIFFwFormatTagLookup[0x0112] = 'Vivo Siren';
		$RIFFwFormatTagLookup[0x0123] = 'Digital G.723';
		$RIFFwFormatTagLookup[0x0140] = 'Windows Media Video V8';
		$RIFFwFormatTagLookup[0x0161] = 'Windows Media Audio V7 / V8 / V9';
		$RIFFwFormatTagLookup[0x0162] = 'Windows Media Audio Professional V9';
		$RIFFwFormatTagLookup[0x0163] = 'Windows Media Audio Lossless V9';
		$RIFFwFormatTagLookup[0x0200] = 'Creative Labs ADPCM';
		$RIFFwFormatTagLookup[0x0202] = 'Creative Labs Fastspeech8';
		$RIFFwFormatTagLookup[0x0203] = 'Creative Labs Fastspeech10';
		$RIFFwFormatTagLookup[0x0220] = 'Quarterdeck';
		$RIFFwFormatTagLookup[0x0300] = 'FM Towns Snd';
		$RIFFwFormatTagLookup[0x0300] = 'Fujitsu FM Towns Snd';
		$RIFFwFormatTagLookup[0x0400] = 'BTV Digital';
		$RIFFwFormatTagLookup[0x0680] = 'VME VMPCM';
		$RIFFwFormatTagLookup[0x1000] = 'Olivetti GSM';
		$RIFFwFormatTagLookup[0x1001] = 'Olivetti ADPCM';
		$RIFFwFormatTagLookup[0x1002] = 'Olivetti CELP';
		$RIFFwFormatTagLookup[0x1003] = 'Olivetti SBC';
		$RIFFwFormatTagLookup[0x1004] = 'Olivetti OPR';
		$RIFFwFormatTagLookup[0x1100] = 'Lernout & Hauspie LH Codec';
		$RIFFwFormatTagLookup[0x1400] = 'Norris';
		$RIFFwFormatTagLookup[0x1401] = 'AT&T ISIAudio';
		$RIFFwFormatTagLookup[0x1500] = 'Soundspace Music Compression';
		$RIFFwFormatTagLookup[0x2000] = 'AC3';
		$RIFFwFormatTagLookup[0x7A21] = 'GSM-AMR (CBR, no SID)';
		$RIFFwFormatTagLookup[0x7A22] = 'GSM-AMR (VBR, including SID)';
		$RIFFwFormatTagLookup[0xFFFF] = 'development';
    }

    return (isset($RIFFwFormatTagLookup[$wFormatTag]) ? $RIFFwFormatTagLookup[$wFormatTag] : 'unknown: 0x'.dechex($wFormatTag));
}

function RIFFfourccLookup($fourcc) {
    static $RIFFfourccLookup = array();
    if (empty($RIFFfourccLookup)) {
		$RIFFfourccLookup['3IV1'] = '3ivx v1';
		$RIFFfourccLookup['3IV2'] = '3ivx v2';
		$RIFFfourccLookup['AASC'] = 'Autodesk Animator';
		$RIFFfourccLookup['ABYR'] = 'Kensington ?ABYR?';
		$RIFFfourccLookup['AEMI'] = 'Array VideoONE MPEG1-I Capture';
		$RIFFfourccLookup['AFLC'] = 'Autodesk Animator FLC';
		$RIFFfourccLookup['AFLI'] = 'Autodesk Animator FLI';
		$RIFFfourccLookup['AMPG'] = 'Array VideoONE MPEG';
		$RIFFfourccLookup['ANIM'] = 'Intel RDX (ANIM)';
		$RIFFfourccLookup['AP41'] = 'AngelPotion Definitive';
		$RIFFfourccLookup['ASV1'] = 'Asus Video v1';
		$RIFFfourccLookup['ASV2'] = 'Asus Video v2';
		$RIFFfourccLookup['ASVX'] = 'Asus Video 2.0 (audio)';
		$RIFFfourccLookup['AUR2'] = 'Aura 2 Codec - YUV 4:2:2';
		$RIFFfourccLookup['AURA'] = 'Aura 1 Codec - YUV 4:1:1';
		$RIFFfourccLookup['BINK'] = 'RAD Game Tools Bink Video';
		$RIFFfourccLookup['BT20'] = 'Conexant Prosumer Video';
		$RIFFfourccLookup['BTCV'] = 'Conexant Composite Video Codec';
		$RIFFfourccLookup['BW10'] = 'Data Translation Broadway MPEG Capture';
		$RIFFfourccLookup['CC12'] = 'Intel YUV12';
		$RIFFfourccLookup['CDVC'] = 'Canopus DV';
		$RIFFfourccLookup['CFCC'] = 'Digital Processing Systems DPS Perception';
		$RIFFfourccLookup['CGDI'] = 'Microsoft Office 97 Camcorder Video';
		$RIFFfourccLookup['CHAM'] = 'Winnov Caviara Champagne';
		$RIFFfourccLookup['CJPG'] = 'Creative WebCam JPEG';
		$RIFFfourccLookup['CLJR'] = 'Cirrus Logic YUV 4 pixels';
		$RIFFfourccLookup['CMYK'] = 'Common Data Format in Printing';
		$RIFFfourccLookup['CPLA'] = 'Weitek 4:2:0 YUV Planar';
		$RIFFfourccLookup['CRAM'] = 'Microsoft Video 1 (CRAM)';
		$RIFFfourccLookup['CVID'] = 'Radius Cinepak';
		$RIFFfourccLookup['CWLT'] = '?CWLT?';
		$RIFFfourccLookup['CWLT'] = 'Microsoft Color WLT DIB';
		$RIFFfourccLookup['CYUV'] = 'Creative Labs YUV';
		$RIFFfourccLookup['CYUY'] = 'ATI YUV';
		$RIFFfourccLookup['D261'] = 'H.261';
		$RIFFfourccLookup['D263'] = 'H.263';
		$RIFFfourccLookup['DIV3'] = 'DivX v3 MPEG-4 Low-Motion';
		$RIFFfourccLookup['DIV4'] = 'DivX v3 MPEG-4 Fast-Motion';
		$RIFFfourccLookup['DIV5'] = '?DIV5?';
		$RIFFfourccLookup['DIVX'] = 'DivX v4';
		$RIFFfourccLookup['divx'] = 'DivX';
		$RIFFfourccLookup['DMB1'] = 'Matrox Rainbow Runner hardware MJPEG';
		$RIFFfourccLookup['DMB2'] = 'Paradigm MJPEG';
		$RIFFfourccLookup['DSVD'] = '?DSVD?';
		$RIFFfourccLookup['DUCK'] = 'Duck True Motion 1.0';
		$RIFFfourccLookup['DVAN'] = '?DVAN?';
		$RIFFfourccLookup['DVE2'] = 'InSoft DVE-2 Videoconferencing';
		$RIFFfourccLookup['dvsd'] = 'DV';
		$RIFFfourccLookup['DVSD'] = 'DV';
		$RIFFfourccLookup['DVX1'] = 'DVX1000SP Video Decoder';
		$RIFFfourccLookup['DVX2'] = 'DVX2000S Video Decoder';
		$RIFFfourccLookup['DVX3'] = 'DVX3000S Video Decoder';
		$RIFFfourccLookup['DX50'] = 'DivX v5';
		$RIFFfourccLookup['DXT1'] = 'Microsoft DirectX Compressed Texture (DXT1)';
		$RIFFfourccLookup['DXT2'] = 'Microsoft DirectX Compressed Texture (DXT2)';
		$RIFFfourccLookup['DXT3'] = 'Microsoft DirectX Compressed Texture (DXT3)';
		$RIFFfourccLookup['DXT4'] = 'Microsoft DirectX Compressed Texture (DXT4)';
		$RIFFfourccLookup['DXT5'] = 'Microsoft DirectX Compressed Texture (DXT5)';
		$RIFFfourccLookup['DXTC'] = 'Microsoft DirectX Compressed Texture (DXTC)';
		$RIFFfourccLookup['EKQ0'] = 'Elsa ?EKQ0?';
		$RIFFfourccLookup['ELK0'] = 'Elsa ?ELK0?';
		$RIFFfourccLookup['ESCP'] = 'Eidos Escape';
		$RIFFfourccLookup['ETV1'] = 'eTreppid Video ETV1';
		$RIFFfourccLookup['ETV2'] = 'eTreppid Video ETV2';
		$RIFFfourccLookup['ETVC'] = 'eTreppid Video ETVC';
		$RIFFfourccLookup['FLJP'] = 'D-Vision Field Encoded Motion JPEG';
		$RIFFfourccLookup['FRWA'] = 'SoftLab-Nsk Forward Motion JPEG w/ alpha channel';
		$RIFFfourccLookup['FRWD'] = 'SoftLab-Nsk Forward Motion JPEG';
		$RIFFfourccLookup['FVF1'] = 'Iterated Systems Fractal Video Frame';
		$RIFFfourccLookup['GLZW'] = 'Motion LZW (gabest@freemail.hu)';
		$RIFFfourccLookup['GPEG'] = 'Motion JPEG (gabest@freemail.hu)';
		$RIFFfourccLookup['GWLT'] = 'Microsoft Greyscale WLT DIB';
		$RIFFfourccLookup['H260'] = 'Intel ITU H.260 Videoconferencing';
		$RIFFfourccLookup['H261'] = 'Intel ITU H.261 Videoconferencing';
		$RIFFfourccLookup['H262'] = 'Intel ITU H.262 Videoconferencing';
		$RIFFfourccLookup['H263'] = 'Intel ITU H.263 Videoconferencing';
		$RIFFfourccLookup['H264'] = 'Intel ITU H.264 Videoconferencing';
		$RIFFfourccLookup['H265'] = 'Intel ITU H.265 Videoconferencing';
		$RIFFfourccLookup['H266'] = 'Intel ITU H.266 Videoconferencing';
		$RIFFfourccLookup['H267'] = 'Intel ITU H.267 Videoconferencing';
		$RIFFfourccLookup['H268'] = 'Intel ITU H.268 Videoconferencing';
		$RIFFfourccLookup['H269'] = 'Intel ITU H.269 Videoconferencing';
		$RIFFfourccLookup['HFYU'] = 'Huffman Lossless Codec';
		$RIFFfourccLookup['HMCR'] = 'Rendition Motion Compensation Format (HMCR)';
		$RIFFfourccLookup['HMRR'] = 'Rendition Motion Compensation Format (HMRR)';
		$RIFFfourccLookup['i263'] = 'Intel ITU H.263 Videoconferencing (i263)';
		$RIFFfourccLookup['I420'] = 'Intel Indeo 4';
		$RIFFfourccLookup['IAN '] = 'Intel RDX';
		$RIFFfourccLookup['ICLB'] = 'InSoft CellB Videoconferencing';
		$RIFFfourccLookup['IGOR'] = 'Power DVD';
		$RIFFfourccLookup['IJPG'] = 'Intergraph JPEG';
		$RIFFfourccLookup['ILVC'] = 'Intel Layered Video';
		$RIFFfourccLookup['ILVR'] = 'ITU-T H.263+';
		$RIFFfourccLookup['IPDV'] = 'I-O Data Device Giga AVI DV Codec';
		$RIFFfourccLookup['IR21'] = 'Intel Indeo 2.1';
		$RIFFfourccLookup['IRAW'] = 'Intel YUV Uncompressed';
		$RIFFfourccLookup['IV30'] = 'Ligos Indeo 3.0';
		$RIFFfourccLookup['IV31'] = 'Ligos Indeo 3.1';
		$RIFFfourccLookup['IV32'] = 'Ligos Indeo 3.2';
		$RIFFfourccLookup['IV33'] = 'Ligos Indeo 3.3';
		$RIFFfourccLookup['IV34'] = 'Ligos Indeo 3.4';
		$RIFFfourccLookup['IV35'] = 'Ligos Indeo 3.5';
		$RIFFfourccLookup['IV36'] = 'Ligos Indeo 3.6';
		$RIFFfourccLookup['IV37'] = 'Ligos Indeo 3.7';
		$RIFFfourccLookup['IV38'] = 'Ligos Indeo 3.8';
		$RIFFfourccLookup['IV39'] = 'Ligos Indeo 3.9';
		$RIFFfourccLookup['IV40'] = 'Ligos Indeo Interactive 4.0';
		$RIFFfourccLookup['IV41'] = 'Ligos Indeo Interactive 4.1';
		$RIFFfourccLookup['IV42'] = 'Ligos Indeo Interactive 4.2';
		$RIFFfourccLookup['IV43'] = 'Ligos Indeo Interactive 4.3';
		$RIFFfourccLookup['IV44'] = 'Ligos Indeo Interactive 4.4';
		$RIFFfourccLookup['IV45'] = 'Ligos Indeo Interactive 4.5';
		$RIFFfourccLookup['IV46'] = 'Ligos Indeo Interactive 4.6';
		$RIFFfourccLookup['IV47'] = 'Ligos Indeo Interactive 4.7';
		$RIFFfourccLookup['IV48'] = 'Ligos Indeo Interactive 4.8';
		$RIFFfourccLookup['IV49'] = 'Ligos Indeo Interactive 4.9';
		$RIFFfourccLookup['IV50'] = 'Ligos Indeo Interactive 5.0';
		$RIFFfourccLookup['JBYR'] = 'Kensington ?JBYR?';
		$RIFFfourccLookup['JPEG'] = 'Still Image JPEG DIB';
		$RIFFfourccLookup['JPGL'] = 'Webcam JPEG Light?';
		$RIFFfourccLookup['KMVC'] = 'Karl Morton\'s Video Codec';
		$RIFFfourccLookup['LEAD'] = 'LEAD Video Codec';
		$RIFFfourccLookup['Ljpg'] = 'LEAD MJPEG Codec';
		$RIFFfourccLookup['M261'] = 'Microsoft H.261';
		$RIFFfourccLookup['M263'] = 'Microsoft H.263';
		$RIFFfourccLookup['M4S2'] = 'Microsoft MPEG-4 (M4S2)';
		$RIFFfourccLookup['m4s2'] = 'Microsoft MPEG-4 (m4s2)';
		$RIFFfourccLookup['MC12'] = 'ATI Motion Compensation Format (MC12)';
		$RIFFfourccLookup['MCAM'] = 'ATI Motion Compensation Format (MCAM)';
		$RIFFfourccLookup['MJ2C'] = 'Morgan Multimedia Motion JPEG2000';
		$RIFFfourccLookup['mJPG'] = 'IBM Motion JPEG w/ Huffman Tables';
		$RIFFfourccLookup['MJPG'] = 'Motion JPEG DIB';
		$RIFFfourccLookup['MP42'] = 'Microsoft MPEG-4 (low-motion)';
		$RIFFfourccLookup['MP43'] = 'Microsoft MPEG-4 (fast-motion)';
		$RIFFfourccLookup['MP4S'] = 'Microsoft MPEG-4 (MP4S)';
		$RIFFfourccLookup['mp4s'] = 'Microsoft MPEG-4 (mp4s)';
		$RIFFfourccLookup['MPEG'] = 'MPEG 1 Video I-Frame';
		$RIFFfourccLookup['MPG4'] = 'Microsoft MPEG-4 Video High Speed Compressor';
		$RIFFfourccLookup['MPGI'] = 'Sigma Designs MPEG';
		$RIFFfourccLookup['MRCA'] = 'FAST Multimedia Mrcodec';
		$RIFFfourccLookup['MRCA'] = 'Martin Regen Codec';
		$RIFFfourccLookup['MRLE'] = 'Microsoft RLE';
		$RIFFfourccLookup['MRLE'] = 'Run Length Encoding';
		$RIFFfourccLookup['MSVC'] = 'Microsoft Video 1';
		$RIFFfourccLookup['MTX1'] = 'Matrox ?MTX1?';
		$RIFFfourccLookup['MTX2'] = 'Matrox ?MTX2?';
		$RIFFfourccLookup['MTX3'] = 'Matrox ?MTX3?';
		$RIFFfourccLookup['MTX4'] = 'Matrox ?MTX4?';
		$RIFFfourccLookup['MTX5'] = 'Matrox ?MTX5?';
		$RIFFfourccLookup['MTX6'] = 'Matrox ?MTX6?';
		$RIFFfourccLookup['MTX7'] = 'Matrox ?MTX7?';
		$RIFFfourccLookup['MTX8'] = 'Matrox ?MTX8?';
		$RIFFfourccLookup['MTX9'] = 'Matrox ?MTX9?';
		$RIFFfourccLookup['MV12'] = '?MV12?';
		$RIFFfourccLookup['MWV1'] = 'Aware Motion Wavelets';
		$RIFFfourccLookup['nAVI'] = '?nAVI?';
		$RIFFfourccLookup['NTN1'] = 'Nogatech Video Compression 1';
		$RIFFfourccLookup['NVS0'] = 'nVidia GeForce Texture (NVS0)';
		$RIFFfourccLookup['NVS1'] = 'nVidia GeForce Texture (NVS1)';
		$RIFFfourccLookup['NVS2'] = 'nVidia GeForce Texture (NVS2)';
		$RIFFfourccLookup['NVS3'] = 'nVidia GeForce Texture (NVS3)';
		$RIFFfourccLookup['NVS4'] = 'nVidia GeForce Texture (NVS4)';
		$RIFFfourccLookup['NVS5'] = 'nVidia GeForce Texture (NVS5)';
		$RIFFfourccLookup['NVT0'] = 'nVidia GeForce Texture (NVT0)';
		$RIFFfourccLookup['NVT1'] = 'nVidia GeForce Texture (NVT1)';
		$RIFFfourccLookup['NVT2'] = 'nVidia GeForce Texture (NVT2)';
		$RIFFfourccLookup['NVT3'] = 'nVidia GeForce Texture (NVT3)';
		$RIFFfourccLookup['NVT4'] = 'nVidia GeForce Texture (NVT4)';
		$RIFFfourccLookup['NVT5'] = 'nVidia GeForce Texture (NVT5)';
		$RIFFfourccLookup['PDVC'] = 'I-O Data Device Digital Video Capture DV codec';
		$RIFFfourccLookup['PGVV'] = 'Radius Video Vision';
		$RIFFfourccLookup['PHMO'] = 'IBM Photomotion';
		$RIFFfourccLookup['PIM1'] = 'Pegasus Imaging ?PIM1?';
		$RIFFfourccLookup['PIM2'] = 'Pegasus Imaging ?PIM2?';
		$RIFFfourccLookup['PIMJ'] = 'Pegasus Imaging Lossless JPEG';
		$RIFFfourccLookup['PVEZ'] = 'Horizons Technology PowerEZ';
		$RIFFfourccLookup['PVMM'] = 'PacketVideo Corporation MPEG-4';
		$RIFFfourccLookup['PVW2'] = 'Pegasus Imaging Wavelet Compression';
		$RIFFfourccLookup['QPEG'] = 'Q-Team QPEG 1.0';
		$RIFFfourccLookup['qpeq'] = 'Q-Team QPEG 1.1';
		$RIFFfourccLookup['RGBT'] = 'Computer Concepts 32-bit support';
		$RIFFfourccLookup['RLE '] = 'Microsoft Run Length Encoder';
		$RIFFfourccLookup['RLE4'] = 'Run Length Encoded 4';
		$RIFFfourccLookup['RLE8'] = 'Run Length Encoded 8';
		$RIFFfourccLookup['RT21'] = 'Intel Indeo 2.1';
		$RIFFfourccLookup['RT21'] = 'Intel Real Time Video 2.1';
		$RIFFfourccLookup['rv20'] = 'RealVideo G2';
		$RIFFfourccLookup['rv30'] = 'RealVideo 8';
		$RIFFfourccLookup['RVX '] = 'Intel RDX (RVX )';
		$RIFFfourccLookup['s422'] = 'Tekram VideoCap C210 YUV 4:2:2';
		$RIFFfourccLookup['SDCC'] = 'Sun Communication Digital Camera Codec';
		$RIFFfourccLookup['SFMC'] = 'CrystalNet Surface Fitting Method';
		$RIFFfourccLookup['SMSC'] = 'Radius SMSC';
		$RIFFfourccLookup['SMSD'] = 'Radius SMSD';
		$RIFFfourccLookup['smsv'] = 'WorldConnect Wavelet Video';
		$RIFFfourccLookup['SPIG'] = 'Radius Spigot';
		$RIFFfourccLookup['SPLC'] = 'Splash Studios ACM Audio Codec';
		$RIFFfourccLookup['SQZ2'] = 'Microsoft VXTreme Video Codec V2';
		$RIFFfourccLookup['STVA'] = 'ST CMOS Imager Data (Bayer)';
		$RIFFfourccLookup['STVB'] = 'ST CMOS Imager Data (Nudged Bayer)';
		$RIFFfourccLookup['STVC'] = 'ST CMOS Imager Data (Bunched)';
		$RIFFfourccLookup['STVX'] = 'ST CMOS Imager Data (Extended CODEC Data Format)';
		$RIFFfourccLookup['STVY'] = 'ST CMOS Imager Data (Extended CODEC Data Format with Correction Data)';
		$RIFFfourccLookup['SV10'] = 'Sorenson Video R1';
		$RIFFfourccLookup['SVQ1'] = 'Sorenson Video';
		$RIFFfourccLookup['TLMS'] = 'TeraLogic Motion Intraframe Codec (TLMS)';
		$RIFFfourccLookup['TLST'] = 'TeraLogic Motion Intraframe Codec (TLST)';
		$RIFFfourccLookup['TM20'] = 'Duck TrueMotion 2.0';
		$RIFFfourccLookup['TM2X'] = 'Duck TrueMotion 2X';
		$RIFFfourccLookup['TMIC'] = 'TeraLogic Motion Intraframe Codec (TMIC)';
		$RIFFfourccLookup['TMOT'] = 'Horizons Technology TrueMotion S';
		$RIFFfourccLookup['tmot'] = 'Horizons TrueMotion Video Compression';
		$RIFFfourccLookup['TR20'] = 'Duck TrueMotion RealTime 2.0';
		$RIFFfourccLookup['TSCC'] = 'TechSmith Screen Capture Codec';
		$RIFFfourccLookup['TV10'] = 'Tecomac Low-Bit Rate Codec';
		$RIFFfourccLookup['TY0N'] = 'Trident ?TY0N?';
		$RIFFfourccLookup['TY2C'] = 'Trident ?TY2C?';
		$RIFFfourccLookup['TY2N'] = 'Trident ?TY2N?';
		$RIFFfourccLookup['UCOD'] = 'eMajix.com ClearVideo';
		$RIFFfourccLookup['ULTI'] = 'IBM Ultimotion';
		$RIFFfourccLookup['UYVY'] = 'UYVY 4:2:2 byte ordering';
		$RIFFfourccLookup['V261'] = 'Lucent VX2000S';
		$RIFFfourccLookup['V422'] = '24 bit YUV 4:2:2 Format';
		$RIFFfourccLookup['V655'] = '16 bit YUV 4:2:2 Format';
		$RIFFfourccLookup['VCR1'] = 'ATI VCR 1.0';
		$RIFFfourccLookup['VCR2'] = 'ATI VCR 2.0';
		$RIFFfourccLookup['VCR3'] = 'ATI VCR 3.0';
		$RIFFfourccLookup['VCR4'] = 'ATI VCR 4.0';
		$RIFFfourccLookup['VCR5'] = 'ATI VCR 5.0';
		$RIFFfourccLookup['VCR6'] = 'ATI VCR 6.0';
		$RIFFfourccLookup['VCR7'] = 'ATI VCR 7.0';
		$RIFFfourccLookup['VCR8'] = 'ATI VCR 8.0';
		$RIFFfourccLookup['VCR9'] = 'ATI VCR 9.0';
		$RIFFfourccLookup['VDCT'] = 'Video Maker Pro DIB';
		$RIFFfourccLookup['VDOM'] = 'VDOnet VDOWave';
		$RIFFfourccLookup['VDOW'] = 'VDOnet VDOLive (H.263)';
		$RIFFfourccLookup['VDTZ'] = 'Darim Vison VideoTizer YUV';
		$RIFFfourccLookup['VGPX'] = 'VGPixel Codec';
		$RIFFfourccLookup['VIDS'] = 'Vitec Multimedia YUV 4:2:2 CCIR 601 for V422';
		$RIFFfourccLookup['VIDS'] = 'YUV 4:2:2 CCIR 601 for V422';
		$RIFFfourccLookup['VIFP'] = '?VIFP?';
		$RIFFfourccLookup['VIVO'] = 'Vivo H.263 v2.00';
		$RIFFfourccLookup['vivo'] = 'Vivo H.263';
		$RIFFfourccLookup['VIXL'] = 'Miro Video XL';
		$RIFFfourccLookup['VLV1'] = 'Videologic VLCAP.DRV';
		$RIFFfourccLookup['VP30'] = 'On2 VP3.0';
		$RIFFfourccLookup['VP31'] = 'On2 VP3.1';
		$RIFFfourccLookup['VX1K'] = 'VX1000S Video Codec';
		$RIFFfourccLookup['VX2K'] = 'VX2000S Video Codec';
		$RIFFfourccLookup['VXSP'] = 'VX1000SP Video Codec';
		$RIFFfourccLookup['WBVC'] = 'Winbond W9960';
		$RIFFfourccLookup['WHAM'] = 'Microsoft Video 1 (WHAM)';
		$RIFFfourccLookup['WINX'] = 'Winnov Software Compression';
		$RIFFfourccLookup['WJPG'] = 'AverMedia Winbond JPEG';
		$RIFFfourccLookup['WMV1'] = 'Windows Media Video V7';
		$RIFFfourccLookup['WMV2'] = 'Windows Media Video V8';
		$RIFFfourccLookup['WMV3'] = 'Windows Media Video V9';
		$RIFFfourccLookup['WNV1'] = 'Winnov Hardware Compression';
		$RIFFfourccLookup['x263'] = 'Xirlink H.263';
		$RIFFfourccLookup['XLV0'] = 'NetXL Video Decoder';
		$RIFFfourccLookup['XMPG'] = 'Xing MPEG (I-Frame only)';
		$RIFFfourccLookup['XVID'] = 'XviD MPEG-4';
		$RIFFfourccLookup['XXAN'] = '?XXAN?';
		$RIFFfourccLookup['Y211'] = 'YUV 2:1:1 Packed';
		$RIFFfourccLookup['Y411'] = 'YUV 4:1:1 Packed';
		$RIFFfourccLookup['Y41B'] = 'YUV 4:1:1 Planar';
		$RIFFfourccLookup['Y41P'] = 'PC1 4:1:1';
		$RIFFfourccLookup['Y41T'] = 'PC1 4:1:1 with transparency';
		$RIFFfourccLookup['Y42B'] = 'YUV 4:2:2 Planar';
		$RIFFfourccLookup['Y42T'] = 'PCI 4:2:2 with transparency';
		$RIFFfourccLookup['Y8  '] = 'Grayscale video';
		$RIFFfourccLookup['YC12'] = 'Intel YUV 12 codec';
		$RIFFfourccLookup['YC12'] = 'Intel YUV12 Codec';
		$RIFFfourccLookup['YUV8'] = 'Winnov Caviar YUV8';
		$RIFFfourccLookup['YUV9'] = 'Intel YUV9';
		$RIFFfourccLookup['YUY2'] = 'Uncompressed YUV 4:2:2';
		$RIFFfourccLookup['YUYV'] = 'Canopus YUV';
		$RIFFfourccLookup['YV12'] = 'YVU12 Planar';
		$RIFFfourccLookup['YVU9'] = 'Intel YVU9 Planar';
		$RIFFfourccLookup['YVYU'] = 'YVYU 4:2:2 byte ordering';
		$RIFFfourccLookup['ZLIB'] = '?ZLIB?';
		$RIFFfourccLookup['ZPEG'] = 'Metheus Video Zipper';
    }

    return (isset($RIFFfourccLookup["$fourcc"]) ? $RIFFfourccLookup["$fourcc"] : '');
}

?>