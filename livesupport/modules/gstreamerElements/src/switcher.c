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
    Version  : $Revision: 1.10 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/switcher.c,v $

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

#include "switcher.h"
#include "smil-util.h"

/* ===================================================  local data structures */

/**
 *  The properties of the Switcher element.
 */
enum {
    ARG_0,
    ARG_SOURCE_CONFIG,
    ARG_CAPS
};

/**
 *  The plugin registry definition.
 */
GST_PLUGIN_DEFINE (
    GST_VERSION_MAJOR,
    GST_VERSION_MINOR,
    "switcher",
    "A filter that connects to a swtich, and changes its source",
    plugin_init,
    "$Revision: 1.10 $",
    "GPL",
    "LiveSupport",
    "http://livesupport.campware.org/"
)


/* ================================================  local constants & macros */

/**
 *  The parent class of the Switcher class.
 */
static GstElementClass    * parent_class = NULL;

/**
 *  The sink pad factory.
 */
static GstStaticPadTemplate sink_factory = GST_STATIC_PAD_TEMPLATE(
                                                      "sink_%d",
                                                      GST_PAD_SINK,
                                                      GST_PAD_REQUEST,
                                                      GST_STATIC_CAPS("ANY"));

/**
 *  The source pad factory.
 */
static GstStaticPadTemplate src_factory = GST_STATIC_PAD_TEMPLATE(
                                                      "src",
                                                      GST_PAD_SRC,
                                                      GST_PAD_ALWAYS,
                                                      GST_STATIC_CAPS("ANY"));


/* ===============================================  local function prototypes */

/**
 *  Initialize the Switcher class.
 *
 *  @param klass the class to initialize
 */
static void
livesupport_switcher_class_init(LivesupportSwitcherClass  * klass);

/**
 *  Base initialization for Switcher objects.
 *
 *  @param klass a Switcher class
 */
static void
livesupport_switcher_base_init(LivesupportSwitcherClass   * klass);

/**
 *  Initialize a Switcher object.
 *
 *  @param switcher the Switcher object to initialize.
 */
static void
livesupport_switcher_init(LivesupportSwitcher * switcher);

/**
 *  Destroy a Switcher object.
 *
 *  @param object the Switcher object to destroy.
 */
static void
livesupport_switcher_dispose(GObject * object);

/**
 *  Return the capabilities of a pad.
 *
 *  @pad the pad to return the capabilities for.
 *  @return the capabilities of pad.
 */
static GstCaps *
livesupport_switcher_get_caps(GstPad      * pad);

/**
 *  Set the capabilities for this switcher element.
 *
 *  @param switcher the switcher to set the capabilities for.
 *  @param caps the capabilities to set.
 */
static void
livesupport_switcher_set_caps(LivesupportSwitcher     * switcher,
                              const GstCaps           * caps);

/**
 *  Set a property on a Switcher object.
 *
 *  @param object a Switcher object
 *  @param prop_id the property id
 *  @param value the value to set
 *  @param pspec the property specification
 */
static void
livesupport_switcher_set_property(GObject         * object,
                                  guint             prop_id,
                                  const GValue    * value,
					              GParamSpec      * pspec);

/**
 *  Get a property from a Switcher object.
 *
 *  @param object a Switcher object.
 *  @param prop_id the property id
 *  @param value the requested property (an out parameter)
 *  @param pspec the property specification
 */
static void
livesupport_switcher_get_property(GObject     * object,
                                  guint         prop_id,
                                  GValue      * value,
						          GParamSpec  * pspec);

/**
 *  The main loop function of the Switcher element.
 *
 *  @param element the Switcher element to loop on.
 *  @param in the data recieved.
 */
static void
livesupport_switcher_loop(GstElement      * element);

/**
 *  Switch to the source next in line.
 *  Call this function if it's time to switch to the next source.
 *
 *  @param switcher a Switcher object to swtich on.
 */
static void
switch_to_next_source(LivesupportSwitcher     * switcher);

/**
 *  Update the source configuration.
 *  Call this function when the sourceConfigList string has been updated.
 *
 *  @param switcher a Switcher object to perform the update on.
 */
static void
update_source_config(LivesupportSwitcher   * switcher);

/**
 *  Create a new pad upon request.
 *
 *  @param element a Switcher element, for which to create the new pas.
 *  @param template the pad template to use for the new pad.
 *  @param name the requested name of the new pad.
 *  @return the new, requested pad.
 */
static GstPad *
request_new_pad(GstElement        * element,
                GstPadTemplate    * template,
                const gchar       * name);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the type structure for the Switcher plugin.
 *----------------------------------------------------------------------------*/
GType
livesupport_switcher_get_type(void)
{
    static GType plugin_type = 0;

    if (!plugin_type) {
        static const GTypeInfo plugin_info = {
            sizeof (LivesupportSwitcherClass),
            (GBaseInitFunc) livesupport_switcher_base_init,
            NULL,
            (GClassInitFunc) livesupport_switcher_class_init,
            NULL,
            NULL,
            sizeof (LivesupportSwitcher),
            0,
            (GInstanceInitFunc) livesupport_switcher_init,
        };

        plugin_type = g_type_register_static(GST_TYPE_ELEMENT,
	                                         "LivesupportSwitcher",
	                                         &plugin_info, 0);
    }

    return plugin_type;
}


/*------------------------------------------------------------------------------
 *  Do base initialization for the Switcher class.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_base_init(LivesupportSwitcherClass   * klass)
{
    static GstElementDetails plugin_details = {
        "Switcher",
        "Generic/Switcher",
       "A plugin that is connected to a switch element, and changes its source",
        "Akos Maroy <maroy@campware.org>"
    };
    GstElementClass   * element_class = GST_ELEMENT_CLASS (klass);

    gst_element_class_add_pad_template(element_class,
                                gst_static_pad_template_get(&src_factory));
    gst_element_class_add_pad_template(element_class,
	                            gst_static_pad_template_get(&sink_factory));
    gst_element_class_set_details(element_class, &plugin_details);
}


/*------------------------------------------------------------------------------
 *  Initialize the plugin's class.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_class_init(LivesupportSwitcherClass  * klass)
{
    GObjectClass      * gobject_class;
    GstElementClass   * element_class;

    gobject_class = (GObjectClass*) klass;
    element_class = (GstElementClass*) klass;

    parent_class = g_type_class_ref(GST_TYPE_ELEMENT);

    g_object_class_install_property(gobject_class,
                                    ARG_SOURCE_CONFIG,
                                    g_param_spec_string("source_config",
                                                        "source config",
                                                        "source config",
                                                        "",
                                                        G_PARAM_READWRITE));

    g_object_class_install_property(gobject_class,
                                    ARG_CAPS,
                                    g_param_spec_pointer("caps",
                                                         "pad capabilites",
                             "fix all switcher capabilities to supplied value",
                                                        G_PARAM_READWRITE));

    gobject_class->dispose      = livesupport_switcher_dispose;
    gobject_class->set_property = livesupport_switcher_set_property;
    gobject_class->get_property = livesupport_switcher_get_property;

    element_class->request_new_pad = request_new_pad;
}


/*------------------------------------------------------------------------------
 *  Create a new request pad.
 *----------------------------------------------------------------------------*/
static GstPad *
request_new_pad(GstElement        * element,
                GstPadTemplate    * template,
                const gchar       * name)
{
    LivesupportSwitcher   * switcher;
    GstPad                * pad;

    g_return_val_if_fail(element != NULL, NULL);
    g_return_val_if_fail(LIVESUPPORT_IS_SWITCHER(element), NULL);

    switcher = LIVESUPPORT_SWITCHER(element);

    if (template->direction != GST_PAD_SINK) {
        GST_DEBUG("only sink pads are created on request");
        return NULL;
    }

    pad = gst_pad_new_from_template(template, name);
    gst_pad_set_link_function(pad, gst_pad_proxy_pad_link);
    gst_pad_set_getcaps_function(pad, livesupport_switcher_get_caps);

    gst_element_add_pad(element, pad);
    /* TODO: catch the pad remove event, and remove the pad from this
     *       list as well */
    switcher->sinkpadList = g_list_append(switcher->sinkpadList, pad);

    if (GST_PAD_CAPS(switcher->srcpad)) {
        gst_pad_try_set_caps(pad, GST_PAD_CAPS(switcher->srcpad));
    }

    return pad;
}


/*------------------------------------------------------------------------------
 *  Initialize a Switcher object.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_init(LivesupportSwitcher * switcher)
{
    GstElementClass   * klass = GST_ELEMENT_GET_CLASS(switcher);

    switcher->srcpad = gst_pad_new_from_template (
	                        gst_element_class_get_pad_template(klass, "src"),
                            "src");
    gst_pad_set_link_function(switcher->srcpad, gst_pad_proxy_pad_link);
    gst_pad_set_getcaps_function(switcher->srcpad,
                                 livesupport_switcher_get_caps);

    gst_element_add_pad(GST_ELEMENT(switcher), switcher->srcpad);
    gst_element_set_loop_function(GST_ELEMENT(switcher),
                                  livesupport_switcher_loop);

    switcher->caps             = NULL;
    switcher->sinkpadList      = NULL;
    switcher->elapsedTime      = 0LL;
    switcher->offset           = 0LL;
    switcher->switchTime       = 0LL;
    switcher->eos              = FALSE;
    switcher->sourceConfig     = 0;
    switcher->sourceConfigList = NULL;
}


/*------------------------------------------------------------------------------
 *  Destroy a Switcher object.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_dispose(GObject * object)
{
    LivesupportSwitcher   * switcher = LIVESUPPORT_SWITCHER(object);

    g_return_if_fail(LIVESUPPORT_IS_SWITCHER(switcher));

    if (switcher->sinkpadList) {
        g_list_free(switcher->sinkpadList);
    }
    if (switcher->sourceConfig) {
        g_free(switcher->sourceConfig);
    }
    if (switcher->sourceConfigList) {
        GList     * element;
       
        for (element = g_list_first(switcher->sourceConfigList);
             element;
             element = g_list_next(element)) {

            g_free(element->data);
        }
        g_list_free(switcher->sourceConfigList);
    }

    if (switcher->caps) {
        gst_caps_free(switcher->caps);
    }

    G_OBJECT_CLASS(parent_class)->dispose(object);
}


/*------------------------------------------------------------------------------
 *  Return the capabilities of the switcher element.
 *----------------------------------------------------------------------------*/
static GstCaps *
livesupport_switcher_get_caps(GstPad      * pad)
{
    LivesupportSwitcher   * switcher =
                                  LIVESUPPORT_SWITCHER(gst_pad_get_parent(pad));

    return switcher->caps != NULL
         ? gst_caps_copy(switcher->caps)
         : gst_pad_proxy_getcaps(pad);
}


/*------------------------------------------------------------------------------
 *  Switch to the source next in line.
 *----------------------------------------------------------------------------*/
static void
switch_to_next_source(LivesupportSwitcher     * switcher)
{
    LivesupportSwitcherSourceConfig   * oldConfig;
    LivesupportSwitcherSourceConfig   * newConfig;

    oldConfig = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;

    if ((switcher->currentConfig = g_list_next(switcher->currentConfig))) {

        GST_INFO("switching from source %d, duration: %" G_GINT64_FORMAT,
                oldConfig->sourceId, oldConfig->duration);

        newConfig = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;

        if (!newConfig->sinkPad) {
            if (!(newConfig->sinkPad = g_list_nth_data(switcher->sinkpadList,
                                                       newConfig->sourceId))) {
                GST_ELEMENT_ERROR(GST_ELEMENT(switcher),
                                  RESOURCE,
                                  NOT_FOUND,
                                  ("can't find sinkpad for next sink"),
                                  (NULL));
            }
        }

        switcher->switchTime = oldConfig->duration >= 0
                             ? switcher->switchTime + newConfig->duration
                             : switcher->elapsedTime + newConfig->duration;
    } else {
        /* mark EOS, as there are no more sources to switch to */
        GST_INFO("no more sources after source %d, duration: %" G_GINT64_FORMAT,
                oldConfig->sourceId, oldConfig->duration);
        switcher->eos = TRUE;
    }
}


/*------------------------------------------------------------------------------
 *  The main loop function.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_loop(GstElement      * element)
{
    LivesupportSwitcher               * switcher;
    GstData                           * data;
    GstBuffer                         * buf;
    LivesupportSwitcherSourceConfig   * config = NULL;

    g_return_if_fail(element != NULL);
    g_return_if_fail(LIVESUPPORT_IS_SWITCHER(element));

    switcher = LIVESUPPORT_SWITCHER(element);

    if (switcher->eos) {
        GstEvent      * event;

        GST_INFO("switcher_loop: eos");
        /* push an EOS event down the srcpad, just to make sure */
        event = gst_event_new(GST_EVENT_EOS);
        gst_pad_send_event(switcher->srcpad, event);
        gst_element_set_eos(GST_ELEMENT(switcher));

        return;
    }

    if (switcher->currentConfig == NULL) {
        /* if this is the very first call to the loop function */
        switcher->currentConfig = g_list_first(switcher->sourceConfigList);
        config                  = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;
        switcher->switchTime = config->duration;
        if (!config->sinkPad) {
            if (!(config->sinkPad = g_list_nth_data(switcher->sinkpadList,
                                                    config->sourceId))) {
                GST_ELEMENT_ERROR(GST_ELEMENT(switcher),
                                  RESOURCE,
                                  NOT_FOUND,
                                  ("can't find sinkpad for first sink"),
                                  (NULL));
            }
        }
    } else {
        config = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;
    }

    data = gst_pad_pull(config->sinkPad);

    if (config->duration < 0LL) {
        /* handle config->duration == -1LL (play until EOS) */
        if (GST_IS_EVENT(data)) {
            GstEvent  * event = GST_EVENT(data);

            GST_INFO("handling event type %d", GST_EVENT_TYPE(event));

            switch (GST_EVENT_TYPE(event)) {
                case GST_EVENT_EOS:
                    switch_to_next_source(switcher);
                    break;

                case GST_EVENT_FLUSH:
                    /* silently discard flush events
                     * this is because when having an Ogg Vorbis source
                     * as the second source, the flush event will indefinately
                     * bounce back and forward, and the filesrc will regenerate
                     * new flush events ad infinitum */
                    break;

                default:
                    gst_pad_event_default(switcher->srcpad, event);
            }
        } else {
            buf = GST_BUFFER(data);

            if (GST_BUFFER_DURATION(buf) != GST_CLOCK_TIME_NONE) {
                switcher->elapsedTime += GST_BUFFER_DURATION(buf);
                GST_BUFFER_TIMESTAMP(buf) = switcher->elapsedTime;
            }
            switcher->offset += GST_BUFFER_SIZE(buf);
            GST_INFO("elapsed time: %" G_GINT64_FORMAT, switcher->elapsedTime);

            GST_BUFFER_OFFSET(buf)    = switcher->offset;

            /* just push out the incoming buffer without touching it */
            gst_pad_push(switcher->srcpad, GST_DATA(buf));
        }
        return;
    }

    if (GST_IS_EVENT(data)) {
        /* handle events */
        GstEvent  * event = GST_EVENT(data);

        GST_INFO("handling event type %d", GST_EVENT_TYPE(event));

        switch (GST_EVENT_TYPE(event)) {
            case GST_EVENT_FLUSH:
                /* silently discard flush events
                 * this is because when having an Ogg Vorbis source
                 * as the second source, the flush event will indefinately
                 * bounce back and forward, and the filesrc will regenerate
                 * new flush events ad infinitum */
                break;

            default:
                gst_pad_event_default(switcher->srcpad, event);
        }

        return;
    }

    buf = GST_BUFFER(data);
    if (GST_BUFFER_DURATION(buf) != GST_CLOCK_TIME_NONE) {
        switcher->elapsedTime += GST_BUFFER_DURATION(buf);
    }
    switcher->offset += GST_BUFFER_SIZE(buf);
    GST_INFO("elapsed time: %" G_GINT64_FORMAT, switcher->elapsedTime);

    if (switcher->elapsedTime >= switcher->switchTime) {
        /* time to switch to the next source */
        switch_to_next_source(switcher);
    }

    GST_INFO("pushing data size: %d, duration: %" G_GINT64_FORMAT,
             GST_BUFFER_SIZE(buf), GST_BUFFER_DURATION(buf));

    GST_BUFFER_TIMESTAMP(buf) = switcher->elapsedTime;
    GST_BUFFER_OFFSET(buf)    = switcher->offset;

    /* just push out the incoming buffer without touching it */
    gst_pad_push(switcher->srcpad, GST_DATA(buf));
}


/*------------------------------------------------------------------------------
 *  Update the source config.
 *----------------------------------------------------------------------------*/
static void
update_source_config(LivesupportSwitcher   * switcher)
{
    gchar    ** tokens;
    gchar     * token;
    guint       i = 0;
    GList     * listElem;

    /* first free the config list */
    for (listElem = g_list_first(switcher->sourceConfigList);
         listElem;
         listElem = g_list_next(listElem)) {
        g_free(listElem->data);
    }
    g_list_free(switcher->sourceConfigList);
    switcher->sourceConfigList = NULL;
    switcher->currentConfig    = NULL;
    listElem                   = NULL;

    tokens  = g_strsplit(switcher->sourceConfig, ";", 0);
    while ((token = tokens[i++])) {
        gchar                             * durationStr;
        gint                                len;
        LivesupportSwitcherSourceConfig   * config;
        gboolean                            found;

        len         = strlen(token);
        durationStr = g_malloc(sizeof(gchar) * len + 1);
        config      = g_malloc(sizeof(LivesupportSwitcherSourceConfig));
        found       = FALSE;

        /* token formats can be:
         *
         * id[]             play the whole thing, until EOS
         * id[duration]     play from begin for duration time
         */
        if (g_str_has_suffix(token, "[]")
         && sscanf(token, "%d[]", &config->sourceId) == 1) {
            config->duration = -1LL;
            found            = TRUE;
        } else if (sscanf(token, "%d[%[^]]]",
                          &config->sourceId, durationStr) == 2) {
            config->duration = smil_clock_value_to_nanosec(durationStr);
            found            = config->duration >= 0;
        }
        /* don't fill in the sinkPad yet, as it may be added later */
        config->sinkPad = NULL;

        g_free(durationStr);

        if (!found) {
            g_free(config);
            continue;
        }

        switcher->sourceConfigList = g_list_append(switcher->sourceConfigList,
                                                   config);
        listElem = g_list_last(switcher->sourceConfigList);
    }

    g_strfreev(tokens);
}


/*------------------------------------------------------------------------------
 *  Set the capabilities of this switcher element.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_set_caps(LivesupportSwitcher     * switcher,
                              const GstCaps           * caps)
{
    if (!switcher || !LIVESUPPORT_IS_SWITCHER(switcher)
     || !caps || !GST_IS_CAPS(caps)) {

        return;
    }

    if (switcher->caps) {
        gst_caps_free(switcher->caps);
    }

    switcher->caps = gst_caps_copy(caps);
}


/*------------------------------------------------------------------------------
 *  Set a property.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_set_property(GObject         * object,
                                  guint             prop_id,
                                  const GValue    * value,
                                  GParamSpec      * pspec)
{
    LivesupportSwitcher   * switcher;

    g_return_if_fail(LIVESUPPORT_IS_SWITCHER(object));
    switcher = LIVESUPPORT_SWITCHER(object);

    switch (prop_id) {
        case ARG_SOURCE_CONFIG:
            if (switcher->sourceConfig) {
                g_free(switcher->sourceConfig);
            }
            switcher->sourceConfig = g_value_dup_string(value);
            update_source_config(switcher);
            break;

        case ARG_CAPS:
            livesupport_switcher_set_caps(switcher, g_value_get_pointer(value));
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
livesupport_switcher_get_property(GObject     * object,
                                  guint         prop_id,
                                  GValue      * value,
                                  GParamSpec  * pspec)
{
    LivesupportSwitcher   * switcher;

    g_return_if_fail(LIVESUPPORT_IS_SWITCHER(object));
    switcher = LIVESUPPORT_SWITCHER(object);

    switch (prop_id) {
        case ARG_SOURCE_CONFIG:
            g_value_set_string(value, switcher->sourceConfig);
            break;

        case ARG_CAPS:
            g_value_set_pointer(value, switcher->caps);
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
                              "switcher",
			                  GST_RANK_NONE,
			                  LIVESUPPORT_TYPE_SWITCHER);
}

