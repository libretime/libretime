#ifndef _AUDIOFX_MIXING_H
#define _AUDIOFX_MIXING_H

#include <vector>

#include "samplebuffer_iterators.h"
#include "audiofx.h"
#include "audiofx_amplitude.h"

/**
 * Virtual base class for channel mixing and routing effects
 * @author Kai Vehmanen
 */
class EFFECT_MIXING : public EFFECT_BASE {
 public:
  typedef std::vector<parameter_t>::size_type ch_type;

  virtual ~EFFECT_MIXING(void);
};

/**
 * Channel copy (one-to-one copy)
 * @author Kai Vehmanen
 */
class EFFECT_CHANNEL_COPY : public EFFECT_MIXING {

private:

  ch_type from_channel, to_channel;
  SAMPLE_ITERATOR_CHANNEL f_iter, t_iter;

public:

  virtual std::string name(void) const { return("Channel copy"); }
  virtual std::string parameter_names(void) const { return("from-channel,to-channel"); }

  int output_channels(int i_channels) const;

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_CHANNEL_COPY* clone(void) const { return new EFFECT_CHANNEL_COPY(*this); }
  EFFECT_CHANNEL_COPY* new_expr(void) const { return new EFFECT_CHANNEL_COPY(); }
  EFFECT_CHANNEL_COPY (parameter_t from_channel = 1.0, parameter_t to_channel = 1.0);
};

/**
 * Channel move (copy one channel and mute the source)
 * @author Kai Vehmanen
 */
class EFFECT_CHANNEL_MOVE : public EFFECT_MIXING {

private:

  ch_type from_channel, to_channel;
  SAMPLE_ITERATOR_CHANNEL f_iter, t_iter;

public:

  virtual std::string name(void) const { return("Channel move"); }
  virtual std::string parameter_names(void) const { return("from-channel,to-channel"); }

  int output_channels(int i_channels) const;

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  void init(SAMPLE_BUFFER *insample);
  void process(void);

  EFFECT_CHANNEL_MOVE* clone(void) const { return new EFFECT_CHANNEL_MOVE(*this); }
  EFFECT_CHANNEL_MOVE* new_expr(void) const { return new EFFECT_CHANNEL_MOVE(); }
  EFFECT_CHANNEL_MOVE (parameter_t from_channel = 1.0, parameter_t to_channel = 1.0);
};

/**
 * Channel mute (mutes one channel)
 * @author Kai Vehmanen
 */
class EFFECT_CHANNEL_MUTE : public EFFECT_AMPLIFY_CHANNEL {

public:

  virtual std::string name(void) const { return("Channel mute"); }
  virtual std::string parameter_names(void) const { return("channel"); }

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  EFFECT_CHANNEL_MUTE* clone(void) const { return new EFFECT_CHANNEL_MUTE(*this); }
  EFFECT_CHANNEL_MUTE* new_expr(void) const { return new EFFECT_CHANNEL_MUTE(); }
  EFFECT_CHANNEL_MUTE (parameter_t channel = 1.0);
};

/**
 * Channel copy (one-to-one copy)
 * @author Kai Vehmanen
 */
class EFFECT_MIX_TO_CHANNEL : public EFFECT_MIXING {

private:

  typedef std::vector<parameter_t>::size_type ch_type;

  int channels;
  ch_type to_channel;
  parameter_t sum;

  SAMPLE_ITERATOR_CHANNEL t_iter;
  SAMPLE_ITERATOR_INTERLEAVED i;

public:

  virtual std::string name(void) const { return("Mix to channel"); }
  virtual std::string parameter_names(void) const { return("to-channel"); }

  int output_channels(int i_channels) const;

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  void init(SAMPLE_BUFFER *insample);
  void process(void);

  EFFECT_MIX_TO_CHANNEL* clone(void) const { return new EFFECT_MIX_TO_CHANNEL(*this); }
  EFFECT_MIX_TO_CHANNEL* new_expr(void) const { return new EFFECT_MIX_TO_CHANNEL(); }
  EFFECT_MIX_TO_CHANNEL (parameter_t to_channel = 1.0);
};

/**
 * Arbitrary channel routing
 * @author Kai Vehmanen
 */
class EFFECT_CHANNEL_ORDER : public EFFECT_MIXING {

private:

  SAMPLE_ITERATOR_CHANNEL f_iter, t_iter;
  SAMPLE_BUFFER *sbuf_repp;
  SAMPLE_BUFFER bouncebuf_rep;
  std::string param_names_rep;
  std::vector<int> chsrc_map_rep;
  int out_channels_rep;

public:

  virtual std::string name(void) const { return("Channel select"); }
  virtual std::string parameter_names(void) const;

  int output_channels(int i_channels) const;

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;
  virtual bool variable_params(void) const { return true; }
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void release(void);
  virtual void process(void);

  EFFECT_CHANNEL_ORDER* clone(void) const;
  EFFECT_CHANNEL_ORDER* new_expr(void) const { return new EFFECT_CHANNEL_ORDER(); }
  EFFECT_CHANNEL_ORDER(void);
};

#endif
