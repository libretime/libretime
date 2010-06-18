/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_NumericTools_h
#define LiveSupport_Core_NumericTools_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <sstream>
#include <glibmm/ustring.h>


namespace LiveSupport {
namespace Core {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A toolbox for various small numeric functions.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class NumericTools
{
    public:

        /**
         *  Convert an integer to a string.
         *
         *  @param  number      the number to be converted.
         *  @return the string value of the number (in base 10).
         */
        static Glib::ustring
        itoa(int    number)                                         throw ();

        /**
         *  Add a number to the end of a string.
         *  This is used in various GUI classes, to generate Glade widget
         *  names like "itemLabel1", "itemLabel2" etc.
         *
         *  NOTE: the <code>index</code> parameter is 0-based (because this
         *  is normal in C++, for containers etc), but the return value is
         *  1-based (because this is what Glade expects)!
         *  Thus <code>addIndex("itemLabel", 0)</code> returns "itemLabel1".
         *
         *  @param  baseString  the string without the index.
         *  @param  index       the index to be added to the string (0-based).
         *  @return the new string, with the index added at the end (1-based).
         */
        static Glib::ustring
        addIndex(const Glib::ustring &      baseString,
                 int                        index)                  throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

#endif // LiveSupport_Core_NumericTools_h

