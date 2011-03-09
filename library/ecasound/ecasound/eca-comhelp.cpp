// ------------------------------------------------------------------------
// ecasound.cpp: Console mode user interface to ecasound.
// Copyright (C) 2000,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#include "eca-comhelp.h"

/* FIXME: get chainop list from libecasound!
 *   - 2009/Jan: or maybe not, people should just be pointed to 
 *               ecasound(1) */

static const char* ecasound_parameter_help_rep =
"USAGE: ecasound [options] \n"
"     -c                       enable interactive mode \n"
"     -C                       disable interactive mode (batchmode)\n"
"     -d:debug_level           show debug info \n"
"     -D                       print all trace messages to stderr\n"
"     -q                       quiet mode, no output\n"
"     -s[:]file.ecs            load chainsetup from 'file' \n"
"     -E \"foo1 ; foo 2\"      execute interactive commands at start\n"
"     --server                 enable ecasound's network control mode\n"
"     --server-tcp-port=NNN    use TCP port NNN\n"
"     --no-server              disable the daemon mode (default)\n"
"     --osc-udp-port=NNN       listen for OSC messages on UDP port NNN\n"
"     --keep-running (or -K)   do not exit from batchmode\n"
"     --help (or -h)           show this help\n"
"     --version                print version info\n"
" --- \n"
"     -b:buffersize            size of sample buffer in samples\n"
"     -B:mode                  buffering mode\n"
"     -m:mixmode               mixmode\n"
"     -n:name                  set chainsetup name\n"
"     -r[:priority]            raise runtime priority\n"
"     -sr:sample_rate          set internal sample rate\n"
"     -x                       truncate outputs\n"
"     -X                       open outputs for update (default)\n"
"     -z:feature               enable feature 'feature',  see ecasound(1)\n"
" --- \n"
"     -t:seconds               processing time in seconds\n"
"     -tl                      enable looping\n"
" --- \n"
"     -a:name1,name2, ...      select/create chains ('all' reserved)\n"
"     -f:type,channels,srate   set file format (for all following inputs/outputs)\n"
"     -i[:]infile              specify an input (assigned to active chains)\n"
"     -o[:]outfile             specify an input (assigned to active chains)\n"
"     -y:seconds               set start position for preceding input/output\n"
" --- \n"
"     -Md:rawmidi,midi_device  set MIDI-device\n"
"     -Mms:device_id           send MMC start/stop\n"
"     -Mss                     send MIDI start/stop\n"
" --- \n"
"     -pf:preset.eep           insert the first preset from file 'preset.eep'\n"
"     -pn:preset_name          insert preset 'preset_name' from the\n"
"                              preset database\n"
" --- \n"
"     -eS:stamp-id             audio stamp\n"
"     -ea:amp-%                amplify\n"
"     -eac:amp-%,channel       channel amplify\n"
"     -eaw:amp-%,max-clipped-samples - \n"
"                              amplify with clip-control\n"
"     -ec:compression-rate-dB,threshold-% ...\n"
"                              compressor\n"
"     -eca:peak-limit-%,release-time-sec,fast-crate,overall-crate ...\n"
"                              advanced compressor\n"
"     -eemb:bpm,on-time-msec   pulse gate (bpm)\n"
"     -eemp:freq-Hz,on-time-%  pulse gate\n"
"     -eemt:bpm,depth-%        tremolo\n"
"     -ef1:center-freq,width   resonant bandpass filter\n"
"     -ef3:cutoff-freq,resonance,gain ...\n"
"                              resonant lowpass filter\n"
"     -ef4:cutoff-freq,resonance ...\n"
"                              resonant lowpass filter (2nd-order,24dB)\n"
"     -efa:delay-samples,feedback-% ...\n"
"                              allpass filter\n"
"     -efb:center-freq,width   bandpass filter\n"
"     -efc:delay-samples,radius ...\n"
"                              comb filter\n"
"     -efh:cutoff-freq         highpass filter\n"
"     -efi:delay-samples,radius ...\n"
"                              inverse comb filter\n"
"     -efl:cutoff-freq         lowpass filter\n"
"     -efr:center-freq,width   bandreject filter\n"
"     -efs:center-freq,width   resonator filter\n"
"     -ei:change-%             pitch shifter\n"
"     -el:name,par1,...,parN   LADSPA-plugin 'name'\n"
"     -eli:id,par1,...,parnN   LADSPA-plugin with numeric 'id'\n"
"     -enm:threshold-level-%,pre-hold-time-msec,attack-time-msec,post-hold-time-msec,release-time-msec ...\n"
"                              noise gate\n"
"     -erc:from-channel,to-channel ...\n"
"                              copy 'from-channel' to 'to-channel'\n"
"     -erm:to-channel          mix all channels to channel 'to-channel' \n"
"     -epp:right-%             normal pan\n"
"     -etc:delay-time-msec,variance-time-samples,feedback-%,lfo-freq ...\n"
"                              chorus\n"
"     -etd:delay-time-msec,surround-mode,number-of-delays,mix-%,feedback-% ...\n"
"                              delay\n"
"     -ete:room-size,feedback-%,wet-% ...\n"
"                              advanced reverb\n"
"     -etf:delay-time-msec     fake stereo\n"
"     -etl:delay-time-msec,variance-time-samples,feedback-%,lfo-freq ...\n"
"                             flanger\n"
"     -etm:delay-time-msec,number-of-delays,mix-% ...\n"
"                              multitap delay\n"
"     -etp:delay-time-msec,variance-time-samples,feedback-%,lfo-freq ...\n"
"                              phaser\n"
"     -etr:delay-time,surround-mode,feedback-% ...\n"
"                              reverb\n"
"     -ev:cumulative-mode,result-max-multiplier ...\n"
"                              analyze/maximize volume\n"
"     -evp:peak-ch1,peak-chN   peak amplitude watcher\n"
"     -ezf                     find optimal value for DC-offset adjustment\n"
"     -ezx:channel-count,delta-ch1,...,delta-chN\n"
"                              adjust DC-offset\n"
" --- \n"
"     -gc:open-at-sec,duration-sec ...\n"
"                              time crop gate\n"
"     -ge:threshold-openlevel-%,threshold-closelevel-%,rms-enabled ...\n"
"                              threshold gate\n"
" --- \n"
"     -kf:param-id,range-low,range-high,freq,mode,preset-number ...\n"
"                              file envelope (generic oscillator)\n"
"     -kl:param-id,range-low,range-high,length-sec ...\n"
"                              linear envelope (fade-in and fade-out)\n"
"     -kl2:param-id,range-low,range-high,1st-stage-sec,2nd-stage-sec ...\n"
"                              two-stage linear envelope\n"
"     -klg:param-id,range-low,range-high,point_count ...\n"
"                              generic linear envelope\n"
"     -km:param-id,range-low,range-high,controller,channel ...\n"
"                              MIDI-controlled envelope\n"
"     -kog:param-id,range-low,range-high,freq,mode,pcount,start_val,end_val ...\n"
"                              generic oscillator\n"
"     -kos:param-id,range-low,range-high,freq,phase-offset ...\n"
"                              sine oscillator\n"
"     -ksv:param-id,range-low,range-high,stamp-id,rms-toggle ...\n"
"                              volume analyzing controller"
" --- \n"
"     -kx                      use last specified controller as\n"
"                              controller target\n"
"\n"
"Note that this is only a partial list of available options. For\n"
"a complete list of available options, as well as more detailed\n"
"descriptions of of their use, see ecasound(1) manual page and\n"
"the documentation at ecasound's website. Documentation is available\n"
"online at:\n"
" - http://eca.cx/ecasound/Documentation/ecasound_manpage.html\n"
" - http://eca.cx/ecasound/Documentation/examples.html\n"
"\n"
"Report bugs to ecasound-list mailing list (http://www.eca.cx/contact).\n";

const char* ecasound_parameter_help(void)
{
  return ecasound_parameter_help_rep;
}
