#ifndef INCLUDE_ECA_TEST_CASE_H
#define INCLUDE_ECA_TEST_CASE_H

#include <list>
#include <string>

/**
 * Macro definitions for subclasses 
 * of ECA_TEST_CASE
 */

/**
 * Reports a failed assertion.
 *
 * @see ECA_TEST_CASE::report_failure
 *
 * @param x description, type 'const string&'
 */
#define ECA_TEST_FAILURE(x) \
        do { report_failure(__FILE__, __LINE__, x); } while(0)

/**
 * Abstract interface for implementing 
 * test cases for component testing.
 *
 * @author Kai Vehmanen
 */
class ECA_TEST_CASE {

  public:

  /** @name Constructors and destructors */
  /*@{*/

  ECA_TEST_CASE(void);
  virtual ~ECA_TEST_CASE(void);

  /*@}*/


  /** @name Public interface for running tests */

  void run(void);
  void run(const std::string &name);

  /*@}*/

  /** @name Public interface for queryng test results */

  std::string name(void) const;
  bool success(void) const;
  const std::list<std::string>& failures(void) const;

  /*@}*/

  protected:

  /** @name Protected interface for reporting test failures */

  void report_failure(const std::string& filename, int lineno, const std::string& description);

  /*@}*/

  /**
   * @name Abtract virtual functions that need 
   *       to be defined by all subclasses.
   */

  virtual std::string do_name(void) const = 0;
  virtual void do_run(void) = 0;
  virtual void do_run(const std::string& name);

  /*@}*/

  private:

  void run_common_before(void);
  void run_common_after(void);

  std::list<std::string> failures_rep;
  bool success_rep;

};

#endif /* INCLUDE_ECA_TEST_CASE_H */
