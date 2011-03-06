#ifndef INCLUDED_KVU_RTCAPS_H
#define INCLUDED_KVU_RTCAPS_H

bool kvu_check_for_sched_fifo(void);
bool kvu_check_for_sched_rr(void);
bool kvu_check_for_mlockall(void);
int kvu_set_thread_scheduling(int policy, int priority);

#endif /* INCLUDED_KVU_RTCAPS_H */
