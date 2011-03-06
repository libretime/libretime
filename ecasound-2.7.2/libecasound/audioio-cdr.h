#ifndef INCLUDED_AUDIOIO_CDR_H
#define INCLUDED_AUDIOIO_CDR_H

#include <string>
#include <cstdio>
#include <iostream>

#include <sys/types.h> /* off_t */

#include "sample-specs.h"
#include "audioio-buffered.h"

typedef struct {
  public:
  int16_t sample[2]; // signed short int
} SAMPLE;

/**
 * Class for handling CDR-files
 * 
 * CDR -format is used on audio-CDs:
 *
 * - 16bit samples, 44100kHz, stereo
 *
 * - sample frame layout: (MSB-left, LSB-left, MSB-right, LSB-right)
 *
 * - files are padded sector size (2352 bytes)
 *
 * @author Kai Vehmanen
 */
class CDRFILE : public AUDIO_IO_BUFFERED {

  static const int sectorsize = 2352;

  long int samples_read;

  FILE* fobject;
  void pad_to_sectorsize(void);
  void set_length_in_bytes(void);

  CDRFILE(const CDRFILE& x) { }
  CDRFILE& operator=(const CDRFILE& x) { return *this; }

  void seek_position_in_samples(long pos);
  unsigned short swapw(unsigned short us) { return ((us >> 8) | (us << 8)) & 0xffff;  }

  void swap_bytes(SAMPLE* t) {
    t->sample[SAMPLE_SPECS::ch_left] = swapw(t->sample[SAMPLE_SPECS::ch_left]);
    t->sample[SAMPLE_SPECS::ch_right] = swapw(t->sample[SAMPLE_SPECS::ch_right]);
  }
  
 public:

  std::string name(void) const { return("CD-R audio file"); }
  std::string description(void) const { return("CD-R/CDDA audio files. This format is used when mastering audio-CDs."); }

  virtual bool locked_audio_format(void) const { return(true); }

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);

  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual bool finished(void) const;
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);
    
  CDRFILE (const std::string& name = "");
  virtual ~CDRFILE(void);
    
  CDRFILE* clone(void) const;
  CDRFILE* new_expr(void) const { return new CDRFILE(); }
};

#endif
