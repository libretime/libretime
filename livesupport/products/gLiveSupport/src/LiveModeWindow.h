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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LiveModeWindow.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveModeWindow_h
#define LiveModeWindow_h

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
#include "LiveSupport/Widgets/WhiteWindow.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ZebraTreeModelColumnRecord.h"
#include "GLiveSupport.h"

namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The LiveMode window, showing recent and relevant audio clips and
 *  playlists.
 *
 *  @author $Author: fgerlits $
 *  @version $Revision: 1.1 $
 */
class LiveModeWindow : public WhiteWindow, public LocalizedObject
{
    private:

    protected:

        /**
         *  The columns model needed by Gtk::TreeView.
         *  Lists one clip per row.
         *
         *  @author $Author: fgerlits $
         *  @version $Revision: 1.1 $
         */
        class ModelColumns : public ZebraTreeModelColumnRecord
        {
            public:
                /**
                 *  The column for the playable object shown in the row.
                 */
                Gtk::TreeModelColumn<Ptr<Playable>::Ref>    playableColumn;

                /**
                 *  The column for the play button.
                 */
                Gtk::TreeModelColumn<Glib::RefPtr<Gdk::Pixbuf> >
                                                            playButtonColumn;

                /**
                 *  The column for the title of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         titleColumn;

                /**
                 *  The column for the creator of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         creatorColumn;

                /**
                 *  The column for the length of the audio clip or playlist.
                 */
                Gtk::TreeModelColumn<Glib::ustring>         lengthColumn;

                /**
                 *  Constructor.
                 */
                ModelColumns(void)                              throw ()
                {
                    add(playableColumn);
                    add(playButtonColumn);
                    add(titleColumn);
                    add(creatorColumn);
                    add(lengthColumn);
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
        ZebraTreeView             * treeView;

        /**
         *  The tree model, as a GTK reference.
         */
        Glib::RefPtr<Gtk::ListStore>    treeModel;

        /**
         *  The right-click context menu,
         *  that comes up when right-clicking an entry in the entry list.
         */
//        Gtk::Menu                 * contextMenu;


    public:
        /**
         *  Constructor.
         *
         *  @param gLiveSupport the GLiveSupport, application object.
         *  @param bundle the resource bundle holding the localized
         *         resources for this window
         */
        LiveModeWindow(Ptr<GLiveSupport>::Ref      gLiveSupport,
                       Ptr<ResourceBundle>::Ref    bundle)      throw ();

        /**
         *  Virtual destructor.
         */
        virtual
        ~LiveModeWindow(void)                                   throw ();

        /**
         *  Update the window contents, with the contents of the LiveMode.
         */
        void
        showContents(void)                                      throw ();
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // LiveModeWindow_h

