#ifndef INCLUDED_ECA_ERROR_H
#define INCLUDED_ECA_ERROR_H

#include <string>

using std::string;

/**
 * A general exception class for error reporting.
 */
class ECA_ERROR {

public:
  
  // ------------------------------
  // error type / suggested action:
  // ------------------------------
  // stop = a critical error (can't allocate memory, etc), program 
  //        should exit at once
  // retry = action failed (badly formatted data, invalid user input,
  //         etc), no need to stop the whole program
  // notice = action succeeded but something unusual occured

  enum Action { stop, retry, notice };
 
private:
  std::string esection_rep;
  std::string eerrormsg_rep;
  Action eaction_rep;

public:

  const std::string& error_section(void) const { return(esection_rep); }
  const std::string& error_message(void) const { return(eerrormsg_rep); }
  const Action& error_action(void) const { return(eaction_rep); }
  
  ECA_ERROR(const std::string& section, 
	    const std::string& errormsg, 
	    const Action action = ECA_ERROR::retry) {
    esection_rep = section;
    eerrormsg_rep = errormsg;
    eaction_rep = action;
  }
};

#endif
