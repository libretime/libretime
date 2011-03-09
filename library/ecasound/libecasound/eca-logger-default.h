#ifndef INCLUDE_ECA_LOGGER_DEFAULT_H
#define INCLUDE_ECA_LOGGER_DEFAULT_H

#include <string>
#include <iostream>

#include "eca-logger-interface.h"

/**
 * Default logging subsystem implementation.
 *
 * Outputs traces to an ostream object (by default to
 * std::cerr).
 *
 * @author Kai Vehmanen
 */
class ECA_LOGGER_DEFAULT : public ECA_LOGGER_INTERFACE {
  
public:

  virtual void do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message);
  virtual void do_flush(void);
  virtual void do_log_level_changed(void);
  virtual ~ECA_LOGGER_DEFAULT(void);
  ECA_LOGGER_DEFAULT(std::ostream& output = std::cerr);

private:
  std::ostream& output_rep;
};

#endif /* INCLUDE_ECA_LOGGER_DEFAULT_H */
