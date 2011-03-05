#ifndef INCLUDED_ECA_RESOURCES_H
#define INCLUDED_ECA_RESOURCES_H

#include <string>

class RESOURCE_FILE;

/**
 * Class for representing ecasound user settings stored
 * in global ({prefix}/share/ecasound/ecasoundrc) and 
 * user-specific (~/.ecasoundrc) resource files.
 */
class ECA_RESOURCES {

 public:

  std::string resource(const std::string& tag) const;
  bool boolean_resource(const std::string& tag) const;

  bool has(const std::string& tag) const;
  bool has_any(void) const;

  void resource(const std::string& tag, const std::string& value);

  ECA_RESOURCES(void);
  ~ECA_RESOURCES(void);

public:

  /**
   * If non-empty, will override all other resource files for
   * newly created ECA_RESOURCES instances.
   */
  static std::string rc_override_file;

 private:

  ECA_RESOURCES(const ECA_RESOURCES&);
  ECA_RESOURCES& operator=(const ECA_RESOURCES&);

  RESOURCE_FILE* globalrc_repp;
  RESOURCE_FILE* userrc_repp;
  RESOURCE_FILE* overriderc_repp;
  std::string user_resource_directory_rep;
  bool resources_found_rep;
};

#endif
