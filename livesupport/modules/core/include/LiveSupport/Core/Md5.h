/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

  ------------------------------------------------------------------------------
 
    This class is based on the following with minor modifications
    (see http://userpages.umbc.edu/~mabzug1/cs/md5/md5.html).

  ------------------------------------------------------------------------------

// Md5.CC - source code for the C++/object oriented translation and 
//          modification of Md5.

// Translation and modification (c) 1995 by Mordechai T. Abzug 

// This translation/ modification is provided "as is," without express or 
// implied warranty of any kind.

// The translator/ modifier does not claim (1) that Md5 will do what you think 
// it does; (2) that this translation/ modification is accurate; or (3) that 
// this software is "merchantible."  (Language for this disclaimer partially 
// copied from the disclaimer below).

  ------------------------------------------------------------------------------

    Which was based on:

  ------------------------------------------------------------------------------

   Md5.H - header file for Md5C.C
   MDDRIVER.C - test driver for MD2, MD4 and Md5

   Copyright (C) 1991-2, RSA Data Security, Inc. Created 1991. All
rights reserved.

License to copy and use this software is granted provided that it
is identified as the "RSA Data Security, Inc. Md5 Message-Digest
Algorithm" in all material mentioning or referencing this software
or this function.

License is also granted to make and use derivative works provided
that such works are identified as "derived from the RSA Data
Security, Inc. Md5 Message-Digest Algorithm" in all material
mentioning or referencing the derived work.

RSA Data Security, Inc. makes no representations concerning either
the merchantability of this software or the suitability of this
software for any particular purpose. It is provided "as is"
without express or implied warranty of any kind.

These notices must be retained in any copies of any part of this
documentation and/or software.

  ------------------------------------------------------------------------------

    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/Md5.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_Md5_h
#define LiveSupport_Core_Md5_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_STDINT_H
#include <stdint.h>
#else
#error need stdint.h
#endif


#include <stdio.h>
#include <sstream>
#include <fstream>
#include <iostream>
#include <iomanip>
#include <stdexcept>


namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class for generating md5 sums.
 *
 *  First, one creates an Md5 object from a file, istream or string; then
 *  one can either call hexDigest() or simply convert the object to std::string
 *  in order to obtain the md5 sum in the form of 32 hexadecimal (lower case)
 *  digits.
 *
 *  This is a trimmed version of the C++ class written by Mordechai T. Abzug
 *  on the basis of the original C code by RSA Data Security, Inc.  See the
 *  header of the source file for further information.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 */
class Md5
{
    private:
    
    // first, some types:
      typedef unsigned       int uint4; // assumes integer is 4 words long
      typedef unsigned short int uint2; // assumes short integer is 2 words long
      typedef unsigned      char uint1; // assumes char is 1 word long
    
    // methods for controlled operation:
      void  update     (uint1 *input, uint4 input_length);
      void  update     (std::istream& stream);
      void  update     (FILE *file);
      void  update     (const std::string& s);
      void  finalize   ()                       throw(std::invalid_argument);

    // next, the private data:
      uint4 state[4];
      uint4 count[2];     // number of *bits*, mod 2^64
      uint1 buffer[64];   // input buffer
      uint1 digest[16];
      uint1 finalized;

      /**
       *  The low 64 bits of the checksum.
       */
      uint64_t      low64;

      /**
       *  The high 64 bits of the checksum.
       */
      uint64_t      high64;
    
    // last, the private methods, mostly static:
      void init             ();               // called by all constructors
      void transform        (uint1 *buffer);  // does the real update work.  Note 
                                              // that length is implied to be 64.
    
      static void encode    (uint1 *dest, uint4 *src, uint4 length);
      static void decode    (uint4 *dest, uint1 *src, uint4 length);
      static void memcpy    (uint1 *dest, uint1 *src, uint4 length);
      static void memset    (uint1 *start, uint1 val, uint4 length);
    
      static inline uint4  rotate_left (uint4 x, uint4 n);
      static inline uint4  F           (uint4 x, uint4 y, uint4 z);
      static inline uint4  G           (uint4 x, uint4 y, uint4 z);
      static inline uint4  H           (uint4 x, uint4 y, uint4 z);
      static inline uint4  I           (uint4 x, uint4 y, uint4 z);
      static inline void   FF  (uint4& a, uint4 b, uint4 c, uint4 d, uint4 x, 
    			    uint4 s, uint4 ac);
      static inline void   GG  (uint4& a, uint4 b, uint4 c, uint4 d, uint4 x, 
    			    uint4 s, uint4 ac);
      static inline void   HH  (uint4& a, uint4 b, uint4 c, uint4 d, uint4 x, 
    			    uint4 s, uint4 ac);
      static inline void   II  (uint4& a, uint4 b, uint4 c, uint4 d, uint4 x, 
    			    uint4 s, uint4 ac);

      /**
       *  Calculate the lower and higher 64 bit values for the checksum
       */
      void
      calcNumericRepresentation(void)                           throw ();


    public:

    /**
     *  Construct from a std::string
     */
      Md5           (const std::string &s)      throw(std::invalid_argument);


    /**
     *  Construct from an istream
     */
      Md5           (std::istream& stream)      throw(std::invalid_argument);


    /**
     *  Construct from a file
     */
      Md5           (FILE *file)                throw(std::invalid_argument);

    
    /**
     *  Get the md5 sum as a 32 digit ascii-hex string
     */
      std::string           hexDigest ()        throw();


    /**
     *  Get the md5 sum as a 32 digit ascii-hex string
     */
      operator std::string  ()                  throw();

    /**
     *  Return the lower 64 bits of the checksum.
     *
     *  @return the lower 64 bits of the checksum.
     */
    uint64_t
    low64bits(void) const                       throw ()
    {
        return low64;
    }

    /**
     *  Return the higher 64 bits of the checksum.
     *
     *  @return the higher 64 bits of the checksum.
     */
    uint64_t
    high64bits(void) const                      throw ()
    {
        return high64;
    }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport


#endif // LiveSupport_Core_Md5_h

