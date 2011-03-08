// ------------------------------------------------------------------------
// definition_by_contract.h: Tools for simulating design-by-contract
// Copyright (C) 1999-2000 Kai Vehmanen
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

#ifndef INCLUDED_DEFINITION_BY_CONTRACT_H
#define INCLUDED_DEFINITION_BY_CONTRACT_H

/**
 * Exception that is thrown when some contract fails
 *
 * Note! Obsolete - not used anymore
 *
 * @author Kai Vehmanen
 */
class DBC_EXCEPTION { 
 public:
  
  const char* type_repp;
  const char* file_repp;
  const char* expression_repp;
  int line_rep;

  DBC_EXCEPTION(const char* type, const char* expr, const char* file, int line) 
    : type_repp(type), file_repp(file), expression_repp(expr), line_rep(line) { }
};

/**
 * Tool for simulating programming/design-by-contract in C++ 
 * classes. Features include routine preconditions, postconditions 
 * and virtual class invariants. Checks are only performed, when
 * ENABLE_DBC is defined.
 *
 * Note! Obsolete - not used anymore
 *
 * @author Kai Vehmanen
 */
class DEFINITION_BY_CONTRACT {

#ifdef ENABLE_DBC
 protected:
  
  inline void require(bool expr, const char* expr_str, const char* file, int line) const {
    check_invariant(file, line); if (!expr) throw(DBC_EXCEPTION("require", expr_str, file, line));
  }
  inline void ensure(bool expr, const char* expr_str, const char* file, int line) const {
    if (!expr) throw(DBC_EXCEPTION("ensure", expr_str, file, line)); check_invariant(file, line);
  }
  inline void check_invariant(const char* file, int line) const {
    if (!class_invariant()) throw(DBC_EXCEPTION("class invariant", "", file, line));
  }

  virtual bool class_invariant(void) const { return(true); }

 public:

  virtual ~DEFINITION_BY_CONTRACT(void) { }

#define REQUIRE(expr)							      \
   (expr) ? static_cast<void>(0) :	(require (false,#expr,__FILE__, __LINE__))
#define ENSURE(expr)							      \
   (expr) ? static_cast<void>(0) :	(ensure (false,#expr,__FILE__, __LINE__))

#else // --> DBC DISABLED

 public:

#define REQUIRE(expr)		((void) 0)
#define ENSURE(expr)		((void) 0)

#endif // <-- DBC DISABLED
};

#endif
