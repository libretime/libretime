#ifndef INCLUDED_ECA_IAMODE_PARSER_H
#define INCLUDED_ECA_IAMODE_PARSER_H

#include <map>
#include <string>
#include <vector>
#include <pthread.h>

#include "eca-error.h"
#include "eca-iamode-parser_impl.h"

/**
 * Class that handles registering and querying interactive mode commands.
 * @author Kai Vehmanen
 */
class ECA_IAMODE_PARSER : protected ECA_IAMODE_PARSER_COMMANDS {

 public:

  static const std::map<std::string,int>& registered_commands(void);
  static std::vector<std::string> registered_commands_list(void);

  bool action_requires_params(int id);
  bool action_requires_connected(int id);
  bool action_requires_selected_not_connected(int id);
  bool action_requires_selected(int id);
  bool action_requires_selected_audio_input(int id);
  bool action_requires_selected_audio_output(int id);

  ECA_IAMODE_PARSER(void);
  virtual ~ECA_IAMODE_PARSER(void);

 protected:

  static int command_to_action_id(const std::string& cmdstring);
  
 private:

  static void register_commands_misc(void);
  static void register_commands_cs(void);
  static void register_commands_c(void);
  static void register_commands_aio(void);
  static void register_commands_ai(void);
  static void register_commands_ao(void);
  static void register_commands_cop(void);
  static void register_commands_copp(void);
  static void register_commands_ctrl(void);
  static void register_commands_ctrlp(void);
  static void register_commands_dump(void);
  static void register_commands_external(void);

  private:

  static std::map<std::string,int>* cmd_map_repp;
  static pthread_mutex_t lock_rep;
};

void show_controller_help(void);
void show_controller_help_more(void);

#endif
