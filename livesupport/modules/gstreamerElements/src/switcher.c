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
    Version  : $Revision: 1.1 $
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
    ARG_SOURCE_CONFIG
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
    "$Revision: 1.1 $",
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
                                                      "sink",
                                                      GST_PAD_SINK,
                                                      GST_PAD_ALWAYS,
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
 *  The main chain function of the Switcher element.
 *
 *  @param pad the pad on which data is received.
 *  @param in the data recieved.
 */
static void
livesupport_switcher_chain(GstPad     * pad,
                           GstData    * in);

/**
 *  This function handles the link with other plug-ins.
 *
 *  @param pad the pad that is about to be linked.
 *  @param caps the set of possible linking capabilities 
 *  @return GST_PAD_LINK_OK or GST_PAD_LINK_DONE if linking can be or has
 *          been done, GST_PAD_LINK_DELAYED if linking can not yet be done,
 *          GST_PAD_LINK_REFUSED in linking can not be done.
 */
static GstPadLinkReturn
livesupport_switcher_link(GstPad          * pad,
                          const GstCaps   * caps);

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


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  This function handles the link with other plug-ins.
 *----------------------------------------------------------------------------*/
static GstPadLinkReturn
livesupport_switcher_link(GstPad          * pad,
                          const GstCaps   * caps)
{
    LivesupportSwitcher   * filter;
    GstPad                * otherpad;

    filter = LIVESUPPORT_SWITCHER(gst_pad_get_parent(pad));
    g_return_val_if_fail(filter != NULL, GST_PAD_LINK_REFUSED);
    g_return_val_if_fail(LIVESUPPORT_IS_SWITCHER(filter),
                         GST_PAD_LINK_REFUSED);
    otherpad = (pad == filter->srcpad ? filter->sinkpad : filter->srcpad);

    /* set caps on next or previous element's pad, and see what they
     * think. In real cases, we would (after this step) extract
     * properties from the caps such as video size or audio samplerat. */
    return gst_pad_try_set_caps (otherpad, caps);
}


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
    GstElementClass   * gstelement_class;

    gobject_class    = (GObjectClass*) klass;
    gstelement_class = (GstElementClass*) klass;

    parent_class = g_type_class_ref(GST_TYPE_ELEMENT);

    g_object_class_install_property(gobject_class,
                                    ARG_SOURCE_CONFIG,
                                    g_param_spec_string("source_config",
                                                        "source config",
                                                        "source config",
                                                        "",
                                                        G_PARAM_READWRITE));

    gobject_class->set_property = livesupport_switcher_set_property;
    gobject_class->get_property = livesupport_switcher_get_property;
}


/*------------------------------------------------------------------------------
 *  Initialize a Switcher object.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_init(LivesupportSwitcher * switcher)
{
    GstElementClass   * klass = GST_ELEMENT_GET_CLASS(switcher);

    switcher->sinkpad = gst_pad_new_from_template(
	                        gst_element_class_get_pad_template(klass, "sink"),
                            "sink");
    gst_pad_set_link_function(switcher->sinkpad, livesupport_switcher_link);
    gst_pad_set_getcaps_function(switcher->sinkpad, gst_pad_proxy_getcaps);

    switcher->srcpad = gst_pad_new_from_template (
	                        gst_element_class_get_pad_template(klass, "src"),
                            "src");
    gst_pad_set_link_function(switcher->srcpad, livesupport_switcher_link);
    gst_pad_set_getcaps_function(switcher->srcpad, gst_pad_proxy_getcaps);

    gst_element_add_pad(GST_ELEMENT(switcher), switcher->sinkpad);
    gst_element_add_pad(GST_ELEMENT(switcher), switcher->srcpad);
    gst_pad_set_chain_function(switcher->sinkpad, livesupport_switcher_chain);

    switcher->elapsedTime      = 0LL;
    switcher->nextOffset       = 0LL;
    switcher->eos              = FALSE;
    /* TODO: dispose of the config string and list later */
    switcher->sourceConfig     = 0;
    switcher->sourceConfigList = NULL;
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
        GValue          gvalue = { 0 };
        GstElement    * switchElement;

        GST_INFO("switching from source %d, duration: %" G_GINT64_FORMAT,
                oldConfig->sourceId, oldConfig->duration);

        newConfig = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;

        switchElement = GST_PAD_PARENT(GST_PAD_PEER(switcher->sinkpad));
        g_value_init(&gvalue, G_TYPE_INT);
        g_value_set_int(&gvalue, newConfig->sourceId);
        gst_element_set_property(switchElement, "active-source", &gvalue);

        switcher->nextOffset = oldConfig->duration >= 0
                             ? switcher->nextOffset + newConfig->duration
                             : switcher->elapsedTime + newConfig->duration;
    } else {
        /* mark EOS, as there are no more sources to switch to */
        switcher->eos = TRUE;
    }
}


/*------------------------------------------------------------------------------
 *  The main chain function.
 *----------------------------------------------------------------------------*/
static void
livesupport_switcher_chain(GstPad     * pad,
                           GstData    * in)
{
    LivesupportSwitcher               * switcher;
    GstBuffer                         * buf;
    LivesupportSwitcherSourceConfig   * config = NULL;

    g_return_if_fail(GST_IS_PAD(pad));
    g_return_if_fail(in != NULL);

    switcher = LIVESUPPORT_SWITCHER(GST_OBJECT_PARENT(pad));
    g_return_if_fail(LIVESUPPORT_IS_SWITCHER(switcher));

    if (switcher->eos) {
        GstElement    * parent;
        GstEvent      * event;

        GST_DEBUG("switcher_chain: eos");
        /* push an EOS event down the srcpad, just to make sure */
        event = gst_event_new(GST_EVENT_EOS);
        gst_pad_send_event(switcher->srcpad, event);
        gst_element_set_eos(GST_ELEMENT(switcher));

        parent = GST_ELEMENT(gst_element_get_parent(GST_ELEMENT(switcher)));
        if (parent) {
            gst_element_set_eos(parent);
        }
        /* TODO: fix this mess here... */
        /*
        for (parent =
                 GST_ELEMENT(gst_element_get_parent(GST_ELEMENT(switcher)));
             parent;
             parent = GST_ELEMENT(gst_element_get_parent(parent))) {

            GST_DEBUG("switcher_chain: eos #1.1");
            gst_element_set_eos(parent);
        }
        */

        return;
    }

    if (switcher->currentConfig == NULL) {
        switcher->currentConfig = g_list_first(switcher->sourceConfigList);
        config                  = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;
        switcher->nextOffset = config->duration;
    } else {
        config = (LivesupportSwitcherSourceConfig*)
                                                switcher->currentConfig->data;
    }

    if (config->duration < 0) {
        /* handle config->duration == -1LL (play until EOS) */
        if (GST_IS_EVENT(in)) {
            GstEvent  * event = GST_EVENT(in);

            if (GST_EVENT_TYPE(event) == GST_EVENT_EOS) {
                switch_to_next_source(switcher);
                return;
            }
        }
    }

    if (GST_IS_EVENT(in)) {
        /* handle events */
        GstEvent  * event = GST_EVENT(in);

        gst_pad_event_default(switcher->srcpad, event);
        return;
    }

    buf = GST_BUFFER(in);
    if (GST_BUFFER_DURATION(buf) != GST_CLOCK_TIME_NONE) {
        switcher->elapsedTime += GST_BUFFER_DURATION(buf);
    }
    GST_INFO("elapsed time: %" G_GINT64_FORMAT, switcher->elapsedTime);

    if (switcher->elapsedTime >= switcher->nextOffset) {
        /* time to switch to the next source */
        switch_to_next_source(switcher);
    }

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

