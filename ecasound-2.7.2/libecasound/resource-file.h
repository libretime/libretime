#ifndef INCLUDED_RESOURCE_FILE_H
#define INCLUDED_RESOURCE_FILE_H

#include <vector>
#include <map>
#include <string>

/**
 * Generic resource file class
 */
class RESOURCE_FILE {

  std::string resfile_rep;
  mutable std::map<std::string,std::string> resmap_rep;
  std::vector<std::string> lines_rep;
  bool modified_rep;

 public:

  /**
   * Returns a vector of registered presets
   */
  std::vector<std::string> keywords(void) const;

  /**
   * Returns current resource file name.
   */
  const std::string& resource_file(void) const { return resfile_rep; }

  /**
   * Set resource file name.
   */
  void resource_file(const std::string& v) { resfile_rep = v; }

  /**
   * Returns value of resource 'tag'.
   */
  std::string resource(const std::string& tag) const;

  /**
   * Set resource 'tag' value to 'value'. If value wasn't 
   * previously defined, it's added.
   */
  void resource(const std::string& tag, const std::string& value);

  /**
   * Returns true if resource 'tag' is 'true', otherwise false
   */
  bool boolean_resource(const std::string& tag) const;
  
  /**
   * Whether resource 'tag' is specified in the resource file
   */
  bool has(const std::string& tag) const;

  /** 
   * Has any resource value been added, removed or modified?
   */
  bool is_modified(void) const { return modified_rep; }

  /**
   * Load/restore resources from file
   */
  void load(void);

  /**
   * Save/store resources to file saving
   */
  void save(void);

  /**
   * Constructor. Resource values are read, if
   * filename argument is given.
   */
  RESOURCE_FILE(const std::string& resource_file = "");
  virtual ~RESOURCE_FILE(void);
};

#endif
