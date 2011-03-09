#ifndef INCLUDED_MIDI_PARSER_H
#define INCLUDED_MIDI_PARSER_H

/**
 * Collection of static functions and small stateful 
 * machines for parsing MIDI messages.
 */
class MIDI_PARSER {
  
 public:

  static bool is_voice_category_status_byte(unsigned char byte);
  static bool is_system_common_category_status_byte(unsigned char byte);
  static bool is_realtime_category_status_byte(unsigned char byte);  
  static bool is_status_byte(unsigned char byte);

};

#endif



