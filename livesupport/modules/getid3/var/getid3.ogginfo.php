<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.ogginfo.php - part of getID3()                       //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function OggWrite($filename, $comments) {

	// Uses vorbiscomment(.exe) to write comments, if available.

	if ((bool) ini_get('safe_mode')) {

		echo 'Failed making system call to vorbiscomment.exe - cannot write comments - error returned: PHP running in Safe Mode (backtick operator not available)';
		return false;

	} else {

		// Prevent user from aborting script
		$old_abort = ignore_user_abort(true);

		// Create file with new comments
		$commentsfilename = tempnam('/tmp', 'getID3');
		if ($fpcomments = fopen($commentsfilename, 'wb')) {

			foreach ($comments as $key => $value) {
				if (!is_array($value)) {
					$comments[$key] = array($value);
				}
			}
			foreach ($comments as $key => $value) {
				foreach ($value as $valuekey => $valuevalue) {
					str_replace("\r", "\n", $valuevalue);
					if (strstr($valuevalue, "\n")) {
						unset($comments[$key][$valuekey]);
						$multilineexploded = explode("\n", $valuevalue);
						foreach ($multilineexploded as $newcomment) {
							if (strlen(trim($newcomment)) > 0) {
								$comments[$key][] = $newcomment;
							}
						}
					}
				}
			}
			foreach ($comments as $key => $value) {
				foreach ($value as $commentdata) {
					fwrite($fpcomments, CleanOggCommentName($key).'='.$commentdata."\n");
				}
			}
			fclose($fpcomments);
		}

		if (substr(php_uname(), 0, 7) == 'Windows') {

			if (file_exists(GETID3_INCLUDEPATH.'vorbiscomment.exe')) {

				$VorbisCommentError = `vorbiscomment.exe -w -c "$commentsfilename" "$filename"`;

			} else {

				$VorbisCommentError = 'vorbiscomment.exe not found in '.GETID3_INCLUDEPATH;

			}

		} else {

			$VorbisCommentError = `vorbiscomment -w -c "$commentsfilename" "$filename" 2>&1`;

		}

		if (!empty($VorbisCommentError)) {

			echo 'Failed making system call to vorbiscomment(.exe) - cannot write comments. If vorbiscomment is unavailable, please download from http://www.vorbis.com/download.psp and put in the getID3() directory. Error returned: '.$VorbisCommentError;
			return false;

		}

		// Remove temporary comments file
		unlink($commentsfilename);

		// Reset abort setting
		ignore_user_abort($old_abort);

		return true;
	}
}


function CleanOggCommentName($originalcommentname) {
    // A case-insensitive field name that may consist of ASCII 0x20 through 0x7D, 0x3D ('=') excluded.
    // ASCII 0x41 through 0x5A inclusive (A-Z) is to be considered equivalent to ASCII 0x61 through
    // 0x7A inclusive (a-z).

    // replace invalid chars with a space, return uppercase text
    // Thanks Chris Bolt <chris-getid3@bolt.cx> for improving this function
    return strtoupper(ereg_replace('[^ -<>-}]', ' ', $originalcommentname));

}

?>