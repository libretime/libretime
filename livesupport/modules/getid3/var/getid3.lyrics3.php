<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.lyrics3.php - part of getID3()                       //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getLyrics3Filepointer(&$ThisFileInfo, $fd, $endoffset, $version, $length) {
    // http://www.volweb.cz/str/tags.htm

    fseek($fd, $endoffset, SEEK_END);
    $rawdata = fread($fd, $length);

    if (substr($rawdata, 0, 11) == 'LYRICSBEGIN') {

		switch ($version) {

			case 1:
				if (substr($rawdata, strlen($rawdata) - 9, 9) == 'LYRICSEND') {
					$ThisFileInfo['lyrics3']['raw']['lyrics3version'] = $version;
					$ThisFileInfo['lyrics3']['raw']['lyrics3tagsize'] = $length;
					$ThisFileInfo['lyrics3']['raw']['LYR'] = trim(substr($rawdata, 11, strlen($rawdata) - 11 - 9));
					Lyrics3LyricsTimestampParse($ThisFileInfo);
				} else {
					$ThisFileInfo['error'] .= "\n".'"LYRICSEND" expected at '.(ftell($fd) - 11 + $length - 9).' but found "'.substr($rawdata, strlen($rawdata) - 9, 9).'" instead';
				}
				break;

			case 2:
				if (substr($rawdata, strlen($rawdata) - 9, 9) == 'LYRICS200') {
					$ThisFileInfo['lyrics3']['raw']['lyrics3version'] = $version;
					$ThisFileInfo['lyrics3']['raw']['lyrics3tagsize'] = $length;
					$ThisFileInfo['lyrics3']['raw']['unparsed'] = substr($rawdata, 11, strlen($rawdata) - 11 - 9 - 6); // LYRICSBEGIN + LYRICS200 + LSZ
					$rawdata = $ThisFileInfo['lyrics3']['raw']['unparsed'];
					while (strlen($rawdata) > 0) {
						$fieldname = substr($rawdata, 0, 3);
						$fieldsize = (int) substr($rawdata, 3, 5);
						$ThisFileInfo['lyrics3']['raw']["$fieldname"] = substr($rawdata, 8, $fieldsize);
						$rawdata = substr($rawdata, 3 + 5 + $fieldsize);
					}

					if (isset($ThisFileInfo['lyrics3']['raw']['IND'])) {
						$i = 0;
						$flagnames = array('lyrics', 'timestamps', 'inhibitrandom');
						foreach ($flagnames as $flagname) {
							if (strlen($ThisFileInfo['lyrics3']['raw']['IND']) > ++$i) {
								$ThisFileInfo['lyrics3']['flags']["$flagname"] = IntString2Bool(substr($ThisFileInfo['lyrics3']['raw']['IND'], $i, 1));
							}
						}
					}

					$fieldnametranslation = array('ETT'=>'title', 'EAR'=>'artist', 'EAL'=>'album', 'INF'=>'comment', 'AUT'=>'author');
					foreach ($fieldnametranslation as $key => $value) {
						if (isset($ThisFileInfo['lyrics3']['raw']["$key"])) {
							$ThisFileInfo['lyrics3']['comments']["$value"] = $ThisFileInfo['lyrics3']['raw']["$key"];
						}
					}
					if (!empty($ThisFileInfo['lyrics3']['comments'])) {
						CopyFormatCommentsToRootComments($ThisFileInfo['lyrics3']['comments'], $ThisFileInfo, true, false, false);
					}

					if (isset($ThisFileInfo['lyrics3']['raw']['IMG'])) {
						$imagestrings = explode("\r\n", $ThisFileInfo['lyrics3']['raw']['IMG']);
						foreach ($imagestrings as $key => $imagestring) {
							if (strpos($imagestring, '||') !== false) {
								$imagearray = explode('||', $imagestring);
								$ThisFileInfo['lyrics3']['images']["$key"]['filename']     = $imagearray[0];
								$ThisFileInfo['lyrics3']['images']["$key"]['description']  = $imagearray[1];
								$ThisFileInfo['lyrics3']['images']["$key"]['timestamp']    = Lyrics3Timestamp2Seconds($imagearray[2]);
							}
						}
					}
					if (isset($ThisFileInfo['lyrics3']['raw']['LYR'])) {
						Lyrics3LyricsTimestampParse($ThisFileInfo);
					}
				} else {
					$ThisFileInfo['error'] .= "\n".'"LYRICS200" expected at '.(ftell($fd) - 11 + $length - 9).' but found "'.substr($rawdata, strlen($rawdata) - 9, 9).'" instead';
				}
				break;

			default:
				$ThisFileInfo['error'] .= "\n".'Cannot process Lyrics3 version '.$version.' (only v1 and v2)';
				break;
		}

    } else {

		$ThisFileInfo['error'] .= "\n".'"LYRICSBEGIN" expected at '.(ftell($fd) - 11).' but found "'.substr($rawdata, 0, 11).'" instead';

    }

    if (isset($ThisFileInfo['lyrics3'])) {
		$ThisFileInfo['tags'][] = 'lyrics3';
    }
    return true;
}

function Lyrics3Timestamp2Seconds($rawtimestamp) {
    if (ereg('^\\[([0-9]{2}):([0-9]{2})\\]$', $rawtimestamp, $regs)) {
		return (int) (($regs[1] * 60) + $regs[2]);
    }
    return false;
}

function Lyrics3LyricsTimestampParse(&$ThisFileInfo) {
    $lyricsarray = explode("\r\n", $ThisFileInfo['lyrics3']['raw']['LYR']);
    foreach ($lyricsarray as $key => $lyricline) {
		$regs = array();
		unset($thislinetimestamps);
		while (ereg('^(\\[[0-9]{2}:[0-9]{2}\\])', $lyricline, $regs)) {
			$thislinetimestamps[] = Lyrics3Timestamp2Seconds($regs[0]);
			$lyricline = str_replace($regs[0], '', $lyricline);
		}
		$notimestamplyricsarray["$key"] = $lyricline;
		if (isset($thislinetimestamps) && is_array($thislinetimestamps)) {
			sort($thislinetimestamps);
			foreach ($thislinetimestamps as $timestampkey => $timestamp) {
				if (isset($ThisFileInfo['lyrics3']['synchedlyrics'][$timestamp])) {
					// timestamps only have a 1-second resolution, it's possible that multiple lines
					// could have the same timestamp, if so, append
					$ThisFileInfo['lyrics3']['synchedlyrics'][$timestamp] .= "\r\n".$lyricline;
				} else {
					$ThisFileInfo['lyrics3']['synchedlyrics'][$timestamp] = $lyricline;
				}
			}
		}
    }
    $ThisFileInfo['lyrics3']['unsynchedlyrics'] = implode("\r\n", $notimestamplyricsarray);
    if (isset($ThisFileInfo['lyrics3']['synchedlyrics']) && is_array($ThisFileInfo['lyrics3']['synchedlyrics'])) {
		ksort($ThisFileInfo['lyrics3']['synchedlyrics']);
    }
    return true;
}

?>