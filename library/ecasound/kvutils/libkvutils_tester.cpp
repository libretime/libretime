// ------------------------------------------------------------------------
// libkvutils_tester.cpp: Runs a set of libkvutils unit tests.
// Copyright (C) 2002-2004,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <string>

#include <cstdio>
#include <cstdlib>
#include <ctime>
#include <stddef.h>  /* ANSI-C: size_t */
#include <stdio.h>   /* for AIX */
#include <time.h>    /* ANSI-C: clock() */

#include "kvu_dbc.h"
#include "kvu_locks.h"
#include "kvu_numtostr.h"
#include "kvu_rtcaps.h"
#include "kvu_timestamp.h"
#include "kvu_utils.h"
#include "kvu_value_queue.h"
#include "kvu_message_queue.h"

using namespace std;

/* --------------------------------------------------------------------- 
 * Options
 */

#define VERBOSE

/* --------------------------------------------------------------------- 
 * Test util macros
 */

#ifdef VERBOSE
#define ECA_TEST_ENTRY()   do { printf("\n%s:%d - Test started\n", __KVU_FUNCTION, __LINE__); } while(0)
#define ECA_TEST_SUCCESS() do { printf("%s:%d - Test passed\n", __KVU_FUNCTION, __LINE__); return 0; } while(0)
#define ECA_TEST_FAIL(x,y) do { printf("\n%s:%d - Test failed: \"%s\"\n", __KVU_FUNCTION, __LINE__, y); return x; } while(0)
#define ECA_TEST_NOTE(x)   do { printf("%s:%d - %s\n", __KVU_FUNCTION, __LINE__, x); fflush(stdout); } while(0)
#else
#define ECA_TEST_ENTRY()   ((void) 0)
#define ECA_TEST_SUCCESS() return 0
#define ECA_TEST_FAIL(x,y) return x
#define ECA_TEST_NOTE(x)   ((void) 0)
#endif

/* --------------------------------------------------------------------- 
 * Type definitions
 */

typedef int (*kvu_test_t)(void);

/* --------------------------------------------------------------------- 
 * Test case declarations
 */

static int kvu_test_1(void);
static int kvu_test_2(void);
static int kvu_test_3(void);
static int kvu_test_4(void);
static int kvu_test_5_timestamp(void);
static int kvu_test_6_msgqueue(void);

static kvu_test_t kvu_funcs[] = { 
  kvu_test_1,  /* kvu_locks.h: ATOMIC_INTEGER */
  kvu_test_2,  /* kvu_utils.h: string handling */
  kvu_test_3,  /* kvu_utils.h: float2str */
  kvu_test_4,  /* kvu_value_queue.h */
  kvu_test_5_timestamp, /* kvu_timestamp.h */
  kvu_test_6_msgqueue,  /* kvu_message_queue.h */
  NULL 
};

/* --------------------------------------------------------------------- 
 * Funtion definitions
 */

int main(int argc, char *argv[])
{
  int n, failed = 0;

  if (argc > 1) {
    /* run just a single test */
    size_t m = std::atoi(argv[1]);
    if (m > 0 && m < (sizeof(kvu_funcs) / sizeof(kvu_test_t))) {
      if (kvu_funcs[m - 1]() != 0) {
	++failed;
      }
    }
  }
  else {
    /* run all tests */
    for(n = 0; kvu_funcs[n] != NULL; n++) {
      int ret = kvu_funcs[n]();
      if (ret != 0) {
	++failed;
      }
    }
  }

  return failed;
}

#define KVU_TEST_1_ROUNDS 5
// #define KVU_TEST_1_ROUNDS 60

static void* kvu_test_1_helper(void* ptr)
{
  ATOMIC_INTEGER* i = (ATOMIC_INTEGER*)ptr;

  int stop_after = KVU_TEST_1_ROUNDS * CLOCKS_PER_SEC / 5;
  clock_t prev, now = clock();

  for(int n = 0, m = 0; n < stop_after;) {
    // if (!(m & 0xffff)) fprintf(stderr, "S");
    ++m;
    int j = i->get();
    if (j < 0) {
      ++j;
      i->set(j);
    }
    j = i->get();
    if (j > 0) { ECA_TEST_FAIL((void*)1, "kvu_test_1_helper access error (1)"); }
    if (j < -1) { ECA_TEST_FAIL((void*)1, "kvu_test_1_helper access error (2)"); }

    prev = now;
    now = clock();
    if (prev > now) 
      n += prev - now;
    else 
      n += now - prev;
  }

  return 0;
}

/**
 * Tests the ATOMIC_INTEGER class defined 
 * in kvu_locks.h. 
 */
static int kvu_test_1(void)
{
  ECA_TEST_ENTRY();

  ATOMIC_INTEGER i (0);

  pthread_t thread;
  pthread_create(&thread, NULL, kvu_test_1_helper, (void*)&i);

  int stop_after = KVU_TEST_1_ROUNDS * CLOCKS_PER_SEC;
  clock_t prev, now = clock();
  
  for(int n = 0, m = 0; n < stop_after;) {
    // if (!(m & 0xffff)) fprintf(stderr, "M");
    ++m;
    int j = i.get();
    if (j < 0) {
      ++j;
      i.set(j);
    }
    else {
      --j;
      i.set(j);
    }

    j = i.get();
    if (j > 0) { ECA_TEST_FAIL(1, "kvu_test_1 access error (3)"); }
    if (j < -1) { ECA_TEST_FAIL(1, "kvu_test_1 access error (4)"); }

    prev = now;
    now = clock();
    if (prev > now) 
      n += prev - now;
    else 
      n += now - prev;
  }

  ECA_TEST_SUCCESS();
}

/**
 * Tests the string handling functions defined 
 * in kvu_utils.h. 
 */
static int kvu_test_2(void)
{
  ECA_TEST_ENTRY();

  /* string comparison: */

  if (kvu_string_icmp(" foo ", " fOo ") != true) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_icmp"); 
  }

  /* vectorization: */

  vector<string> vec = kvu_string_to_tokens(" a foo string ");
  if (vec.size() != 3) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens (1)");
  }
  if (vec[2] != "string") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens (2)"); 
  }

  vec = kvu_string_to_tokens_quoted("a foo\\ string");
  if (vec.size() != 2) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens_quoted (1)");
  }
  if (vec[1] != "foo string") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens_quoted (2)"); 
  }

  vec = kvu_string_to_tokens_quoted("another\\ foo \"with substring\" \\\\slashes");
  if (vec.size() != 3) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens_quoted (3)");
  }
  if (vec[1] != "\"with substring\"") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens_quoted (4)"); 
  }
  if (vec[2] != "\\slashes") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_to_tokens_quoted (5)"); 
  }

  /* de-vectorization: */
  vector<string> test;
  test.push_back(" foo");
  test.push_back("bar ");
  if (kvu_vector_to_string(test, "") != " foobar ") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_vector_to_string (1)"); 
  }

  /* argument string parsing: */

  const string test_arg1 ("-efoobarsouNd:arg1,arg2,arg3,long\\,arg\\:4,arg5,\"arg,6,comma1,comma2\"");

  if (kvu_get_argument_prefix(test_arg1) != "efoobarsouNd") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_argument_prefix"); 
  }

  if (kvu_get_argument_number(5, test_arg1) != "arg5") {
    // fprintf(stderr, "vec2: '%s'.\n", kvu_get_argument_number(5, test_arg1).c_str());
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_argument_number (1)"); 
  }

  if (kvu_get_argument_number(6, test_arg1) != "arg,6,comma1,comma2") {
    fprintf(stderr, "vec2: '%s'.\n", kvu_get_argument_number(6, test_arg1).c_str());
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_argument_number (2)"); 
  }

  /* request for non-existant arg should return an empty string */
  if (kvu_get_argument_number(7, test_arg1) != "") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_argument_number (3)"); 
  }

  vec = kvu_get_arguments(test_arg1);
  if (vec.size() != 6) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_arguments (1)"); 
  }

  if (vec[2] != "arg3" || vec[0] != "arg1" || vec[3] != "long,arg:4") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_arguments (2)"); 
  }

  if (kvu_get_number_of_arguments(test_arg1) != 6) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments"); 
  }

  const string test_arg2 ("-e:\"\\arg1,arg2,arg3");

  if (kvu_get_number_of_arguments(test_arg2) != 3) {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments (arg2-2)"); 
  }

  if (kvu_get_argument_number(1, test_arg2) != "\"\\arg1") {
    fprintf(stderr, "vec2: '%s'.\n", kvu_get_argument_number(1, test_arg2).c_str());
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_argument_number (arg2-1)"); 
  }

  if (kvu_get_number_of_arguments("-f:,,") != 3)
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments (empty args)"); 
  if (kvu_get_number_of_arguments("-f:a,") != 2)
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments (valid+null)"); 
  if (kvu_get_number_of_arguments("-f:,a") != 2)
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments (null+valid)"); 
  if (kvu_get_number_of_arguments("-f:") != 0)
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_get_number_of_arguments (no args)"); 


  /* search and replace: */

  if (kvu_string_search_and_replace("foo bar", 'f', 'b')
      != "boo bar") {
    ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_search_and_replace"); 
  }

  /* meta-char espacing: */

  if (kvu_string_shell_meta_escape("foo\"bar") != "foo\\\"bar") ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_meta_escape");
  if (kvu_string_shell_meta_escape("foo'bar") != "foo\\'bar") ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_meta_escape");
  if (kvu_string_shell_meta_escape("foo|bar") != "foo\\|bar") ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_meta_escape");
  if (kvu_string_shell_meta_escape("foo&bar&&&") != "foo\\&bar\\&\\&\\&") ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_meta_escape");
  if (kvu_string_shell_meta_escape("foo bar ") != "foo\\ bar\\ ") ECA_TEST_FAIL(1, "kvu_test_2 kvu_string_meta_escape");

  ECA_TEST_SUCCESS();
}

/**
 * Tests the floating point to text conversion functions defined
 * in kvu_numtostr.h.
 */
static int kvu_test_3(void)
{
  ECA_TEST_ENTRY();

  /* 17 digits after decimal point */
  double foo = 0.12345678912345678;
  string foostr = kvu_numtostr(foo, 17);
  if (foostr != "0.12345678912345678") {
    // fprintf(stderr, "foo=%.17lf, res=%s.\n", foo, foostr.c_str());
    ECA_TEST_FAIL(1, "kvu_test_3 kvu_numtostr double"); 
  }

  /* 8 digits after decimal point */
  float bar = 0.12345678;
  string barstr = kvu_numtostr(bar, 8);
  if (barstr != "0.12345678") {
    // fprintf(stderr, "bar=%.8f, res=%s.\n", bar, barstr.c_str());
    ECA_TEST_FAIL(1, "kvu_test_3 kvu_numtostr float"); 
  }

  ECA_TEST_SUCCESS();
}

static const int kvu_test_4_iterations_const = 16384;
static int kvu_test_4_retval = 0;

static void* kvu_test_4_helper(void* ptr);

/**
 * Tests the VALUE_QUEUE_RT_C class implementation.
 */
static int kvu_test_4(void)
{
  ECA_TEST_ENTRY();

  /* guarantee bounded execution time only upto 16 items */
  VALUE_QUEUE_RT_C rqueue (16);

  ECA_TEST_NOTE("start-test");

  pthread_t thread;
  pthread_create(&thread, NULL, kvu_test_4_helper, (void*)&rqueue);

  kvu_sleep(1, 0);

  ECA_TEST_NOTE("start-item-push");

  for(int iter = 0; iter < kvu_test_4_iterations_const; iter++) {
    // fprintf(stderr, "%s:%d push.\n", __FUNCTION__, __LINE__);
    rqueue.push_back(iter, 1.0f);
    // kvu_sleep(1, 0);
  }

  void *res_ptr = 0;
  pthread_join(thread, (void**)&res_ptr);
  if (*(int*)res_ptr != 0) {
    ECA_TEST_FAIL(1, "kvu_test_4 slave-thread-failed"); 
  }

  ECA_TEST_NOTE("end-test.");

  ECA_TEST_SUCCESS();
}

/**
 * The real-time consumer thread used in testing VALUE_QUEUE_RT_C.
 */
static void* kvu_test_4_helper(void* ptr)
{
  VALUE_QUEUE_RT_C *rqueue = (VALUE_QUEUE_RT_C*)ptr;
  kvu_test_4_retval = 0;

  ECA_TEST_NOTE("start-thread.");

  int res = kvu_set_thread_scheduling(SCHED_FIFO, 1);
  if (res == 0) {
    ECA_TEST_NOTE("schedfifo-scheduling-enabled");
  }
  else {
    ECA_TEST_NOTE("schedfifo-scheduling-disabled");
  }

  int last_received_v = 0;
  for(int received = 0; received < kvu_test_4_iterations_const; ) {
    if (rqueue->is_empty() != true) {
      const pair<int, double>* ref = rqueue->front();
      if (ref != rqueue->invalid_item()) {
	// fprintf(stderr, "%s:%d front-success.\n", __FUNCTION__, __LINE__);
	++received;
	last_received_v = ref->first + 1;
	if (received != last_received_v) {
	  ECA_TEST_NOTE("queue-sync-error"); 
	  kvu_test_4_retval = -1;
	}
	rqueue->pop_front();
      }
      else {
	ECA_TEST_NOTE("corner-case-queue-busy");
      }
    }
  }

  if (last_received_v != kvu_test_4_iterations_const) {
    ECA_TEST_NOTE("end-of-queue-sync-error"); 
    kvu_test_4_retval = -1;
  }

  ECA_TEST_NOTE("exit-thread");

  pthread_exit(&kvu_test_4_retval);
  /* never reached */
  return 0;
}

/**
 * Tests the kvu_timestamp.h interface
 */
static int kvu_test_5_timestamp(void)
{
  ECA_TEST_ENTRY();

  struct timespec stamp;
  int res;
  int monotonic =
    kvu_clock_is_monotonic();
  double start, now, prev, end;

  res = kvu_clock_gettime(&stamp);
  if (res)
    ECA_TEST_FAIL(1, "kvu_test_5-1 clock_gettime failed"); 

  start = kvu_timespec_seconds(&stamp);
  end = start + 3.0;
  now = prev = start;

  ECA_TEST_NOTE("3s timer loop starts");

  while (now < end) {

    /* 5ms sleeps */
    kvu_sleep(0, 5000000);

    res = kvu_clock_gettime(&stamp);
    if (res)
      ECA_TEST_FAIL(1, "kvu_test_5-2 clock_gettime failed"); 
    now = kvu_timespec_seconds(&stamp);

#ifdef VERY_VERBOSE
    fprintf(stderr, "now=%.09f delta=%.09f end=%.03f\n",
	    now - start, now - prev, end - start);
#endif

    if (now < prev) {
      if (monotonic)
	ECA_TEST_FAIL(1, "kvu_test_5-3 clock goes backwards"); 
      else
	ECA_TEST_NOTE("kvu_test_5 - clock went backwards");
    }
    
    prev = now;
  }
  
  ECA_TEST_SUCCESS();
}

/* note: around 50ms per iteration */
static const int kvu_test_6_iterations_const = 100;
static int kvu_test_6_retval = 0;

static void* kvu_test_6_helper(void* ptr);

/**
 * Tests the MESSAGE_QUEUE_RT_C class implementation.
 */
static int kvu_test_6_msgqueue(void)
{
  ECA_TEST_ENTRY();

  std::srand(std::time(0));

  /* guarantee bounded execution time only upto 16 items */
  MESSAGE_QUEUE_RT_C<std::string> rqueue (16);

  ECA_TEST_NOTE("start-test");

  pthread_t thread;
  pthread_create(&thread, NULL, kvu_test_6_helper, (void*)&rqueue);

  kvu_sleep(1, 0);

  ECA_TEST_NOTE("start-item-push");

  for(int iter = 0; iter < kvu_test_6_iterations_const; iter++) {
    // fprintf(stderr, "%s:%d push.\n", __FUNCTION__, __LINE__);
    std::string msg = kvu_numtostr(iter + 1);
    //std::fprintf(stdout, "%s:%d pushed '%s'\n", __FUNCTION__, __LINE__, msg.c_str());
    rqueue.push_back(msg);
    int sleep_ns = std::rand() % 100;

    kvu_sleep(0, sleep_ns * 1000000); /* [0,100]ms */
  }

  void *res_ptr = 0;
  pthread_join(thread, (void**)&res_ptr);
  if (*(int*)res_ptr != 0) {
    ECA_TEST_FAIL(1, "kvu_test_6 slave-thread-failed"); 
  }

  ECA_TEST_NOTE("end-test.");

  ECA_TEST_SUCCESS();
}

/**
 * The real-time consumer thread.
 */
static void* kvu_test_6_helper(void* ptr)
{
  MESSAGE_QUEUE_RT_C<std::string> *rqueue = 
    static_cast<MESSAGE_QUEUE_RT_C<std::string>*>(ptr);

  kvu_test_6_retval = 0;

  ECA_TEST_NOTE("start-thread.");

  int res = kvu_set_thread_scheduling(SCHED_FIFO, 1);
  if (res == 0) {
    ECA_TEST_NOTE("schedfifo-scheduling-enabled");
  }
  else {
    ECA_TEST_NOTE("schedfifo-scheduling-disabled");
  }

  std::string last_received_v;
  for(int received = 0; received < kvu_test_6_iterations_const; ) {
    int sleep_ns = std::rand() % 100;

    kvu_sleep(0, sleep_ns * 1000000); /* [0,100]ms */

    if (rqueue->is_empty() != true) {
      std::string ref;
      int popres = rqueue->pop_front(&ref);
      if (popres > 0) {
	// std::fprintf(stdout, "%s:%d popped '%s'\n", __FUNCTION__, __LINE__, ref.c_str());
	++received;
	if (last_received_v == ref) {
	  ECA_TEST_NOTE("queue-sync-error"); 
	  kvu_test_6_retval = -1;
	  break;
	}
	last_received_v = ref;
      }
      else {
	ECA_TEST_NOTE("corner-case-queue-busy");
      }
    }
  }

  if (std::atoi(last_received_v.c_str()) != kvu_test_6_iterations_const) {
    // std::fprintf(stdout, "%s:%d last item '%s'\n", __FUNCTION__, __LINE__, last_received_v.c_str());
    ECA_TEST_NOTE("end-of-queue-sync-error"); 
    kvu_test_6_retval = -1;
  }

  ECA_TEST_NOTE("exit-thread");

  pthread_exit(&kvu_test_6_retval);
  /* never reached */
  return 0;
}
