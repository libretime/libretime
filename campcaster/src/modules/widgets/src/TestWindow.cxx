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

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "LiveSupport/Widgets/Colors.h"
#include "TestWindow.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the configuration file for the resource bundle.
 *----------------------------------------------------------------------------*/
const std::string       bundleConfigFileName = "etc/resourceBundle.xml";

/*------------------------------------------------------------------------------
 *  The name of the Glade file.
 *----------------------------------------------------------------------------*/
const std::string       gladeFileName = "var/glade/TestWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
TestWindow :: TestWindow (void)                                     throw ()
{
    configureBundle();

    Glib::RefPtr<Gnome::Glade::Xml>     glade = Gnome::Glade::Xml::create(
                                                                gladeFileName);
    
    glade->get_widget("mainWindow1", mainWindow);
    mainWindow->set_title(*getResourceUstring("windowTitle"));
    mainWindow->signal_delete_event().connect(sigc::mem_fun(*this,
                                    &TestWindow::onDeleteEvent));

    glade->get_widget_derived("combobox1", comboBox);
    comboBox->append_text(*getResourceUstring("firstOption"));
    comboBox->append_text(*getResourceUstring("secondOption"));
    comboBox->append_text(*getResourceUstring("thirdOption"));
    comboBox->set_active(0);
    comboBox->signal_changed().connect(sigc::mem_fun(*this,
                                    &TestWindow::onComboBoxSelectionChanged));

    treeModel[0] = Gtk::ListStore::create(modelColumns);
    glade->get_widget_derived("treeview1", treeView[0]);
    treeView[0]->set_model(treeModel[0]);
    treeView[0]->connectModelSignals(treeModel[0]);
    treeView[0]->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
    treeView[0]->appendColumn(*getResourceUstring("pixbufColumnTitle"),
                              modelColumns.pixbufColumn);
    treeView[0]->appendColumn(*getResourceUstring("textColumnTitle"),
                              modelColumns.textColumn);
    fillTreeModel(0);

    treeModel[1] = Gtk::ListStore::create(modelColumns);
    glade->get_widget_derived("treeview2", treeView[1]);
    treeView[1]->set_model(treeModel[1]);
    treeView[1]->connectModelSignals(treeModel[1]);
    treeView[1]->get_selection()->set_mode(Gtk::SELECTION_MULTIPLE);
    treeView[1]->appendColumn(*getResourceUstring("pixbufColumnTitle"),
                              modelColumns.pixbufColumn);
    treeView[1]->appendColumn(*getResourceUstring("textColumnTitle"),
                              modelColumns.textColumn);
    fillTreeModel(1);

    glade->get_widget("label1", label);
    label->set_label(*getResourceUstring("dropHereText"));

    setupDndCallbacks();

    glade->connect_clicked("button1", sigc::mem_fun(*this,
                                    &TestWindow::onOkButtonClicked));
}


/*------------------------------------------------------------------------------
 *  Configure the resource bundle.
 *----------------------------------------------------------------------------*/
void
TestWindow :: configureBundle (void)                                throw ()
{
    Ptr<ResourceBundle>::Ref        bundle;
    
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(
                              new xmlpp::DomParser(bundleConfigFileName, true));
        const xmlpp::Document *     document = parser->get_document();
        const xmlpp::Element *      root     = document->get_root_node();

        bundle = LocalizedObject::getBundle(*root);

    } catch (std::invalid_argument &e) {
        std::cerr << "semantic error in bundle configuration file:\n"
                  << e.what() << std::endl;
        exit(1);
    } catch (std::exception &e) {
        std::cerr << "XML error in bundle configuration file:\n"
                  << e.what() << std::endl;
        exit(1);
    }
    
    setBundle(bundle);
}


/*------------------------------------------------------------------------------
 *  Fill the left tree model.
 *----------------------------------------------------------------------------*/
void
TestWindow :: fillTreeModel (int    index)                          throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    Glib::RefPtr<Gdk::Pixbuf>   pixbuf = wf->getPixbuf(
                                        WidgetConstants::audioClipIconImage);
    Gtk::TreeModel::Row         row;

    treeModel[index]->clear();

    if (index == 0) {
        Glib::ustring               text;
        
        switch (comboBox->get_active_row_number()) {
            case -1:        break;
            
            case 0:         text = *getResourceUstring("textOne");
                            break;
            
            case 1:         text = *getResourceUstring("textTwo");
                            break;
            
            case 2:         text = *getResourceUstring("textThree");
                            break;
            
            default:        break;
        }
        
        row = *treeModel[index]->append();
        row[modelColumns.pixbufColumn] = pixbuf;
        row[modelColumns.textColumn] = text;
    }

    row = *treeModel[index]->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = (index == 0) ? "1111111111"
                                                : "AAAAAAAAAA";

    row = *treeModel[index]->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = (index == 0) ? "2222222222"
                                                : "BBBBBBBBBB";

    row = *treeModel[index]->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = (index == 0) ? "3333333333"
                                                : "CCCCCCCCCC";

    row = *treeModel[index]->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = (index == 0) ? "4444444444"
                                                : "EEEEEEEEEE";
}


/*------------------------------------------------------------------------------
 *  Set up the D'n'D callbacks.
 *----------------------------------------------------------------------------*/
void
TestWindow :: setupDndCallbacks (void)                              throw ()
{
    std::list<Gtk::TargetEntry>     targets;
    targets.push_back(Gtk::TargetEntry("STRING",
                                       Gtk::TARGET_SAME_APP));
    
    // set up the left tree view
    treeView[0]->drag_source_set(targets,
                                 Gdk::MODIFIER_MASK,
                                 Gdk::ACTION_COPY | Gdk::ACTION_MOVE);
    treeView[0]->signal_drag_data_get().connect(sigc::bind<int>(
                    sigc::mem_fun(*this,
                                  &TestWindow::onTreeViewDragDataGet),
                    0));
    treeView[0]->drag_dest_set(targets,
                               Gtk::DEST_DEFAULT_ALL,
                               Gdk::ACTION_COPY | Gdk::ACTION_MOVE);
    treeView[0]->signal_drag_data_received().connect(sigc::bind<int>(
                    sigc::mem_fun(*this,
                                  &TestWindow::onTreeViewDragDataReceived),
                    0));

    // set up the right tree view
    treeView[1]->drag_source_set(targets,
                                 Gdk::MODIFIER_MASK,
                                 Gdk::ACTION_COPY | Gdk::ACTION_MOVE);

    treeView[1]->signal_drag_data_get().connect(sigc::bind<int>(
                    sigc::mem_fun(*this,
                                  &TestWindow::onTreeViewDragDataGet),
                    1));
    treeView[1]->drag_dest_set(targets,
                               Gtk::DEST_DEFAULT_ALL,
                               Gdk::ACTION_COPY | Gdk::ACTION_MOVE);
    treeView[1]->signal_drag_data_received().connect(sigc::bind<int>(
                    sigc::mem_fun(*this,
                                  &TestWindow::onTreeViewDragDataReceived),
                    1));

    // set up the label
    label->drag_dest_set(targets,
                         Gtk::DEST_DEFAULT_ALL,
                         Gdk::ACTION_COPY | Gdk::ACTION_MOVE);
    label->signal_drag_data_received().connect(sigc::mem_fun(*this,
                                &TestWindow::onLabelDragDataReceived));
}


/*------------------------------------------------------------------------------
 *  Event handler for selection change in the combo box.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onComboBoxSelectionChanged (void)                     throw ()
{
    fillTreeModel(0);
}


/*------------------------------------------------------------------------------
 *  Event handler for the OK button being clicked.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onOkButtonClicked (void)                              throw ()
{
    std::cerr << "TestWindow::onOkButtonClicked() called." << std::endl;
    mainWindow->hide();
}


/*------------------------------------------------------------------------------
 *  Event handler for the window being hidden.
 *----------------------------------------------------------------------------*/
bool
TestWindow :: onDeleteEvent (GdkEventAny *      event)              throw ()
{
    // We could add a confirmation dialog here.
    std::cerr << "TestWindow::onDeleteEvent() called." << std::endl;
    return false;
}


/*------------------------------------------------------------------------------
 *  Run the window.
 *----------------------------------------------------------------------------*/
void
TestWindow :: run (void)                                            throw ()
{
    Gtk::Main::run(*mainWindow);
}


/*------------------------------------------------------------------------------
 *  The callback for the start of the drag.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onTreeViewDragDataGet(
            const Glib::RefPtr<Gdk::DragContext> &      context,
            Gtk::SelectionData &                        selectionData,
            guint                                       info,
            guint                                       time,
            int                                         index)
                                                                    throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                            = treeView[index]->get_selection();
    std::list<Gtk::TreePath>    rows = selection->get_selected_rows();
    Glib::ustring               dropString = leftOrRight(index);
    // we can assume there is only one row selected, due to bug
    // ...
    if (rows.size() >= 1) {
        Gtk::TreeRow    row = *treeModel[index]->get_iter(rows.front());
        dropString += " ";
        dropString += row[modelColumns.textColumn];
    }

    selectionData.set(selectionData.get_target(),
                      8 /* 8 bits format*/,
                      (const guchar *) dropString.c_str(),
                      dropString.bytes());
}


/*------------------------------------------------------------------------------
 *  The callback for the end of the drag.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onTreeViewDragDataReceived(
            const Glib::RefPtr<Gdk::DragContext> &      context,
            int                                         x,
            int                                         y,
            const Gtk::SelectionData &                  selectionData,
            guint                                       info,
            guint                                       time,
            int                                         index)
                                                                    throw ()
{
    if (selectionData.get_length() >= 0 && selectionData.get_format() == 8) {
        Glib::ustring   data = selectionData.get_data_as_string();
        if (data.find("left") == 0) {
            std::cerr << "left -> "
                      << leftOrRight(index)
                      << ": "
                      << data.substr(5)
                      << std::endl;
            context->drag_finish(true, false, time);
        } else if (data.find("right") == 0) {
            std::cerr << "right -> "
                      << leftOrRight(index)
                      << ": " 
                      << data.substr(6)
                      << std::endl;
            context->drag_finish(true, false, time);
        } else {
            std::cerr << "unknown string dropped on "
                      << leftOrRight(index)
                      << " tree view: "
                      << data
                      << std::endl;
            context->drag_finish(false, false, time);
        }
    } else {
        std::cerr << "unknown type of data dropped on "
                  << leftOrRight(index)
                  << " tree view"
                  << std::endl;
        context->drag_finish(false, false, time);
    }
}


/*------------------------------------------------------------------------------
 *  The callback for the end of the drag.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onLabelDragDataReceived(
            const Glib::RefPtr<Gdk::DragContext> &      context,
            int                                         x,
            int                                         y,
            const Gtk::SelectionData &                  selectionData,
            guint                                       info,
            guint                                       time)
                                                                    throw ()
{
    if (selectionData.get_length() >= 0 && selectionData.get_format() == 8) {
        Glib::ustring   data = selectionData.get_data_as_string();
        label->set_label(data);
        context->drag_finish(true, true, time);
        
    } else {
        label->set_label(*getResourceUstring("dropHereText"));
        context->drag_finish(false, false, time);
    }
}

