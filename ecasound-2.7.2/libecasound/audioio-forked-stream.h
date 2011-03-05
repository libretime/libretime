#ifndef INCLUDED_AUDIOIO_FORKED_STREAM_H
#define INCLUDED_AUDIOIO_FORKED_STREAM_H

#include <string>
#include <kvu_temporary_file_directory.h>

#include "audioio-barrier.h"
#include "sample-specs.h" 

/**
 * Helper class providing routines for forking new processes 
 * and creating read/write pipes between the child and the 
 * parent process.
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_FORKED_STREAM : public AUDIO_IO_BARRIER {

 private:

  int pid_of_parent_rep;
  int pid_of_child_rep;
  int fd_rep;
  bool last_fork_rep;
  bool sigkill_sent_rep;
  std::string tmpfile_repp;
  bool tmp_file_created_rep;
  bool use_named_pipe_rep;
  std::string command_rep;
  std::string object_rep;
  TEMPORARY_FILE_DIRECTORY tempfile_dir_rep;

  void init_temp_directory(void);
  void fork_child_for_fifo_read(void);

  void init_state_before_fork(void);

public:

  virtual void stop_io(void);

 protected:
  
  /**
   * Initializes the command string. This must be done before any other set_* 
   * calls.
   */
  void set_fork_command(const std::string& cmd) { command_rep = cmd; }

  void set_fork_file_name(const std::string& filename);
  void set_fork_pipe_name(void);
  void set_fork_channels(int channels);
  void set_fork_sample_rate(long int sample_rate);
  void set_fork_bits(int bits);
  
  void fork_child_for_read(void);
  void fork_child_for_write(void);
  void clean_child(bool force = false);

  const std::string& fork_command(void) const { return(command_rep); }

  bool wait_for_child(void) const;
  bool child_fork_succeeded(void) const { return(last_fork_rep); }
  int pid_of_child(void) const { return(pid_of_child_rep); }
  int file_descriptor(void) const { return(fd_rep); }

  virtual bool do_supports_seeking(void) const = 0;
  virtual void do_set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos) = 0;

public:

  AUDIO_IO_FORKED_STREAM(void) : 
    pid_of_parent_rep(-1),
    pid_of_child_rep(-1),
    fd_rep(0),
    last_fork_rep(false),
    sigkill_sent_rep(false),
    tmp_file_created_rep(false),
    use_named_pipe_rep(false) { }
  virtual ~AUDIO_IO_FORKED_STREAM(void);
};

#endif
