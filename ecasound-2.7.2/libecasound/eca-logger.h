#ifndef INCLUDE_ECA_LOGGER_H
#define INCLUDE_ECA_LOGGER_H

#include <string>
#include <pthread.h>

/**
 * Forward declarations
 */

class ECA_LOGGER_INTERFACE;

/**
 * A logging subsystem implemented as a singleton
 * class.
 *
 * Related design patterns:
 *     - Singleton (GoF127)
 *
 * @author Kai Vehmanen
 */
class ECA_LOGGER {
  
  public:

  /**
   * Log level is a bitmasked integer value that is used to
   * categorize different log message types.
   *
   *   disabled       = no output
   *
   *   errors         = error messages
   *
   *   info           = high-level info about user-visible objects 
   *                    and concepts, warning messages; low volume
   * 
   *   subsystems     = notifications of control flow transitions 
   *                    between high-level subsystems; low volume
   *
   *   module_names   = include module names in log output
   *
   *   user_objects   = info about user-visible objects (audio i/o, 
   *                    chain operators, controllers); high volume
   * 
   *   system_objects = info about internal objects; high volume
   *
   *   functions      = info about internal operation of individual 
   *                    functions and algorithms; high volume bursts
   *
   *   continuous     = debug info printed for during processing;
   *                    continuous high volume
   *
   *   eiam_return_values = return values for EIAM commands
   *
   * @see level_to_string()
   */
  typedef enum {
    disabled = 0,
    errors = 1,
    info = 2,
    subsystems = 4,
    module_names = 8,
    user_objects = 16,
    system_objects = 32,
    functions = 64,
    continuous = 128,
    eiam_return_values = 256
  } Msg_level_t;

  /**
   * Returns a reference to a logging system 
   * implementation object.
   *
   * Note! Return value is a reference to 
   *       avoid accidental deletion of 
   *       the singleton object.
   */
  static ECA_LOGGER_INTERFACE& instance(void);

  /**
   * Replace the default logging sybsystem 
   * with a custom implementation.
   *
   * Note! Ownership of 'logger' is transferred 
   *       to the singleton object.
   */
  static void attach_logger(ECA_LOGGER_INTERFACE* logger);

  /**
   * Detaches the current logger implementation.
   */
  static void detach_logger(void);

  /**
   * Returns description of log level 'arg'.
   */
  static const char* level_to_string(Msg_level_t arg);

  private:

  static ECA_LOGGER_INTERFACE* interface_impl_repp;
  static pthread_mutex_t lock_rep;

  /** 
   * @name Constructors and destructors
   * 
   * To prevent accidental use, located in private scope and 
   * without a valid definition.
   */
  /*@{*/

  ECA_LOGGER(void);
  ECA_LOGGER(const ECA_LOGGER&);
  ECA_LOGGER& operator=(const ECA_LOGGER&);
  ~ECA_LOGGER(void);

  /*@}*/
};

/**
 * Macro definitions
 */

/**
 * Issues a log message.
 *
 * @param x log level, type 'ECA_LOGGER::Msg_level_t'
 * @param y log message, type 'const std:string&'
 */
#define ECA_LOG_MSG(x,y) \
        do { ECA_LOGGER::instance().msg(x, __FILE__, y); } while(0)

/**
 * Issue a log message, but do not print out the module prefix.
 * A variant of ECA_LOG_MSG().
 */
#define ECA_LOG_MSG_NOPREFIX(x,y) \
        do { ECA_LOGGER::instance().msg(x, std::string(), y); } while(0)

/**
 * To make ECA_LOG_MSG work we need to include the 
 * public interface ECA_LOGGER_INTERFACE.
 */

#include "eca-logger-interface.h"

#endif /* INCLUDE_ECA_LOGGER_H */
