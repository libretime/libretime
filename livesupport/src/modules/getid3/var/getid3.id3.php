<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.id3.php - part of getID3()                           //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function ArrayOfGenres() {
    static $GenreLookup = array();
    if (empty($GenreLookup)) {
		$GenreLookup[0]    = 'Blues';
		$GenreLookup[1]    = 'Classic Rock';
		$GenreLookup[2]    = 'Country';
		$GenreLookup[3]    = 'Dance';
		$GenreLookup[4]    = 'Disco';
		$GenreLookup[5]    = 'Funk';
		$GenreLookup[6]    = 'Grunge';
		$GenreLookup[7]    = 'Hip-Hop';
		$GenreLookup[8]    = 'Jazz';
		$GenreLookup[9]    = 'Metal';
		$GenreLookup[10]   = 'New Age';
		$GenreLookup[11]   = 'Oldies';
		$GenreLookup[12]   = 'Other';
		$GenreLookup[13]   = 'Pop';
		$GenreLookup[14]   = 'R&B';
		$GenreLookup[15]   = 'Rap';
		$GenreLookup[16]   = 'Reggae';
		$GenreLookup[17]   = 'Rock';
		$GenreLookup[18]   = 'Techno';
		$GenreLookup[19]   = 'Industrial';
		$GenreLookup[20]   = 'Alternative';
		$GenreLookup[21]   = 'Ska';
		$GenreLookup[22]   = 'Death Metal';
		$GenreLookup[23]   = 'Pranks';
		$GenreLookup[24]   = 'Soundtrack';
		$GenreLookup[25]   = 'Euro-Techno';
		$GenreLookup[26]   = 'Ambient';
		$GenreLookup[27]   = 'Trip-Hop';
		$GenreLookup[28]   = 'Vocal';
		$GenreLookup[29]   = 'Jazz+Funk';
		$GenreLookup[30]   = 'Fusion';
		$GenreLookup[31]   = 'Trance';
		$GenreLookup[32]   = 'Classical';
		$GenreLookup[33]   = 'Instrumental';
		$GenreLookup[34]   = 'Acid';
		$GenreLookup[35]   = 'House';
		$GenreLookup[36]   = 'Game';
		$GenreLookup[37]   = 'Sound Clip';
		$GenreLookup[38]   = 'Gospel';
		$GenreLookup[39]   = 'Noise';
		$GenreLookup[40]   = 'Alt. Rock';
		$GenreLookup[41]   = 'Bass';
		$GenreLookup[42]   = 'Soul';
		$GenreLookup[43]   = 'Punk';
		$GenreLookup[44]   = 'Space';
		$GenreLookup[45]   = 'Meditative';
		$GenreLookup[46]   = 'Instrumental Pop';
		$GenreLookup[47]   = 'Instrumental Rock';
		$GenreLookup[48]   = 'Ethnic';
		$GenreLookup[49]   = 'Gothic';
		$GenreLookup[50]   = 'Darkwave';
		$GenreLookup[51]   = 'Techno-Industrial';
		$GenreLookup[52]   = 'Electronic';
		$GenreLookup[53]   = 'Folk/Pop';
		$GenreLookup[54]   = 'Eurodance';
		$GenreLookup[55]   = 'Dream';
		$GenreLookup[56]   = 'Southern Rock';
		$GenreLookup[57]   = 'Comedy';
		$GenreLookup[58]   = 'Cult';
		$GenreLookup[59]   = 'Gangsta';
		$GenreLookup[60]   = 'Top 40';
		$GenreLookup[61]   = 'Christian Rap';
		$GenreLookup[62]   = 'Pop/Funk';
		$GenreLookup[63]   = 'Jungle';
		$GenreLookup[64]   = 'Native American';
		$GenreLookup[65]   = 'Cabaret';
		$GenreLookup[66]   = 'New Wave';
		$GenreLookup[67]   = 'Psychadelic';
		$GenreLookup[68]   = 'Rave';
		$GenreLookup[69]   = 'Showtunes';
		$GenreLookup[70]   = 'Trailer';
		$GenreLookup[71]   = 'Lo-Fi';
		$GenreLookup[72]   = 'Tribal';
		$GenreLookup[73]   = 'Acid Punk';
		$GenreLookup[74]   = 'Acid Jazz';
		$GenreLookup[75]   = 'Polka';
		$GenreLookup[76]   = 'Retro';
		$GenreLookup[77]   = 'Musical';
		$GenreLookup[78]   = 'Rock & Roll';
		$GenreLookup[79]   = 'Hard Rock';
		$GenreLookup[80]   = 'Folk';
		$GenreLookup[81]   = 'Folk/Rock';
		$GenreLookup[82]   = 'National Folk';
		$GenreLookup[83]   = 'Swing';
		$GenreLookup[84]   = 'Fast-Fusion';
		$GenreLookup[85]   = 'Bebob';
		$GenreLookup[86]   = 'Latin';
		$GenreLookup[87]   = 'Revival';
		$GenreLookup[88]   = 'Celtic';
		$GenreLookup[89]   = 'Bluegrass';
		$GenreLookup[90]   = 'Avantgarde';
		$GenreLookup[91]   = 'Gothic Rock';
		$GenreLookup[92]   = 'Progressive Rock';
		$GenreLookup[93]   = 'Psychedelic Rock';
		$GenreLookup[94]   = 'Symphonic Rock';
		$GenreLookup[95]   = 'Slow Rock';
		$GenreLookup[96]   = 'Big Band';
		$GenreLookup[97]   = 'Chorus';
		$GenreLookup[98]   = 'Easy Listening';
		$GenreLookup[99]   = 'Acoustic';
		$GenreLookup[100]  = 'Humour';
		$GenreLookup[101]  = 'Speech';
		$GenreLookup[102]  = 'Chanson';
		$GenreLookup[103]  = 'Opera';
		$GenreLookup[104]  = 'Chamber Music';
		$GenreLookup[105]  = 'Sonata';
		$GenreLookup[106]  = 'Symphony';
		$GenreLookup[107]  = 'Booty Bass';
		$GenreLookup[108]  = 'Primus';
		$GenreLookup[109]  = 'Porn Groove';
		$GenreLookup[110]  = 'Satire';
		$GenreLookup[111]  = 'Slow Jam';
		$GenreLookup[112]  = 'Club';
		$GenreLookup[113]  = 'Tango';
		$GenreLookup[114]  = 'Samba';
		$GenreLookup[115]  = 'Folklore';
		$GenreLookup[116]  = 'Ballad';
		$GenreLookup[117]  = 'Power Ballad';
		$GenreLookup[118]  = 'Rhythmic Soul';
		$GenreLookup[119]  = 'Freestyle';
		$GenreLookup[120]  = 'Duet';
		$GenreLookup[121]  = 'Punk Rock';
		$GenreLookup[122]  = 'Drum Solo';
		$GenreLookup[123]  = 'A Cappella';
		$GenreLookup[124]  = 'Euro-House';
		$GenreLookup[125]  = 'Dance Hall';
		$GenreLookup[126]  = 'Goa';
		$GenreLookup[127]  = 'Drum & Bass';
		$GenreLookup[128]  = 'Club-House';
		$GenreLookup[129]  = 'Hardcore';
		$GenreLookup[130]  = 'Terror';
		$GenreLookup[131]  = 'Indie';
		$GenreLookup[132]  = 'BritPop';
		$GenreLookup[133]  = 'Negerpunk';
		$GenreLookup[134]  = 'Polsk Punk';
		$GenreLookup[135]  = 'Beat';
		$GenreLookup[136]  = 'Christian Gangsta Rap';
		$GenreLookup[137]  = 'Heavy Metal';
		$GenreLookup[138]  = 'Black Metal';
		$GenreLookup[139]  = 'Crossover';
		$GenreLookup[140]  = 'Contemporary Christian';
		$GenreLookup[141]  = 'Christian Rock';
		$GenreLookup[142]  = 'Merengue';
		$GenreLookup[143]  = 'Salsa';
		$GenreLookup[144]  = 'Trash Metal';
		$GenreLookup[145]  = 'Anime';
		$GenreLookup[146]  = 'Jpop';
		$GenreLookup[147]  = 'Synthpop';
		$GenreLookup[255]  = 'Unknown';

		$GenreLookup['CR'] = 'Cover';
		$GenreLookup['RX'] = 'Remix';
    }
    return $GenreLookup;
}

function LookupGenre($genreid, $returnkey=false) {
    if (($genreid != 'RX') && ($genreid === 'CR')) {
		$genreid = (int) $genreid; // to handle 3 or '3' or '03'
    }
    $GenreLookup = ArrayOfGenres();
    if ($returnkey) {

		$LowerCaseNoSpaceSearchTerm = strtolower(str_replace(' ', '', $genreid));
		foreach ($GenreLookup as $key => $value) {
			if (strtolower(str_replace(' ', '', $value)) == $LowerCaseNoSpaceSearchTerm) {
				return $key;
			}
		}
		return '';

    } else {

		return (isset($GenreLookup[$genreid]) ? $GenreLookup[$genreid] : '');

    }
}

?>