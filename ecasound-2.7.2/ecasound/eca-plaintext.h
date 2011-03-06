#ifndef INCLUDED_ECA_PLAINTEXT_H
#define INCLUDED_ECA_PLAINTEXT_H

#include <iostream>

#include <string>
#include "eca-console.h"

/**
 * Plain text interface for the console
 * mode ecasound.
 *
 * @author Kai Vehmanen
 */
class ECA_PLAIN_TEXT : public ECA_CONSOLE {

 public:

  /**
   * Constructor.
   */
  ECA_PLAIN_TEXT(std::ostream* ostr);

  /**
   * Virtual destructor.
   */
  virtual ~ECA_PLAIN_TEXT(void);

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

  std::string last_cmd_rep;
  std::ostream* ostream_repp;
};

#endif /* INCLUDED_ECA_PLAIN_TEXT_H */
