<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.getimagesize.php - part of getID3()                  //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////
// GetURLImageSize( $urlpic ) determines the                   //
// dimensions of local/remote URL pictures.                    //
// returns array with ($width, $height, $type)                 //
//                                                             //
// Thanks to: Oyvind Hallsteinsen aka Gosub / ELq -            //
// gosub@elq.org  for the original size determining code       //
//                                                             //
// PHP Hack by Filipe Laborde-Basto Oct 21/2000                //
// FREELY DISTRIBUTABLE -- use at your sole discretion! :)     //
// Enjoy. (Not to be sold in commercial packages though,       //
// keep it free!) Feel free to contact me at fil@rezox.com     //
// (http://www.rezox.com)                                      //
//                                                             //
// Modified by James Heinrich <getid3@users.sourceforge.net>   //
// June 1, 2001 - created GetDataImageSize($imgData) by        //
// seperating the fopen() stuff to GetURLImageSize($urlpic)    //
// which then calls GetDataImageSize($imgData). The idea being //
// you can call GetDataImageSize($imgData) with image data     //
// from a database etc.                                        //
//                                                             //
/////////////////////////////////////////////////////////////////

define('GIF_SIG',     chr(0x47).chr(0x49).chr(0x46));  // 'GIF'

define('PNG_SIG',     chr(0x89).chr(0x50).chr(0x4E).chr(0x47).chr(0x0D).chr(0x0A).chr(0x1A).chr(0x0A));

define('JPG_SIG',     chr(0xFF).chr(0xD8).chr(0xFF));
define('JPG_SOS',     chr(0xDA)); // Start Of Scan - image data start
define('JPG_SOF0',    chr(0xC0)); // Start Of Frame N
define('JPG_SOF1',    chr(0xC1)); // N indicates which compression process
define('JPG_SOF2',    chr(0xC2)); // Only SOF0-SOF2 are now in common use
define('JPG_SOF3',    chr(0xC3));
// NB: codes C4 and CC are *not* SOF markers
define('JPG_SOF5',    chr(0xC5));
define('JPG_SOF6',    chr(0xC6));
define('JPG_SOF7',    chr(0xC7));
define('JPG_SOF9',    chr(0xC9));
define('JPG_SOF10',   chr(0xCA));
define('JPG_SOF11',   chr(0xCB));
// NB: codes C4 and CC are *not* SOF markers
define('JPG_SOF13',   chr(0xCD));
define('JPG_SOF14',   chr(0xCE));
define('JPG_SOF15',   chr(0xCF));
define('JPG_EOI',     chr(0xD9)); // End Of Image (end of datastream)


function GetURLImageSize($urlpic) {
    if ($fd = @fopen($urlpic, 'rb')){
		$imgData = fread($fd, filesize($urlpic));
		fclose($fd);
		return GetDataImageSize($imgData);
    } else {
		return array('', '', '');
    }
}


function GetDataImageSize($imgData) {
    $height = '';
    $width  = '';
    $type   = '';
    if ((substr($imgData, 0, 3) == GIF_SIG) && (strlen($imgData) > 10)) {
		$dim = unpack('v2dim', substr($imgData, 6, 4));
		$width  = $dim['dim1'];
		$height = $dim['dim2'];
		$type = 1;
    } elseif ((substr($imgData, 0, 8) == PNG_SIG) && (strlen($imgData) > 24)) {
		$dim = unpack('N2dim', substr($imgData, 16, 8));
		$width  = $dim['dim1'];
		$height = $dim['dim2'];
		$type = 3;
    } elseif ((substr($imgData, 0, 3) == JPG_SIG) && (strlen($imgData) > 4)) {
		///////////////// JPG CHUNK SCAN ////////////////////
		$imgPos = 2;
		$type = 2;
		$buffer = strlen($imgData) - 2;
		while ($imgPos < strlen($imgData)) {
			// synchronize to the marker 0xFF
			$imgPos = strpos($imgData, 0xFF, $imgPos) + 1;
			$marker = $imgData[$imgPos];
			do {
				$marker = ord($imgData[$imgPos++]);
			} while ($marker == 255);
			// find dimensions of block
			switch (chr($marker)) {
				// Grab width/height from SOF segment (these are acceptable chunk types)
				case JPG_SOF0:
				case JPG_SOF1:
				case JPG_SOF2:
				case JPG_SOF3:
				case JPG_SOF5:
				case JPG_SOF6:
				case JPG_SOF7:
				case JPG_SOF9:
				case JPG_SOF10:
				case JPG_SOF11:
				case JPG_SOF13:
				case JPG_SOF14:
				case JPG_SOF15:
					$dim = unpack('n2dim', substr($imgData, $imgPos + 3, 4));
					$height = $dim['dim1'];
					$width  = $dim['dim2'];
					break 2; // found it so exit
				case JPG_EOI:
				case JPG_SOS:
					return false;       // End loop in case we find one of these markers
				default:            // We're not interested in other markers
					$skiplen = (ord($imgData[$imgPos++]) << 8) + ord($imgData[$imgPos++]) - 2;
					// if the skip is more than what we've read in, read more
					$buffer -= $skiplen;
					if ($buffer < 512) { // if the buffer of data is too low, read more file.
						// $imgData .= fread( $fd,$skiplen+1024 );
						// $buffer += $skiplen + 1024;
						return false; // End loop in case we find run out of data
					}
					$imgPos += $skiplen;
					break;
			} // endswitch check marker type
		} // endif loop through JPG chunks
    } // endif chk for valid file types

    return array($width, $height, $type);
} // end function


function ImageTypesLookup($imagetypeid) {
    static $ImageTypesLookup = array();
    if (empty($ImageTypesLookup)) {
		$ImageTypesLookup[1]  = 'gif';
		$ImageTypesLookup[2]  = 'jpg';
		$ImageTypesLookup[3]  = 'png';
		$ImageTypesLookup[4]  = 'swf';
		$ImageTypesLookup[5]  = 'psd';
		$ImageTypesLookup[6]  = 'bmp';
		$ImageTypesLookup[7]  = 'tiff (little-endian)';
		$ImageTypesLookup[8]  = 'tiff (big-endian)';
		$ImageTypesLookup[9]  = 'jpc';
		$ImageTypesLookup[10] = 'jp2';
		$ImageTypesLookup[11] = 'jpx';
		$ImageTypesLookup[12] = 'jb2';
		$ImageTypesLookup[13] = 'swc';
		$ImageTypesLookup[14] = 'iff';
    }
    return (isset($ImageTypesLookup[$imagetypeid]) ? $ImageTypesLookup[$imagetypeid] : '');
}

?>