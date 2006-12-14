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

#define DEBUG_PREFIX "GstreamerPlayer"
#include "LiveSupport/Core/Debug.h"

#include "LiveSupport/Core/TimeConversion.h"
#include "GstreamerPlayer.h"


using namespace boost::posix_time;
using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string GstreamerPlayer::configElementNameStr = "gstreamerPlayer";

/**
 *  The name of the audio device attribute.
 */
static const std::string    audioDeviceName = "audioDevice";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the Audio Player.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    DEBUG_FUNC_INFO

    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute = 0;

    if ((attribute = element.get_attribute(audioDeviceName))) {
        m_audioDevice = attribute->get_value();
    }
}


/*------------------------------------------------------------------------------
 *  Initialize the Audio Player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: initialize(void)                 throw (std::exception)
{
    DEBUG_FUNC_INFO

    if (m_initialized) {
        return;
    }

    // initialize the gstreamer library
    if (!gst_init_check(0, 0)) {
        throw std::runtime_error("couldn't initialize the gstreamer library");
    }

    // create the pipeline container (threaded)
    m_pipeline   = gst_thread_new("audio-player");

    m_filesrc         = 0;
    m_decoder         = 0;
    m_audioconvert    = 0;
    m_audioscale      = 0;

    g_signal_connect(m_pipeline, "error", G_CALLBACK(errorHandler), this);

    // TODO: read the caps from the config file
    m_sinkCaps = gst_caps_new_simple("audio/x-raw-int",
                                   "width", G_TYPE_INT, 16,
                                   "depth", G_TYPE_INT, 16,
                                   "endiannes", G_TYPE_INT, G_BYTE_ORDER,
                                   "channels", G_TYPE_INT, 2,
                                   "rate", G_TYPE_INT, 44100,
                                   NULL);

    setAudioDevice(m_audioDevice);

    // set up other variables
    m_initialized = true;
}


/*------------------------------------------------------------------------------
 *  Handler for gstreamer errors.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: errorHandler(GstElement   * pipeline,
                                GstElement   * source,
                                GError       * error,
                                gchar        * debug,
                                gpointer       self)
                                                                throw ()
{
    std::cerr << "gstreamer error: " << error->message << std::endl;

    // Important: We *must* use an idle function call here, so that the signal handler returns 
    // before fireOnStopEvent() is executed.
    g_idle_add(fireOnStopEvent, self);
}


/*------------------------------------------------------------------------------
 *  De-initialize the Gstreamer Player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: deInitialize(void)                       throw ()
{
    DEBUG_FUNC_INFO

    if (m_initialized) {
        gst_element_set_state(m_pipeline, GST_STATE_NULL);
        gst_bin_sync_children_state(GST_BIN(m_pipeline));

        if (!gst_element_get_parent(m_audiosink)) {
            // delete manually, if audiosink wasn't added to the pipeline
            // for some reason
            gst_object_unref(GST_OBJECT(m_audiosink));
        }
        gst_object_unref(GST_OBJECT(m_pipeline));
        gst_caps_free(m_sinkCaps);

        m_audiosink   = 0;
        m_initialized = false;
    }
}


/*------------------------------------------------------------------------------
 *  Attach an event listener.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: attachListener(AudioPlayerEventListener*     eventListener)
                                                                    throw ()
{
    m_listeners.push_back(eventListener);
}


/*------------------------------------------------------------------------------
 *  Detach an event listener.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: detachListener(AudioPlayerEventListener*     eventListener)
                                                throw (std::invalid_argument)
{
    ListenerVector::iterator    it  = m_listeners.begin();
    ListenerVector::iterator    end = m_listeners.end();

    while (it != end) {
        if (*it == eventListener) {
            m_listeners.erase(it);
            return;
        }
        ++it;
    }

    throw std::invalid_argument("supplied event listener not found");
}


/*------------------------------------------------------------------------------
 *  Send the onStop event to all attached listeners.
 *----------------------------------------------------------------------------*/
gboolean
GstreamerPlayer :: fireOnStopEvent(gpointer self)                        throw ()
{
    DEBUG_BLOCK

    GstreamerPlayer* const player = (GstreamerPlayer*) self;

    ListenerVector::iterator    it  = player->m_listeners.begin();
    ListenerVector::iterator    end = player->m_listeners.end();

    while (it != end) {
        (*it)->onStop();
        ++it;
    }

    // false == Don't call this idle function again
    return false;
}


/*------------------------------------------------------------------------------
 *  An EOS event handler, that will put the pipeline to EOS as well.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: eosEventHandler(GstElement    * element,
                                   gpointer        self)
                                                                throw ()
{
    DEBUG_BLOCK

    GstreamerPlayer* const player = (GstreamerPlayer*) self;

    gst_element_set_eos(player->m_pipeline);
    
    // Important: We *must* use an idle function call here, so that the signal handler returns 
    // before fireOnStopEvent() is executed.
    g_idle_add(fireOnStopEvent, player);
}


/*------------------------------------------------------------------------------
 * NewPad event handler. Links the decoder after decodebin's autoplugging. 
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer::newpadEventHandler(GstElement*, GstPad* pad, gboolean, gpointer self) throw ()
{
    DEBUG_BLOCK

    GstreamerPlayer* const player = (GstreamerPlayer*) self;
    GstPad* const audiopad = gst_element_get_pad(player->m_audioconvert, "sink");

    if (GST_PAD_IS_LINKED(audiopad)) {
        debug() << "audiopad is already linked. Unlinking old pad." << endl;
        gst_pad_unlink(audiopad, GST_PAD_PEER(audiopad));
    }

    gst_pad_link(pad, audiopad);

    if (gst_element_get_parent(player->m_audiosink) == NULL)
        gst_bin_add(GST_BIN(player->m_pipeline), player->m_audiosink);

    gst_bin_sync_children_state(GST_BIN(player->m_pipeline));
}


/*------------------------------------------------------------------------------
 * Preload a file, to speed up the subsequent open() call. 
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: preload(const std::string   fileUrl)
                                                throw (std::invalid_argument,
                                                       std::runtime_error)
{
    DEBUG_BLOCK

    if (m_preloadThread) {
        m_preloadThread->stop();
        m_preloadThread->join();
        m_preloadThread.reset();
    }

    Ptr<Preloader>::Ref loader;
    loader.reset(new Preloader(this, fileUrl));

    m_preloadThread.reset(new Thread(loader));
    m_preloadThread->start();
}


/*------------------------------------------------------------------------------
 *  Specify which file to play
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: open(const std::string   fileUrl)
                                                throw (std::invalid_argument,
                                                       std::runtime_error)
{
    // GStreamer pipeline overview:
    // filesrc -> decoder -> audioconvert -> audioscale -> audiosink

    DEBUG_BLOCK

    if (isOpen()) {
        close();
    }

    debug() << "Opening URL: " << fileUrl << endl;
    debug() << "Timestamp: " << *TimeConversion::now() << endl;

    std::string filePath;

    if (fileUrl.find("file://") == 0) {
        filePath = fileUrl.substr(7, fileUrl.size());
    }
    else if (fileUrl.find("file:") == 0) {
        filePath = fileUrl.substr(5, fileUrl.size());
    }
    else {
        throw std::invalid_argument("badly formed URL or unsupported protocol");
    }

    if (m_preloadThread) {
        debug() << "Waiting for Preloader thread to finish..." << endl;
        m_preloadThread->join();
    }

    const bool isSmil = fileUrl.substr(fileUrl.size()-5, fileUrl.size()) == ".smil" ? true : false;
    const bool isPreloaded = (m_preloadUrl == fileUrl);

    if (isPreloaded)
        m_filesrc = m_preloadFilesrc;
    else {
        m_filesrc    = gst_element_factory_make("filesrc", "file-source");
        gst_element_set(m_filesrc, "location", filePath.c_str(), NULL);
    }

    // converts between different audio formats (e.g. bitrate) 
    m_audioconvert    = gst_element_factory_make("audioconvert", NULL);

    // scale the sampling rate, if necessary
    m_audioscale      = gst_element_factory_make("audioscale", NULL);

    // Due to bugs in the minimalaudiosmil element, it does not currently work with decodebin.
    // Therefore we instantiate it manually if the file has the .smil extension. 
    if (isSmil) {
        if (isPreloaded) {
            debug() << "Using preloaded SMIL element instance." << endl;
            m_decoder = m_preloadDecoder;
            gst_element_link(m_decoder, m_audioconvert);
        }
        else {
            debug() << "SMIL file detected." << endl;
            m_stopPreloader = false;
            m_decoder = gst_element_factory_make("minimalaudiosmil", NULL);
            gst_element_set(m_decoder, "abort", &m_stopPreloader, NULL);
            gst_element_link_many(m_filesrc, m_decoder, m_audioconvert, NULL);
        }
        if (gst_element_get_parent(m_audiosink) == NULL)
            gst_bin_add(GST_BIN(m_pipeline), m_audiosink);
    }
    // Using GStreamer's decodebin autoplugger for everything else
    else {
        m_decoder = gst_element_factory_make("decodebin", NULL);
        gst_element_link(m_filesrc, m_decoder);
        g_signal_connect(m_decoder, "new-decoded-pad", G_CALLBACK(newpadEventHandler), this);
    }

    if (!m_decoder) {
        throw std::invalid_argument(std::string("can't open URL ") + fileUrl);
    }

    gst_bin_add_many(GST_BIN(m_pipeline), m_filesrc, m_decoder, m_audioconvert, m_audioscale, NULL);

    gst_element_link_many(m_audioconvert, m_audioscale, m_audiosink, NULL);

    // connect the eos signal handler
    g_signal_connect(m_decoder, "eos", G_CALLBACK(eosEventHandler), this);

    m_preloadUrl.clear();
    
    if (gst_element_set_state(m_pipeline,GST_STATE_PAUSED) == GST_STATE_FAILURE) {
        close();
        // the error is most probably caused by not being able to open
        // the audio device (as it might be blocked by an other process
        throw std::runtime_error("can't open audio device " + m_audioDevice);
    }
}


/*------------------------------------------------------------------------------
 *  Tell if we've been opened.
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: isOpen(void)                                 throw ()
{
    return m_decoder != 0;
}


/*------------------------------------------------------------------------------
 *  Get the length of the current audio clip.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
GstreamerPlayer :: getPlaylength(void)              throw (std::logic_error)
{
    Ptr<time_duration>::Ref   length;
    gint64                    ns;
    GstFormat                 format = GST_FORMAT_TIME;

    if (!isOpen()) {
        throw std::logic_error("player not open");
    }

    if (m_decoder
     && gst_element_query(m_decoder, GST_QUERY_TOTAL, &format, &ns)
     && format == GST_FORMAT_TIME) {

        // use microsec, as nanosec() is not found by the compiler (?)
        length.reset(new time_duration(microsec(ns / 1000LL)));
    } else {
        length.reset(new time_duration(microsec(0LL)));
    }

    return length;
}


/*------------------------------------------------------------------------------
 *  Get the current position of the current audio clip.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
GstreamerPlayer :: getPosition(void)                throw (std::logic_error)
{
    Ptr<time_duration>::Ref   length;
    gint64                    ns = 0;

    if (!isOpen()) {
        throw std::logic_error("player not open");
    }
    
    GstFormat fmt = GST_FORMAT_TIME;
    gst_element_query(m_audiosink, GST_QUERY_POSITION, &fmt, &ns);
    
    length.reset(new time_duration(microseconds(ns / 1000LL)));

    return length;
}


/*------------------------------------------------------------------------------
 *  Start playing
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: start(void)                      throw (std::logic_error)
{
    DEBUG_BLOCK

    if (!isOpen()) {
        throw std::logic_error("GstreamerPlayer not opened yet");
    }

    if (!isPlaying()) {
        gst_element_set_state(m_pipeline, GST_STATE_PLAYING);
    }
}


/*------------------------------------------------------------------------------
 *  Pause the player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: pause(void)                      throw (std::logic_error)
{
    if (isPlaying()) {
        gst_element_set_state(m_pipeline, GST_STATE_PAUSED);
    }
}


/*------------------------------------------------------------------------------
 *  Tell if we're playing
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: isPlaying(void)                  throw ()
{
    return gst_element_get_state(m_pipeline) == GST_STATE_PLAYING;
}


/*------------------------------------------------------------------------------
 *  Stop playing
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: stop(void)                       throw (std::logic_error)
{
    if (!isOpen()) {
        throw std::logic_error("GstreamerPlayer not opened yet");
    }

    if (isPlaying()) {
        gst_element_set_state(m_pipeline, GST_STATE_READY);
    }
}
 

/*------------------------------------------------------------------------------
 *  Close the currently opened audio file.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: close(void)                       throw (std::logic_error)
{
    DEBUG_BLOCK

    if (isPlaying()) {
        stop();
    }

    gst_element_set_state(m_pipeline, GST_STATE_NULL);

    // Unlink elements:
    if (m_filesrc && m_decoder) {
        gst_element_unlink(m_filesrc, m_decoder);
    }
    if (m_decoder && m_audioconvert) {
        gst_element_unlink(m_decoder, m_audioconvert);
    }
    if (m_audioconvert && m_audioscale ) {
        gst_element_unlink(m_audioconvert, m_audioscale);
    }
    if (m_audioscale && m_audiosink) {
        gst_element_unlink(m_audioscale, m_audiosink);
    }

    // Remove elements from pipeline:
    if (m_audioscale) {
        gst_bin_remove(GST_BIN(m_pipeline), m_audioscale);
    }
    if (m_audioconvert) {
        gst_bin_remove(GST_BIN(m_pipeline), m_audioconvert);
    }
    if (m_decoder) {
        gst_bin_remove(GST_BIN(m_pipeline), m_decoder);
    }
    if (m_filesrc) {
        gst_bin_remove(GST_BIN(m_pipeline), m_filesrc);
    }
    if (m_audiosink && gst_element_get_parent(m_audiosink) == GST_OBJECT(m_pipeline)) {
        gst_object_ref(GST_OBJECT(m_audiosink));
        gst_bin_remove(GST_BIN(m_pipeline), m_audiosink);
    }

    m_filesrc         = 0;
    m_decoder         = 0;
    m_audioconvert    = 0;
    m_audioscale      = 0;
}


/*------------------------------------------------------------------------------
 *  Get the volume of the player. *Unimplemented*: Feature is currently not used.
 *----------------------------------------------------------------------------*/
unsigned int
GstreamerPlayer :: getVolume(void)                                  throw ()
{
    return 0;
}


/*------------------------------------------------------------------------------
 *  Set the volume of the player. *Unimplemented*: Feature is currently not used.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: setVolume(unsigned int   volume)                 throw ()
{}


/*------------------------------------------------------------------------------
 *  Set the audio device.
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: setAudioDevice(const std::string &deviceName)       
                                                                throw ()
{
    DEBUG_BLOCK

    if (deviceName.size() == 0) {
        return false;
    }

    const bool oss = deviceName.find("/dev") == 0;

    if (m_audiosink) {
        debug() << "Destroying old sink." << endl;
        if (m_audioscale) {
            gst_element_unlink(m_audioscale, m_audiosink);
        }
        if (gst_element_get_parent(m_audiosink) == NULL)
            gst_object_unref(GST_OBJECT(m_audiosink));
        else
            gst_bin_remove(GST_BIN(m_pipeline), m_audiosink);
        m_audiosink = 0;
    }

    if (!m_audiosink) {
        m_audiosink = (oss ? gst_element_factory_make("osssink", "osssink")
                           : gst_element_factory_make("alsasink", "alsasink"));
    }
    if (!m_audiosink) {
        return false;
    }

    // it's the same property, "device" for both alsasink and osssink
    gst_element_set(m_audiosink, "device", deviceName.c_str(), NULL);

    if (m_audioscale) {
        gst_element_link_filtered(m_audioscale, m_audiosink, m_sinkCaps);
    }

    return true;
}



//////////////////////////////////////////////////////////////////////////////
// CLASS Preloader
//////////////////////////////////////////////////////////////////////////////

Preloader::Preloader(GstreamerPlayer* player, const std::string url) throw()
    : RunnableInterface()
    , m_player(player)
    , m_fileUrl(url)
{
    DEBUG_FUNC_INFO

    player->m_stopPreloader = false;
}


Preloader::~Preloader() throw()
{
    DEBUG_FUNC_INFO
}


void Preloader::run() throw()
{
    DEBUG_BLOCK

    GstreamerPlayer* const p = m_player;
    const std::string fileUrl(m_fileUrl);

    const bool isSmil = fileUrl.substr(fileUrl.size()-5, fileUrl.size()) == ".smil" ? true : false;
    if (!isSmil)
        return;

    debug() << "Preloading SMIL file: " << fileUrl << endl;

    std::string filePath;

    if (fileUrl.find("file://") == 0) {
        filePath = fileUrl.substr(7, fileUrl.size());
    }
    else if (fileUrl.find("file:") == 0) {
        filePath = fileUrl.substr(5, fileUrl.size());
    }
    else {
        return;
    }

    if (!p->m_preloadUrl.empty()) {
        p->m_preloadUrl.clear();
        g_object_unref(G_OBJECT(p->m_preloadFilesrc));
        g_object_unref(G_OBJECT(p->m_preloadDecoder));
    }

    p->m_preloadFilesrc = gst_element_factory_make("filesrc", NULL);
    gst_element_set(p->m_preloadFilesrc, "location", filePath.c_str(), NULL);

    p->m_preloadDecoder = gst_element_factory_make("minimalaudiosmil", NULL);
    gst_element_set(p->m_preloadDecoder, "abort", &p->m_stopPreloader, NULL);

    GstElement* pipe     = gst_pipeline_new("pipe");
    GstElement* fakesink = gst_element_factory_make("fakesink", "fakesink");
    gst_element_link_many(p->m_preloadFilesrc, p->m_preloadDecoder, fakesink, NULL);
    gst_bin_add_many(GST_BIN(pipe), p->m_preloadFilesrc, p->m_preloadDecoder, fakesink, NULL);

    gst_element_set_state(pipe, GST_STATE_PLAYING);

    gint64 position = 0LL;
    while (position == 0LL && !p->m_stopPreloader && gst_bin_iterate(GST_BIN(pipe))) {
        GstFormat   format = GST_FORMAT_DEFAULT;
        gst_element_query(fakesink, GST_QUERY_POSITION, &format, &position);
    }

    gst_element_set_state(pipe, GST_STATE_PAUSED);

    if (p->m_stopPreloader) {
        debug() << "Aborting preloader, per request." << endl;
        g_object_unref(G_OBJECT(p->m_preloadFilesrc));
        g_object_unref(G_OBJECT(p->m_preloadDecoder));
        return;
    }

    g_object_ref(G_OBJECT(p->m_preloadFilesrc));
    g_object_ref(G_OBJECT(p->m_preloadDecoder));
    gst_bin_remove_many(GST_BIN(pipe), p->m_preloadFilesrc, p->m_preloadDecoder, NULL);
    gst_element_unlink(p->m_preloadFilesrc, fakesink);
    gst_object_unref(GST_OBJECT(pipe));
   
    p->m_preloadUrl = fileUrl;

    p->m_preloadThread.reset();
}


void Preloader::signal(int) throw()
{}


void Preloader::stop() throw()
{
    DEBUG_FUNC_INFO

    m_player->m_stopPreloader = true;
}


