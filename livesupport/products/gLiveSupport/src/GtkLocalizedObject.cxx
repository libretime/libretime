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
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/GtkLocalizedObject.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "GtkLocalizedObject.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Create a Glib ustring from an ICU UnicodeString
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
GtkLocalizedObject :: unicodeStringToUstring(
                            Ptr<const UnicodeString>::Ref   unicodeString)
                                                                    throw ()
{
    const UChar   * uchars = unicodeString->getBuffer();
    int32_t         length = unicodeString->length();
    Ptr<Glib::ustring>::Ref    ustr(new Glib::ustring());
    ustr->reserve(length);

    while (length--) {
        ustr->push_back((gunichar) (*(uchars++)));
    }

    return ustr;
}


/*------------------------------------------------------------------------------
 *  Create an ICU UnicodeString from a Glib ustring
 *----------------------------------------------------------------------------*/
Ptr<UnicodeString>::Ref
GtkLocalizedObject :: ustringToUnicodeString(
                                Ptr<const Glib::ustring>::Ref   gString)
                                                                    throw ()
{
    Ptr<UnicodeString>::Ref     uString(new UnicodeString());

    Glib::ustring::const_iterator     it = gString->begin();
    Glib::ustring::const_iterator     end = gString->end();
    while (it < end) {
        uString->append((UChar32) *it++);
    }

    return uString;
}

