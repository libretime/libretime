// -*- mode: C++; -*-
#ifndef INCLUDED_TEMPORARY_FILE_DIRECTORY
#define INCLUDED_TEMPORARY_FILE_DIRECTORY

#include <string>

/**
 * Provides services for allocating and reserving 
 * secure, temporary directories.
 * 
 * @author Kai Vehmanen
 */
class TEMPORARY_FILE_DIRECTORY {

 public:

  static const int max_temp_files = 512;

  TEMPORARY_FILE_DIRECTORY(void);
  TEMPORARY_FILE_DIRECTORY(const std::string& dir);
  ~TEMPORARY_FILE_DIRECTORY(void);

  void set_directory_prefix(const std::string& dir);
  void reserve_directory(const std::string& nspace);
  void release_directory(void);

  std::string get_directory_prefix(void) const;
  std::string get_reserved_directory(void) const;
  std::string create_filename(const std::string& prefix, const std::string& postfix);
  bool is_valid(void) const;

 private:

  void check_validity(void);

  std::string tdir_rep;
  std::string dirprefix_rep;
  int tmp_index_rep;
  bool valid_rep;
};

#endif
