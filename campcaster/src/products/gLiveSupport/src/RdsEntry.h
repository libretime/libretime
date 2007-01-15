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
#ifndef RdsEntry_h
#define RdsEntry_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gtkmm/box.h>
#include <gtkmm/checkbutton.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "GLiveSupport.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;
    
/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A single RDS input field.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class RdsEntry : public Gtk::HBox, 
                 public LocalizedObject
{
    private:
        /**
         *  The RDS type of the object (PS, PI, RT, etc).
         */
        Ptr<const Glib::ustring>::Ref   type;
        
        /**
         *  The saved state of the checkbox.
         */
        bool                            checkBoxSaved;
        
        /**
         *  The saved contents of the entry bin.
         */
        Ptr<const Glib::ustring>::Ref   entryBinSaved;


    protected:
        /**
         *  The enable/disable checkbox.
         */
        Gtk::CheckButton *          checkBox;

        /**
         *  The entry field.
         */
        EntryBin *                  entryBin;


    public:
        /**
         *  Constructor.
         *  The type parameter is a string of 2 or 3 upper-case characters,
         *  see http://en.wikipedia.org/wiki/Radio_Data_System.
         *
         *  @param  bundle      the resource bundle holding the localized
         *                      resources for this window.
         *  @param  type        the type of RDS data (PS, PI, RT, etc).
         *  @param  width       the width of the entry, in characters.
         */
        RdsEntry(Ptr<ResourceBundle>::Ref   bundle,
                 const Glib::ustring &      type,
                 int                        width)                  throw ();

        /**
         *  A virtual destructor.
         */
        virtual
        ~RdsEntry(void)                                             throw ()
        {
        }

        /**
         *  Get the type of the widget.
         *  Returns the RDS option type (PS, PI, RT, ...).
         *
         *  @return the "type" parameter passed to the constructor.
         */
        Ptr<const Glib::ustring>::Ref
        getType(void)                                               throw ()
        {
            return type;
        }

        /**
         *  Set the state of the widget.
         *
         *  @param  enabled     the new state of the checkBox.
         *  @param  value       the new contents of the entryBin.
         */
        void
        setOptions(bool                           enabled,
                   Ptr<const Glib::ustring>::Ref  value)            throw ();

        /**
         *  Save the changes made by the user.
         *
         *  @param  gLiveSupport    the GLiveSupport object holding the 
         *                          RDS options to be modified.
         *  @return true if any changes were saved; false otherwise.
         */
        bool
        saveChanges(Ptr<GLiveSupport>::Ref      gLiveSupport)       throw ();

        /**
         *  Clear the entries of the widget.
         */
        void
        reset(void)                                                 throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // RdsEntry_h

