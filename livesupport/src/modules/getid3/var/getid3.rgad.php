<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.rgad.php - part of getID3()                          //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function RGADnameLookup($namecode) {
    static $RGADname = array();
    if (empty($RGADname)) {
		$RGADname[0] = 'not set';
		$RGADname[1] = 'Radio Gain Adjustment';
		$RGADname[2] = 'Audiophile Gain Adjustment';
    }

    return (isset($RGADname[$namecode]) ? $RGADname[$namecode] : '');
}

function RGADoriginatorLookup($originatorcode) {
    static $RGADoriginator = array();
    if (empty($RGADoriginator)) {
		$RGADoriginator[0] = 'unspecified';
		$RGADoriginator[1] = 'pre-set by artist/producer/mastering engineer';
		$RGADoriginator[2] = 'set by user';
		$RGADoriginator[3] = 'determined automatically';
    }

    return (isset($RGADoriginator[$originatorcode]) ? $RGADoriginator[$originatorcode] : '');
}

function RGADadjustmentLookup($rawadjustment, $signbit) {
    $adjustment = $rawadjustment / 10;
    if ($signbit == 1) {
		$adjustment *= -1;
    }
    return (float) $adjustment;
}

function RGADgainString($namecode, $originatorcode, $replaygain) {
    if ($replaygain < 0) {
		$signbit = '1';
    } else {
		$signbit = '0';
    }
    $storedreplaygain = round($replaygain * 10);
    $gainstring  = str_pad(decbin($namecode), 3, '0', STR_PAD_LEFT);
    $gainstring .= str_pad(decbin($originatorcode), 3, '0', STR_PAD_LEFT);
    $gainstring .= $signbit;
    $gainstring .= str_pad(decbin(round($replaygain * 10)), 9, '0', STR_PAD_LEFT);

    return $gainstring;
}

?>