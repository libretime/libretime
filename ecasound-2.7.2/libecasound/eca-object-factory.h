#ifndef INCLUDED_ECA_OBJECT_FACTORY_H
#define INCLUDED_ECA_OBJECT_FACTORY_H

#include <map>
#include <list>
#include <string>
#include <pthread.h>

/**
 * Forward declarations
 */

class AUDIO_IO;
class CHAIN_OPERATOR;
class ECA_AUDIO_FORMAT;
class ECA_OBJECT;
class ECA_OBJECT_MAP;
class ECA_PRESET_MAP;
class EFFECT_LADSPA;
class GENERIC_CONTROLLER;
class OPERATOR;
class LOOP_DEVICE;
class MIDI_IO;
class PRESET;

/**
 * Abstract factory for creating libecasound objects.
 * Implemented as a static singleton class.
 *
 * Related design patterns:
 *     - Abstract Factory (GoF87)
 *     - Singleton (GoF127)
 *
 * @author Kai Vehmanen
 */
class ECA_OBJECT_FACTORY {

 public:

  /** 
   * @name Functions for accessing object map instances 
   *
   * Note! Return value is a reference to avoid 
   *       accidental deletion of the singleton objects.
   **/
  /*@{*/

  static ECA_OBJECT_MAP& audio_io_rt_map(void);
  static ECA_OBJECT_MAP& audio_io_nonrt_map(void);
  static ECA_OBJECT_MAP& chain_operator_map(void);
  static ECA_OBJECT_MAP& ladspa_plugin_map(void);
  static ECA_OBJECT_MAP& ladspa_plugin_id_map(void);
  static ECA_PRESET_MAP& preset_map(void);
  static ECA_OBJECT_MAP& controller_map(void);
  static ECA_OBJECT_MAP& midi_device_map(void);

  /*@}*/

  /** @name Functions for creating objects based on EOS (Ecasound Option Syntax) strings. */
  /*@{*/

  static AUDIO_IO* create_audio_object(const std::string& arg);
  static MIDI_IO* create_midi_device(const std::string& arg);
  static AUDIO_IO* create_loop_output(const std::string& argu, std::map<std::string,LOOP_DEVICE*>* loop_map);
  static AUDIO_IO* create_loop_input(const std::string& argu, std::map<std::string,LOOP_DEVICE*>* loop_map);
  static CHAIN_OPERATOR* create_chain_operator (const std::string& arg);
  static CHAIN_OPERATOR* create_ladspa_plugin (const std::string& arg);
  static GENERIC_CONTROLLER* create_controller (const std::string& arg);

  /*@}*/

  /** @name Functions for creating EOS strings */
  /*@{*/

  static std::string probe_default_output_device(void);

  /*@}*/

  /** @name Functions for describing existing objects with EOS strings */
  /*@{*/

  static std::string chain_operator_to_eos(const CHAIN_OPERATOR* chainop);
  static std::string controller_to_eos(const GENERIC_CONTROLLER* gctrl);
  static std::string operator_parameters_to_eos(const OPERATOR* chainop);
  static std::string audio_format_to_eos(const ECA_AUDIO_FORMAT* aformat);
  static std::string audio_object_to_eos(const AUDIO_IO* aiod, const std::string& direction);
  static std::string audio_object_format_to_eos(const AUDIO_IO* aiod);

  /*@}*/

  private:

  static ECA_OBJECT_MAP* audio_io_rt_map_repp;
  static ECA_OBJECT_MAP* audio_io_nonrt_map_repp;
  static ECA_OBJECT_MAP* chain_operator_map_repp;
  static ECA_OBJECT_MAP* ladspa_plugin_map_repp;
  static ECA_OBJECT_MAP* ladspa_plugin_id_map_repp;
  static ECA_PRESET_MAP* preset_map_repp;
  static ECA_OBJECT_MAP* controller_map_repp;
  static ECA_OBJECT_MAP* midi_device_map_repp;

  static pthread_mutex_t lock_rep;

  /** 
   * @name Constructors and destructors
   * 
   * To prevent accidental use, located in private scope and 
   * without a valid definition.
   */
  /*@{*/

  ECA_OBJECT_FACTORY(void);
  ECA_OBJECT_FACTORY(const ECA_OBJECT_FACTORY&);
  ECA_OBJECT_FACTORY& operator=(const ECA_OBJECT_FACTORY&);
  ~ECA_OBJECT_FACTORY(void);

  /*@}*/
};

#endif /* INCLUDED_ECA_OBJECT_FACTORY_H */
