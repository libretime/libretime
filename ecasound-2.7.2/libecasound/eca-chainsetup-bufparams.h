#ifndef INCLUDED_ECA_CHAINSETUP_BUFPARAMS_H
#define INCLUDED_ECA_CHAINSETUP_BUFPARAMS_H

#include <string>

using std::string;

class ECA_CHAINSETUP_BUFPARAMS {

 public:

  ECA_CHAINSETUP_BUFPARAMS(void);

  void set_all(const std::string& paramstring);

  void set_buffersize(long int value);
  void toggle_raised_priority(bool value);
  void set_sched_priority(int prio);
  void toggle_double_buffering(bool value);
  void set_double_buffer_size(long int v);
  void toggle_max_buffers(bool v);

  bool are_all_set(void) const;
  int number_of_set(void) const;

  long int buffersize(void) const { return(buffersize_rep); }
  bool raised_priority(void) const { return(raisedpriority_rep); }
  int get_sched_priority(void) const { return(sched_priority_rep); }
  bool double_buffering(void) const { return(double_buffering_rep); }
  long int double_buffer_size(void) const { return(double_buffer_size_rep); }
  bool max_buffers(void) const { return(max_buffers_rep); }

  bool is_set_buffersize(void) const { return(set_buffersize_rep); }
  bool is_set_raised_priority(void) const { return(set_raisedpriority_rep); }
  bool is_set_sched_priority(void) const { return(set_sched_priority_rep); }
  bool is_set_double_buffering(void) const { return(set_double_buffering_rep); }
  bool is_set_double_buffer_size(void) const { return(set_double_buffer_size_rep); }
  bool is_set_max_buffers(void) const { return(set_max_buffers_rep); }

  std::string to_string(void) const;
  
 private:

  long int buffersize_rep;
  bool raisedpriority_rep;
  int sched_priority_rep;
  bool double_buffering_rep;
  long int double_buffer_size_rep;
  bool max_buffers_rep;

  bool set_buffersize_rep;
  bool set_raisedpriority_rep;
  bool set_sched_priority_rep;
  bool set_double_buffering_rep;
  bool set_double_buffer_size_rep;
  bool set_max_buffers_rep;
};

#endif 
