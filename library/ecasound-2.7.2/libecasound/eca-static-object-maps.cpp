// ------------------------------------------------------------------------
// eca-static-object-maps.h: Static object map instances
// Copyright (C) 2000-2004,2006,2008,2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cstdlib>
#include <iostream>
#include <string>
#include <algorithm>

#include <dlfcn.h>
#include <dirent.h>
#include <sys/stat.h>
#include <unistd.h>
#include <string.h>
#include <errno.h>

#include <kvu_numtostr.h>
#include <kvu_utils.h>

#include "eca-version.h"
#include "eca-chainop.h"
#include "audiofx.h"
#include "audiofx_misc.h"
#include "audiofx_amplitude.h"
#include "audiofx_analysis.h"
#include "audiofx_envelope_modulation.h"
#include "audiofx_filter.h"
#include "audiofx_rcfilter.h"
#include "audiofx_reverb.h"
#include "audiofx_mixing.h"
#include "audiofx_timebased.h"
#include "audiogate.h"
#include "audiofx_ladspa.h"

#include "generic-controller.h"
#include "ctrl-source.h"
#include "midi-cc.h"
#include "osc-gen.h"
#include "osc-gen-file.h"
#include "osc-sine.h"
#include "linear-envelope.h"
#include "two-stage-linear-envelope.h"
#include "stamp-ctrl.h"
#include "generic-linear-envelope.h"

#include "audioio-plugin.h"
#include "audioio-cdr.h"
#include "audioio-wave.h"
#ifdef ECA_COMPILE_OSS
#include "audioio-oss.h"
#endif
#include "audioio-ewf.h"
#include "audioio-mp3.h"
#include "audioio-ogg.h"
#include "audioio-mikmod.h"
#include "audioio-timidity.h"
#include "audioio-flac.h"
#include "audioio-aac.h"
#include "audioio-raw.h"
#include "audioio-null.h"
#include "audioio-rtnull.h"
#include "audioio-typeselect.h"
#include "audioio-resample.h"
#include "audioio-reverse.h"
#include "audioio-tone.h"
#include "audioio-acseq.h"

#ifndef ECA_ENABLE_AUDIOIO_PLUGINS
#ifdef ECA_COMPILE_AUDIOFILE
#include "plugins/audioio_af.h"
#endif
#ifdef ECA_COMPILE_SNDFILE
#include "plugins/audioio_sndfile.h"
#endif
#ifdef ECA_COMPILE_ALSA
#include "plugins/audioio_alsa.h"
#include "plugins/audioio_alsa_named.h"
#endif
#ifdef ECA_COMPILE_ARTS
#include "plugins/audioio_arts.h"
#endif
#ifdef ECA_COMPILE_JACK
#include "plugins/audioio_jack.h"
#endif
#endif /* ECA_ENABLE_AUDIOIO_PLUGINS */

#include "midiio-raw.h"
#ifdef ECA_COMPILE_ALSA
#include "midiio-aseq.h"
#endif

#include "eca-object-map.h"
#include "eca-preset-map.h"
#include "eca-static-object-maps.h"

#include "eca-resources.h"
#include "eca-logger.h"
#include "eca-error.h"

using std::cerr;
using std::endl;
using std::find;
using std::string;
using std::list;
using std::vector;

/**
 * Declarations for static private helper functions
 */

static vector<EFFECT_LADSPA*> eca_create_ladspa_plugins(const string& fname);
static void eca_import_ladspa_plugins(ECA_OBJECT_MAP* objmap, bool reg_with_id);

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
static void eca_import_internal_audioio_plugin(ECA_OBJECT_MAP* objmap, const string& filename);
#endif

/**
 * Definitions of static member functions
 */

void ECA_STATIC_OBJECT_MAPS::register_audio_io_rt_objects(ECA_OBJECT_MAP* objmap)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_audio_io_rt_objects()");

  AUDIO_IO* device = 0;  
#ifdef ECA_COMPILE_OSS
  device = new OSSDEVICE();
  objmap->register_object("/dev/dsp", "/dev/dsp[0-9]*", device);
  objmap->register_object("/dev/sound/dsp", "/dev/sound/dsp[0-9]*", device);
#endif

  device = new REALTIME_NULL();
  objmap->register_object("rtnull", "^rtnull$", device);

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
  eca_import_internal_audioio_plugin(objmap, "libaudioio_alsa.so");
  eca_import_internal_audioio_plugin(objmap, "libaudioio_alsa_named.so");
  eca_import_internal_audioio_plugin(objmap, "libaudioio_arts.so");
  eca_import_internal_audioio_plugin(objmap, "libaudioio_jack.so");
#else /* ECA_ENABLE_AUDIOIO_PLUGINS */
#ifdef ECA_COMPILE_ALSA
  device = new AUDIO_IO_ALSA_PCM();
  objmap->register_object("alsahw_09", "(^alsahw_09$)|(^alsaplugin_09$)", device);

  device = new AUDIO_IO_ALSA_PCM_NAMED();
  objmap->register_object("alsa_09", "^alsa_09$", device);
#endif

#ifdef ECA_COMPILE_ARTS
  device = new ARTS_INTERFACE();
  objmap->register_object("arts", "^arts$", device);
#endif

#ifdef ECA_COMPILE_JACK
  device = new AUDIO_IO_JACK();
  objmap->register_object("jack", "(^jack$)|(^jack_multi$)|(^jack_alsa$)|(^jack_auto$)|(^jack_generic$)", device);
#endif
#endif /* ECA_ENABLE_AUDIOIO_PLUGINS */

  const ECA_OBJECT* aobj = 0;

  aobj = objmap->object("alsahw_09");
  if (aobj != 0) {
    objmap->register_object("alsahw", "^alsahw$", const_cast<ECA_OBJECT*>(aobj));
    objmap->register_object("alsaplugin", "^alsaplugin$", const_cast<ECA_OBJECT*>(aobj));
  }

  aobj = objmap->object("alsa_09");
  if (aobj != 0) {
    objmap->register_object("alsa", "^alsa$", const_cast<ECA_OBJECT*>(aobj));
  }
}

void ECA_STATIC_OBJECT_MAPS::register_audio_io_nonrt_objects(ECA_OBJECT_MAP* objmap)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_audio_io_nonrt_objects()");

  bool native_flac = false;

  objmap->register_object("wav", "wav$", new WAVEFILE());
  objmap->register_object("ewf", "ewf$", new EWFFILE());
  objmap->register_object("cdr", "cdr$", new CDRFILE());

  AUDIO_IO* raw = new RAWFILE();
  objmap->register_object("raw", "raw$", raw);

  AUDIO_IO* mp3 = new MP3FILE();
  objmap->register_object("mp3", "mp3$", mp3);
  objmap->register_object("mp2", "mp2$", mp3);

  AUDIO_IO* ogg = new OGG_VORBIS_INTERFACE();
  objmap->register_object("ogg", "ogg$", ogg);

  AUDIO_IO* mikmod = new MIKMOD_INTERFACE();
  objmap->register_object("mikmod", 
			  "(^mikmod$)|(xm$)|(669$)|(amf$)|(dsm$)|(far$)|(gdm$)|(imf$)|"
			  "(it$)|(m15$)|(ed$)|(mod$)|(mtm$)|(s3m$)|"
			  "(stm$)|(stx$)|(ult$)|(uni$)", mikmod);

  AUDIO_IO* timidity = new TIMIDITY_INTERFACE();
  objmap->register_object("mid", "(mid$)|(midi$)", timidity);


  AUDIO_IO* forkedaac = new AAC_FORKED_INTERFACE();
  objmap->register_object("aac", "aac$", forkedaac);
  objmap->register_object("mp4", "mp4$", forkedaac);
  objmap->register_object("m4a", "m4a$", forkedaac);

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
  eca_import_internal_audioio_plugin(objmap, "libaudioio_af.so");
  eca_import_internal_audioio_plugin(objmap, "libaudioio_sndfile.so");
#else /* !ECA_ENABLE_AUDIOIO_PLUGINS */

  /* ---------------------------------------------------------*/
  /* register file types to plugins handling audio file types */

#if defined(ECA_COMPILE_SNDFILE) || defined(ECA_COMPILE_AUDIOFILE)
  string common_types ("(aif*$)|(au$)|(snd$)");
#endif

#ifdef ECA_COMPILE_SNDFILE
  SNDFILE_INTERFACE* sndfile = new SNDFILE_INTERFACE();
  /* 1. register types supported by libsndfile */
  string sf_types ("(^sndfile$)");
  list<string> el = sndfile->supported_extensions();

  list<string>::const_iterator i = el.begin();
  string sf_all_types;
  while(i != el.end()) {
    sf_all_types += *i + ",";
    ++i;
  }
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "All libsndfile supported extensions: " + sf_all_types);

  if (find(el.begin(), el.end(), "flac") != el.end()) {
    sf_types += "|(flac$)";
    native_flac = true;
  }
  if (find(el.begin(), el.end(), "avr") != el.end()) sf_types += "|(avr$)";
  if (find(el.begin(), el.end(), "caf") != el.end()) sf_types += "|(caf$)";
  if (find(el.begin(), el.end(), "htk") != el.end()) sf_types += "|(htk$)";
  if (find(el.begin(), el.end(), "iff") != el.end()) sf_types += "|(iff$)";
  if (find(el.begin(), el.end(), "mat") != el.end()) sf_types += "|(mat$)";
  if (find(el.begin(), el.end(), "paf") != el.end()) sf_types += "|(paf$)";
  if (find(el.begin(), el.end(), "pvf") != el.end()) sf_types += "|(pvf$)";
  if (find(el.begin(), el.end(), "nist") != el.end()) sf_types += "|(nist$)";
  if (find(el.begin(), el.end(), "sf") != el.end()) sf_types += "|(sf$)";
  if (find(el.begin(), el.end(), "sd2") != el.end()) sf_types += "|(sd2$)";
  if (find(el.begin(), el.end(), "sds") != el.end()) sf_types += "|(sds$)";
  if (find(el.begin(), el.end(), "voc") != el.end()) sf_types += "|(voc$)";
  if (find(el.begin(), el.end(), "w64") != el.end()) sf_types += "|(w64$)";
  if (find(el.begin(), el.end(), "xi") != el.end()) sf_types += "|(xi$)";

  /* add formats supported by both libaudiofile and libsndfile */
  sf_types += string("|") + common_types;
  common_types.clear();
  
  objmap->register_object("sndfile", sf_types.c_str(), dynamic_cast<AUDIO_IO*>(sndfile));
#endif

#ifdef ECA_COMPILE_AUDIOFILE
  /* 2. register types for libaudiofile */
  string af_types ("(^audiofile$)");
  /* note, if sndfile not available, common_types are registered
   *       to libaudiofile */
  if (common_types.size() > 0)
    af_types += string("|") + common_types;
  AUDIO_IO* af = new AUDIOFILE_INTERFACE();
  objmap->register_object("audiofile", af_types.c_str(), af);
#endif

  /* ---------------------------------------------------------*/

#endif /* ECA_ENABLE_AUDIOIO_PLUGINS */

  objmap->register_object("-", "^-$", raw);
  objmap->register_object("stdin", "^stdin$", raw);
  objmap->register_object("stdout", "^stdout$", raw);
  objmap->register_object("null", "^null$", new NULLFILE());
  objmap->register_object("typeselect", "^typeselect$", new AUDIO_IO_TYPESELECT());
  objmap->register_object("resample", "^resample$", new AUDIO_IO_RESAMPLE());
  objmap->register_object("resample-hq", "^resample-hq$", new AUDIO_IO_RESAMPLE());
  objmap->register_object("resample-lq", "^resample-lq$", new AUDIO_IO_RESAMPLE());
  objmap->register_object("reverse", "^reverse$", new AUDIO_IO_REVERSE());
  objmap->register_object("tone", "^tone$", new AUDIO_IO_TONE());
  objmap->register_object("audioloop", "^(audioloop|select|playat)$", new AUDIO_CLIP_SEQUENCER());

  if (native_flac != true) {
    AUDIO_IO* forkedflac = new FLAC_FORKED_INTERFACE();
    objmap->register_object("flac", "flac$", forkedflac);
  }
}

void ECA_STATIC_OBJECT_MAPS::register_chain_operator_objects(ECA_OBJECT_MAP* objmap)
{
#ifndef ECA_DISABLE_EFFECTS
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_chain_operator_objects()");

  objmap->register_object("eS", "^eS$", new EFFECT_AUDIO_STAMP());
  objmap->register_object("ea", "^ea$", new EFFECT_AMPLIFY());
  objmap->register_object("eadb", "^eadb$", new EFFECT_AMPLIFY_DB());
  objmap->register_object("eac", "^eac$", new EFFECT_AMPLIFY_CHANNEL());
  objmap->register_object("eal", "^eal$", new EFFECT_LIMITER());
  objmap->register_object("eaw", "^eaw$", new EFFECT_AMPLIFY_CLIPCOUNT());
  objmap->register_object("ec", "^ec$", new EFFECT_COMPRESS());
  objmap->register_object("eca", "^eca$", new ADVANCED_COMPRESSOR());
  objmap->register_object("eemb", "^eemb$", new EFFECT_PULSE_GATE_BPM());
  objmap->register_object("eemp", "^eemp$", new EFFECT_PULSE_GATE());
  objmap->register_object("eemt", "^eemt$", new EFFECT_TREMOLO());
  objmap->register_object("ef1", "^ef1$", new EFFECT_RESONANT_BANDPASS());
  objmap->register_object("ef3", "^ef3$", new EFFECT_RESONANT_LOWPASS());
  objmap->register_object("ef4", "^ef4$", new EFFECT_RC_LOWPASS_FILTER());
  objmap->register_object("efa", "^efa$", new EFFECT_ALLPASS_FILTER());
  objmap->register_object("efb", "^efb$", new EFFECT_BANDPASS());
  objmap->register_object("efc", "^efc$", new EFFECT_COMB_FILTER());
  objmap->register_object("efh", "^efh$", new EFFECT_HIGHPASS());
  objmap->register_object("efi", "^efi$", new EFFECT_INVERSE_COMB_FILTER());
  objmap->register_object("efl", "^efl$", new EFFECT_LOWPASS());
  objmap->register_object("efr", "^efr$", new EFFECT_BANDREJECT());
  objmap->register_object("efs", "^efs$", new EFFECT_RESONATOR());
  objmap->register_object("ei", "^ei$", new EFFECT_PITCH_SHIFT());
  objmap->register_object("enm", "^enm$", new EFFECT_NOISEGATE());
  objmap->register_object("epp", "^epp$", new EFFECT_NORMAL_PAN());
  objmap->register_object("chorder", "^chorder$", new EFFECT_CHANNEL_ORDER());
  EFFECT_CHANNEL_COPY *op_cp = new EFFECT_CHANNEL_COPY();
  objmap->register_object("chcopy", "^chcopy$", op_cp);
  objmap->register_object("erc", "^erc$", op_cp);
  objmap->register_object("chmove", "^chmove$", new EFFECT_CHANNEL_MOVE());
  objmap->register_object("chmute", "^chmute$", new EFFECT_CHANNEL_MUTE());
  EFFECT_MIX_TO_CHANNEL *op_mix = new EFFECT_MIX_TO_CHANNEL();
  objmap->register_object("erm", "^erm$", op_mix);
  objmap->register_object("chmix", "^chmix$", op_mix);
  objmap->register_object("etc", "^etc$", new EFFECT_CHORUS());
  objmap->register_object("etd", "^etd$", new EFFECT_DELAY());
  objmap->register_object("ete", "^ete$", new ADVANCED_REVERB());
  objmap->register_object("etf", "^etf$", new EFFECT_FAKE_STEREO());
  objmap->register_object("etl", "^etl$", new EFFECT_FLANGER());
  objmap->register_object("etm", "^etm$", new EFFECT_MULTITAP_DELAY());
  objmap->register_object("etp", "^etp$", new EFFECT_PHASER());
  objmap->register_object("etr", "^etr$", new EFFECT_REVERB());
  objmap->register_object("ev", "^ev$", new EFFECT_VOLUME_BUCKETS());
  objmap->register_object("evp", "^evp$", new EFFECT_VOLUME_PEAK());
  objmap->register_object("ezf", "^ezf$", new EFFECT_DCFIND());
  objmap->register_object("ezx", "^ezx$", new EFFECT_DCFIX());
  objmap->register_object("gc", "^gc$", new TIME_CROP_GATE());
  objmap->register_object("ge", "^ge$", new THRESHOLD_GATE());
  objmap->register_object("gm", "^gm$", new MANUAL_GATE());
#endif
}

void ECA_STATIC_OBJECT_MAPS::register_ladspa_plugin_objects(ECA_OBJECT_MAP* objmap)
{
#ifndef ECA_DISABLE_EFFECTS
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_ladspa_plugin_objects()");

  eca_import_ladspa_plugins(objmap, false);
#endif
}

void ECA_STATIC_OBJECT_MAPS::register_ladspa_plugin_id_objects(ECA_OBJECT_MAP* objmap)
{
#ifndef ECA_DISABLE_EFFECTS
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_ladspa_plugin_id_objects()");

  eca_import_ladspa_plugins(objmap, true);
#endif
}

void ECA_STATIC_OBJECT_MAPS::register_preset_objects(ECA_PRESET_MAP* objmap)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_preset_objects()");
  /* @see ECA_PRESET_MAP */
}

void ECA_STATIC_OBJECT_MAPS::register_controller_objects(ECA_OBJECT_MAP* objmap)
{
#ifndef ECA_DISABLE_EFFECTS
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_controller_objects()");

  objmap->register_object("kf", "^kf$", new GENERIC_CONTROLLER(new GENERIC_OSCILLATOR_FILE()));
  objmap->register_object("kog", "^kog$", new GENERIC_CONTROLLER(new GENERIC_OSCILLATOR()));
  objmap->register_object("kl", "^kl$", new GENERIC_CONTROLLER(new LINEAR_ENVELOPE()));
  objmap->register_object("kl2", "^kl2$", new GENERIC_CONTROLLER(new TWO_STAGE_LINEAR_ENVELOPE()));
  objmap->register_object("klg", "^klg$", new GENERIC_CONTROLLER(new GENERIC_LINEAR_ENVELOPE()));
  objmap->register_object("km", "^km$", new GENERIC_CONTROLLER(new MIDI_CONTROLLER()));
  objmap->register_object("kos", "^kos$", new GENERIC_CONTROLLER(new SINE_OSCILLATOR()));
  objmap->register_object("ksv", "^ksv$", new GENERIC_CONTROLLER(new VOLUME_ANALYZE_CONTROLLER()));
#endif
}

void ECA_STATIC_OBJECT_MAPS::register_midi_device_objects(ECA_OBJECT_MAP* objmap)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "register_midi_device_objects()");

  objmap->register_object("rawmidi", "^rawmidi$", new MIDI_IO_RAW());
#ifdef ECA_COMPILE_ALSA
  objmap->register_object("alsaseq", "^alsaseq$", new MIDI_IO_ASEQ());
#endif
}

/**
 * Definitions for static private helper functions
 */

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
/**
 * Load ecasound's internal plugins. Not used since 2.2.0.
 */ 
static void eca_import_internal_audioio_plugin(ECA_OBJECT_MAP* objmap,
					       const string& filename)
{
  ECA_RESOURCES ecarc;
  string libdir = ecarc.resource("internal-plugin-directory");

  struct stat fbuf;
  if (stat(libdir.c_str(), &fbuf) < 0) {
    ECA_LOG_MSG(ECA_LOGGER::info, "Internal-plugin directory not found. Check your ~/.ecasoundrc!");
    return;
  }

  string file = libdir + string("/") + filename;

  audio_io_descriptor desc_func = 0;
  audio_io_interface_version plugin_version = 0;
  audio_io_keyword plugin_keyword = 0;
  audio_io_keyword_regex plugin_keyword_regex = 0;
  
  void *plugin_handle = dlopen(file.c_str(), RTLD_NOW | RTLD_GLOBAL); /* RTLD_LAZY */

  if (plugin_handle != 0) {
    plugin_version = (audio_io_interface_version)dlsym(plugin_handle, "audio_io_interface_version");
    if (plugin_version != 0) {
      int version = plugin_version();
      if (version < ecasound_library_version_current -
	  ecasound_library_version_age ||
	  version > ecasound_library_version_current) {
	ECA_LOG_MSG(ECA_LOGGER::info, 
		      "Opening internal plugin file \"" + 
		      file + 
		      "\" failed. Plugin version " + 
		      kvu_numtostr(version) +
		      " doesn't match libecasound version " +
		      kvu_numtostr(ecasound_library_version_current) + "." +
		      kvu_numtostr(ecasound_library_version_revision) + "." +
		      kvu_numtostr(ecasound_library_version_age) + ".");
      }
      else {
	desc_func = (audio_io_descriptor)dlsym(plugin_handle, "audio_io_descriptor");
	plugin_keyword = (audio_io_keyword)dlsym(plugin_handle, "audio_io_keyword");
	plugin_keyword_regex = (audio_io_keyword_regex)dlsym(plugin_handle, "audio_io_keyword_regex");
	if (desc_func != 0) {
	  AUDIO_IO* aobj = desc_func();
	  if (plugin_keyword != 0 && plugin_keyword_regex != 0) {
	    objmap->register_object(plugin_keyword(), plugin_keyword_regex(), aobj);
	    // std::cerr << "Registering audio io type: " << aobj->name()  << "\nType keyword " << plugin_keyword() << ",  regex " << plugin_keyword_regex() << "." << std::endl;
	  }
	}
      }
    }
    else {
      std::cerr << "(eca-static-object-maps) dlsym() failed; " << file;
      std::cerr << ": \"" << dlerror() << "\"." << std::endl;
    }
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"dlopen() failed; " + file +
		+ ": \"" + string(dlerror()) + "\".");
  }

  if (plugin_handle == 0 ||
      plugin_version == 0 ||
      plugin_keyword == 0 ||
      plugin_keyword_regex == 0 ||
      desc_func == 0) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"Opening internal plugin file \"" + 
		file + "\" failed.");
  }
}
#endif

static void eca_import_ladspa_plugins(ECA_OBJECT_MAP* objmap, bool reg_with_id)
{
  DIR *dp;

  vector<string> dir_names;
  char* env = std::getenv("LADSPA_PATH");
  if (env != 0) {
    dir_names = kvu_string_to_vector(string(env), ':');
  }

  /* add directories mentioned in 'ladspa-plugin-directory'
   * to the directory search list and remove duplicates */
  ECA_RESOURCES ecarc;
  string add_file = ecarc.resource("ladspa-plugin-directory");
  vector<string> more_dir_names = kvu_string_to_vector(add_file, ':');
  vector<string>::const_iterator di = more_dir_names.begin();
  while(di != more_dir_names.end()) {
    if (find(dir_names.begin(), dir_names.end(), *di) == dir_names.end()) {
      dir_names.push_back(*di);
    }
    ++di;
  }

  /* go through all directories in the list and 
   * try to open all encountered files as LADSPA plugins */
  struct stat statbuf;
  vector<string>::const_iterator p = dir_names.begin();
  while (p != dir_names.end()) {
    dp = opendir(p->c_str());
    if (dp != 0) {
      struct dirent *entry = readdir(dp);
      for(; entry != 0; entry = readdir(dp)) {
	string full_path_str =  string(*p + "/" + entry->d_name);

	int err = lstat(full_path_str.c_str(), &statbuf);
	 
	if (err) {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		      string("Invalid LADSPA plugin file \"") + 
		      entry->d_name + "\" (" +
		      strerror(errno) + ").");
	  continue;
	}

	if (S_ISDIR(statbuf.st_mode)) {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		      string("Skipping directory \"") + 
		      entry->d_name + "\" while loading LADSPA plugins.");
	  continue;
	}

	if (S_ISCHR(statbuf.st_mode) ||
	    S_ISBLK(statbuf.st_mode)) {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		      string("Skipping device \"") + 
		      entry->d_name + "\" while loading LADSPA plugins.");
	  continue;
	}

	if (S_ISFIFO(statbuf.st_mode) ||
	    S_ISSOCK(statbuf.st_mode)) {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		      string("Skipping pipe/socket \"") + 
		      entry->d_name + "\" while loading LADSPA plugins.");
	  continue;
	}

	vector<EFFECT_LADSPA*> ladspa_plugins;

	try {
	  if (entry->d_name[0] != '.')
	    ladspa_plugins = eca_create_ladspa_plugins(full_path_str.c_str());
	}
	catch(ECA_ERROR& e) {  }

	for(unsigned int n = 0; n < ladspa_plugins.size(); n++) {
	  if (reg_with_id == true) {
	    objmap->register_object(kvu_numtostr(ladspa_plugins[n]->unique_number()),
				    "^" + kvu_numtostr(ladspa_plugins[n]->unique_number()) +  "$", 
				    ladspa_plugins[n]);
	  }
	  else {
	    objmap->register_object(ladspa_plugins[n]->unique(), "^" + 
				    kvu_string_regex_meta_escape(ladspa_plugins[n]->unique()) + "$", 
				    ladspa_plugins[n]);
	  }
	}
      }
    }
    ++p;
  }
}

static vector<EFFECT_LADSPA*> eca_create_ladspa_plugins(const string& fname)
{
  vector<EFFECT_LADSPA*> plugins;

#ifndef ECA_DISABLE_EFFECTS
  void *plugin_handle = dlopen(fname.c_str(), RTLD_NOW);
  if (plugin_handle != 0) {
    LADSPA_Descriptor_Function desc_func;
    
    desc_func = (LADSPA_Descriptor_Function)dlsym(plugin_handle, "ladspa_descriptor");
    if (desc_func != 0) {
      const LADSPA_Descriptor *plugin_desc = 0;
      for (int i = 0;; i++) {
	plugin_desc = desc_func(i);
	if (plugin_desc == 0) break;
	try {
	  plugins.push_back(new EFFECT_LADSPA(plugin_desc));
	}
	catch (ECA_ERROR&) { }
	plugin_desc = 0;
      }
    }
    else { 
      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "Unable find plugin LADSPA-descriptor.");
    }
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::user_objects,
		string("Unable to open plugin file \"") + fname + "\".");
  }
#endif
  
  return plugins;
}
