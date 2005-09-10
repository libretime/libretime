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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/include/LiveSupport/PlaylistExecutor/AudioPlayerFactory.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_PlaylistExecutor_AudioPlayerFactory_h
#define LiveSupport_PlaylistExecutor_AudioPlayerFactory_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>

#include "LiveSupport/Core/Configurable.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  The factory to create appropriate AudioPlayer objects.
 *  This singleton class has to be configured with an XML element,
 *  describing the AudioPlayerInterface that it should build
 *  and maintain. This is done by including the configuration element
 *  for the desired type of connection manager inside the configuration
 *  element for the factory.
 *
 *  Currently only the GstreamerAudioPlayer is supported, thus a
 *  configuration file may look like this:
 *
 *  <pre><code>
 *  &lt;audioPlayer&gt;
        <gstreamerPlayer audioDevice = "plughw:0,0" />
 *  &lt;/audioPlayer&gt;
 *  </code></pre>
 *
 *  The DTD for the above XML structure is:
 *
 *  <pre><code>
 *  <!ELEMENT audioPlayer   (gstreamerPlayer) >
 *  </code></pre>
 *
 *  For the DTD and details of the gstreamerPlayer configuration
 *  element, see the GstreamerPlayer documentation.
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see GstreamerPlayer
 */
class AudioPlayerFactory :
                        virtual public Configurable
{
    private:
        /**
         *  The name of the configuration XML elmenent used by this object.
         */
        static const std::string    configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<AudioPlayerFactory>::Ref     singleton;

        /**
         *  The audio player created by this factory.
         */
        Ptr<AudioPlayerInterface>::Ref          audioPlayer;

        /**
         *  The default constructor.
         */
        AudioPlayerFactory(void)              throw ()
        {
        }


    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~AudioPlayerFactory(void)             throw ()
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
        static Ptr<AudioPlayerFactory>::Ref
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
         *  Return an audio player.
         *  The returned player will already have been initialized.
         *
         *  @return the appropriate audio player, according to the
         *          configuration of this factory.
         */
        Ptr<AudioPlayerInterface>::Ref
        getAudioPlayer(void)                    throw ()
        {
            return audioPlayer;
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // LiveSupport_PlaylistExecutor_AudioPlayerFactory_h

