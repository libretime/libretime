#ifndef INCLUDE_ECA_LOGGER_WELLFORMED_H
#define INCLUDE_ECA_LOGGER_WELLFORMED_H

#include <iostream>
#include <string>

#include "eca-logger-interface.h"

/**
 * Logging implementation that outputs 
 * messages in a well-formed format. The 
 * exact syntax is defined in TBD.
 *
 * @author Kai Vehmanen
 */
class ECA_LOGGER_WELLFORMED : public ECA_LOGGER_INTERFACE {
  
public:

  ECA_LOGGER_WELLFORMED(void);
  virtual ~ECA_LOGGER_WELLFORMED(void);

  virtual void do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message);
  virtual void do_flush(void);
  virtual void do_log_level_changed(void);

  static std::string create_wellformed_message(ECA_LOGGER::Msg_level_t  level, const std::string& message);
};

#endif /* INCLUDE_ECA_LOGGER_WELLFORMED_H */
