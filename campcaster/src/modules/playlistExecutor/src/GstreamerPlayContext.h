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
 
 
    Author   : $Author: Kapil Agrawal$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef GstreamerPlayContext_h
#define GstreamerPlayContext_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <gst/gst.h>
#include <gst/controller/gstcontroller.h>
#include <gst/controller/gstinterpolationcontrolsource.h>

#include "SmilHandler.h"

/*------------------------------------------------------------------------------
*  User of this class must implement my_bus_callback function to receive bus callbacks.
*----------------------------------------------------------------------------*/
static gboolean
my_bus_callback (GstBus     *bus,
         GstMessage *message,
         gpointer    data);

/**
 *  A class to play audio files using Gstreamer library.
 *
 *  Usage sequence:
 *
 *  create an instance of GstreamerPlayContext object
 *  call setParentData to provide data to be returned by my_bus_callback
 *  call setAudioDevice on it
 *  call openSource on it
 *  call playContext on it
 *
 *  when done, call closeContext on it
 *  destroy instance
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class GstreamerPlayContext
{
    GstElement *m_pipeline;
    GstElement *m_source;
    GstElement *m_decoder;
    GstElement *m_sink;
    GstController *m_ctrl;
    GstElement *m_volume;
    GstInterpolationControlSource *m_ics;
    gpointer m_data;
    AudioDescription *m_audioDescription;
    std::string m_audioDevice;
	gint64 m_clipOffset;

public:

    GstreamerPlayContext(){
        m_pipeline = NULL;
        m_source = NULL;
        m_decoder = NULL;
        m_sink = NULL;
        m_ctrl = NULL;
        m_volume = NULL;
        m_ics = NULL;
        m_data = NULL;
        m_audioDescription = NULL;
        m_audioDevice = "default";
		m_clipOffset = 0;
    }

    ~GstreamerPlayContext(){
    }

    void closeContext(){
        stopContext();
        if(m_ctrl != NULL){
            g_object_unref(G_OBJECT(m_ctrl));
            m_ctrl = NULL;
        }
        if(m_ics != NULL){
            g_object_unref(G_OBJECT(m_ics));
            m_ics = NULL;
        }
        if(m_pipeline != NULL){
            gst_element_set_state (m_pipeline, GST_STATE_NULL);
            gst_bin_remove (GST_BIN (m_pipeline), m_sink);
            m_sink=NULL;
            gst_object_unref(GST_OBJECT(m_pipeline));
            m_source = NULL;
            m_decoder = NULL;
            m_volume = NULL;
            m_pipeline = NULL;
        }
        if(m_sink != NULL){
            gst_object_unref(GST_OBJECT(m_sink));
            m_sink=NULL;
        }
        if(m_audioDescription != NULL){
            m_audioDescription->release();
            delete m_audioDescription;
            m_audioDescription = NULL;
        }
		m_clipOffset = 0;
   }

    void playContext(){
        GstStateChangeReturn st = gst_element_set_state (m_pipeline, GST_STATE_PLAYING);
		if(NULL != m_audioDescription)
		{
			//enforce PLAYING state in case this was an asynch state change
			//this is essential for seek to succeed
			if(GST_STATE_CHANGE_ASYNC == st)
			{
				GstState state, pending;
				gst_element_get_state (m_pipeline, &state, &pending, 2000000000);//just in case, do not wait for more than 2 sec				
			}
			gst_element_seek(m_pipeline, 1.0, GST_FORMAT_TIME, GST_SEEK_FLAG_FLUSH, GST_SEEK_TYPE_SET, 
				std::max(m_clipOffset, m_audioDescription->m_clipBegin)*GST_NSECOND, GST_SEEK_TYPE_SET, m_audioDescription->m_clipEnd*GST_NSECOND);
			m_clipOffset = 0;//reset clipOffset after it's been used
		}
        g_object_set(G_OBJECT(m_volume), "volume", 1.0, NULL);
    }

    void pauseContext(){
        g_object_set(G_OBJECT(m_volume), "volume", 0.0, NULL);
        gst_element_set_state (m_pipeline, GST_STATE_PAUSED);
    }

    void stopContext(){
        if(GST_IS_ELEMENT(m_volume)) g_object_set(G_OBJECT(m_volume), "volume", 0.0, NULL);
        if(GST_IS_ELEMENT(m_volume)) gst_element_set_state (m_pipeline, GST_STATE_READY);
    }

    void setParentData(gpointer data){
        m_data = data;
    }
	
	void forceEOS(){
        GstBus *bus = gst_pipeline_get_bus (GST_PIPELINE (m_pipeline));
		gst_bus_post (bus, gst_message_new_eos(GST_OBJECT(m_sink)));
        gst_object_unref (bus);
	}

    /*------------------------------------------------------------------------------
    *  Set the audio device.
    *----------------------------------------------------------------------------*/
    void setAudioDevice(const std::string &deviceName) {
        m_audioDevice = deviceName;
    }
    /*------------------------------------------------------------------------------
    *  Set the audio device.
    *----------------------------------------------------------------------------*/
    GstElement* getAudioBin() {
        return m_sink;
    }
    /*------------------------------------------------------------------------------
    *  Opens source element for the file name given.
    *----------------------------------------------------------------------------*/
    bool openSource(const gchar *fileUri) throw() {
        m_source = gst_element_make_from_uri (GST_URI_SRC, fileUri, NULL);
        if(m_source == NULL){
            return false;
        }

        prepareAudioDevice();
        
        if(m_sink==NULL){
            std::cerr << "openSource: Failed to create sink!" << std::endl;
            gst_object_unref (m_source);
            m_source = NULL;
            return false;
        }
        if(!prepareDecodebin()){
            std::cerr << "openSource: Failed to create decodebin!" << std::endl;
            if(m_sink){
                gst_object_unref (m_sink);
                m_sink = NULL;
            }
            gst_object_unref (m_source);
            m_source = NULL;
            return false;
        }
        if(!preparePipeline()){
            std::cerr << "openSource: Failed to create pipeline!" << std::endl;
            if(m_sink){
                gst_object_unref (m_sink);
                m_sink = NULL;
            }
            gst_object_unref (m_decoder);
            m_decoder = NULL;
            gst_object_unref (m_source);
            m_source = NULL;
            return false;
        }
        return true;
    }
    /*------------------------------------------------------------------------------
    *  Opens source element for the file name given.
    *----------------------------------------------------------------------------*/
    bool openSource(AudioDescription *audioDescription) throw() {
        if(audioDescription == NULL) return false;
        m_audioDescription = audioDescription;
        return openSource(m_audioDescription->m_src);
    }
    /*------------------------------------------------------------------------------
    *  Returns current stream's duration.
    *----------------------------------------------------------------------------*/
    gint64 getPlayLength() throw() {
        gint64 ns = 0LL;
        if(m_pipeline!=NULL){
            GstFormat format = GST_FORMAT_TIME;
            gst_element_query_duration(m_pipeline, &format, &ns);
            if(format != GST_FORMAT_TIME){
                ns = 0LL;
            }
        }
        return ns;
    }
    /*------------------------------------------------------------------------------
     * Offsets playback within the clip
     *---------------------------------------------------------------------------*/
    void setClipOffset(gint64 startTime){
		m_clipOffset = startTime;
    }
    
    /*------------------------------------------------------------------------------
    *  Returns current stream's position.
    *----------------------------------------------------------------------------*/
    gint64 getPosition() throw() {
        gint64 ns = 0LL;
        if(m_pipeline!=NULL){
            GstFormat format = GST_FORMAT_TIME;
            gst_element_query_position(m_pipeline, &format, &ns);
            if(format != GST_FORMAT_TIME){
                ns = 0LL;
            }
        }
        return ns;
    }
    /*------------------------------------------------------------------------------
    *  Checks if the stream is currently playing.
    *----------------------------------------------------------------------------*/
    bool isPlaying() throw() {
        GstState state;
        GstState pending;
        gst_element_get_state(m_pipeline, &state, &pending, 50000000);
        return state == GST_STATE_PLAYING;
    }

private:

    /*------------------------------------------------------------------------------
    *  Prepare audio device from the name provided previously.
    *----------------------------------------------------------------------------*/
    bool prepareAudioDevice() throw() {
        if(m_sink != NULL){
            gst_object_unref(GST_OBJECT(m_sink));
            m_sink=NULL;
        }

        GstPad *audiopad;

        // this constant checks if the audio device configuration contains the string
        // "/dev", if so the below pipeline definition uses oss.
        // Perhaps the logic can go three ways and check if the device is labled jack.
        // Or keep the if-else logic and eliminate OSS as an option as it is obsolete anyway
        // and ALSA can emulate it.
        // const bool oss = m_audioDevice.find("/dev") == 0;
        const bool autosink = m_audioDevice.find("auto") == 0;

        m_sink = gst_bin_new ("audiobin");
        if(m_sink == NULL){
            return false;
        }
        GstElement *conv = gst_element_factory_make ("audioconvert", "aconv");
        audiopad = gst_element_get_pad (conv, "sink");

        // set the string to be sent to gstreamer. the option here is to set it to autoaudiosink.
        GstElement *sink = (autosink ? gst_element_factory_make("autoaudiosink", NULL) : gst_element_factory_make("alsasink", NULL));
        if(sink == NULL){
            return false;
        }
        g_object_set(G_OBJECT(sink), "device", m_audioDevice.c_str(), NULL);
        
        m_volume = gst_element_factory_make("volume", NULL);
        g_object_set(G_OBJECT(m_volume), "volume", 0.0, NULL);

        gst_bin_add_many (GST_BIN (m_sink), conv, m_volume, sink, NULL);
        gst_element_link (conv, m_volume);
        gst_element_link (m_volume, sink);
        gst_element_add_pad (m_sink, gst_ghost_pad_new ("sink", audiopad));
        gst_object_unref (audiopad);
        return true;
    }

    /*------------------------------------------------------------------------------
    *  Prepare empty pipeline.
    *----------------------------------------------------------------------------*/
    bool preparePipeline(){
        if(m_pipeline!=NULL){
            return false;//pipeline can only be created once per instance
        }
        m_pipeline = gst_pipeline_new ("pipeline");
        if(m_pipeline==NULL){
            return false;
        }

        GstBus *bus = gst_pipeline_get_bus (GST_PIPELINE (m_pipeline));
        gst_bus_add_watch (bus, my_bus_callback, m_data);
        gst_object_unref (bus);
        
        //link up all elements in the pipeline
        gst_bin_add_many (GST_BIN (m_pipeline), m_source, m_decoder, NULL);
        gst_element_link (m_source, m_decoder);
        gst_bin_add (GST_BIN (m_pipeline), m_sink);
        //lastly prepare animations if desired
        prepareAnimations();
        return true;
    }
    /*------------------------------------------------------------------------------
    *  Prepare decode bin.
    *----------------------------------------------------------------------------*/
    bool prepareDecodebin(){
        if(m_pipeline!=NULL){
            return false;//pipeline can only be created once per instance
        }
        m_decoder = gst_element_factory_make ("decodebin", NULL);
        g_signal_connect (m_decoder, "new-decoded-pad", G_CALLBACK (cb_newpad), this);
        return true;
    }
    /*------------------------------------------------------------------------------
    *  Prepare animations bin.
    *----------------------------------------------------------------------------*/
    bool prepareAnimations(){
       if(m_audioDescription && m_audioDescription->m_animations.size() > 0){
            m_ctrl = gst_controller_new (G_OBJECT (m_volume), "volume", NULL);
            if (m_ctrl == NULL) {
                return false;
            }
            GValue vol = { 0, };
            m_ics = gst_interpolation_control_source_new ();
            gst_controller_set_control_source (m_ctrl, "volume", GST_CONTROL_SOURCE (m_ics));
            // Set interpolation mode
            gst_interpolation_control_source_set_interpolation_mode (m_ics, GST_INTERPOLATE_LINEAR);//GST_INTERPOLATE_CUBIC);
            // set control values, first fade in
            g_value_init (&vol, G_TYPE_DOUBLE);
            if(m_audioDescription->m_animations[0] != NULL){
                g_value_set_double (&vol, m_audioDescription->m_animations[0]->m_from);
                gst_interpolation_control_source_set (m_ics, m_audioDescription->m_animations[0]->m_begin, &vol);
                g_value_set_double (&vol, m_audioDescription->m_animations[0]->m_to);
                gst_interpolation_control_source_set (m_ics, m_audioDescription->m_animations[0]->m_end, &vol);
                g_print("prepareAnimations: animation set begin=%d, end=%d, from=%f, to=%f\n", 
                    m_audioDescription->m_animations[0]->m_begin,
                    m_audioDescription->m_animations[0]->m_end,
                    m_audioDescription->m_animations[0]->m_from,
                    m_audioDescription->m_animations[0]->m_to);
            }
            if(m_audioDescription->m_animations.size() > 1){
                if(m_audioDescription->m_animations[1] != NULL){
                    //set fade out, between fadein and fadeout we have a hold period
                    g_value_set_double (&vol, m_audioDescription->m_animations[1]->m_from);
                    gst_interpolation_control_source_set (m_ics, m_audioDescription->m_animations[1]->m_begin, &vol);
                    g_value_set_double (&vol, m_audioDescription->m_animations[1]->m_to);
                    gst_interpolation_control_source_set (m_ics, m_audioDescription->m_animations[1]->m_end, &vol);
                    g_print("prepareAnimations: animation set begin=%d, end=%d, from=%f, to=%f\n", 
                        m_audioDescription->m_animations[1]->m_begin,
                        m_audioDescription->m_animations[1]->m_end,
                        m_audioDescription->m_animations[1]->m_from,
                        m_audioDescription->m_animations[1]->m_to);
                }
            }
        }
        return true;
    }
    /*------------------------------------------------------------------------------
    *  New pad signal callback.
    *----------------------------------------------------------------------------*/
    static void cb_newpad (GstElement *decodebin, GstPad *pad, gboolean last, gpointer data)
    {
        GstCaps *caps;
        GstStructure *str;
        GstPad *audiopad;
        audiopad = gst_element_get_pad (((GstreamerPlayContext*)data)->getAudioBin(), "sink");
        if (GST_PAD_IS_LINKED (audiopad)) {
            g_object_unref (audiopad);
            return;
        }
        caps = gst_pad_get_caps (pad);
        str = gst_caps_get_structure (caps, 0);
        if (!g_strrstr (gst_structure_get_name (str), "audio")) {
            gst_caps_unref (caps);
            gst_object_unref (audiopad);
            return;
        }
        gst_caps_unref (caps);
        gst_pad_link (pad, audiopad);
    }
};



#endif // GstreamerPlayContext_h

