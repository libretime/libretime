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
    Version  : $Revision: 1.29 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/widgets/src/WidgetFactory.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <unicode/resbund.h>
#include <gtkmm/entry.h>

#include "LiveSupport/Widgets/Colors.h"
#include "LiveSupport/Widgets/ZebraTreeView.h"
#include "MessageWindow.h"

#include "LiveSupport/Widgets/WidgetFactory.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string WidgetFactory::configElementNameStr = "widgetFactory";

/*------------------------------------------------------------------------------
 *  The singleton instance of WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref WidgetFactory::singleton;

/**
 *  The name of the attribute to get the path for the widget images.
 */
static const std::string    pathAttrName = "path";

/**
 *  The relative path for the standard button images.
 */
static const std::string    buttonPath = "button/";

/**
 *  The relative path for the tab button images.
 */
static const std::string    tabButtonPath = "tabButton/";

/**
 *  The relative path for the blue bin images.
 */
static const std::string    blueBinPath = "blueBin/";

/**
 *  The relative path for the dark blue bin images.
 */
static const std::string    darkBlueBinPath = "darkBlueBin/";

/**
 *  The relative path for the entry bin images.
 */
static const std::string    entryBinPath = "entryBin/";

/**
 *  The relative path for the white window images.
 */
static const std::string    whiteWindowPath = "whiteWindow/";

/**
 *  The name of the passive image for the delete button.
 */
static const std::string    deleteButtonPassiveName = "imageButton/delete.png";

/**
 *  The name of the rollover image for the delete button.
 */
static const std::string    deleteButtonRollName = "imageButton/deleteRoll.png";

/**
 *  The name of the passive image for the plus button.
 */
static const std::string    plusButtonPassiveName = "imageButton/plus.png";

/**
 *  The name of the rollover image for the plus button.
 */
static const std::string    plusButtonRollName = "imageButton/plusRoll.png";

/**
 *  The name of the passive image for the minus button.
 */
static const std::string    minusButtonPassiveName = "imageButton/minus.png";

/**
 *  The name of the rollover image for the minus button.
 */
static const std::string    minusButtonRollName = "imageButton/minusRoll.png";

/**
 *  The name of the passive image for the small play button.
 */
static const std::string    smallPlayButtonPassiveName 
                            = "imageButton/smallPlay.png";

/**
 *  The name of the rollover image for the small play button.
 */
static const std::string    smallPlayButtonRollName 
                            = "imageButton/smallPlayRoll.png";

/**
 *  The name of the passive image for the small pause button.
 */
static const std::string    smallPauseButtonPassiveName 
                            = "imageButton/smallPause.png";

/**
 *  The name of the rollover image for the small pause button.
 */
static const std::string    smallPauseButtonRollName 
                            = "imageButton/smallPauseRoll.png";

/**
 *  The name of the passive image for the small stop button.
 */
static const std::string    smallStopButtonPassiveName 
                            = "imageButton/smallStop.png";

/**
 *  The name of the rollover image for the small stop button.
 */
static const std::string    smallStopButtonRollName 
                            = "imageButton/smallStopRoll.png";

/**
 *  The name of the passive image for the huge play button.
 */
static const std::string    hugePlayButtonPassiveName 
                            = "imageButton/hugePlay.png";

/**
 *  The name of the rollover image for the huge play button.
 */
static const std::string    hugePlayButtonRollName 
                            = "imageButton/hugePlayRoll.png";

/**
 *  The name of the passive image for the cue play button.
 */
static const std::string    cuePlayButtonPassiveName 
                            = "imageButton/cuePlay.png";

/**
 *  The name of the rollover image for the cue play button.
 */
static const std::string    cuePlayButtonRollName 
                            = "imageButton/cuePlayRoll.png";

/**
 *  The name of the passive image for the cue stop button.
 */
static const std::string    cueStopButtonPassiveName 
                            = "imageButton/cueStop.png";

/**
 *  The name of the rollover image for the cue stop button.
 */
static const std::string    cueStopButtonRollName 
                            = "imageButton/cueStopRoll.png";

/**
 *  The name of the passive image for the master panel play button.
 */
static const std::string    masterPlayButtonPassiveName 
                            = "imageButton/masterPlay.gif";

/**
 *  The name of the rollover image for the master panel play button.
 */
static const std::string    masterPlayButtonRollName 
                            = "imageButton/masterPlayRoll.gif";

/**
 *  The name of the passive image for the master panel pause button.
 */
static const std::string    masterPauseButtonPassiveName 
                            = "imageButton/masterPause.png";

/**
 *  The name of the rollover image for the master panel pause button.
 */
static const std::string    masterPauseButtonRollName 
                            = "imageButton/masterPauseRoll.png";

/**
 *  The name of the passive image for the master panel stop button.
 */
static const std::string    masterStopButtonPassiveName 
                            = "imageButton/masterStop.gif";

/**
 *  The name of the rollover image for the master panel stop button.
 */
static const std::string    masterStopButtonRollName 
                            = "imageButton/masterStopRoll.gif";

/**
 *  The name of the combo box left image.
 */
static const std::string    comboBoxLeftName = "combo/left.png";

/**
 *  The name of the combo box center image.
 */
static const std::string    comboBoxCenterName = "combo/center.png";

/**
 *  The name of the combo box right image.
 */
static const std::string    comboBoxRightName = "combo/right.png";

/**
 *  The name of the image for the resize handle.
 */
static const std::string    resizeImageName = "whiteWindow/resize.png";

/**
 *  The name of the image for the title of the scratchpad window.
 */
static const std::string    scratchpadWindowTitleImageName 
                            = "titleImages/scratchpadWindowTitle.png";

/**
 *  The name of the image for the title of the search window.
 */
static const std::string    searchWindowTitleImageName 
                            = "titleImages/searchWindowTitle.png";

/**
 *  The name of the image for the title of the live mode window.
 */
static const std::string    liveModeWindowTitleImageName 
                            = "titleImages/liveModeWindowTitle.png";

/**
 *  The name of the image for the title of the playlist editor window.
 */
static const std::string    playlistsWindowTitleImageName 
                            = "titleImages/playlistsWindowTitle.png";

/**
 *  The name of the image for the audio clip icon.
 */
static const std::string    audioClipIconImageName = "icons/audioClipIcon.gif";

/**
 *  The name of the image for the playlist icon.
 */
static const std::string    playlistIconImageName = "icons/playlistIcon.gif";

/**
 *  The name of the image for the title of the scheduler window.
 */
static const std::string    schedulerWindowTitleImageName 
                            = "titleImages/schedulerWindowTitle.png";

/**
 *  The name of the passive image for the minimize window button.
 */
static const std::string    windowMinimizeButtonPassiveName 
                            = "imageButton/windowMin.png";

/**
 *  The name of the rollover image for the minimize window button.
 */
static const std::string    windowMinimizeButtonRollName 
                            = "imageButton/windowMinRoll.png";

/**
 *  The name of the passive image for the maximize window button.
 */
static const std::string    windowMaximizeButtonPassiveName 
                            = "imageButton/windowMax.png";

/**
 *  The name of the rollover image for the maximize window button.
 */
static const std::string    windowMaximizeButtonRollName 
                            = "imageButton/windowMaxRoll.png";

/**
 *  The name of the passive image for the close window button.
 */
static const std::string    windowCloseButtonPassiveName 
                            = "imageButton/windowClose.png";

/**
 *  The name of the rollover image for the close window button.
 */
static const std::string    windowCloseButtonRollName 
                            = "imageButton/windowCloseRoll.png";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance to WidgetFactory
 *----------------------------------------------------------------------------*/
Ptr<WidgetFactory>::Ref
WidgetFactory :: getInstance(void)                                  throw ()
{
    if (!singleton.get()) {
        singleton.reset(new WidgetFactory());
    }

    return singleton;
}


/*------------------------------------------------------------------------------
 *  Configure the widget factory.
 *----------------------------------------------------------------------------*/
void
WidgetFactory :: configure(const xmlpp::Element & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(pathAttrName))) {
        throw std::invalid_argument("Missing path attribute");
    }
    path = attribute->get_value();

    // load the button images, and check if all exist
    buttonImages.reset(new ButtonImages(path + buttonPath));
    tabButtonImages.reset(new ButtonImages(path + tabButtonPath));

    // load the combo box images
    comboBoxLeftImage        = loadImage(comboBoxLeftName);
    comboBoxCenterImage      = loadImage(comboBoxCenterName);
    comboBoxRightImage       = loadImage(comboBoxRightName);

    // load the images for the bins
    blueBinImages.reset(new CornerImages(path + blueBinPath));
    darkBlueBinImages.reset(new CornerImages(path + darkBlueBinPath));
    entryBinImages.reset(new CornerImages(path + entryBinPath));

    // load the white window corner images
    whiteWindowImages.reset(new CornerImages(path + whiteWindowPath));

    // load the miscellaneous images
    imageTypePixbufs[resizeImage]   = loadImage(resizeImageName);
    imageTypePixbufs[scratchpadWindowTitleImage]
                                    = loadImage(scratchpadWindowTitleImageName);
    imageTypePixbufs[searchWindowTitleImage]
                                    = loadImage(searchWindowTitleImageName);
    imageTypePixbufs[liveModeWindowTitleImage]
                                    = loadImage(liveModeWindowTitleImageName);
    imageTypePixbufs[playlistsWindowTitleImage]
                                    = loadImage(playlistsWindowTitleImageName);
    imageTypePixbufs[schedulerWindowTitleImage]
                                    = loadImage(schedulerWindowTitleImageName);
    imageTypePixbufs[audioClipIconImage]
                                    = loadImage(audioClipIconImageName);
    imageTypePixbufs[playlistIconImage]
                                    = loadImage(playlistIconImageName);
}


/*------------------------------------------------------------------------------
 *  Load an image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
WidgetFactory :: loadImage(const std::string    imageName)
                                                throw (std::invalid_argument)
{
    Glib::RefPtr<Gdk::Pixbuf>   image;

    bool    success = true;
    try {
        image = Gdk::Pixbuf::create_from_file(path + imageName);
    } catch (Glib::FileError &e) {
        success = false;
    } catch (Gdk::PixbufError &e) {
        success = false;
    }
    
    if (!success || !image) {
        throw std::invalid_argument("Missing " + imageName);
    }

    return image;
}


/*------------------------------------------------------------------------------
 *  Create a button
 *----------------------------------------------------------------------------*/
Button *
WidgetFactory :: createButton(const Glib::ustring & label,
                              ButtonType            type)           throw ()
{
    switch (type) {
        case pushButton:
            return new Button(label, buttonImages);

        case tabButton:
            return new Button(label, tabButtonImages);

        default:
            return 0;
    }
}


/*------------------------------------------------------------------------------
 *  Create a combo box
 *----------------------------------------------------------------------------*/
ComboBoxText *
WidgetFactory :: createComboBoxText(void)                           throw ()
{
    return new ComboBoxText(comboBoxLeftImage,
                            comboBoxCenterImage,
                            comboBoxRightImage);
}


/*------------------------------------------------------------------------------
 *  Create a metadata combo box
 *----------------------------------------------------------------------------*/
MetadataComboBoxText *
WidgetFactory :: createMetadataComboBoxText(
                        Ptr<MetadataTypeContainer>::Ref     metadataTypes)
                                                                    throw ()
{
    return new MetadataComboBoxText(comboBoxLeftImage,
                                    comboBoxCenterImage,
                                    comboBoxRightImage,
                                    metadataTypes);
}


/*------------------------------------------------------------------------------
 *  Create a comparison operator combo box
 *----------------------------------------------------------------------------*/
OperatorComboBoxText *
WidgetFactory :: createOperatorComboBoxText(
                        Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
{
    return new OperatorComboBoxText(comboBoxLeftImage,
                                    comboBoxCenterImage,
                                    comboBoxRightImage,
                                    bundle);
}


/*------------------------------------------------------------------------------
 *  Create a blue bin
 *----------------------------------------------------------------------------*/
BlueBin *
WidgetFactory :: createBlueBin(void)                                throw ()
{
    return new BlueBin(Colors::LightBlue, blueBinImages);
}


/*------------------------------------------------------------------------------
 *  Create a dark blue bin
 *----------------------------------------------------------------------------*/
BlueBin *
WidgetFactory :: createDarkBlueBin(void)                            throw ()
{
    return new BlueBin(Colors::MasterPanelCenterBlue, darkBlueBinImages);
}


/*------------------------------------------------------------------------------
 *  Create an entry bin
 *----------------------------------------------------------------------------*/
EntryBin *
WidgetFactory :: createEntryBin(void)                               throw ()
{
    return new EntryBin(Colors::LightBlue, entryBinImages);
}


/*------------------------------------------------------------------------------
 *  Create a stock button
 *----------------------------------------------------------------------------*/
ImageButton *
WidgetFactory :: createButton(ImageButtonType    type)              throw ()
{
    Glib::RefPtr<Gdk::Pixbuf>   passiveImage;
    Glib::RefPtr<Gdk::Pixbuf>   rollImage;

    switch (type) {
        case deleteButton:
            passiveImage = loadImage(deleteButtonPassiveName);
            rollImage    = loadImage(deleteButtonRollName);
            break;

        case plusButton:
            passiveImage = loadImage(plusButtonPassiveName);
            rollImage    = loadImage(plusButtonRollName);
            break;

        case minusButton:
            passiveImage = loadImage(minusButtonPassiveName);
            rollImage    = loadImage(minusButtonRollName);
            break;

        case smallPlayButton:
            passiveImage = loadImage(smallPlayButtonPassiveName);
            rollImage    = loadImage(smallPlayButtonRollName);
            break;

        case smallPauseButton:
            passiveImage = loadImage(smallPauseButtonPassiveName);
            rollImage    = loadImage(smallPauseButtonRollName);
            break;

        case smallStopButton:
            passiveImage = loadImage(smallStopButtonPassiveName);
            rollImage    = loadImage(smallStopButtonRollName);
            break;

        case hugePlayButton:
            passiveImage = loadImage(hugePlayButtonPassiveName);
            rollImage    = loadImage(hugePlayButtonRollName);
            break;

        case cuePlayButton:
            passiveImage = loadImage(cuePlayButtonPassiveName);
            rollImage    = loadImage(cuePlayButtonRollName);
            break;

        case cueStopButton:
            passiveImage = loadImage(cueStopButtonPassiveName);
            rollImage    = loadImage(cueStopButtonRollName);
            break;

        case masterPlayButton:
            passiveImage = loadImage(masterPlayButtonPassiveName);
            rollImage    = loadImage(masterPlayButtonRollName);
            break;

        case masterPauseButton:
            passiveImage = loadImage(masterPauseButtonPassiveName);
            rollImage    = loadImage(masterPauseButtonRollName);
            break;

        case masterStopButton:
            passiveImage = loadImage(masterStopButtonPassiveName);
            rollImage    = loadImage(masterStopButtonRollName);
            break;

        case windowMinimizeButton:
            passiveImage = loadImage(windowMinimizeButtonPassiveName);
            rollImage    = loadImage(windowMinimizeButtonRollName);
            break;

        case windowMaximizeButton:
            passiveImage = loadImage(windowMaximizeButtonPassiveName);
            rollImage    = loadImage(windowMaximizeButtonRollName);
            break;

        case windowCloseButton:
            passiveImage = loadImage(windowCloseButtonPassiveName);
            rollImage    = loadImage(windowCloseButtonRollName);
            break;

        default:
            return 0;
    }

    return new ImageButton(passiveImage, rollImage);
}


/*------------------------------------------------------------------------------
 *  Return a Gdk::Pixbuf reference to a named image
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
WidgetFactory :: getPixbuf(ImageType  imageName)                    throw ()
{
    return imageTypePixbufs[imageName];
}


/*------------------------------------------------------------------------------
 *  Create a Gtk::Image
 *----------------------------------------------------------------------------*/
Gtk::Image *
WidgetFactory :: createImage(ImageType  imageName)                  throw ()
{
    return new Gtk::Image(getPixbuf(imageName));
}


/*------------------------------------------------------------------------------
 *  Create a ZebraTreeView table
 *----------------------------------------------------------------------------*/
ZebraTreeView *
WidgetFactory :: createTreeView(Glib::RefPtr<Gtk::TreeModel> treeModel)
                                                                    throw ()
{
    return new ZebraTreeView(treeModel);
}


/*------------------------------------------------------------------------------
 *  Create a message window.
 *----------------------------------------------------------------------------*/
WhiteWindow *
WidgetFactory :: createMessageWindow(Ptr<Glib::ustring>::Ref    message)
                                                                    throw ()
{
    return new MessageWindow(message);
}

