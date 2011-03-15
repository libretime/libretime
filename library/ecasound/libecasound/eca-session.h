#ifndef INCLUDED_ECA_SESSION_H
#define INCLUDED_ECA_SESSION_H

#include <vector>
#include <string>

#include <kvu_com_line.h>
#include "eca-error.h"

class AUDIO_IO;
class ECA_CHAIN;
class ECA_CHAINSETUP;

/**
 * Class representing a set of chainsetups. Provided
 * functionality includes creating, removing, 
 * selecting and connecting of chainsetups. 
 *
 * Notes: Friendship access to data members is 
 *        allowed for ECA_CONTROL objects.
 */
class ECA_SESSION {

 public:

  // --
  // type definitions and constants

  friend class ECA_CONTROL_BASE;
  friend class ECA_CONTROL_OBJECTS;
  friend class ECA_CONTROL;

 public:

  // --
  // Public/const routines
  // --

  const std::vector<ECA_CHAINSETUP*>& get_chainsetups(void) const { return chainsetups_rep; }
  const ECA_CHAINSETUP* get_selected_chainsetup(void) const { return selected_chainsetup_repp; }
  const ECA_CHAINSETUP* get_connected_chainsetup(void) const { return connected_chainsetup_repp; }
  const ECA_CHAINSETUP* get_chainsetup_with_name(const std::string& name) const;
  bool is_selected_chainsetup_connected(void) const { return(selected_chainsetup_repp == connected_chainsetup_repp); }

  // --
  // Constructors and destructors
  // --
  ECA_SESSION(void);
  ECA_SESSION(COMMAND_LINE& cline) throw(ECA_ERROR&);
  ~ECA_SESSION(void);

 private:

  // ---
  // Status data
  // ---
  std::vector<ECA_CHAINSETUP*> chainsetups_rep;

  ECA_CHAINSETUP* connected_chainsetup_repp;
  ECA_CHAINSETUP* selected_chainsetup_repp;

  bool cs_defaults_set_rep;

  // ---
  // Setup interpretation
  // ---
  void set_cs_param_defaults(void);

  void preprocess_options(std::vector<std::string>* opts);
  void create_chainsetup_options(COMMAND_LINE& cline, std::vector<std::string>* options);
  int interpret_general_options(const std::vector<std::string>& inopts, std::vector<std::string>* outopts);
  int interpret_general_option(const std::string& opts);
  int interpret_chainsetup_option(const std::string& argu);
  int interpret_general_option(COMMAND_LINE& cline, std::vector<std::string>* options);
  bool is_session_option(const std::string& arg) const;

  // ---
  // Function for handling chainsetups
  // ---

  void add_chainsetup(const std::string& name);
  void add_chainsetup(ECA_CHAINSETUP* comline_setup);
  void remove_chainsetup(void);

  /**
   * Select chainsetup with name 'name'
   *
   * require:
   *  name.empty() != true &&
   *
   * ensure:
   *  (selected_chainsetup->name() == name) ||
   *  (selected_chainsetup == 0)
   */
  void select_chainsetup(const std::string& name);

  /**
   * Save selected chainsetup
   *
   * require:
   *  selected_chainsetup != 0
   */
  void save_chainsetup(void) throw(ECA_ERROR&);

  /**
   * Save selected chainsetup to file 'filename'
   *
   * require:
   *  selected_chainsetup != 0 &&
   *  filename.empty() != true
   */
  void save_chainsetup(const std::string& filename) throw(ECA_ERROR&);

  /**
   * Load chainsetup from file "filename"
   *
   * require:
   *  filename.empty() != true
   *
   * ensure:
   *  selected_chainsetup->filename() == filename
   */
  void load_chainsetup(const std::string& filename);

  /**
   * Connect selected chainsetup
   *
   * require:
   *  selected_chainsetup != 0 &&
   *  selected_chainsetup->is_valid()
   *
   * ensure:
   *  selected_chainsetup == connected_chainsetup
   */
  void connect_chainsetup(void) throw(ECA_ERROR&);

  /**
   * Disconnect connected chainsetup
   *
   * require:
   *  connected_chainsetup != 0
   *
   * ensure:
   *  connected_chainsetup == 0
   */
  void disconnect_chainsetup(void);

  /**
   * Gets a vector of all chainsetup names.
   */
  std::vector<std::string> chainsetup_names(void) const;

  void update_controller_sources(void);

  // --
  // Make sure that objects of this class aren't copy constucted/assigned
  // --
  ECA_SESSION (const ECA_SESSION& x) { }
  ECA_SESSION& operator=(const ECA_SESSION& x) { return *this; }
};

#endif
