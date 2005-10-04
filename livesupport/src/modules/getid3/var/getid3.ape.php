<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.ape.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getAPEtagFilepointer(&$fd, &$ThisFileInfo) {
    $id3v1tagsize     = 128;
    $apetagheadersize = 32;
    fseek($fd, 0 - $id3v1tagsize - $apetagheadersize, SEEK_END);
    $APEfooterID3v1 = fread($fd, $id3v1tagsize + $apetagheadersize);
    if (substr($APEfooterID3v1, 0, strlen('APETAGEX')) == 'APETAGEX') {

		// APE tag found before ID3v1
		$APEfooterData = substr($APEfooterID3v1, 0, $apetagheadersize);
		$APEfooterOffset = 0 - $apetagheadersize - $id3v1tagsize;

    } elseif (substr($APEfooterID3v1, $id3v1tagsize, strlen('APETAGEX')) == 'APETAGEX') {

		// APE tag found, no ID3v1
		$APEfooterData = substr($APEfooterID3v1, $id3v1tagsize, $apetagheadersize);
		$APEfooterOffset = 0 - $apetagheadersize;

    } else {

		// APE tag not found
		return false;

    }

    if (empty($ThisFileInfo['fileformat'])) {
		$ThisFileInfo['fileformat'] = 'ape';
    }
    $ThisFileInfo['ape']['footer'] = parseAPEheaderFooter($APEfooterData);

    if (isset($ThisFileInfo['ape']['footer']['flags']['header']) && $ThisFileInfo['ape']['footer']['flags']['header']) {
		fseek($fd, $APEfooterOffset - $ThisFileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize - $apetagheadersize, SEEK_END);
		$APEtagData = fread($fd, $ThisFileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize);
    } else {
		fseek($fd, $APEfooterOffset - $ThisFileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize, SEEK_END);
		$APEtagData = fread($fd, $ThisFileInfo['ape']['footer']['raw']['tagsize']);
    }
    $offset = 0;
    if (isset($ThisFileInfo['ape']['footer']['flags']['header']) && $ThisFileInfo['ape']['footer']['flags']['header']) {
		$ThisFileInfo['ape']['header'] = parseAPEheaderFooter(substr($APEtagData, 0, $apetagheadersize));
		$offset += $apetagheadersize;
    }

    for ($i = 0; $i < $ThisFileInfo['ape']['footer']['raw']['tag_items']; $i++) {
		$value_size    = LittleEndian2Int(substr($APEtagData, $offset, 4));
		$offset       += 4;
		$item_flags    = LittleEndian2Int(substr($APEtagData, $offset, 4));
		$offset       += 4;
		$ItemKeyLength = strpos($APEtagData, chr(0), $offset) - $offset;
		$item_key      = strtolower(substr($APEtagData, $offset, $ItemKeyLength));
		$offset       += $ItemKeyLength + 1; // skip 0x00 terminator
		$data          = substr($APEtagData, $offset, $value_size);
		$offset       += $value_size;

		$ThisFileInfo['ape']['items']["$item_key"]['raw']['value_size'] = $value_size;
		$ThisFileInfo['ape']['items']["$item_key"]['raw']['item_flags'] = $item_flags;
		if ($ThisFileInfo['ape']['footer']['tag_version'] >= 2) {
			$ThisFileInfo['ape']['items']["$item_key"]['flags']         = parseAPEtagFlags($item_flags);
		}
		$ThisFileInfo['ape']['items']["$item_key"]['data']              = $data;
		$ThisFileInfo['ape']['items']["$item_key"]['data_ascii']        = $data;
		if (APEtagItemIsUTF8Lookup($item_key)) {
			$ThisFileInfo['ape']['items']["$item_key"]['data_ascii'] = RoughTranslateUnicodeToASCII($ThisFileInfo['ape']['items']["$item_key"]['data'], 3);
		}

		switch ($item_key) {
			case 'replaygain_track_gain':
				$ThisFileInfo['replay_gain']['radio']['adjustment']      = (float) $ThisFileInfo['ape']['items']["$item_key"]['data_ascii'];
				$ThisFileInfo['replay_gain']['radio']['originator']      = 'unspecified';
				break;

			case 'replaygain_track_peak':
				$ThisFileInfo['replay_gain']['radio']['peak']            = (float) $ThisFileInfo['ape']['items']["$item_key"]['data_ascii'];
				$ThisFileInfo['replay_gain']['radio']['originator']      = 'unspecified';
				break;

			case 'replaygain_album_gain':
				$ThisFileInfo['replay_gain']['audiophile']['adjustment'] = (float) $ThisFileInfo['ape']['items']["$item_key"]['data_ascii'];
				$ThisFileInfo['replay_gain']['audiophile']['originator'] = 'unspecified';
				break;

			case 'replaygain_album_peak':
				$ThisFileInfo['replay_gain']['audiophile']['peak']       = (float) $ThisFileInfo['ape']['items']["$item_key"]['data_ascii'];
				$ThisFileInfo['replay_gain']['audiophile']['originator'] = 'unspecified';
				break;

			case 'title':
			case 'artist':
			case 'album':
			case 'track':
			case 'genre':
			case 'comment':
			case 'year':
				$ThisFileInfo['ape']['comments']["$item_key"][] = $ThisFileInfo['ape']['items']["$item_key"]['data_ascii'];
				break;

		}

    }
    if (isset($ThisFileInfo['ape']['comments'])) {
		CopyFormatCommentsToRootComments($ThisFileInfo['ape']['comments'], $ThisFileInfo, true, true, true);
    }

    return true;
}

function parseAPEheaderFooter($APEheaderFooterData) {
    // http://www.uni-jena.de/~pfk/mpp/sv8/apeheader.html
    $headerfooterinfo['raw']['footer_tag']   =                  substr($APEheaderFooterData,  0, 8);
    $headerfooterinfo['raw']['version']      = LittleEndian2Int(substr($APEheaderFooterData,  8, 4));
    $headerfooterinfo['raw']['tagsize']      = LittleEndian2Int(substr($APEheaderFooterData, 12, 4));
    $headerfooterinfo['raw']['tag_items']    = LittleEndian2Int(substr($APEheaderFooterData, 16, 4));
    $headerfooterinfo['raw']['global_flags'] = LittleEndian2Int(substr($APEheaderFooterData, 20, 4));
    $headerfooterinfo['raw']['reserved']     =                  substr($APEheaderFooterData, 24, 8);

    $headerfooterinfo['tag_version']         = $headerfooterinfo['raw']['version'] / 1000;
    if ($headerfooterinfo['tag_version'] >= 2) {
		$headerfooterinfo['flags'] = parseAPEtagFlags($headerfooterinfo['raw']['global_flags']);
    }
    return $headerfooterinfo;
}

function parseAPEtagFlags($rawflagint) {
    // "Note: APE Tags 1.0 do not use any of the APE Tag flags.
    // All are set to zero on creation and ignored on reading."
    // http://www.uni-jena.de/~pfk/mpp/sv8/apetagflags.html
    $flags['header']            = (bool) ($rawflagint & 0x80000000);
    $flags['footer']            = (bool) ($rawflagint & 0x40000000);
    $flags['this_is_header']    = (bool) ($rawflagint & 0x20000000);
    $flags['item_contents_raw'] = ($rawflagint & 0x00000006) >> 1;
    $flags['item_contents']     = APEcontentTypeFlagLookup($flags['item_contents_raw']);
    $flags['read_only']         = (bool) ($rawflagint & 0x00000001);

    return $flags;
}

function APEcontentTypeFlagLookup($contenttypeid) {
    static $APEcontentTypeFlagLookup = array();
    if (empty($APEcontentTypeFlagLookup)) {
		$APEcontentTypeFlagLookup[0]  = 'utf-8';
		$APEcontentTypeFlagLookup[1]  = 'binary';
		$APEcontentTypeFlagLookup[2]  = 'external';
		$APEcontentTypeFlagLookup[3]  = 'reserved';
    }
    return (isset($APEcontentTypeFlagLookup[$contenttypeid]) ? $APEcontentTypeFlagLookup[$contenttypeid] : 'invalid');
}

function APEtagItemIsUTF8Lookup($itemkey) {
    static $APEtagItemIsUTF8Lookup = array();
    if (empty($APEtagItemIsUTF8Lookup)) {
		$APEtagItemIsUTF8Lookup[]  = 'Title';
		$APEtagItemIsUTF8Lookup[]  = 'Subtitle';
		$APEtagItemIsUTF8Lookup[]  = 'Artist';
		$APEtagItemIsUTF8Lookup[]  = 'Album';
		$APEtagItemIsUTF8Lookup[]  = 'Debut Album';
		$APEtagItemIsUTF8Lookup[]  = 'Publisher';
		$APEtagItemIsUTF8Lookup[]  = 'Conductor';
		$APEtagItemIsUTF8Lookup[]  = 'Track';
		$APEtagItemIsUTF8Lookup[]  = 'Composer';
		$APEtagItemIsUTF8Lookup[]  = 'Comment';
		$APEtagItemIsUTF8Lookup[]  = 'Copyright';
		$APEtagItemIsUTF8Lookup[]  = 'Publicationright';
		$APEtagItemIsUTF8Lookup[]  = 'File';
		$APEtagItemIsUTF8Lookup[]  = 'Year';
		$APEtagItemIsUTF8Lookup[]  = 'Record Date';
		$APEtagItemIsUTF8Lookup[]  = 'Record Location';
		$APEtagItemIsUTF8Lookup[]  = 'Genre';
		$APEtagItemIsUTF8Lookup[]  = 'Media';
		$APEtagItemIsUTF8Lookup[]  = 'Related';
		$APEtagItemIsUTF8Lookup[]  = 'ISRC';
		$APEtagItemIsUTF8Lookup[]  = 'Abstract';
		$APEtagItemIsUTF8Lookup[]  = 'Language';
		$APEtagItemIsUTF8Lookup[]  = 'Bibliography';
    }
    return in_array($itemkey, $APEtagItemIsUTF8Lookup);
}

?>