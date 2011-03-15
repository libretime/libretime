// ------------------------------------------------------------------------
// audioio-resample.cpp: A proxy class that resamples the child 
//                       object's data.
// Copyright (C) 2002-2004,2008,2009,2010 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
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

#include <cstdlib> /* atoi() */
#include <cmath>   /* ceil(), floor() */
#include <iostream>

#include <math.h>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "audioio-resample.h"
#include "eca-logger.h"
#include "eca-object-factory.h"
#include "samplebuffer.h"

// #define RESAMPLE_VERBOSE_DEBUG 1

/**
 * Constructor.
 */
AUDIO_IO_RESAMPLE::AUDIO_IO_RESAMPLE (void) 
  :  psfactor_rep(1.0f),
     sbuf_rep(buffersize(), 0)
{
  init_rep = false;
  quality_rep = 50;
}

/**
 * Destructor.
 */
AUDIO_IO_RESAMPLE::~AUDIO_IO_RESAMPLE (void)
{
}

AUDIO_IO_RESAMPLE* AUDIO_IO_RESAMPLE::clone(void) const
{
  AUDIO_IO_RESAMPLE* target = new AUDIO_IO_RESAMPLE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void AUDIO_IO_RESAMPLE::recalculate_psfactor(void)
{
  DBC_REQUIRE(child_srate_conf_rep > 0);
  DBC_REQUIRE(io_mode() == AUDIO_IO::io_read);
   
  psfactor_rep = static_cast<float>(samples_per_second()) / child_srate_conf_rep;
  child()->set_buffersize(static_cast<long int>(std::floor(buffersize() * (1.0f / psfactor_rep))));

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "recalc; psfactor=" + kvu_numtostr(psfactor_rep));
}

void AUDIO_IO_RESAMPLE::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "open " + 
	      child_params_as_string(1 + AUDIO_IO_RESAMPLE::child_parameter_offset, &params_rep) + ".");  

  if (init_rep != true) {
    AUDIO_IO* tmp = 0;

    const string& objname = 
      child_params_as_string(1 + AUDIO_IO_RESAMPLE::child_parameter_offset, &params_rep);

    if (objname.size() > 0)
      tmp = ECA_OBJECT_FACTORY::create_audio_object(objname);

    /* FIXME: add check for real-time devices, resample does _not_
     *        work with them (rt API not proxied properly)
     */
    
    if (tmp == 0)
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-RESAMPLE: unable to open child object '" + objname + "'"));
    
    set_child(tmp);

    int numparams = child()->number_of_params();
    for(int n = 0; n < numparams; n++) {
      child()->set_parameter(n + 1, get_parameter(n + 1 + AUDIO_IO_RESAMPLE::child_parameter_offset));
      if (child()->variable_params())
	numparams = child()->number_of_params();
    }

    init_rep = true; /* must be set after dyn. parameters */
  }

  if (child_srate_conf_rep == 0) {
    /* query the sampling rate from child object */
    child()->set_io_mode(io_mode());
    child()->set_audio_format(audio_format());
    child()->open();
    child_srate_conf_rep = child()->samples_per_second();
    child()->close();
  }


  if (io_mode() != AUDIO_IO::io_read) 
    throw(SETUP_ERROR(SETUP_ERROR::io_mode,
		      "AUDIOIO-RESAMPLE: 'io_write' and 'io_readwrite' modes are not supported."));

  recalculate_psfactor();

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "pre-open(); psfactor=" + kvu_numtostr(psfactor_rep) +
	      ", child_srate=" + kvu_numtostr(child_srate_conf_rep) +
	      ", srate=" + kvu_numtostr(samples_per_second()) +
	      ", bsize=" + kvu_numtostr(buffersize()) +
	      ", c-bsize=" + kvu_numtostr(child()->buffersize()) + 
	      ", child=" + child()->label() + ".");

  /* note, we don't use pre_child_open() as 
   * we want to set srate differently */
  child()->set_io_mode(io_mode());
  child()->set_audio_format(audio_format());
  child()->set_samples_per_second(child_srate_conf_rep);

  child()->open();

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "post-open(); child=" + child()->label() + ".");

  /* same for the post processing */ 
  SAMPLE_SPECS::sample_rate_t orig_srate = samples_per_second();
  if (child()->locked_audio_format() == true) {
    set_channels(child()->channels());
    set_sample_format(child()->sample_format());
    set_samples_per_second(orig_srate);
    toggle_interleaved_channels(child()->interleaved_channels());
  }

  sbuf_rep.length_in_samples(buffersize());
  sbuf_rep.number_of_channels(channels());
  sbuf_rep.resample_init_memory(child_srate_conf_rep, samples_per_second());
  sbuf_rep.resample_set_quality(quality_rep);
    
  set_label("resample:" + child()->label());

  set_length_in_samples(static_cast<long int>(std::ceil(child()->length_in_samples() * psfactor_rep)));
  //set_length_in_seconds(child()->length_in_seconds_exact());

  AUDIO_IO_PROXY::open();
}

void AUDIO_IO_RESAMPLE::close(void)
{
  if (child()->is_open() == true) 
    child()->close();

  init_rep = false;

  AUDIO_IO_PROXY::close();
}

void AUDIO_IO_RESAMPLE::set_buffersize(long int samples)
{
  if (samples != buffersize()) {
    long old_bsize = buffersize();
    AUDIO_IO_PROXY::set_buffersize(samples);
    child()->set_buffersize(static_cast<long int>(std::floor(samples * (1.0f / psfactor_rep))));
    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"setting bsize from " + 
		kvu_numtostr(old_bsize) +
		" to " +
		kvu_numtostr(child()->buffersize()));
  }
}

long int AUDIO_IO_RESAMPLE::buffersize(void) const
{
  return AUDIO_IO_PROXY::buffersize();
}

string AUDIO_IO_RESAMPLE::parameter_names(void) const
{
  return string("resample,srate,") + child()->parameter_names();
}

void AUDIO_IO_RESAMPLE::set_parameter(int param, string value)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      AUDIO_IO::parameter_set_to_string(param, value));

  /* total of n+1 params, where n is number of childobj params */
  if (param > static_cast<int>(params_rep.size())) params_rep.resize(param);

  if (param > 0) {
    params_rep[param - 1] = value;

    if (param == 1) {
      if (value == "resample-hq") {
	quality_rep = 100;
	ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		    "using high-quality resampler");
      }
      else if (value == "resample-lq") {
	quality_rep = 5;
	ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		    "using low-quality resampler");
      }
      else {
	quality_rep = 50;
	ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		    "using default resampler");
      }
    }
    else if (param == 2) {
      if (value == "auto") {
	if (init_rep != true) 
	  child_srate_conf_rep = 0;
	ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		  "resampling with automatic detection of child srate");
      }
      else {
	child_srate_conf_rep = std::atoi(value.c_str());
	ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		  "resampling w/ child srate of " + 
		  kvu_numtostr(child_srate_conf_rep));
      }
    }
  }
  
  sbuf_rep.resample_set_quality(quality_rep);

  if (param > AUDIO_IO_RESAMPLE::child_parameter_offset 
      && init_rep == true) {
    child()->set_parameter(param - AUDIO_IO_RESAMPLE::child_parameter_offset, value);
  }
}

string AUDIO_IO_RESAMPLE::get_parameter(int param) const
{
  if (param > 0 && param < static_cast<int>(params_rep.size()) + 1) {
    if (param > AUDIO_IO_RESAMPLE::child_parameter_offset 
	&& init_rep == true) {
      params_rep[param - 1] = 
	child()->get_parameter(param - AUDIO_IO_RESAMPLE::child_parameter_offset);
    }
    return params_rep[param - 1];
  }

  return "";
}

SAMPLE_SPECS::sample_pos_t AUDIO_IO_RESAMPLE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  SAMPLE_SPECS::sample_pos_t pub_pos, child_pos =
    static_cast<long int>(std::floor(pos * (1.0f / psfactor_rep)));

  child()->seek_position_in_samples(child_pos);
  child_pos = 
    child()->position_in_samples();

  pub_pos = static_cast<long int>(std::floor(child_pos * (psfactor_rep)));

  return pub_pos;
}

void AUDIO_IO_RESAMPLE::set_audio_format(const ECA_AUDIO_FORMAT& f_str)
{
  AUDIO_IO::set_audio_format(f_str);
  child()->set_audio_format(f_str);
  
  /* set_audio_format() also sets the sample rate so we need to 
     reset the value back to the correct one */
  child()->set_samples_per_second(child_srate_conf_rep);
}

void AUDIO_IO_RESAMPLE::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  /* the child srate is set in open */
  
  if (child()->is_open() == true)
    recalculate_psfactor();

  AUDIO_IO::set_samples_per_second(v);
}

void AUDIO_IO_RESAMPLE::read_buffer(SAMPLE_BUFFER* dst_sbuf)
{
  long int dst_left = buffersize();
  SAMPLE_BUFFER::buf_size_t dst_write_pos = 0;

  dst_sbuf->number_of_channels(channels());
  dst_sbuf->length_in_samples(dst_left);

#ifdef RESAMPLE_VERBOSE_DEBUG
  std::fprintf(stderr, "--- (%ld samples)\n", dst_left);
#endif
    
  /* step: copy any leftover resampled audio from 
   *       last iteration */
  if (dst_left > 0 &&
      leftoverbuf_rep.length_in_samples() > 0) {
    DBC_CHECK(leftoverbuf_rep.length_in_samples() <= dst_left);

#ifdef RESAMPLE_VERBOSE_DEBUG
    std::fprintf(stderr, "leftover copy_range 0..%ld -> %ld..%ld, copied %ld, dst_left=%ld\n",
		 leftoverbuf_rep.length_in_samples() - 1, 
		 dst_write_pos, dst_write_pos + leftoverbuf_rep.length_in_samples() - 1, 
		 leftoverbuf_rep.length_in_samples(), dst_left);
#endif
    dst_sbuf->copy_range(leftoverbuf_rep, 
			 0,
			 leftoverbuf_rep.length_in_samples(),
			 dst_write_pos);
    dst_sbuf->event_tags_add(leftoverbuf_rep);

    dst_left -= leftoverbuf_rep.length_in_samples();
    dst_write_pos += leftoverbuf_rep.length_in_samples();;

#ifdef RESAMPLE_VERBOSE_DEBUG
    std::fprintf(stderr, 
		 "copied %ld leftover samples, %ld remain, dst_write_pos=%ld\n", 
		 leftoverbuf_rep.length_in_samples(), dst_left, dst_write_pos);
#endif

    leftoverbuf_rep.length_in_samples(0);
  }
  DBC_CHECK(leftoverbuf_rep.length_in_samples() == 0);

  /* note: loop until we have buffersize() worth of samples,
   *       or until we encounter end-of-stream */
  for(int i = 0; dst_left > 0; i++) {
    long int src_to_copy = dst_left;

    /* step: read sample buffer,  src-rate */
    child()->read_buffer(&sbuf_rep);
#ifdef RESAMPLE_VERBOSE_DEBUG
    std::fprintf(stderr, "%d: asked for %ld samples, got %ld\n", i, child()->buffersize(), sbuf_rep.length_in_samples());
#endif

    /* step: resample dst-rate */
    sbuf_rep.resample(child_srate_conf_rep, samples_per_second());

#ifdef RESAMPLE_VERBOSE_DEBUG
    std::fprintf(stderr, "after resample, %ld samples\n", 
		 sbuf_rep.length_in_samples());
#endif

    /* step: if we didn't get enough samples, adjust src_to_copy */
    if (sbuf_rep.length_in_samples() < src_to_copy)
      src_to_copy = sbuf_rep.length_in_samples();

    /* step: copy src_to_copy resampled samples */
#ifdef RESAMPLE_VERBOSE_DEBUG
    std::fprintf(stderr, "copy_range 0..%ld -> %ld..%ld, copied %ld,  dst_left=%ld\n",
		 src_to_copy - 1, dst_write_pos, dst_write_pos + src_to_copy - 1, 
		 src_to_copy, dst_left);
#endif
    dst_sbuf->copy_range(sbuf_rep,
			 0,
			 src_to_copy,
			 dst_write_pos);
    dst_sbuf->event_tags_add(sbuf_rep);
    
    dst_write_pos += src_to_copy;
    dst_left -= src_to_copy;

    if (dst_left > 0 &&
	sbuf_rep.event_tag_test(SAMPLE_BUFFER::tag_end_of_stream)) {
      dst_sbuf->event_tag_set(SAMPLE_BUFFER::tag_end_of_stream);
      dst_sbuf->length_in_samples(dst_sbuf->length_in_samples() - dst_left);
      break;
    }

    /* step: if there are any new leftovers, store them for 
     *       the next iteration */
    if (sbuf_rep.length_in_samples() > src_to_copy) {
      DBC_CHECK(dst_left <= 0);
      SAMPLE_BUFFER::buf_size_t leftover = 
	sbuf_rep.length_in_samples() - src_to_copy;
      leftoverbuf_rep.length_in_samples(leftover);
      leftoverbuf_rep.number_of_channels(channels());

#ifdef RESAMPLE_VERBOSE_DEBUG
      std::fprintf(stderr, "copy_range leftovers %ld..%ld -> %ld..%ld, copied %ld, dst_left=%ld\n",
		   src_to_copy, sbuf_rep.length_in_samples() - 1, 
		   0, leftover - 1, 
		   leftover, dst_left);
#endif
      leftoverbuf_rep.copy_range(sbuf_rep, 
				 src_to_copy,
				 sbuf_rep.length_in_samples(),
				 0);
      leftoverbuf_rep.event_tags_set(sbuf_rep);
    }
  }

  change_position_in_samples(dst_sbuf->length_in_samples());

#ifdef RESAMPLE_VERBOSE_DEBUG
  std::fprintf(stderr, "exit with %ld samples\n", dst_sbuf->length_in_samples());
#endif

  DBC_ENSURE(dst_sbuf->length_in_samples() <= buffersize());
  DBC_ENSURE(dst_sbuf->number_of_channels() == channels());
}

void AUDIO_IO_RESAMPLE::write_buffer(SAMPLE_BUFFER* sbuf)
{
  /* FIXME: not implemented */
  DBC_NEVER_REACHED();
  change_position_in_samples(sbuf->length_in_samples());
}
