<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <getid3@users.sourceforge.net>  //
//        available at http://getid3.sourceforge.net          ///
/////////////////////////////////////////////////////////////////
//                                                             //
// getid3.midi.php - part of getID3()                          //
// See getid3.readme.txt for more details                      //
//                                                             //
/////////////////////////////////////////////////////////////////

function getMIDIHeaderFilepointer(&$fd, &$ThisFileInfo, $scanwholefile=true) {

    $ThisFileInfo['fileformat']          = 'midi';
    $ThisFileInfo['audio']['dataformat'] = 'midi';

    fseek($fd, $ThisFileInfo['avdataoffset'], SEEK_SET);
    $MIDIdata = fread($fd, FREAD_BUFFER_SIZE);
    $offset = 0;
    $MIDIheaderID                                = substr($MIDIdata, $offset, 4); // 'MThd'
    $offset += 4;
    $ThisFileInfo['midi']['raw']['headersize']    = BigEndian2Int(substr($MIDIdata, $offset, 4));
    $offset += 4;
    $ThisFileInfo['midi']['raw']['fileformat']    = BigEndian2Int(substr($MIDIdata, $offset, 2));
    $offset += 2;
    $ThisFileInfo['midi']['raw']['tracks']        = BigEndian2Int(substr($MIDIdata, $offset, 2));
    $offset += 2;
    $ThisFileInfo['midi']['raw']['ticksperqnote'] = BigEndian2Int(substr($MIDIdata, $offset, 2));
    $offset += 2;

    for ($i = 0; $i < $ThisFileInfo['midi']['raw']['tracks']; $i++) {
		if ((strlen($MIDIdata) - $offset) < 8) {
			$MIDIdata .= fread($fd, FREAD_BUFFER_SIZE);
		}
		$trackID = substr($MIDIdata, $offset, 4);
		$offset += 4;
		if ($trackID == 'MTrk') {
			$tracksize = BigEndian2Int(substr($MIDIdata, $offset, 4));
			$offset += 4;
			// $ThisFileInfo['midi']['tracks'][$i]['size'] = $tracksize;
			$trackdataarray[$i] = substr($MIDIdata, $offset, $tracksize);
			$offset += $tracksize;
		} else {
			$ThisFileInfo['error'] .= "\n".'Expecting "MTrk" at '.$offset.', found '.$trackID.' instead';
			return false;
		}
    }

    if (!isset($trackdataarray) || !is_array($trackdataarray)) {
		$ThisFileInfo['error'] .= "\n".'Cannot find MIDI track information';
		unset($ThisFileInfo['midi']);
		unset($ThisFileInfo['fileformat']);
		return false;
    }

    if ($scanwholefile) { // this can take quite a long time, so have the option to bypass it if speed is very important
		$ThisFileInfo['midi']['totalticks'] = 0;
		$ThisFileInfo['playtime_seconds']   = 0;
		$CurrentMicroSecondsPerBeat = 500000; // 120 beats per minute;  60,000,000 microseconds per minute -> 500,000 microseconds per beat
		$CurrentBeatsPerMinute      = 120;    // 120 beats per minute;  60,000,000 microseconds per minute -> 500,000 microseconds per beat

		foreach ($trackdataarray as $tracknumber => $trackdata) {

			$eventsoffset               = 0;
			$LastIssuedMIDIcommand      = 0;
			$LastIssuedMIDIchannel      = 0;
			$CumulativeDeltaTime        = 0;
			$TicksAtCurrentBPM = 0;
			while ($eventsoffset < strlen($trackdata)) {
				$eventid = 0;
				if (isset($MIDIevents[$tracknumber]) && is_array($MIDIevents[$tracknumber])) {
					$eventid = count($MIDIevents[$tracknumber]);
				}
				$deltatime = 0;
				for ($i=0;$i<4;$i++) {
					$deltatimebyte = ord(substr($trackdata, $eventsoffset++, 1));
					$deltatime = ($deltatime << 7) + ($deltatimebyte & 0x7F);
					if ($deltatimebyte & 0x80) {
						// another byte follows
					} else {
						break;
					}
				}
				$CumulativeDeltaTime += $deltatime;
				$TicksAtCurrentBPM   += $deltatime;
				$MIDIevents[$tracknumber][$eventid]['deltatime'] = $deltatime;
				$MIDI_event_channel                                  = ord(substr($trackdata, $eventsoffset++, 1));
				if ($MIDI_event_channel & 0x80) {
					// OK, normal event - MIDI command has MSB set
					$LastIssuedMIDIcommand = $MIDI_event_channel >> 4;
					$LastIssuedMIDIchannel = $MIDI_event_channel & 0x0F;
				} else {
					// running event - assume last command
					$eventsoffset--;
				}
				$MIDIevents[$tracknumber][$eventid]['eventid']   = $LastIssuedMIDIcommand;
				$MIDIevents[$tracknumber][$eventid]['channel']   = $LastIssuedMIDIchannel;
				if ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x8) { // Note off (key is released)

					$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
					$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0x9) { // Note on (key is pressed)

					$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
					$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xA) { // Key after-touch

					$notenumber = ord(substr($trackdata, $eventsoffset++, 1));
					$velocity   = ord(substr($trackdata, $eventsoffset++, 1));

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xB) { // Control Change

					$controllernum = ord(substr($trackdata, $eventsoffset++, 1));
					$newvalue      = ord(substr($trackdata, $eventsoffset++, 1));

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xC) { // Program (patch) change

					$newprogramnum = ord(substr($trackdata, $eventsoffset++, 1));

					$ThisFileInfo['midi']['raw']['track'][$tracknumber]['instrumentid'] = $newprogramnum;
					if ($tracknumber == 10) {
						$ThisFileInfo['midi']['raw']['track'][$tracknumber]['instrument'] = GeneralMIDIpercussionLookup($newprogramnum);
					} else {
						$ThisFileInfo['midi']['raw']['track'][$tracknumber]['instrument'] = GeneralMIDIinstrumentLookup($newprogramnum);
					}

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xD) { // Channel after-touch

					$channelnumber = ord(substr($trackdata, $eventsoffset++, 1));

				} elseif ($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xE) { // Pitch wheel change (2000H is normal or no change)

					$changeLSB = ord(substr($trackdata, $eventsoffset++, 1));
					$changeMSB = ord(substr($trackdata, $eventsoffset++, 1));
					$pitchwheelchange = (($changeMSB & 0x7F) << 7) & ($changeLSB & 0x7F);

				} elseif (($MIDIevents[$tracknumber][$eventid]['eventid'] == 0xF) && ($MIDIevents[$tracknumber][$eventid]['channel'] == 0xF)) {

					$METAeventCommand = ord(substr($trackdata, $eventsoffset++, 1));
					$METAeventLength  = ord(substr($trackdata, $eventsoffset++, 1));
					$METAeventData    = substr($trackdata, $eventsoffset, $METAeventLength);
					$eventsoffset += $METAeventLength;
					switch ($METAeventCommand) {
						case 0x00: // Set track sequence number
							$track_sequence_number = BigEndian2Int(substr($METAeventData, 0, $METAeventLength));
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['seqno'] = $track_sequence_number;
							break;

						case 0x01: // Text: generic
							$text_generic = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['text'] = $text_generic;
							if (empty($ThisFileInfo['midi']['comments']['comment'])) {
								$ThisFileInfo['midi']['comments']['comment'] = '';
							}
							$ThisFileInfo['midi']['comments']['comment'] .= $text_generic."\n";
							break;

						case 0x02: // Text: copyright
							$text_copyright = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['copyright'] = $text_copyright;
							if (empty($ThisFileInfo['midi']['comments']['copyright'])) {
								$ThisFileInfo['midi']['comments']['copyright'] = '';
							}
							$ThisFileInfo['midi']['comments']['copyright'] = $text_copyright."\n";
							break;

						case 0x03: // Text: track name
							$text_trackname = substr($METAeventData, 0, $METAeventLength);
							$ThisFileInfo['midi']['raw']['track'][$tracknumber]['name'] = $text_trackname;
							break;

						case 0x04: // Text: track instrument name
							$text_instrument = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['instrument'] = $text_instrument;
							break;

						case 0x05: // Text: lyric
							$text_lyric  = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['lyric'] = $text_lyric;
							if (!isset($ThisFileInfo['midi']['lyric'])) {
								$ThisFileInfo['midi']['lyric'] = '';
							}
							$ThisFileInfo['midi']['lyric'] .= $text_lyric."\n";
							break;

						case 0x06: // Text: marker
							$text_marker = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['marker'] = $text_marker;
							break;

						case 0x07: // Text: cue point
							$text_cuepoint = substr($METAeventData, 0, $METAeventLength);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['cuepoint'] = $text_cuepoint;
							break;

						case 0x2F: // End Of Track
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['EOT'] = $CumulativeDeltaTime;
							break;

						case 0x51: // Tempo: microseconds / quarter note
							$CurrentMicroSecondsPerBeat = BigEndian2Int(substr($METAeventData, 0, $METAeventLength));
							if ($CurrentMicroSecondsPerBeat == 0) {
								$ThisFileInfo['error'] .= "\n".'Corrupt MIDI file: CurrentMicroSecondsPerBeat == zero';
								return false;
							}
							$ThisFileInfo['midi']['raw']['events'][$tracknumber][$CumulativeDeltaTime]['us_qnote'] = $CurrentMicroSecondsPerBeat;
							$CurrentBeatsPerMinute      = (1000000 / $CurrentMicroSecondsPerBeat) * 60;
							$MicroSecondsPerQuarterNoteAfter[$CumulativeDeltaTime] = $CurrentMicroSecondsPerBeat;
							$TicksAtCurrentBPM = 0;
							break;

						case 0x58: // Time signature
							$timesig_numerator   = BigEndian2Int($METAeventData{0});
							$timesig_denominator = pow(2, BigEndian2Int($METAeventData{1})); // $02 -> x/4, $03 -> x/8, etc
							$timesig_32inqnote   = BigEndian2Int($METAeventData{2});         // number of 32nd notes to the quarter note
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['timesig_32inqnote']   = $timesig_32inqnote;
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['timesig_numerator']   = $timesig_numerator;
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['timesig_denominator'] = $timesig_denominator;
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['timesig_text']        = $timesig_numerator.'/'.$timesig_denominator;
							$ThisFileInfo['midi']['timesignature'][] = $timesig_numerator.'/'.$timesig_denominator;
							break;

						case 0x59: // Keysignature
							$keysig_sharpsflats = BigEndian2Int($METAeventData{0});
							if ($keysig_sharpsflats & 0x80) {
								// (-7 -> 7 flats, 0 ->key of C, 7 -> 7 sharps)
								$keysig_sharpsflats -= 256;
							}

							$keysig_majorminor  = BigEndian2Int($METAeventData{1}); // 0 -> major, 1 -> minor
							$keysigs = array(-7=>'Cb', -6=>'Gb', -5=>'Db', -4=>'Ab', -3=>'Eb', -2=>'Bb', -1=>'F', 0=>'C', 1=>'G', 2=>'D', 3=>'A', 4=>'E', 5=>'B', 6=>'F#', 7=>'C#');
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['keysig_sharps'] = (($keysig_sharpsflats > 0) ? abs($keysig_sharpsflats) : 0);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['keysig_flats']  = (($keysig_sharpsflats < 0) ? abs($keysig_sharpsflats) : 0);
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['keysig_minor']  = (bool) $keysig_majorminor;
							//$ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['keysig_text']   = $keysigs[$keysig_sharpsflats].' '.($ThisFileInfo['midi']['raw']['events'][$tracknumber][$eventid]['keysig_minor'] ? 'minor' : 'major');

							// $keysigs[$keysig_sharpsflats] gets an int key (correct) - $keysigs["$keysig_sharpsflats"] gets a string key (incorrect)
							$ThisFileInfo['midi']['keysignature'][] = $keysigs[$keysig_sharpsflats].' '.((bool) $keysig_majorminor ? 'minor' : 'major');
							break;

						case 0x7F: // Sequencer specific information
							$custom_data = substr($METAeventData, 0, $METAeventLength);
							break;

						default:
							$ThisFileInfo['warning'] .= "\n".'Unhandled META Event Command: '.$METAeventCommand;
							break;
					}

				} else {

					$ThisFileInfo['warning'] .= "\n".'Unhandled MIDI Event ID: '.$MIDIevents[$tracknumber][$eventid]['eventid'];

				}
			}
			if ($tracknumber > 0) {
				$ThisFileInfo['midi']['totalticks'] = max($ThisFileInfo['midi']['totalticks'], $CumulativeDeltaTime);
			}
		}
		$previoustickoffset = 0;
		foreach ($MicroSecondsPerQuarterNoteAfter as $tickoffset => $microsecondsperbeat) {
			if ($ThisFileInfo['midi']['totalticks'] > $tickoffset) {

				if ($ThisFileInfo['midi']['raw']['ticksperqnote'] == 0) {
					$ThisFileInfo['error'] .= "\n".'Corrupt MIDI file: ticksperqnote == zero';
					return false;
				}

				$ThisFileInfo['playtime_seconds'] += (($tickoffset - $previoustickoffset) / $ThisFileInfo['midi']['raw']['ticksperqnote']) * ($microsecondsperbeat / 1000000);
				$previoustickoffset = $tickoffset;
			}
		}
		if ($ThisFileInfo['midi']['totalticks'] > $previoustickoffset) {

			if ($ThisFileInfo['midi']['raw']['ticksperqnote'] == 0) {
				$ThisFileInfo['error'] .= "\n".'Corrupt MIDI file: ticksperqnote == zero';
				return false;
			}

			$ThisFileInfo['playtime_seconds'] += (($ThisFileInfo['midi']['totalticks'] - $previoustickoffset) / $ThisFileInfo['midi']['raw']['ticksperqnote']) * ($microsecondsperbeat / 1000000);

		}
    }

    // MIDI tags have highest priority
    if (!empty($ThisFileInfo['midi']['comments'])) {
		CopyFormatCommentsToRootComments($ThisFileInfo['midi']['comments'], $ThisFileInfo, true, true, true);
		// add tag to array of tags
		$ThisFileInfo['tags'][] = 'midi';
    }

    return true;
}

function GeneralMIDIinstrumentLookup($instrumentid) {
    static $GeneralMIDIinstrumentLookup = array();
    if (empty($GeneralMIDIinstrumentLookup)) {
		$GeneralMIDIinstrumentLookup[0]   = 'Acoustic Grand';
		$GeneralMIDIinstrumentLookup[1]   = 'Bright Acoustic';
		$GeneralMIDIinstrumentLookup[2]   = 'Electric Grand';
		$GeneralMIDIinstrumentLookup[3]   = 'Honky-Tonk';
		$GeneralMIDIinstrumentLookup[4]   = 'Electric Piano 1';
		$GeneralMIDIinstrumentLookup[5]   = 'Electric Piano 2';
		$GeneralMIDIinstrumentLookup[6]   = 'Harpsichord';
		$GeneralMIDIinstrumentLookup[7]   = 'Clav';
		$GeneralMIDIinstrumentLookup[8]   = 'Celesta';
		$GeneralMIDIinstrumentLookup[9]   = 'Glockenspiel';
		$GeneralMIDIinstrumentLookup[10]  = 'Music Box';
		$GeneralMIDIinstrumentLookup[11]  = 'Vibraphone';
		$GeneralMIDIinstrumentLookup[12]  = 'Marimba';
		$GeneralMIDIinstrumentLookup[13]  = 'Xylophone';
		$GeneralMIDIinstrumentLookup[14]  = 'Tubular Bells';
		$GeneralMIDIinstrumentLookup[15]  = 'Dulcimer';
		$GeneralMIDIinstrumentLookup[16]  = 'Drawbar Organ';
		$GeneralMIDIinstrumentLookup[17]  = 'Percussive Organ';
		$GeneralMIDIinstrumentLookup[18]  = 'Rock Organ';
		$GeneralMIDIinstrumentLookup[19]  = 'Church Organ';
		$GeneralMIDIinstrumentLookup[20]  = 'Reed Organ';
		$GeneralMIDIinstrumentLookup[21]  = 'Accordian';
		$GeneralMIDIinstrumentLookup[22]  = 'Harmonica';
		$GeneralMIDIinstrumentLookup[23]  = 'Tango Accordian';
		$GeneralMIDIinstrumentLookup[24]  = 'Acoustic Guitar (nylon)';
		$GeneralMIDIinstrumentLookup[25]  = 'Acoustic Guitar (steel)';
		$GeneralMIDIinstrumentLookup[26]  = 'Electric Guitar (jazz)';
		$GeneralMIDIinstrumentLookup[27]  = 'Electric Guitar (clean)';
		$GeneralMIDIinstrumentLookup[28]  = 'Electric Guitar (muted)';
		$GeneralMIDIinstrumentLookup[29]  = 'Overdriven Guitar';
		$GeneralMIDIinstrumentLookup[30]  = 'Distortion Guitar';
		$GeneralMIDIinstrumentLookup[31]  = 'Guitar Harmonics';
		$GeneralMIDIinstrumentLookup[32]  = 'Acoustic Bass';
		$GeneralMIDIinstrumentLookup[33]  = 'Electric Bass (finger)';
		$GeneralMIDIinstrumentLookup[34]  = 'Electric Bass (pick)';
		$GeneralMIDIinstrumentLookup[35]  = 'Fretless Bass';
		$GeneralMIDIinstrumentLookup[36]  = 'Slap Bass 1';
		$GeneralMIDIinstrumentLookup[37]  = 'Slap Bass 2';
		$GeneralMIDIinstrumentLookup[38]  = 'Synth Bass 1';
		$GeneralMIDIinstrumentLookup[39]  = 'Synth Bass 2';
		$GeneralMIDIinstrumentLookup[40]  = 'Violin';
		$GeneralMIDIinstrumentLookup[41]  = 'Viola';
		$GeneralMIDIinstrumentLookup[42]  = 'Cello';
		$GeneralMIDIinstrumentLookup[43]  = 'Contrabass';
		$GeneralMIDIinstrumentLookup[44]  = 'Tremolo Strings';
		$GeneralMIDIinstrumentLookup[45]  = 'Pizzicato Strings';
		$GeneralMIDIinstrumentLookup[46]  = 'Orchestral Strings';
		$GeneralMIDIinstrumentLookup[47]  = 'Timpani';
		$GeneralMIDIinstrumentLookup[48]  = 'String Ensemble 1';
		$GeneralMIDIinstrumentLookup[49]  = 'String Ensemble 2';
		$GeneralMIDIinstrumentLookup[50]  = 'SynthStrings 1';
		$GeneralMIDIinstrumentLookup[51]  = 'SynthStrings 2';
		$GeneralMIDIinstrumentLookup[52]  = 'Choir Aahs';
		$GeneralMIDIinstrumentLookup[53]  = 'Voice Oohs';
		$GeneralMIDIinstrumentLookup[54]  = 'Synth Voice';
		$GeneralMIDIinstrumentLookup[55]  = 'Orchestra Hit';
		$GeneralMIDIinstrumentLookup[56]  = 'Trumpet';
		$GeneralMIDIinstrumentLookup[57]  = 'Trombone';
		$GeneralMIDIinstrumentLookup[58]  = 'Tuba';
		$GeneralMIDIinstrumentLookup[59]  = 'Muted Trumpet';
		$GeneralMIDIinstrumentLookup[60]  = 'French Horn';
		$GeneralMIDIinstrumentLookup[61]  = 'Brass Section';
		$GeneralMIDIinstrumentLookup[62]  = 'SynthBrass 1';
		$GeneralMIDIinstrumentLookup[63]  = 'SynthBrass 2';
		$GeneralMIDIinstrumentLookup[64]  = 'Soprano Sax';
		$GeneralMIDIinstrumentLookup[65]  = 'Alto Sax';
		$GeneralMIDIinstrumentLookup[66]  = 'Tenor Sax';
		$GeneralMIDIinstrumentLookup[67]  = 'Baritone Sax';
		$GeneralMIDIinstrumentLookup[68]  = 'Oboe';
		$GeneralMIDIinstrumentLookup[69]  = 'English Horn';
		$GeneralMIDIinstrumentLookup[70]  = 'Bassoon';
		$GeneralMIDIinstrumentLookup[71]  = 'Clarinet';
		$GeneralMIDIinstrumentLookup[72]  = 'Piccolo';
		$GeneralMIDIinstrumentLookup[73]  = 'Flute';
		$GeneralMIDIinstrumentLookup[74]  = 'Recorder';
		$GeneralMIDIinstrumentLookup[75]  = 'Pan Flute';
		$GeneralMIDIinstrumentLookup[76]  = 'Blown Bottle';
		$GeneralMIDIinstrumentLookup[77]  = 'Shakuhachi';
		$GeneralMIDIinstrumentLookup[78]  = 'Whistle';
		$GeneralMIDIinstrumentLookup[79]  = 'Ocarina';
		$GeneralMIDIinstrumentLookup[80]  = 'Lead 1 (square)';
		$GeneralMIDIinstrumentLookup[81]  = 'Lead 2 (sawtooth)';
		$GeneralMIDIinstrumentLookup[82]  = 'Lead 3 (calliope)';
		$GeneralMIDIinstrumentLookup[83]  = 'Lead 4 (chiff)';
		$GeneralMIDIinstrumentLookup[84]  = 'Lead 5 (charang)';
		$GeneralMIDIinstrumentLookup[85]  = 'Lead 6 (voice)';
		$GeneralMIDIinstrumentLookup[86]  = 'Lead 7 (fifths)';
		$GeneralMIDIinstrumentLookup[87]  = 'Lead 8 (bass + lead)';
		$GeneralMIDIinstrumentLookup[88]  = 'Pad 1 (new age)';
		$GeneralMIDIinstrumentLookup[89]  = 'Pad 2 (warm)';
		$GeneralMIDIinstrumentLookup[90]  = 'Pad 3 (polysynth)';
		$GeneralMIDIinstrumentLookup[91]  = 'Pad 4 (choir)';
		$GeneralMIDIinstrumentLookup[92]  = 'Pad 5 (bowed)';
		$GeneralMIDIinstrumentLookup[93]  = 'Pad 6 (metallic)';
		$GeneralMIDIinstrumentLookup[94]  = 'Pad 7 (halo)';
		$GeneralMIDIinstrumentLookup[95]  = 'Pad 8 (sweep)';
		$GeneralMIDIinstrumentLookup[96]  = 'FX 1 (rain)';
		$GeneralMIDIinstrumentLookup[97]  = 'FX 2 (soundtrack)';
		$GeneralMIDIinstrumentLookup[98]  = 'FX 3 (crystal)';
		$GeneralMIDIinstrumentLookup[99]  = 'FX 4 (atmosphere)';
		$GeneralMIDIinstrumentLookup[100] = 'FX 5 (brightness)';
		$GeneralMIDIinstrumentLookup[101] = 'FX 6 (goblins)';
		$GeneralMIDIinstrumentLookup[102] = 'FX 7 (echoes)';
		$GeneralMIDIinstrumentLookup[103] = 'FX 8 (sci-fi)';
		$GeneralMIDIinstrumentLookup[104] = 'Sitar';
		$GeneralMIDIinstrumentLookup[105] = 'Banjo';
		$GeneralMIDIinstrumentLookup[106] = 'Shamisen';
		$GeneralMIDIinstrumentLookup[107] = 'Koto';
		$GeneralMIDIinstrumentLookup[108] = 'Kalimba';
		$GeneralMIDIinstrumentLookup[109] = 'Bagpipe';
		$GeneralMIDIinstrumentLookup[110] = 'Fiddle';
		$GeneralMIDIinstrumentLookup[111] = 'Shanai';
		$GeneralMIDIinstrumentLookup[112] = 'Tinkle Bell';
		$GeneralMIDIinstrumentLookup[113] = 'Agogo';
		$GeneralMIDIinstrumentLookup[114] = 'Steel Drums';
		$GeneralMIDIinstrumentLookup[115] = 'Woodblock';
		$GeneralMIDIinstrumentLookup[116] = 'Taiko Drum';
		$GeneralMIDIinstrumentLookup[117] = 'Melodic Tom';
		$GeneralMIDIinstrumentLookup[118] = 'Synth Drum';
		$GeneralMIDIinstrumentLookup[119] = 'Reverse Cymbal';
		$GeneralMIDIinstrumentLookup[120] = 'Guitar Fret Noise';
		$GeneralMIDIinstrumentLookup[121] = 'Breath Noise';
		$GeneralMIDIinstrumentLookup[122] = 'Seashore';
		$GeneralMIDIinstrumentLookup[123] = 'Bird Tweet';
		$GeneralMIDIinstrumentLookup[124] = 'Telephone Ring';
		$GeneralMIDIinstrumentLookup[125] = 'Helicopter';
		$GeneralMIDIinstrumentLookup[126] = 'Applause';
		$GeneralMIDIinstrumentLookup[127] = 'Gunshot';
    }

    return (isset($GeneralMIDIinstrumentLookup[$instrumentid]) ? $GeneralMIDIinstrumentLookup[$instrumentid] : '');
}

function GeneralMIDIpercussionLookup($instrumentid) {
    static $GeneralMIDIpercussionLookup = array();
    if (empty($GeneralMIDIpercussionLookup)) {
		$GeneralMIDIpercussionLookup[35] = 'Acoustic Bass Drum';
		$GeneralMIDIpercussionLookup[36] = 'Bass Drum 1';
		$GeneralMIDIpercussionLookup[37] = 'Side Stick';
		$GeneralMIDIpercussionLookup[38] = 'Acoustic Snare';
		$GeneralMIDIpercussionLookup[39] = 'Hand Clap';
		$GeneralMIDIpercussionLookup[40] = 'Electric Snare';
		$GeneralMIDIpercussionLookup[41] = 'Low Floor Tom';
		$GeneralMIDIpercussionLookup[42] = 'Closed Hi-Hat';
		$GeneralMIDIpercussionLookup[43] = 'High Floor Tom';
		$GeneralMIDIpercussionLookup[44] = 'Pedal Hi-Hat';
		$GeneralMIDIpercussionLookup[45] = 'Low Tom';
		$GeneralMIDIpercussionLookup[46] = 'Open Hi-Hat';
		$GeneralMIDIpercussionLookup[47] = 'Low-Mid Tom';
		$GeneralMIDIpercussionLookup[48] = 'Hi-Mid Tom';
		$GeneralMIDIpercussionLookup[49] = 'Crash Cymbal 1';
		$GeneralMIDIpercussionLookup[50] = 'High Tom';
		$GeneralMIDIpercussionLookup[51] = 'Ride Cymbal 1';
		$GeneralMIDIpercussionLookup[52] = 'Chinese Cymbal';
		$GeneralMIDIpercussionLookup[53] = 'Ride Bell';
		$GeneralMIDIpercussionLookup[54] = 'Tambourine';
		$GeneralMIDIpercussionLookup[55] = 'Splash Cymbal';
		$GeneralMIDIpercussionLookup[56] = 'Cowbell';
		$GeneralMIDIpercussionLookup[57] = 'Crash Cymbal 2';
		$GeneralMIDIpercussionLookup[59] = 'Ride Cymbal 2';
		$GeneralMIDIpercussionLookup[60] = 'Hi Bongo';
		$GeneralMIDIpercussionLookup[61] = 'Low Bongo';
		$GeneralMIDIpercussionLookup[62] = 'Mute Hi Conga';
		$GeneralMIDIpercussionLookup[63] = 'Open Hi Conga';
		$GeneralMIDIpercussionLookup[64] = 'Low Conga';
		$GeneralMIDIpercussionLookup[65] = 'High Timbale';
		$GeneralMIDIpercussionLookup[66] = 'Low Timbale';
		$GeneralMIDIpercussionLookup[67] = 'High Agogo';
		$GeneralMIDIpercussionLookup[68] = 'Low Agogo';
		$GeneralMIDIpercussionLookup[69] = 'Cabasa';
		$GeneralMIDIpercussionLookup[70] = 'Maracas';
		$GeneralMIDIpercussionLookup[71] = 'Short Whistle';
		$GeneralMIDIpercussionLookup[72] = 'Long Whistle';
		$GeneralMIDIpercussionLookup[73] = 'Short Guiro';
		$GeneralMIDIpercussionLookup[74] = 'Long Guiro';
		$GeneralMIDIpercussionLookup[75] = 'Claves';
		$GeneralMIDIpercussionLookup[76] = 'Hi Wood Block';
		$GeneralMIDIpercussionLookup[77] = 'Low Wood Block';
		$GeneralMIDIpercussionLookup[78] = 'Mute Cuica';
		$GeneralMIDIpercussionLookup[79] = 'Open Cuica';
		$GeneralMIDIpercussionLookup[80] = 'Mute Triangle';
		$GeneralMIDIpercussionLookup[81] = 'Open Triangle';
    }

    return (isset($GeneralMIDIpercussionLookup[$instrumentid]) ? $GeneralMIDIpercussionLookup[$instrumentid] : '');
}

?>