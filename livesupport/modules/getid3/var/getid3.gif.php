<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.gif.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getGIFHeaderFilepointer(&$fd, &$ThisFileInfo) {
    $ThisFileInfo['fileformat']          = 'gif';
    $ThisFileInfo['video']['dataformat'] = 'gif';

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $GIFheader = fread($fd, 13);
    $offset = 0;

    $ThisFileInfo['gif']['header']['raw']['identifier']            =                  substr($GIFheader, $offset, 3);
    $offset += 3;
    $ThisFileInfo['gif']['header']['raw']['version']               =                  substr($GIFheader, $offset, 3);
    $offset += 3;
    $ThisFileInfo['gif']['header']['raw']['width']                 = LittleEndian2Int(substr($GIFheader, $offset, 2));
    $offset += 2;
    $ThisFileInfo['gif']['header']['raw']['height']                = LittleEndian2Int(substr($GIFheader, $offset, 2));
    $offset += 2;
    $ThisFileInfo['gif']['header']['raw']['flags']                 = LittleEndian2Int(substr($GIFheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['gif']['header']['raw']['bg_color_index']        = LittleEndian2Int(substr($GIFheader, $offset, 1));
    $offset += 1;
    $ThisFileInfo['gif']['header']['raw']['aspect_ratio']          = LittleEndian2Int(substr($GIFheader, $offset, 1));
    $offset += 1;

    $ThisFileInfo['video']['resolution_x']                                  = $ThisFileInfo['gif']['header']['raw']['width'];
    $ThisFileInfo['video']['resolution_y']                                  = $ThisFileInfo['gif']['header']['raw']['height'];
    $ThisFileInfo['gif']['version']                                = $ThisFileInfo['gif']['header']['raw']['version'];
    $ThisFileInfo['gif']['header']['flags']['global_color_table']  = (bool) ($ThisFileInfo['gif']['header']['raw']['flags'] & 0x80);
    if ($ThisFileInfo['gif']['header']['raw']['flags'] & 0x80) {
		// Number of bits per primary color available to the original image, minus 1
		$ThisFileInfo['gif']['header']['flags']['bits_per_pixel']  = 3 * ((($ThisFileInfo['gif']['header']['raw']['flags'] & 0x70) >> 4) + 1);
    } else {
		$ThisFileInfo['gif']['header']['flags']['bits_per_pixel']  = 0;
    }
    $ThisFileInfo['gif']['header']['flags']['global_color_sorted'] = (bool) ($ThisFileInfo['gif']['header']['raw']['flags'] & 0x40);
    if ($ThisFileInfo['gif']['header']['flags']['global_color_table']) {
		// the number of bytes contained in the Global Color Table. To determine that
		// actual size of the color table, raise 2 to [the value of the field + 1]
		$ThisFileInfo['gif']['header']['flags']['global_color_size'] = pow(2, ($ThisFileInfo['gif']['header']['raw']['flags'] & 0x07) + 1);
    } else {
		$ThisFileInfo['gif']['header']['flags']['global_color_size'] = 0;
    }
    if ($ThisFileInfo['gif']['header']['raw']['aspect_ratio'] != 0) {
		// Aspect Ratio = (Pixel Aspect Ratio + 15) / 64
		$ThisFileInfo['gif']['header']['aspect_ratio']             = ($ThisFileInfo['gif']['header']['raw']['aspect_ratio'] + 15) / 64;
    }

    if ($ThisFileInfo['gif']['header']['flags']['global_color_table']) {
		$GIFcolorTable = fread($fd, 3 * $ThisFileInfo['gif']['header']['flags']['global_color_size']);
		$offset = 0;
		for ($i = 0; $i < $ThisFileInfo['gif']['header']['flags']['global_color_size']; $i++) {
			//$ThisFileInfo['gif']['global_color_table']['red'][$i]   = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			//$ThisFileInfo['gif']['global_color_table']['green'][$i] = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			//$ThisFileInfo['gif']['global_color_table']['blue'][$i]  = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			$red   = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			$green = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			$blue  = LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
			$ThisFileInfo['gif']['global_color_table'][$i] = (($red << 16) | ($green << 8) | ($blue));
		}
    }

    return true;
}

?>