#ifndef INCLUDED_ECA_CONSOLE_H
#define INCLUDED_ECA_CONSOLE_H

#include <string>

/**
 * Abstract interface for console
 * mode ecasound user interface 
 * implementations.
 *
 * @author Kai Vehmanen
 */
class ECA_CONSOLE {

 public:

  /**
   * Prints the text string 'msg'.
   */
  virtual void print(const std::string& msg) = 0;

  /**
   * Prints the ecasound banner.
   */
  virtual void print_banner(void) = 0;

  /**
   * Reads the next user command.
   *
   * @param prompt prompt shown to user
   *
   * @see last_command()
   */
  virtual void read_command(const std::string& prompt) = 0;

  /**
   * Returns the last read user command.
   */
  virtual const std::string& last_command(void) const = 0;

  virtual ~ECA_CONSOLE(void) {};
};

#endif /* INCLUDED_ECA_CONSOLE_H */
