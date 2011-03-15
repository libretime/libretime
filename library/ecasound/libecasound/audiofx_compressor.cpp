// ------------------------------------------------------------------------
// audiofx_compressor.cpp: C++ implementation of John S. Dyson's 
//                         compressor code. If you want the original
//			   C-sources, mail me. 
// Copyright (C) 1999-2000 Kai Vehmanen
//
// Copyright for the actual algorithm (compressor2.c):
// ***************************************************
/*
 * Copyright (c) 1996, John S. Dyson
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * This code (easily) runs realtime on a P5-166 w/EDO, Triton-II on FreeBSD.
 *
 * More info/comments: dyson@freebsd.org
 *
 * This program provides compression of a stereo 16bit audio stream,
 * such as that contained by a 16Bit wav file.  Extreme measures have
 * been taken to make the compression as subtile as possible.  One
 * possible purpose for this code would be to master cassette tapes from
 * CD's for playback in automobiles where dynamic range needs to be
 * restricted.
 *
 * Suitably recoded for an embedded DSP, this would make a killer audio
 * compressor for broadcast or recording.  When writing this code, I
 * ignored the issues of roundoff error or trucation -- Pentiums have
 * really nice FP processors :-).
 */
// ------------------------------------------------------------------------

#include <cmath>
#include <vector>

#include <kvu_dbc.h>
#include <kvu_message_item.h>

#include "samplebuffer_iterators.h"

#include "audiofx_amplitude.h"

#include "eca-logger.h"

ADVANCED_COMPRESSOR::ADVANCED_COMPRESSOR (double peak_limit, double release_time, double cfrate,
					  double crate) 
  : rlevelsqn(ADVANCED_COMPRESSOR::NFILT),
    rlevelsqe(ADVANCED_COMPRESSOR::NEFILT)
{
  init_values();

  set_parameter(1, peak_limit);
  set_parameter(2, release_time);
  set_parameter(3, cfrate);
  set_parameter(4, crate);

  MESSAGE_ITEM otemp;
  otemp.setprecision(2);
  otemp << "(audiofx_compressor) Advanced compressor enabled;";
  otemp << " peak limit " << peakpercent;
  otemp << " release time " << release_time;
  otemp << " cfrate " << fastgaincompressionratio;
  otemp << " crate " << compressionratio << ".";
  ECA_LOG_MSG(ECA_LOGGER::info, otemp.to_string());
}

void ADVANCED_COMPRESSOR::init_values(void) {
  mingain = 10000;
  maxgain = 0;

  /* These filters should filter at least the lowest audio freq */
  rlevelsq0filter = .001;
  rlevelsq1filter = .010;
  /* These are the attack time for the rms measurement */
  rlevelsq0ffilter = .001;
  rlevelsqefilter = .001;

  /*
   * maximum gain for fast compressor
   */
  maxfastgain = 3;
  /*
   * maximum gain for slow compressor
   */
  maxslowgain = 9;

  /*
   * Level below which gain tracking shuts off
   */
  floorlevel = SAMPLE_SPECS::max_amplitude * 0.06;   // was 2000
  //  floorlevel = 2000;
  
  /*
   * Slow compressor time constants
   */
  rmastergain0filter = .000003;
  
  rpeakgainfilter = .001;
  rpeaklimitdelay = 2500;
  
  rgain = rmastergain0 = 1.0;
  rlevelsq0 = levelsq1 = 0;
  rlevelsq1 = 0;
  compress = 1;
  ndelay = (int)(1.0 / rlevelsq0ffilter);
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audiofx_compressor) Number of delays : " +
  //	       kvu_numtostr(ndelay) + ".");

  rightdelay.resize(ndelay);
  leftdelay.resize(ndelay);

  //  rlevelsqn = new vector<double> (NFILT + 1);
  //  rlevelsqe = new vector<double> (NEFILT + 1);

  rpeakgain0 = 1.0;
  rpeakgain1 = 1.0;
  rpeaklimitdelay = 0;
  ndelayptr = 0;
  lastrgain = 1.0;

  for(i = 0; i < NFILT;i++)
    rlevelsqn[i] = 0.0;
  for(i = 0; i < NEFILT;i++)
    rlevelsqe[i] = 0.0;

  /* set defaults to some sane values */
  peakpercent = 100.0f;
  releasetime = 0;
  fratio = 1.0;
  ratio = 1.0;
}

ADVANCED_COMPRESSOR::~ADVANCED_COMPRESSOR (void)
{
}

void ADVANCED_COMPRESSOR::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) {

  //  cerr << "Param: " << param << ", value: " << value << ".\n";

  switch (param) {
  case 1: 
    {
      // ---
      // target level for compression
      // ---

      maxlevel = SAMPLE_SPECS::max_amplitude * 0.9; // limiter level (was 32000)
      //      maxlevel = 32000;
      peakpercent = value;
      if (peakpercent == 0) peakpercent = 69;
      targetlevel = maxlevel * peakpercent / 100.0;
      break;
    }

  case 2:
    {
      // --
      // Linear gain filters as opposed to the level measurement filters
      // --
      DBC_CHECK(samples_per_second() != 0);
      releasetime = value;
      if (releasetime == 0) releasetime = 0.01;
      rgainfilter = 1.0 / (releasetime * samples_per_second());
      break;
    }

  case 3:
    {
      // --
      // compression ratio for fast gain.  This will determine how
      // much the audio is made more dense.  .5 is equiv to 2:1
      // compression.  1.0 is equiv to inf:1 compression.
      // --
      fratio = value;
      if (fratio == 0) fratio = 0.5;
      fastgaincompressionratio = fratio;
      break;      
    }
    
  case 4:
    {
      // --
      // overall ompression ratio.
      // --
      ratio = value;
      if (ratio == 0) ratio = 1.0;
      compressionratio = ratio;
      break;
    }
  }
}   

CHAIN_OPERATOR::parameter_t ADVANCED_COMPRESSOR::get_parameter(int param) const { 
  switch (param) 
    {
    case 1: 
      return(peakpercent);

    case 2:
      return(releasetime);
      
    case 3:
      return(fratio);
      
    case 4:
      return(ratio);
    }

  return(0.0);
}

double ADVANCED_COMPRESSOR::hardlimit(double value, double knee, double limit)
{
  //  double lrange = (limit - knee);
  double ab = fabs(value);
/*
	if (ab > knee) {
		double abslimit = (limit * 1.1);
		if (ab < abslimit) 
			value = knee + lrange * sin( ((value - knee)/abslimit) * (3.14 / (4*1.1)));
	}
*/
  if (ab >= limit)
    value = value > 0 ? limit : -limit;
  return value;
}

void ADVANCED_COMPRESSOR::init(SAMPLE_BUFFER* insample) {
  iter.init(insample);

  set_channels(insample->number_of_channels());
  set_samples_per_second(samples_per_second());
}

void ADVANCED_COMPRESSOR::process(void) {
  iter.begin();
  while(!iter.end()) {
    //  right = insample->get_right() * 32767.0;
    //  left = insample->get_left() * 32767.0;
      
    left = (*iter.current(0));
    right = (*iter.current(1));

    rightdelay[ndelayptr] = right;
    leftdelay[ndelayptr] = left;
    ndelayptr++;
      
    // cerr << "1.l:" << left << "\n";
    // cerr << "1.r:" << right << "\n";
    // cerr << "2.l:" << leftdelay[ndelayptr - 1] << "should be 1=2\n";

    if (ndelayptr >= ndelay)
      ndelayptr = 0;
    /* enable/disable compression */
    
    skipmode = 0;
    if (compress == 0) {
      skipmode = 1;
      goto skipagc;
    }
    levelsq0 = (right) * (right) + (left) * (left);

    //  if (ndelayptr == 0) {
    //    cerr << "3.1.l: " << "rlevelsq0 " << rlevelsq0 << "\n";
    //    cerr << "3.2.l: " << "rlevelsq0ffilter " << rlevelsq0ffilter << "\n";
    //    cerr << "3.2.l: " << "rlevelsq0filter " << rlevelsq0filter << "\n";
    //  }
    
    if (levelsq0 > rlevelsq0) {
      rlevelsq0 = (levelsq0 * rlevelsq0ffilter) +
	rlevelsq0 * (1 - rlevelsq0ffilter);
    } else {
      rlevelsq0 = (levelsq0 * rlevelsq0filter) +
	rlevelsq0 * (1 - rlevelsq0filter);
    }

    if (rlevelsq0 <= floorlevel * floorlevel)
      goto skipagc;

    //  if (ndelayptr == 0)
    //    cerr << "3.3.l: " << "rlevelsq0 " << rlevelsq0 << "\n";

    if (rlevelsq0 > rlevelsq1) {
      rlevelsq1 = rlevelsq0;
    } else {
      rlevelsq1 = rlevelsq0 * rlevelsq1filter +
	rlevelsq1 * (1 - rlevelsq1filter);
    }
    
    // vika.. rlevelsq1 joskus  menee pahasti yli ayrauden
    
    //  if (ndelayptr == 0)
    //    cerr << "3.3.l: " << "rlevelsq1 " << rlevelsq1 << "\n";

    rlevelsqn[0] = rlevelsq1;
    for(i = 0; i < NFILT-1; i++) {
      if (rlevelsqn[i] > rlevelsqn[i+1])
	rlevelsqn[i+1] = rlevelsqn[i];
      else
	rlevelsqn[i+1] = rlevelsqn[i] * rlevelsq1filter +
	  rlevelsqn[i+1] * (1 - rlevelsq1filter);
    }
	
    efilt = rlevelsqefilter;
    levelsqe = rlevelsqe[0] = rlevelsqn[NFILT-1];
    for(i = 0; i < NEFILT-1; i++) {
      //    if (rlevelsqe[i] > FLT_MAX) rlevelsqe[i] = FLT_MAX;
      //    else {
      rlevelsqe[i+1] = rlevelsqe[i] * efilt +
	rlevelsqe[i+1] * (1.0 - efilt);
      if (rlevelsqe[i+1] > levelsqe)
	levelsqe = rlevelsqe[i+1];
      efilt *= 1.0 / 1.5;
    }

    gain = targetlevel / sqrt(levelsqe);
    if (compressionratio < 0.99) {
      if (compressionratio == 0.50)
	gain = sqrt(gain);
      else
	gain = exp(log(gain) * compressionratio);
    }
    
    if (gain < rgain)
      rgain = gain * rlevelsqefilter/2 +
	rgain * (1 - rlevelsqefilter/2);
    else
      rgain = gain * rgainfilter +
	rgain * (1 - rgainfilter);

    lastrgain = rgain;
    if ( gain < lastrgain)
      lastrgain = gain;

  skipagc:;
    
    tgain = lastrgain;
  
    leftd = leftdelay[ndelayptr];
    rightd = rightdelay[ndelayptr];

    // cerr << "4.l:" << leftd << ", ndelayptr " << ndelayptr << "\n";

    fastgain = tgain;
    if (fastgain > maxfastgain)
      fastgain = maxfastgain;
    
    if (fastgain < 0.0001)
      fastgain = 0.0001;

    if (fastgaincompressionratio == 0.25) {
      qgain = sqrt(sqrt(fastgain));
    } else if (fastgaincompressionratio == 0.5) {
      qgain = sqrt(fastgain);
    } else if (fastgaincompressionratio == 1.0) {
      qgain = fastgain;
    } else {
      qgain = exp(log(fastgain) * fastgaincompressionratio);
    }

  // cerr << "4.4-qgain: " << qgain << "\n";

    tslowgain = tgain / qgain;
    if (tslowgain > maxslowgain)
      tslowgain = maxslowgain;
    if (tslowgain < rmastergain0)
      rmastergain0 = tslowgain;
    else
      rmastergain0 = tslowgain * rmastergain0filter +
	(1 - rmastergain0filter) * rmastergain0;

    slowgain = rmastergain0;
    if (skipmode == 0)
      npeakgain = slowgain * qgain;

/**/
    newright = rightd * npeakgain;
    if (fabs(newright) >= maxlevel)
      nrgain = maxlevel / fabs(newright);
    else
      nrgain = 1.0;

    newleft = leftd * npeakgain;
    if (fabs(newleft) >= maxlevel)
      nlgain = maxlevel / fabs(newleft);
    else
      nlgain = 1.0;

    // cerr << "4.5.l:" << newleft << "\n";
    
    ngain = nrgain;
    if (nlgain < ngain)
      ngain = nlgain;

    ngsq = ngain * ngain;
    if (ngsq <= rpeakgain0) {
      // --debug
      // if (ngsq < rpeakgain0) // cerr << "*";

      rpeakgain0 = ngsq /* * 0.50 + rpeakgain0 * 0.50 */;
      rpeaklimitdelay = peaklimitdelay;
    } else if (rpeaklimitdelay == 0) {
      if (nrgain > 1.0)
	tnrgain = 1.0;
      else
	tnrgain = nrgain;
      rpeakgain0 = tnrgain * rpeakgainfilter +
	(1.0 - rpeakgainfilter) * rpeakgain0;
    }
    
    if (rpeakgain0 <= rpeakgain1) {
      rpeakgain1 = rpeakgain0;
      rpeaklimitdelay = peaklimitdelay;
    } else if (rpeaklimitdelay == 0) {
      rpeakgain1 = rpeakgainfilter * rpeakgain0 +
	(1.0 - rpeakgainfilter) * rpeakgain1;
    } else {
      --rpeaklimitdelay;
    }

    sqrtrpeakgain = sqrt(rpeakgain1);
    totalgain = npeakgain * sqrtrpeakgain;
    
    right = newright * sqrtrpeakgain;
    
    *iter.current(1) = right;
    //insample->put_left(left / 32767.0);
    //  *righta = hardlimit(right, 32200, 32767);
    // cerr << "5.l:" << newleft << "\n";
    
    left = newleft * sqrtrpeakgain;
    *iter.current(0) = left;
    //  insample->put_left(left / 32767.0);
    
    // cerr << "6.l:" << left << "\n";
    //  
    // *lefta = hardlimit(left, 32200, 32767);
    
    //  if (right != *righta || left != *lefta) {
    //    fprintf(stderr,"!");
    //  }
    
    if (totalgain > maxgain)
      maxgain = totalgain;
    if (totalgain < mingain)
      mingain = totalgain;
    if (right > extra_maxlevel)
      extra_maxlevel = right;
    if (left > extra_maxlevel)
      extra_maxlevel = left;
    
    iter.next();
  }
  //  cerr << "post:" << insample->get_left() << "\n";
}
