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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/Attic/AudioClipListWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef AudioClipListWindow_h
#define AudioClipListWindow_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>

#include <unicode/resbund.h>

#include <gtkmm.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedObject.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A window, showing and handling audio clips.
 *
 *  @author $Author: maroy $
 *  @version $Revision: 1.3 $
 */
class AudioClipListWindow : public Gtk::Window, public LocalizedObject
{

    protected:

        /**
         *  The model columns, for the audio clip window.
         *  Lists one clip per row.
         *
         *  @author $Author: maroy $
         *  @version $Revision: 1.3 $
         */
        class ModelColumns : public Gtk::TreeModel::ColumnRecord
        {
            public:
                /**
                 *  The column for the id of the audio clip.
                 */
                Gtk::TreeModelColumn<unsigned int>      idColumn;

                /**
                 *  The column for the length of the audio clip.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     lengthColumn;

                /**
                 *  The column for the URI of the audio clip.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     uriColumn;

                /**
                 *  The column for the token of the audio clip.
                 */
                Gtk::TreeModelColumn<Glib::ustring>     tokenColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                  throw ()
                {
                    add(idColumn);
                    add(lengthColumn);
                    add(uriColumn);
                    add(tokenColumn);
                }
        };


        /**
         *  The GLiveSupport object, holding the state of the application.
         */
        Ptr<GLiveSupport>::Ref      gLiveSupport;

        /**
         *  The column model.
         */
        ModelColumns                modelColumns;

        /**
         *  The main container in the window.
         */
        Gtk::VBox                   vBox;

        /**
         *  A scrolled window, so that the list can be scrolled.
         */
        Gtk::ScrolledWindow         scrolledWindow;

        /**
         *  The tree view, now only showing rows.
         */
        Gtk::TreeView               treeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The box containing the close button.
         */
        Gtk::HButtonBox             buttonBox;

        /**
         *  The close button.
         */
        Ptr<Gtk::Button>::Ref       closeButton;

        /**
         *  Signal handler for the close button clicked.
         */
        virtual void
        onCloseButtonClicked(void)                              throw ();

        /**
         *  Update the window contents, with all the audio clips.
         */
        void
        showAllAudioClips(void)                                 throw ();


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        AudioClipListWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                            Ptr<ResourceBundle>::Ref    bundle)     throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~AudioClipListWindow(void)                                  throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // AudioClipListWindow_h

