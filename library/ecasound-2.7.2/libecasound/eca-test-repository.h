#ifndef INCLUDE_ECA_TEST_REPOSITORY_H
#define INCLUDE_ECA_TEST_REPOSITORY_H

#include <list>
#include <string>
#include <pthread.h>
#include "eca-test-case.h"

/**
 * A testing subsystem implemented as a 
 * singleton class.
 *
 * Note! Also the non-static part of this class
 *       implements the ECA_TEST_CASE interface.
 *       This means you can use the repository
 *       just like a single test case (Composite
 *       design pattern).
 *
 * Related design patterns:
 *     - Singleton (GoF127)
 *     - Composite (GoF163)
 *
 * @author Kai Vehmanen
 */
class ECA_TEST_REPOSITORY : public ECA_TEST_CASE {
  
  public:

  /**
   * Returns a reference to a unit testing object.
   *
   * Note! Return value is a reference to 
   *       avoid accidental deletion of 
   *       the singleton object.
   */
  static ECA_TEST_REPOSITORY& instance(void);

  protected:

  virtual void do_run(void);
  virtual void do_run(const std::string& name);
  virtual std::string do_name(void) const { return("ECA_TEST_REPOSITORY"); }

  private:

  void do_run_worker(ECA_TEST_CASE* testcase);

  static ECA_TEST_REPOSITORY* interface_impl_repp;
  static pthread_mutex_t lock_rep;

  std::list<ECA_TEST_CASE*> test_cases_rep;

  /** 
   * @name Constructors and destructors
   * 
   * To prevent accidental use, located in private scope and 
   * without a valid definition.
   */
  /*@{*/

  ECA_TEST_REPOSITORY(void);
  ~ECA_TEST_REPOSITORY(void);
  ECA_TEST_REPOSITORY(const ECA_TEST_REPOSITORY&) {}
  ECA_TEST_REPOSITORY& operator=(const ECA_TEST_REPOSITORY&) { return *this; }

  /*@}*/
};

#endif /* INCLUDE_ECA_TEST_REPOSITORY_H */
