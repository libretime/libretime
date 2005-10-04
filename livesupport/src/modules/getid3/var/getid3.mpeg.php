<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.mpeg.php - part of getID3()                          //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getMPEGHeaderFilepointer(&$fd, &$ThisFileInfo) {
    $ThisFileInfo['fileformat'] = 'mpeg';

    // Start code                       32 bits
    // horizontal frame size            12 bits
    // vertical frame size              12 bits
    // pixel aspect ratio                4 bits
    // frame rate                        4 bits
    // bitrate                          18 bits
    // marker bit                        1 bit
    // VBV buffer size                  10 bits
    // constrained parameter flag        1 bit
    // intra quant. matrix flag          1 bit
    // intra quant. matrix values      512 bits (present if matrix flag == 1)
    // non-intra quant. matrix flag      1 bit
    // non-intra quant. matrix values  512 bits (present if matrix flag == 1)

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $MPEGvideoHeader = fread($fd, FREAD_BUFFER_SIZE);
    $offset = 0;

    // MPEG video information is found as $00 $00 $01 $B3
    $matching_pattern = chr(0x00).chr(0x00).chr(0x01).chr(0xB3);
    while (substr($MPEGvideoHeader, $offset++, 4) !== $matching_pattern) {
		if ($offset >= (strlen($MPEGvideoHeader) - 12)) {
			$MPEGvideoHeader .= fread($fd, FREAD_BUFFER_SIZE);
			$MPEGvideoHeader = substr($MPEGvideoHeader, $offset);
			$offset = 0;
			if (strlen($MPEGvideoHeader) < 12) {
				$ThisFileInfo['error'] .= "\n".'Could not find start of video block before end of file';
				unset($ThisFileInfo['fileformat']);
				return false;
			} elseif (ftell($fd) >= 100000) {
				$ThisFileInfo['error'] .= "\n".'Could not find start of video block in the first 100,000 bytes (this might not be an MPEG-video file?)';
				unset($ThisFileInfo['fileformat']);
				return false;
			}
		}
    }

    $ThisFileInfo['video']['dataformat'] = 'mpeg';

    $offset += (strlen($matching_pattern) - 1);

    $FrameSizeAspectRatioFrameRateDWORD = BigEndian2Int(substr($MPEGvideoHeader, $offset, 4));
    $offset += 4;

    $assortedinformation = BigEndian2Int(substr($MPEGvideoHeader, $offset, 4));
    $offset += 4;

    $ThisFileInfo['mpeg']['video']['raw']['framesize_horizontal'] = ($FrameSizeAspectRatioFrameRateDWORD & 0xFFF00000) >> 20; // 12 bits for horizontal frame size
    $ThisFileInfo['mpeg']['video']['raw']['framesize_vertical']   = ($FrameSizeAspectRatioFrameRateDWORD & 0x000FFF00) >> 8;  // 12 bits for vertical frame size
    $ThisFileInfo['mpeg']['video']['raw']['pixel_aspect_ratio']   = ($FrameSizeAspectRatioFrameRateDWORD & 0x000000F0) >> 4;
    $ThisFileInfo['mpeg']['video']['raw']['frame_rate']           = ($FrameSizeAspectRatioFrameRateDWORD & 0x0000000F);

    $ThisFileInfo['mpeg']['video']['framesize_horizontal'] = $ThisFileInfo['mpeg']['video']['raw']['framesize_horizontal'];
    $ThisFileInfo['mpeg']['video']['framesize_vertical']   = $ThisFileInfo['mpeg']['video']['raw']['framesize_vertical'];
    $ThisFileInfo['video']['resolution_x'] = $ThisFileInfo['mpeg']['video']['framesize_horizontal'];
    $ThisFileInfo['video']['resolution_y'] = $ThisFileInfo['mpeg']['video']['framesize_vertical'];

    $ThisFileInfo['mpeg']['video']['pixel_aspect_ratio']        = MPEGvideoAspectRatioLookup($ThisFileInfo['mpeg']['video']['raw']['pixel_aspect_ratio']);
    $ThisFileInfo['mpeg']['video']['pixel_aspect_ratio_text']   = MPEGvideoAspectRatioTextLookup($ThisFileInfo['mpeg']['video']['raw']['pixel_aspect_ratio']);
    $ThisFileInfo['mpeg']['video']['frame_rate']                = MPEGvideoFramerateLookup($ThisFileInfo['mpeg']['video']['raw']['frame_rate']);
    $ThisFileInfo['video']['frame_rate']                                 = $ThisFileInfo['mpeg']['video']['frame_rate'];

    $ThisFileInfo['mpeg']['video']['raw']['bitrate']                = ($assortedinformation & 0xFFFFC000) >> 14;
    $ThisFileInfo['mpeg']['video']['raw']['marker_bit']             = ($assortedinformation & 0x00002000) >> 13;
    $ThisFileInfo['mpeg']['video']['raw']['vbv_buffer_size']        = ($assortedinformation & 0x00001FF8) >> 3;
    $ThisFileInfo['mpeg']['video']['raw']['constrained_param_flag'] = ($assortedinformation & 0x00000004) >> 2;
    $ThisFileInfo['mpeg']['video']['raw']['intra_quant_flag']       = ($assortedinformation & 0x00000002) >> 1;

    if ($ThisFileInfo['mpeg']['video']['raw']['bitrate'] == 0x3FFFF) { // 18 set bits
		$ThisFileInfo['mpeg']['video']['bitrate_type'] = 'variable';
		$ThisFileInfo['bitrate_mode']                  = 'vbr';
    } else {
		$ThisFileInfo['mpeg']['video']['bitrate']      = $ThisFileInfo['mpeg']['video']['raw']['bitrate'] * 400;
		$ThisFileInfo['mpeg']['video']['bitrate_mode'] = 'cbr';
		$ThisFileInfo['video']['bitrate_mode']         = $ThisFileInfo['mpeg']['video']['bitrate_mode'];
		$ThisFileInfo['video']['bitrate']              = $ThisFileInfo['mpeg']['video']['bitrate'];
    }
    $ThisFileInfo['video']['bitrate_mode']         = $ThisFileInfo['mpeg']['video']['bitrate_mode'];
    $ThisFileInfo['video']['bitrate']              = $ThisFileInfo['mpeg']['video']['bitrate'];

    return true;
}

function MPEGvideoFramerateLookup($rawframerate) {
    $MPEGvideoFramerateLookup = array(0, 23.976, 24, 25, 29.97, 30, 50, 59.94, 60);
    return (isset($MPEGvideoFramerateLookup[$rawframerate]) ? (float) $MPEGvideoFramerateLookup[$rawframerate] : (float) 0);
}

function MPEGvideoAspectRatioLookup($rawaspectratio) {
    $MPEGvideoAspectRatioLookup = array(0, 1, 0.6735, 0.7031, 0.7615, 0.8055, 0.8437, 0.8935, 0.9157, 0.9815, 1.0255, 1.0695, 1.0950, 1.1575, 1.2015, 0);
    return (isset($MPEGvideoAspectRatioLookup[$rawaspectratio]) ? (float) $MPEGvideoAspectRatioLookup[$rawaspectratio] : (float) 0);
}

function MPEGvideoAspectRatioTextLookup($rawaspectratio) {
    $MPEGvideoAspectRatioTextLookup = array('forbidden', 'square pixels', '0.6735', '16:9, 625 line, PAL', '0.7615', '0.8055', '16:9, 525 line, NTSC', '0.8935', '4:3, 625 line, PAL, CCIR601', '0.9815', '1.0255', '1.0695', '4:3, 525 line, NTSC, CCIR601', '1.1575', '1.2015', 'reserved');
    return (isset($MPEGvideoAspectRatioTextLookup[$rawaspectratio]) ? $MPEGvideoAspectRatioTextLookup[$rawaspectratio] : '');
}

?>