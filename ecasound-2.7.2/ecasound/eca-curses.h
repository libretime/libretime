#ifndef INCLUDED_ECA_CURSES_H
#define INCLUDED_ECA_CURSES_H

#include <string>
#include "eca-console.h"

/**
 * Abstract interface for console
 * mode ecasound user interface 
 * implementations.
 *
 * @author Kai Vehmanen
 */
class ECA_CURSES : public ECA_CONSOLE {

 public:

  /**
   * Constructor. Initialized the GNU readline 
   * interface.
   */
  ECA_CURSES(void);

  /**
   * Virtual destructor.
   */
  virtual ~ECA_CURSES(void);

  /**
   * Prints the text string 'msg'.
   */
  virtual void print(const std::string& msg);

  /**
   * Prints the ecasound banner.
   */
  virtual void print_banner(void);

  /**
   * Reads the next user command.
   *
   * @see last_command()
   */
  virtual void read_command(const std::string& prompt);

  /**
   * Returns the last read user command.
   */
  virtual const std::string& last_command(void) const;

 private:

  void init_readline_support(void);

  char* last_cmdchar_repp;
  std::string last_cmd_rep;
  bool rl_initialized_rep;
};

#endif /* INCLUDED_ECA_CURSES_H */
