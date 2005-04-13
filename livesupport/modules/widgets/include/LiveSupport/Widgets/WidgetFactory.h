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
    Version  : $Revision: 1.12 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/include/LiveSupport/Widgets/WidgetFactory.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Widgets_WidgetFactory_h
#define LiveSupport_Widgets_WidgetFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <gtkmm/image.h>

#include "LiveSupport/Core/Configurable.h"

#include "LiveSupport/Widgets/CornerImages.h"
#include "LiveSupport/Widgets/ButtonImages.h"
#include "LiveSupport/Widgets/Button.h"
#include "LiveSupport/Widgets/ImageButton.h"
#include "LiveSupport/Widgets/ComboBoxText.h"
#include "LiveSupport/Widgets/BlueBin.h"
#include "LiveSupport/Widgets/EntryBin.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"


namespace LiveSupport {
namespace Widgets {

using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A factory to provide access to LiveSupport Widgets.
 *  
 *  The singleton instance of this class has to be configured with an XML
 *  element, which looks like the following:
 *
 *  <pre><code>
 *  &lt;widgetFactory&gt;   path = "path/to/widget/images/"
 *  &lt;/&gt;
 *  </code></pre>
 *
 *  The DTD for the above XML structure is:
 *
 *  <pre><code>
 *  <!ELEMENT widgetFactory   EMPTY >
 *  <!ATTLIST widgetFactory   path        CDATA   #REQUIRED >
 *  </code></pre>
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.12 $
 */
class WidgetFactory :
                        virtual public Configurable
{
    public:
        /**
         *  The types of available buttons.
         */
        typedef enum { pushButton, tabButton }      ButtonType;

        /**
         *  The types of available image buttons.
         */
        typedef enum { deleteButton, 
                       smallPlayButton, smallPauseButton, smallStopButton,
                       hugePlayButton }
                                                    ImageButtonType;

        /**
         *  The list of available miscellaneous images.
         */
        typedef enum { resizeImage,
                       scratchpadWindowTitleImage }
                                                    ImageType;


    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string                configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<WidgetFactory>::Ref          singleton;

        /**
         *  The path to load the images from for the widgets.
         */
        std::string                             path;

        /**
         *  The images for the standard button.
         */
        Ptr<ButtonImages>::Ref          buttonImages;

        /**
         *  The images for the tab button.
         */
        Ptr<ButtonImages>::Ref          tabButtonImages;

        /**
         *  The corner images for the blue bin.
         */
        Ptr<CornerImages>::Ref          blueBinImages;

        /**
         *  The corner images for the dark blue bin.
         */
        Ptr<CornerImages>::Ref          darkBlueBinImages;

        /**
         *  The corner images for the entry bin.
         */
        Ptr<CornerImages>::Ref          entryBinImages;

        /**
         *  The corner images for the white window.
         */
        Ptr<CornerImages>::Ref          whiteWindowImages;

        /**
         *  The combo box left image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       comboBoxLeftImage;

        /**
         *  The combo box center image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       comboBoxCenterImage;

        /**
         *  The combo box right image.
         */
        Glib::RefPtr<Gdk::Pixbuf>       comboBoxRightImage;

        /**
         *  The default constructor.
         */
        WidgetFactory(void)              throw ()
        {
        }

        /**
         *  Load an image relative the path, and signal error if not found.
         *
         *  @param imageName the name of the image, relative to path
         *  @return the loaded image
         *  @exception std::invalid_argument if the image was not found
         */
        Glib::RefPtr<Gdk::Pixbuf>
        loadImage(const std::string     imageName)
                                                throw (std::invalid_argument);


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~WidgetFactory(void)             throw ()
        {
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                  throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<WidgetFactory>::Ref
        getInstance()                                   throw ();

        /**
         *  Configure the object based on the XML element supplied.
         *
         *  @param element the XML element to configure the object from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the object has already
         *             been configured, and can not be reconfigured.
         */
        virtual void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  Create and return a button.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @param label the label shown inside the button.
         *  @param type the type of the button to create
         *  @return a button with the specified label.
         */
        Button *
        createButton(const Glib::ustring      & label,
                     ButtonType                 type = pushButton)
                                                                    throw ();

        /**
         *  Create a stock button.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @param type the type of the button.
         *  @return a button of the requested type, or 0
         */
        ImageButton *
        createButton(ImageButtonType    type)               throw ();

        /**
         *  Create a combo box, that holds text entries.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return a combo box, that holds text entries.
         */
        ComboBoxText *
        createComboBoxText(void)                            throw ();
        
        /**
         *  Create and return a blue singular container.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return a blue singular container.
         */
        BlueBin *
        createBlueBin(void)                                 throw ();

        /**
         *  Create and return a dark blue singular container.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return a dark blue singular container.
         */
        BlueBin *
        createDarkBlueBin(void)                             throw ();

        /**
         *  Create and return a singular container holding a text entry.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return a singular container holding a text entry.
         */
        EntryBin *
        createEntryBin(void)                                throw ();

        /**
         *  Return the images for the white window.
         *
         *  @return the corner images for the white window.
         */
        Ptr<CornerImages>::Ref
        getWhiteWindowCorners(void)                         throw ()
        {
            return whiteWindowImages;
        }

        /**
         *  Create and return a container holding an image.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return the container holding the requested image.
         */
        Gtk::Image *
        createImage(ImageType   imageName)                  throw ();

        /**
         *  Create and return a ZebraTreeView instance.
         *  It is the reponsibility of the caller to dispose of the created
         *  object properly.
         *
         *  @return the ZebraTreeView object.
         */
        ZebraTreeView *
        createTreeView(Glib::RefPtr<Gtk::TreeModel> treeModel)
                                                            throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Widgets
} // namespace LiveSupport

#endif // LiveSupport_Widgets_WidgetFactory_h

