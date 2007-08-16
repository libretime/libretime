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

    glade->get_widget_derived("comboBox1", comboBox);
    comboBox->append_text(*getResourceUstring("firstOption"));
    comboBox->append_text(*getResourceUstring("secondOption"));
    comboBox->append_text(*getResourceUstring("thirdOption"));
    comboBox->set_active(0);
    comboBox->signal_changed().connect(sigc::mem_fun(*this,
                                    &TestWindow::onComboBoxSelectionChanged));

    treeModel = Gtk::ListStore::create(modelColumns);

    glade->get_widget_derived("treeView1", treeView);
    treeView->set_model(treeModel);
    treeView->connectModelSignals(treeModel);
    treeView->appendColumn(*getResourceUstring("pixbufColumnTitle"),
                           modelColumns.pixbufColumn);
    treeView->appendColumn(*getResourceUstring("textColumnTitle"),
                           modelColumns.textColumn);
    fillTreeModel();

    glade->get_widget("label1", label);
    label->set_label(*getResourceUstring("dropHereText"));
    setupDndCallbacks();

    glade->connect_clicked("okButton1", sigc::mem_fun(*this,
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
 *  Fill the tree model.
 *----------------------------------------------------------------------------*/
void
TestWindow :: fillTreeModel (void)                                  throw ()
{
    Ptr<WidgetFactory>::Ref     wf = WidgetFactory::getInstance();
    Glib::RefPtr<Gdk::Pixbuf>   pixbuf = wf->getPixbuf(
                                        WidgetConstants::audioClipIconImage);
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

    treeModel->clear();
    Gtk::TreeModel::Row     row = *treeModel->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = text;

    row = *treeModel->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = "1";

    row = *treeModel->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = "2";

    row = *treeModel->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = "3";

    row = *treeModel->append();
    row[modelColumns.pixbufColumn] = pixbuf;
    row[modelColumns.textColumn] = "4";
}


/*------------------------------------------------------------------------------
 *  Set up the D'n'D callbacks.
 *----------------------------------------------------------------------------*/
void
TestWindow :: setupDndCallbacks (void)                              throw ()
{
    std::list<Gtk::TargetEntry>     targets;
    targets.push_back(Gtk::TargetEntry("STRING"));
    
    treeView->drag_source_set(targets);
    treeView->signal_drag_data_get().connect(sigc::mem_fun(*this,
                                    &TestWindow::onTreeViewDragDataGet));
    label->drag_dest_set(targets);
    label->signal_drag_data_received().connect(sigc::mem_fun(*this,
                                    &TestWindow::onLabelDragDataReceived));
}


/*------------------------------------------------------------------------------
 *  Event handler for selection change in the combo box.
 *----------------------------------------------------------------------------*/
void
TestWindow :: onComboBoxSelectionChanged (void)                     throw ()
{
    fillTreeModel();
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
            guint                                       time)
                                                                    throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                            = treeView->get_selection();
    Gtk::TreeModel::Row     row = *selection->get_selected();
    Glib::ustring           dropString = row[modelColumns.textColumn];

    selectionData.set(selectionData.get_target(),
                      8 /* 8 bits format*/,
                      (const guchar *) dropString.c_str(),
                      dropString.bytes());
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
        
    } else {
        label->set_label(*getResourceUstring("dropHereText"));
    }

    context->drag_finish(false, false, time);
}

