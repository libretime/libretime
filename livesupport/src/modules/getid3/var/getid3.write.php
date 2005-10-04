<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.write.php - part of getID3()                         //
// sample script for demonstrating writing ID3v1 and  ID3v2    //
// tags for MP3, or Ogg comment tags for Ogg Vorbis            //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

require_once('getid3.php');
require_once(GETID3_INCLUDEPATH.'getid3.putid3.php');
require_once(GETID3_INCLUDEPATH.'getid3.ogginfo.php');
require_once(GETID3_INCLUDEPATH.'getid3.functions.php');
require_once(GETID3_INCLUDEPATH.'getid3.id3.php');

$EditorFilename = (isset($_REQUEST['EditorFilename']) ? SafeStripSlashes($_REQUEST['EditorFilename']) : '');

if (isset($_POST['WriteOggCommentTagNow'])) {

    $data['title']       = $_POST['EditorTitle'];
    $data['artist']      = $_POST['EditorArtist'];
    $data['album']       = $_POST['EditorAlbum'];
    $data['genre']       = LookupGenre($_POST['EditorGenre']);
    $data['tracknumber'] = $_POST['EditorTrack'];
    $data['comment']     = $_POST['EditorComment'];
    echo 'Ogg tag'.(OggWrite($EditorFilename, $data) ? '' : ' NOT').' written successfully<HR>';

} elseif (isset($_POST['WriteID3v2TagNow'])) {
    echo 'starting to write tag<BR>';

    if ($_POST['EditorTitle']) {
		$data['id3v2']['TIT2']['encodingid'] = 0;
		$data['id3v2']['TIT2']['data']       = SafeStripSlashes($_POST['EditorTitle']);
    }
    if ($_POST['EditorArtist']) {
		$data['id3v2']['TPE1']['encodingid'] = 0;
		$data['id3v2']['TPE1']['data']       = SafeStripSlashes($_POST['EditorArtist']);
    }
    if ($_POST['EditorAlbum']) {
		$data['id3v2']['TALB']['encodingid'] = 0;
		$data['id3v2']['TALB']['data']       = SafeStripSlashes($_POST['EditorAlbum']);
    }
    if ($_POST['EditorYear']) {
		$data['id3v2']['TYER']['encodingid'] = 0;
		$data['id3v2']['TYER']['data']       = (int) SafeStripSlashes($_POST['EditorYear']);
    }
    if ($_POST['EditorTrack']) {
		$data['id3v2']['TRCK']['encodingid'] = 0;
		$data['id3v2']['TRCK']['data']       = (int) SafeStripSlashes($_POST['EditorTrack']);
    }
    if ($_POST['EditorGenre']) {
		$data['id3v2']['TCON']['encodingid'] = 0;
		$data['id3v2']['TCON']['data']       = '('.$_POST['EditorGenre'].')';
    }
    if ($_POST['EditorComment']) {
		$data['id3v2']['COMM'][0]['encodingid']  = 0;
		$data['id3v2']['COMM'][0]['language']    = 'eng';
		$data['id3v2']['COMM'][0]['description'] = '';
		$data['id3v2']['COMM'][0]['data']        = SafeStripSlashes($_POST['EditorComment']);
    }

    if (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['tmp_name']) {
		if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			if ($fd = @fopen($_FILES['userfile']['tmp_name'], 'rb')) {
				$data['id3v2']['APIC'][0]['data']          = fread($fd, filesize($_FILES['userfile']['tmp_name']));
				fclose ($fd);

				$data['id3v2']['APIC'][0]['encodingid']    = (isset($EditorAPICencodingID)  ? $EditorAPICencodingID : 0);
				$data['id3v2']['APIC'][0]['picturetypeid'] = (isset($EditorAPICpictypeID)   ? $EditorAPICpictypeID  : 0);
				$data['id3v2']['APIC'][0]['description']   = (isset($EditorAPICdescription) ? $EditorAPICdescription : '');

				require_once(GETID3_INCLUDEPATH.'getid3.getimagesize.php');
				$imageinfo = GetDataImageSize($data['id3v2']['APIC'][0]['data']);
				$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
				if (isset($imageinfo[2]) && ($imageinfo[2] >= 1) && ($imageinfo[2] <= 3)) {
					$data['id3v2']['APIC'][0]['mime']      = 'image/'.$imagetypes[$imageinfo[2]];
				} else {
					echo '<B>invalid image format</B><BR>';
				}
			} else {
				echo '<B>cannot open '.$_FILES['userfile']['tmp_name'].'</B><BR>';
			}
		} else {
			echo '<B>!is_uploaded_file('.$_FILES['userfile']['tmp_name'].')</B><BR>';
		}
    }

    $data['id3v2']['TXXX'][0]['encodingid']  = 0;
    $data['id3v2']['TXXX'][0]['description'] = 'ID3v2-tagged by';
    $data['id3v2']['TXXX'][0]['data']        = 'getID3() v'.GETID3VERSION.' (www.silisoftware.com)';


    if ($_POST['WriteOrDelete'] == 'W') { // write tags
		if (isset($_POST['VersionToEdit1']) && ($_POST['VersionToEdit1'] == '1')) {
			if (!is_numeric($_POST['EditorGenre'])) {
				$EditorGenre = 255; // ID3v1 only supports predefined numeric genres (255 = unknown)
			}
			echo 'ID3v1 changes'.(WriteID3v1($EditorFilename, $_POST['EditorTitle'], $_POST['EditorArtist'], $_POST['EditorAlbum'], $_POST['EditorYear'], $_POST['EditorComment'], $_POST['EditorGenre'], $_POST['EditorTrack'], true) ? '' : ' NOT').' written successfully<HR>';
		}
		if (isset($_POST['VersionToEdit2']) && ($_POST['VersionToEdit2'] == '2')) {
			echo 'ID3v2 changes'.(WriteID3v2($EditorFilename, $data, 3, 0, true, 0, true) ? '' : ' NOT').' written successfully<HR>';
		}
    } else { // delete tags
		if (isset($_POST['VersionToEdit1']) && ($_POST['VersionToEdit1'] == '1')) {
			echo 'ID3v1 tag'.(RemoveID3v1($EditorFilename, true) ? '' : ' NOT').' successfully deleted<HR>';
		}
		if (isset($_POST['VersionToEdit2']) && ($_POST['VersionToEdit2'] == '2')) {
			echo 'ID3v2 tag'.(RemoveID3v2($EditorFilename, true) ? '' : ' NOT').' successfully deleted<HR>';
		}
    }
}

echo '<A HREF="'.$_SERVER['PHP_SELF'].'">Start Over</A><BR>';
echo '<TABLE BORDER="0"><FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST" ENCTYPE="multipart/form-data">';
echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><B>Sample ID3v1/ID3v2/OggComment editor</B></TD></TR>';
if ($EditorFilename) {
	echo '<TR><TD ALIGN="RIGHT"><B>Filename: </B></TD><TD><INPUT TYPE="HIDDEN" NAME="EditorFilename" VALUE="'.FixTextFields($EditorFilename).'"><I>'.$EditorFilename.'</I></TD></TR>';
    if (file_exists($EditorFilename)) {
		$OldThisfileInfo = GetAllFileInfo($EditorFilename);
		echo '<TR><TD ALIGN="RIGHT"><B>Title</B></TD><TD><INPUT TYPE="TEXT" SIZE="40"  NAME="EditorTitle" VALUE="'.FixTextFields(isset($OldThisfileInfo['comments']['title'][0]) ? $OldThisfileInfo['comments']['title'][0] : '').'"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Artist</B></TD><TD><INPUT TYPE="TEXT" SIZE="40" NAME="EditorArtist" VALUE="'.FixTextFields(isset($OldThisfileInfo['comments']['artist'][0]) ? $OldThisfileInfo['comments']['artist'][0] : '').'"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Album</B></TD><TD><INPUT TYPE="TEXT" SIZE="40"  NAME="EditorAlbum" VALUE="'.FixTextFields(isset($OldThisfileInfo['comments']['album'][0]) ? $OldThisfileInfo['comments']['album'][0] : '').'"></TD></TR>';
		if ($OldThisfileInfo['fileformat'] == 'mp3') {
			echo '<TR><TD ALIGN="RIGHT"><B>Year</B></TD><TD><INPUT TYPE="TEXT" SIZE="4" NAME="EditorYear" VALUE="'.FixTextFields(isset($OldThisfileInfo['comments']['year'][0]) ? $OldThisfileInfo['comments']['year'][0] : '').'"></TD></TR>';
		}
		echo '<TR><TD ALIGN="RIGHT"><B>Track</B></TD><TD><INPUT TYPE="TEXT" SIZE="2" NAME="EditorTrack" VALUE="'.FixTextFields(isset($OldThisfileInfo['comments']['track'][0]) ? $OldThisfileInfo['comments']['track'][0] : '').'"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Genre</B></TD><TD><SELECT NAME="EditorGenre">';

		require_once(GETID3_INCLUDEPATH.'getid3.id3.php');
		$ArrayOfGenres = ArrayOfGenres();   // get the array of genres
		unset($ArrayOfGenres['CR']);        // take off these special cases
		unset($ArrayOfGenres['RX']);
		unset($ArrayOfGenres[255]);
		asort($ArrayOfGenres);              // sort into alphabetical order
		$ArrayOfGenres[255]  = '-Unknown-'; // and put the special cases back on the end
		$ArrayOfGenres['CR'] = '-Cover-';
		$ArrayOfGenres['RX'] = '-Remix-';
		$EditorGenre = (isset($OldThisfileInfo['comments']['genre'][0]) ? LookupGenre($OldThisfileInfo['comments']['genre'][0], true) : 255);
		foreach ($ArrayOfGenres as $key => $value) {
			echo '<OPTION VALUE="'.$key.'"'.(($EditorGenre == $key) ? ' SELECTED' : '').'>'.$value.'</OPTION>';
		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD ALIGN="RIGHT"><B>Comment</B></TD><TD><TEXTAREA COLS="30" ROWS="3" NAME="EditorComment" WRAP="VIRTUAL">'.(isset($OldThisfileInfo['comment']) ? $OldThisfileInfo['comment'] : '').'</TEXTAREA></TD></TR>';
		if ($OldThisfileInfo['fileformat'] == 'mp3') {
			echo '<TR><TD ALIGN="RIGHT"><B>Picture</B></TD><TD><INPUT TYPE="FILE" NAME="userfile" ACCEPT="image/jpeg, image/gif, image/png"></TD></TR>';
			echo '<INPUT TYPE="HIDDEN" NAME="WriteID3v2TagNow" VALUE="1">';
			echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="RADIO" NAME="WriteOrDelete" VALUE="W" CHECKED> Write <INPUT TYPE="RADIO" NAME="WriteOrDelete" VALUE="D"> Delete</TD></TR>';
			echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="CHECKBOX" NAME="VersionToEdit1" VALUE="1"> ID3v1 <INPUT TYPE="CHECKBOX" NAME="VersionToEdit2" VALUE="2" CHECKED> ID3v2</TD></TR>';
		} elseif ($OldThisfileInfo['fileformat'] == 'ogg') {
			echo '<INPUT TYPE="HIDDEN" NAME="WriteOggCommentTagNow" VALUE="1">';
		}
		echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Save Changes"> <INPUT TYPE="RESET" VALUE="Reset"></TD></TR>';
    } else {
		echo '<TR><TD ALIGN="RIGHT"><B>Error</B></TD><TD>'.FixTextFields($EditorFilename).' does not exist</TD></TR>';
		echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Find File"></TD></TR>';
    }
} else {
    echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="TEXT" NAME="EditorFilename"></TD></TR>';
    echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Find File"></TD></TR>';
}
echo '</FORM></TABLE>';

?>