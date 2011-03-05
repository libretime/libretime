#ifndef INCLUDED_ECA_STATIC_OBJECT_MAPS_H
#define INCLUDED_ECA_STATIC_OBJECT_MAPS_H

class ECA_OBJECT_FACTORY;
class ECA_OBJECT_MAP;
class ECA_PRESET_MAP;

/**
 * A private classed used by ECA_OBJECT_FACTORY
 * to access object maps.
 *
 * @author Kai Vehmanen
 */
class ECA_STATIC_OBJECT_MAPS {

 public:

  friend class ECA_OBJECT_FACTORY;

 private:

  static void register_audio_io_rt_objects(ECA_OBJECT_MAP* objmap);
  static void register_audio_io_nonrt_objects(ECA_OBJECT_MAP* objmap);
  static void register_chain_operator_objects(ECA_OBJECT_MAP* objmap);
  static void register_ladspa_plugin_objects(ECA_OBJECT_MAP* objmap);
  static void register_ladspa_plugin_id_objects(ECA_OBJECT_MAP* objmap);
  static void register_preset_objects(ECA_PRESET_MAP* objmap);
  static void register_controller_objects(ECA_OBJECT_MAP* objmap);
  static void register_midi_device_objects(ECA_OBJECT_MAP* objmap);

  /** 
   * @name Constructors and destructors
   * 
   * To prevent accidental use, located in private scope and 
   * without a valid definition.
   */
  /*@{*/

  ECA_STATIC_OBJECT_MAPS(void);
  ECA_STATIC_OBJECT_MAPS(const ECA_STATIC_OBJECT_MAPS&);
  ECA_STATIC_OBJECT_MAPS& operator=(const ECA_STATIC_OBJECT_MAPS&);
  ~ECA_STATIC_OBJECT_MAPS(void);

  /*@}*/
};

#endif
