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
    Version  : $Revision: 1.5 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/partial-play.c,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_STRING_H
#include <string.h>
#else
#error need string.h
#endif


#include <gst/gst.h>

#include "partial-play.h"
#include "smil-util.h"


/* ===================================================  local data structures */

/**
 *  The arguments this element supports.
 */
enum {
  ARG_0,
  ARG_LOCATION,
  ARG_CONFIG
};

/**
 *  The factory for the source pad.
 */
static GstStaticPadTemplate src_factory = GST_STATIC_PAD_TEMPLATE (
                                            "src",
                                            GST_PAD_SRC,
                                            GST_PAD_ALWAYS,
                                            GST_STATIC_CAPS("audio/x-raw-int, "
                                               "width = (int) 16, "
                                               "depth = (int) 16, "
                                               "endianness = (int) BYTE_ORDER, "
                                               "channels = (int) { 1, 2 }, "
                                               "rate = (int) [ 8000, 96000 ]")
);

/**
 *  The parent class of PartialPlay.
 */
static GstBinClass    * parent_class = NULL;

/**
 *  The plugin definition structure.
 */
GST_PLUGIN_DEFINE (
    GST_VERSION_MAJOR,
    GST_VERSION_MINOR,
    "partialplay",
    "Partial play",
    plugin_init,
    "$Revision: 1.5 $",
    "GPL",
    "LiveSupport",
    "http://livesupport.campware.org/"
)

/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */

/**
 *  Initializer function for the PartialPlay class.
 *
 *  @para klass the class to initialize.
 */
static void
livesupport_partial_play_class_init(LivesupportPartialPlayClass   * klass);

/**
 *  Base initializer for the PartialPlay plugin.
 *
 *  @param klass the PartialPlay class.
 */
static void
livesupport_partial_play_base_init(LivesupportPartialPlayClass    * klass);

/**
 *  Signal handler for the eos event of the SeekPack->bin element.
 *
 *  @param element the element emitting the eos signal
 *  @param userData pointer to the container bin of the switcher,
 *         which is this PartialPlay element
 */
static void
seek_pack_eos_signal_handler(GstElement     * element,
                             gpointer         userData);

/**
 *  PartialPlay instance initializer.
 *
 *  @param pplay the PartialPlay object to initialize.
 */
static void
livesupport_partial_play_init(LivesupportPartialPlay      * pplay);

/**
 *  Set a property for a PartialPlay object.
 *
 *  @param object the PartialPlay object.
 *  @param prop_id the property id.
 *  @param value the value to set.
 *  @param pspec the property specification
 */
static void
livesupport_partial_play_set_property(GObject         * object,
                                      guint             prop_id,
                                      const GValue    * value,
					                  GParamSpec      * pspec);

/**
 *  Get a property from a PartialPlay object.
 *
 *  @param object the PartialPlay object.
 *  @param prop_id the property id.
 *  @param value the value to return the property in (out parameter).
 *  @param pspec the property specification.
 */
static void
livesupport_partial_play_get_property(GObject     * object,
                                      guint         prop_id,
                                      GValue      * value,
						              GParamSpec  * pspec);

/**
 *  Return the type structure for the PartialPlay plugin.
 *
 *  @return the type structure for the PartialPlay plugin.
 */
GType
livesupport_partial_play_get_type(void);

/**
 *  Handle the state change on a PartialPlay object.
 *
 *  @param element the PartialPlay object.
 *  @return GST_STATE_SUCCES if the state change was successful, 
 *          GST_STATE_FAILURE on failure.
 */
static GstElementStateReturn
livesupport_partial_play_change_state(GstElement * element);

/**
 *  Update the source configration for a PartialPlay object.
 *  This should be called after each update to the config string.
 *
 *  @param pplay the partial play object.
 */
static void
update_source_config(LivesupportPartialPlay   * pplay);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the type structure for the PartialPlay plugin.
 *----------------------------------------------------------------------------*/
GType
livesupport_partial_play_get_type(void)
{
    static GType plugin_type = 0;

    if (!plugin_type) {
        static const GTypeInfo plugin_info = {
            sizeof (LivesupportPartialPlayClass),
            (GBaseInitFunc) livesupport_partial_play_base_init,
            NULL,
            (GClassInitFunc) livesupport_partial_play_class_init,
            NULL,
            NULL,
            sizeof (LivesupportPartialPlay),
            0,
            (GInstanceInitFunc) livesupport_partial_play_init,
        };

        plugin_type = g_type_register_static(GST_TYPE_BIN,
	                                         "LivesupportPartialPlay",
	                                         &plugin_info, 0);
    }

    return plugin_type;
}


/*------------------------------------------------------------------------------
 *  Handle the state change for a partial play object.
 *----------------------------------------------------------------------------*/
static GstElementStateReturn
livesupport_partial_play_change_state(GstElement * element)
{
    LivesupportPartialPlay   * pplay;

    pplay = LIVESUPPORT_PARTIAL_PLAY(element);

    switch (GST_STATE_TRANSITION (element)) {
        case GST_STATE_NULL_TO_READY:
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_READY);
            break;

        case GST_STATE_READY_TO_PAUSED:
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_PAUSED);
            break;

        case GST_STATE_PAUSED_TO_PLAYING:
            if (!pplay->seekPackInited) {
                GstPad               * srcPad;
                const GstCaps        * caps;

                pplay->seekPackInited = TRUE;
                if (pplay->source) {
                    g_object_unref(pplay->source);
                }
                /* TODO: check for NULL returns here */
                pplay->source = gst_element_factory_make("filesrc", "source");
                g_object_set(G_OBJECT(pplay->source),
                             "location",
                             pplay->location,
                             NULL);
                srcPad = gst_element_get_pad((GstElement *) pplay, "src");
                caps   = gst_pad_get_caps(srcPad);

                livesupport_seek_pack_init(pplay->seekPack,
                                           pplay->source,
                                           caps,
                                           pplay->silenceDuration,
                                           pplay->playFrom,
                                           pplay->playTo);
            }
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_PLAYING);
            break;

        case GST_STATE_PLAYING_TO_PAUSED:
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_PAUSED);
            break;
            
        case GST_STATE_PAUSED_TO_READY:
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_READY);
            /* TODO: maybe de-init seekPack somehow? */
            break;
            
        case GST_STATE_READY_TO_NULL:
            livesupport_seek_pack_set_state(pplay->seekPack, GST_STATE_NULL);
            break;

        default:
            break;
    }

    if (GST_ELEMENT_CLASS(parent_class)->change_state) {
        return GST_ELEMENT_CLASS(parent_class)->change_state(element);
    }

    return GST_STATE_SUCCESS;
}


/*------------------------------------------------------------------------------
 *  Do base initialization.
 *----------------------------------------------------------------------------*/
static void
livesupport_partial_play_base_init(LivesupportPartialPlayClass    * klass)
{
    static GstElementDetails plugin_details = {
        "PartialPlay",
        "Audio/PartialPlay",
        "A filter that plays an audio source partially",
        "Akos Maroy <maroy@campware.org>"
    };
    GstElementClass       * element_class = GST_ELEMENT_CLASS(klass);

    gst_element_class_add_pad_template(element_class,
	                                gst_static_pad_template_get(&src_factory));
    gst_element_class_set_details(element_class, &plugin_details);
}


/*------------------------------------------------------------------------------
 *  Initialize the plugin's class
 *----------------------------------------------------------------------------*/
static void
livesupport_partial_play_class_init(LivesupportPartialPlayClass   * klass)
{
    GObjectClass      * gobject_class;
    GstElementClass   * gstelement_class;

    gobject_class    = (GObjectClass*) klass;
    gstelement_class = (GstElementClass*) klass;

    parent_class     = g_type_class_ref(GST_TYPE_BIN);

    g_object_class_install_property(gobject_class,
                                    ARG_LOCATION,
                                    g_param_spec_string("location",
                                                        "Location",
                                                 "Location of the file to read",
                                                         "",
                                                         G_PARAM_READWRITE));

    g_object_class_install_property(gobject_class,
                                    ARG_CONFIG,
                                    g_param_spec_string("config",
                                                        "Play configuration",
                                         "specify the silence and play details",
                                                         "",
                                                         G_PARAM_READWRITE));

    gobject_class->set_property = livesupport_partial_play_set_property;
    gobject_class->get_property = livesupport_partial_play_get_property;

    gstelement_class->change_state = livesupport_partial_play_change_state;
}


/*------------------------------------------------------------------------------
 *  eos signal handler for the seekPack->bin element
 *----------------------------------------------------------------------------*/
static void
seek_pack_eos_signal_handler(GstElement     * element,
                             gpointer         userData)
{
    GstElement    * container = GST_ELEMENT(userData);

    g_return_if_fail(container != NULL);
    g_return_if_fail(GST_IS_ELEMENT(container));

    /* set the container into eos state */

    GST_DEBUG("SeekPack.bin setting PartialPlay to eos");
    gst_element_set_eos(container);
}


/*------------------------------------------------------------------------------
 *  Initialize a new PartialPlay element.
 *----------------------------------------------------------------------------*/
static void
livesupport_partial_play_init(LivesupportPartialPlay  * pplay)
{
    pplay->seekPack       = livesupport_seek_pack_new("seekPack");
    pplay->seekPackInited = FALSE;

    livesupport_seek_pack_add_to_bin(pplay->seekPack, GST_BIN(pplay));
    pplay->srcpad = gst_element_add_ghost_pad(GST_ELEMENT(pplay),
                            gst_element_get_pad(pplay->seekPack->bin, "src"),
                            "src");

    g_signal_connect(pplay->seekPack->bin,
                     "eos",
                     G_CALLBACK(seek_pack_eos_signal_handler),
                     pplay);

    /* TODO: free these strings when disposing of the object */
    pplay->location        = g_strdup("");
    pplay->config          = g_strdup("");
    pplay->silenceDuration = 0LL;
    pplay->playFrom        = 0LL;
    pplay->playTo          = 0LL;
}


/*------------------------------------------------------------------------------
 *  Update the source config.
 *----------------------------------------------------------------------------*/
static void
update_source_config(LivesupportPartialPlay   * pplay)
{
    gchar    ** tokens;
    gchar     * token;
    guint       i = 0;

    tokens  = g_strsplit(pplay->config, ";", 0);
    if ((token = tokens[i++])) {
        pplay->silenceDuration = smil_clock_value_to_nanosec(token);
    }
    if ((token = tokens[i++])) {
        gint        len  = strlen(token);
        gchar     * from = g_malloc(sizeof(gchar) * len + 1);
        gchar     * to   = g_malloc(sizeof(gchar) * len + 1);

        if (sscanf(token, "%[^-]-%s", from, to) == 2) {
            pplay->playFrom = smil_clock_value_to_nanosec(from);
            pplay->playTo   = smil_clock_value_to_nanosec(to);
        } else if (sscanf(token, "%[^-]-", from) == 1) {
            pplay->playFrom = smil_clock_value_to_nanosec(from);
            pplay->playTo   = -1LL;
        }

        g_free(to);
        g_free(from);
    }

    g_strfreev(tokens);
}

/*------------------------------------------------------------------------------
 *  Set a property.
 *----------------------------------------------------------------------------*/
static void
livesupport_partial_play_set_property(GObject         * object,
                                      guint             prop_id,
                                      const GValue    * value,
                                      GParamSpec      * pspec)
{
    LivesupportPartialPlay    * pplay;

    g_return_if_fail(LIVESUPPORT_IS_PARTIAL_PLAY(object));
    pplay = LIVESUPPORT_PARTIAL_PLAY(object);

    switch (prop_id) {
        case ARG_LOCATION:
            if (pplay->location) {
                g_free(pplay->location);
            }
            pplay->location = g_strdup(g_value_get_string(value));
            break;

        case ARG_CONFIG:
            if (pplay->config) {
                g_free(pplay->config);
            }
            pplay->config = g_strdup(g_value_get_string(value));
            update_source_config(pplay);
            break;

        default:
            G_OBJECT_WARN_INVALID_PROPERTY_ID(object, prop_id, pspec);
            break;
    }
}

/*------------------------------------------------------------------------------
 *  Get a property.
 *----------------------------------------------------------------------------*/
static void
livesupport_partial_play_get_property(GObject     * object,
                                      guint         prop_id,
                                      GValue      * value,
                                      GParamSpec  * pspec)
{
    LivesupportPartialPlay    * pplay;

    g_return_if_fail(LIVESUPPORT_IS_PARTIAL_PLAY(object));
    pplay = LIVESUPPORT_PARTIAL_PLAY(object);

    switch (prop_id) {
        case ARG_LOCATION:
            g_value_set_string(value, pplay->location);
            break;

        case ARG_CONFIG:
            g_value_set_string(value, pplay->config);
            break;

        default:
            G_OBJECT_WARN_INVALID_PROPERTY_ID(object, prop_id, pspec);
            break;
    }
}

/*------------------------------------------------------------------------------
 *  Initialize the plugin.
 *----------------------------------------------------------------------------*/
static gboolean
plugin_init(GstPlugin     * plugin)
{
    return gst_element_register(plugin,
                                "partialplay",
			                    GST_RANK_NONE,
			                    LIVESUPPORT_TYPE_PARTIAL_PLAY);
}

