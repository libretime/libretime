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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/minimal-audio-smil.c,v $

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


#include <gst/bytestream/bytestream.h>

#include <libxml/parser.h>
#include <libxml/tree.h>

#include "minimal-audio-smil.h"


/* ===================================================  local data structures */

/**
 *  ElementFactory information
 */
static GstElementDetails livesupport_minimal_audio_smil_details =
             GST_ELEMENT_DETAILS("MinimalAudioSmil",
                                 "Parse/Smil",
                                 "A minimal SMIL player, supporting only audio",
                                 "Akos Maroy <maroy@campware.org>");

/**
 *  The parent class.
 */
static GstBinClass    * parent_class = NULL;

/**
 *  Declaration of the sink pad factory.
 */
static GstStaticPadTemplate sink_factory = GST_STATIC_PAD_TEMPLATE (
                                        "sink",
                                        GST_PAD_SINK,
                                        GST_PAD_ALWAYS,
                                        GST_STATIC_CAPS("application/smil"));

/**
 *  Declaration of the source pad factory.
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
                                            "rate = (int) [ 8000, 96000 ]"));


/* ================================================  local constants & macros */

#define UNREF_IF_NOT_NULL(gst_object)               \
{                                                   \
    if ((gst_object)) {                             \
        gst_object_unref(GST_OBJECT(gst_object));   \
    }                                               \
}

/**
 *  The debug definition.
 */
GST_DEBUG_CATEGORY_STATIC(minimal_audio_smil_debug);

/**
 *  The plugin definition.
 */
GST_PLUGIN_DEFINE(GST_VERSION_MAJOR,
                  GST_VERSION_MINOR,
                  "minimalaudiosmil",
                  "Minimal Audio-only SMIL",
                  plugin_init,
                  "$Revision: 1.3 $",
                  "GPL",
                  "LiveSupport",
                  "http://livesupport.campware.org/")


/* ===============================================  local function prototypes */

/**
 *  Read the sink stream into memory, using a oneshotreader element.
 *
 *  @param smil a MinimalAudioSmil object.
 *  @param outbuffer the buffer containing the contents of the sink stream.
 *         (out parameter). must be freed by g_free() after no longer needed.
 *  @param outlength the length of outbuffer (an out parameter).
 */
static void
read_stream_into_memory(LivesupportMinimalAudioSmil    * smil,
                        guint8                        ** outbuffer,
                        guint32                        * outlength);

/**
 *  Return the the first "<body>" element of an XML document.
 *
 *  @param document the XML document to return the root element for.
 *  @return the "<body>" element closes to the document root,
 *          or NULL if no "<body>" element found.
 */
static xmlNode *
get_body_element(xmlDocPtr  document);

/**
 *  Handle an "<audio>" SMIL element.
 *
 *  @param smil a MinimalAudioSmil object.
 *  @param audio an "<audio>" SMIL element.
 *  @param index the index of the "<audio>" element with respect to it's
 *         containing element.
 *  @return a gstreamer element that will play audio as described by the
 *          "<audio>" SMIL element. the element will be named using the
 *          supplied index, thus is will be uniquely named with respect
 *          to it's parent.
 */
static GstElement *
handle_audio_element(LivesupportMinimalAudioSmil  * smil,
                     xmlNode                      * audio,
                     int                            index);

/**
 *  Handle a "<par>" SMIL element.
 *
 *  @param smil a MinimalAudioSmil object.
 *  @param par a "<par>" SMIL element.
 *  @return a gstreamer element that will play audio as described by the
 *          supplied "<par>" element.
 */
static GstElement *
handle_par_element(LivesupportMinimalAudioSmil    * smil,
                   xmlNode                        * par);

/**
 *  Process the sink input as a SMIL file.
 *  The bin container inside the MinimalAudioSmil object will be filled
 *  with gstreamer elements, playing audio as described by the SMIL file.
 *
 *  @para smil a MinimalAudioSmil object.
 *  @return TRUE if processing was successful, FALSE otherwise.
 */
static gboolean
process_smil_file(LivesupportMinimalAudioSmil * smil);

/**
 *  Handle state change for a MinimalAudioSmil object.
 *
 *  @param element a MinimalAudioSmil object.
 *  @return the success or failure status of the state change.
 */
static GstElementStateReturn
livesupport_minimal_audio_smil_change_state(GstElement * element);

/**
 *  Destroy a MinimalAudioSmil object.
 *
 *  @param object the MinimalAudioSmil object to destroy.
 */
static void
livesupport_minimal_audio_smil_dispose(GObject * object);

/**
 *  Initialize a MinimalAudioSmil object.
 *
 *  @param smil the MinimalAudioSmil object to initialie.
 */
static void
livesupport_minimal_audio_smil_init(LivesupportMinimalAudioSmil * smil);

/**
 *  Do base-initialization for a MinimalAudioSmil object.
 *
 *  @param g_class a MinimalAudioSmil class.
 */
static void
livesupport_minimal_audio_smil_base_init(gpointer g_class);

/**
 *  Initialize the MinimalAudioSmil class.
 *
 *  @param klass a MinimalAudioSmil class.
 */
static void
livesupport_minimal_audio_smil_class_init(
                                    LivesupportMinimalAudioSmilClass  * klass);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Read the sink stream into memory, using a oneshotreader element.
 *----------------------------------------------------------------------------*/
static void
read_stream_into_memory(LivesupportMinimalAudioSmil    * smil,
                        guint8                        ** outbuffer,
                        guint32                        * outlength)
{
    guint32                 length;
    guint8                * buffer;
    GstElementState         oldState;

    *outbuffer = 0;
    *outlength = 0;

    oldState = gst_element_get_state(smil->oneshotReader);
    if (oldState != GST_STATE_PLAYING) {
        gst_element_set_state(smil->oneshotReader, GST_STATE_PLAYING);
    }
    g_object_get(G_OBJECT(smil->oneshotReader), "length", &length, NULL);
    g_object_get(G_OBJECT(smil->oneshotReader), "contents", &buffer, NULL);

    if (!length) {
        return;
    }

    *outbuffer = g_malloc(length);
    *outlength = length;
    memcpy(*outbuffer, buffer, length);
}


/*------------------------------------------------------------------------------
 *  Return the the first "<body>" element of an XML document.
 *----------------------------------------------------------------------------*/
static xmlNode *
get_body_element(xmlDocPtr  document)
{
    xmlNode * node = document->children;

    if (!node || strcmp(node->name, "smil")) {
        return 0;
    }
    for (node = node->children; node; node = node->next) {
        if (node->type == XML_ELEMENT_NODE
         && !strcmp(node->name, "body")) {

            return node;
        }
    }

    return 0;
}


/*------------------------------------------------------------------------------
 *  Handle an "<audio>" SMIL element.
 *----------------------------------------------------------------------------*/
static GstElement *
handle_audio_element(LivesupportMinimalAudioSmil  * smil,
                     xmlNode                      * audio,
                     int                            index)
{
    xmlAttribute          * attr;
    gchar                 * src       = 0;
    gchar                 * begin     = 0;
    gchar                 * clipBegin = 0;
    gchar                 * clipEnd   = 0;
    gchar                 * str;
    guint                   len;
    GstElement            * pplay;

    for (attr = ((xmlElement*)audio)->attributes;
         attr;
         attr = (xmlAttribute*) attr->next) {

        xmlNode * node;

        /* TODO: support attribute values that are represented with
         *       more than one text node, in all content assignments below */
        if (!strcmp(attr->name, "src")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                src = (gchar*) node->content;
            }
        } else if (!strcmp(attr->name, "begin")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                begin = (gchar*) node->content;
            }
        } else if (!strcmp(attr->name, "clipBegin")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                clipBegin = (gchar*) node->content;
            }
        } else if (!strcmp(attr->name, "clipEnd")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                clipEnd = (gchar*) node->content;
            }
        } else {
            GST_WARNING("unsupported SMIL audio element attribute: %s",
                        attr->name);
        }
    }

    if (!src) {
        GST_WARNING("required SMIL audio element attribute src missing");
        return 0;
    }

    if (!begin) {
        begin = "0s";
    }
    if (!clipBegin) {
        clipBegin = "0s";
    }

    /* we only support file: URLs at the moment */
    if (!g_str_has_prefix(src, "file:")) {
        GST_WARNING("SMIL audio element src attribute with unsupported "
                    "protocol: %s", src);
        return 0;
    }
    src += g_str_has_prefix(src, "file://") ? 7 : 5;

    /* now create a partial play element */
    len = strlen("partialplay_XXXXXXXXXX") + 1;
    str = g_malloc(len);
    g_snprintf(str, len, "partialplay_%d", index);
    if (!(pplay = gst_element_factory_make("partialplay", str))) {
        GST_WARNING("can't create a required gstreamer element");
        g_free(str);
        return 0;
    }
    g_free(str);
    g_object_set(G_OBJECT(pplay), "location", src, NULL);

    len = strlen(begin)
        + strlen(clipBegin)
        + (clipEnd ? strlen(clipEnd) : 0)
        + 3;
    str = g_malloc(sizeof(gchar) * len);
    g_snprintf(str, len, "%s;%s-%s",
               begin, clipBegin, clipEnd ? clipEnd : "");
    g_object_set(G_OBJECT(pplay), "config", str, NULL);
    g_free(str);

    return pplay;
}


/*------------------------------------------------------------------------------
 *  Handle a "<par>" SMIL element.
 *----------------------------------------------------------------------------*/
static GstElement *
handle_par_element(LivesupportMinimalAudioSmil    * smil,
                   xmlNode                        * par)
{
    xmlNode       * node;
    GstElement    * pipeline;
    GstElement    * adder;
    int             index;
    GValue          gvalue = { 0 };

    /* TODO: create unique name for pipeline */
    pipeline = gst_bin_new("par_bin");
    adder    = gst_element_factory_make("adder", "adder");

    if (!pipeline || !adder) {
        GST_WARNING("can't create a required gstreamer element");
        UNREF_IF_NOT_NULL(pipeline);
        UNREF_IF_NOT_NULL(adder);

        return 0;
    }
    g_value_init(&gvalue, G_TYPE_BOOLEAN);
    g_value_set_boolean(&gvalue, TRUE);
    gst_element_set_property(adder, "eos", &gvalue);


    for (index = 0, node = par->children; node; node = node->next, ++index) {
        if (node->type == XML_ELEMENT_NODE) {
            GstElement    * element = 0;

            if (!strcmp(node->name, "audio")) {
                element = handle_audio_element(smil, node, index);
            } else {
                GST_WARNING("unsupported SMIL element %s found inside a par",
                            node->name);
            }

            if (element) {
                if (!gst_element_link(element, adder)) {
                    GST_WARNING("can't link par child to adder");
                }
                gst_bin_add(GST_BIN(pipeline), element);
            }
        }
    }

    gst_bin_add(GST_BIN(pipeline), adder);
    gst_element_add_ghost_pad(GST_ELEMENT(pipeline),
                              gst_element_get_pad(adder, "src"),
                              "src");

    return pipeline;
}


/*------------------------------------------------------------------------------
 *  Process the sink input as a SMIL file.
e*----------------------------------------------------------------------------*/
static gboolean
process_smil_file(LivesupportMinimalAudioSmil * smil)
{
    guint32                 length;
    guint8                * buffer;
    xmlDocPtr               document;
    xmlNode               * node;

    if (smil->fileProcessed) {
        return TRUE;
    }

    smil->fileProcessed = TRUE;

    if (!GST_PAD_IS_LINKED(smil->sinkpad)) {
        return FALSE;
    }

    /* read the source document into memory */
    read_stream_into_memory(smil, &buffer, &length);
    if (!buffer) {
        smil->fileProcessed = FALSE;
        return FALSE;
    }

    /* parse the XML files */
    document = xmlReadMemory((const char *) buffer,
                             length, "noname.xml", NULL, 0);
    g_free(buffer);
    if (!document
     || !(node = get_body_element(document))) {
        GST_ELEMENT_ERROR(GST_ELEMENT(smil),
                          STREAM,
                          WRONG_TYPE,
                          ("SMIL input does not seem to be an XML file"),
                          (NULL));
        return FALSE;
    }

    for (node = node->children; node; node = node->next) {
        if (node->type == XML_ELEMENT_NODE) {
            GstElement    * element = 0;

            if (!strcmp(node->name, "par")) {
                element = handle_par_element(smil, node);
            } else {
                GST_WARNING("unsupported SMIL element %s found",
                            node->name);
            }

            if (element) {
                gst_bin_add(GST_BIN(smil->bin), element);
                if (!gst_element_link(element, smil->finalAdder)) {
                    GST_WARNING("couldn't link par element to final adder");
                }
            }
        }
    }

    /* free the XML document */
    xmlFreeDoc(document);

    gst_bin_sync_children_state(GST_BIN(smil->bin));

    return TRUE;
}


/*------------------------------------------------------------------------------
 *  Handle state change for the MinimalAudioSmil element.
 *----------------------------------------------------------------------------*/
static GstElementStateReturn
livesupport_minimal_audio_smil_change_state(GstElement * element)
{
    LivesupportMinimalAudioSmil   * smil;
    GstElementState                 transition = GST_STATE_TRANSITION(element);

    smil = LIVESUPPORT_MINIMAL_AUDIO_SMIL(element);

    switch (transition) {
        case GST_STATE_NULL_TO_READY:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_READY);
            break;

        case GST_STATE_READY_TO_PAUSED:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_PAUSED);
            break;

        case GST_STATE_PAUSED_TO_PLAYING:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_PLAYING);

            if (!process_smil_file(smil)) {
                GST_ELEMENT_ERROR(GST_ELEMENT(smil),
                                  STREAM,
                                  WRONG_TYPE,
                                  ("unable to process SMIL file"),
                                  (NULL));
            }

            break;

        case GST_STATE_PLAYING_TO_PAUSED:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_PAUSED);
            break;
            
        case GST_STATE_PAUSED_TO_READY:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_READY);
            break;
            
        case GST_STATE_READY_TO_NULL:
            gst_element_set_state(GST_ELEMENT(smil->bin), GST_STATE_NULL);
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
 *  Destroy a MinimalAudioSmil object.
 *----------------------------------------------------------------------------*/
static void
livesupport_minimal_audio_smil_dispose(GObject * object)
{
    LivesupportMinimalAudioSmil * smil = LIVESUPPORT_MINIMAL_AUDIO_SMIL(object);

    g_return_if_fail(LIVESUPPORT_IS_MINIMAL_AUDIO_SMIL(smil));
    xmlCleanupParser();
    G_OBJECT_CLASS(parent_class)->dispose(object);
}


/*------------------------------------------------------------------------------
 *  Initialize a MinimalAudioSmil object.
 *----------------------------------------------------------------------------*/
static void
livesupport_minimal_audio_smil_init(LivesupportMinimalAudioSmil * smil)
{
    GValue      gvalue = { 0 };

    smil->bin    = GST_BIN(gst_bin_new("smilbin"));

    smil->finalAdder = gst_element_factory_make("adder", "finalAdder");
    g_value_init(&gvalue, G_TYPE_BOOLEAN);
    g_value_set_boolean(&gvalue, TRUE);
    gst_element_set_property(smil->finalAdder, "eos", &gvalue);

    gst_bin_add(smil->bin, smil->finalAdder);
    /* create and attach an adder to the src pad, so that the bin
     * actually has a src pad, that we can attach to ourselves below */
    gst_element_add_ghost_pad(GST_ELEMENT(smil->bin),
                              gst_element_get_pad(smil->finalAdder, "src"),
                              "src");

    gst_bin_add(GST_BIN(smil), GST_ELEMENT(smil->bin));
    smil->srcpad = gst_element_add_ghost_pad(GST_ELEMENT(smil),
                            gst_element_get_pad(GST_ELEMENT(smil->bin), "src"),
                            "src");
    smil->oneshotReader = gst_element_factory_make("oneshotreader", "oneshot");
    smil->oneshotReaderSink = gst_element_get_pad(smil->oneshotReader, "sink");
    gst_bin_add(GST_BIN(smil), smil->oneshotReader);
    smil->sinkpad = gst_element_add_ghost_pad(GST_ELEMENT(smil),
                                              smil->oneshotReaderSink,
                                              "sink");

    smil->fileProcessed = FALSE;
}


/*------------------------------------------------------------------------------
 *  Do base-initialization for a MinimalAudioSmil object.
 *----------------------------------------------------------------------------*/
static void
livesupport_minimal_audio_smil_base_init(gpointer g_class)
{
    GstElementClass *element_class = GST_ELEMENT_CLASS(g_class);

    gst_element_class_set_details(element_class,
                                  &livesupport_minimal_audio_smil_details);

    gst_element_class_add_pad_template(element_class,
                                    gst_static_pad_template_get(&src_factory));
    gst_element_class_add_pad_template(element_class,
                                    gst_static_pad_template_get(&sink_factory));
}


/*------------------------------------------------------------------------------
 *  Initialize a MinimalAudioSmil class.
 *----------------------------------------------------------------------------*/
static void
livesupport_minimal_audio_smil_class_init(
                                    LivesupportMinimalAudioSmilClass  * klass)
{
    GObjectClass      * gobject_class;
    GstElementClass   * gstelement_class;

    gobject_class    = (GObjectClass *) klass;
    gstelement_class = (GstElementClass *) klass;
    parent_class     = g_type_class_ref(GST_TYPE_BIN);

    gobject_class->dispose         = livesupport_minimal_audio_smil_dispose;
    gstelement_class->change_state =
                                    livesupport_minimal_audio_smil_change_state;

    /* check for the libxml version */
    LIBXML_TEST_VERSION
}


/*------------------------------------------------------------------------------
 *  Return the type structure for the plugin.
 *----------------------------------------------------------------------------*/
GType
livesupport_minimal_audio_smil_get_type(void)
{
    static GType minimal_audio_smil_type = 0;

    if (!minimal_audio_smil_type) {
        static const GTypeInfo minimal_audio_smil_info = {
            sizeof (LivesupportMinimalAudioSmilClass),
            livesupport_minimal_audio_smil_base_init,
            NULL,
            (GClassInitFunc) livesupport_minimal_audio_smil_class_init,
            NULL,
            NULL,
            sizeof (LivesupportMinimalAudioSmil),
            0,
            (GInstanceInitFunc) livesupport_minimal_audio_smil_init,
        };

        minimal_audio_smil_type = g_type_register_static(GST_TYPE_BIN,
                                                  "LivesupportMinimalAudioSmil",
                                                  &minimal_audio_smil_info,
                                                  0);

        GST_DEBUG_CATEGORY_INIT(minimal_audio_smil_debug,
                                "minimalaudiosmil",
                                0,
                                "minimal audio-only SMIL element");
    }

    return minimal_audio_smil_type;
}


/*------------------------------------------------------------------------------
 *  Initialize the plugin
 *----------------------------------------------------------------------------*/
static gboolean
plugin_init (GstPlugin * plugin)
{
    return gst_element_register(plugin,
                                "minimalaudiosmil",
                                GST_RANK_SECONDARY,
                                LIVESUPPORT_TYPE_MINIMAL_AUDIO_SMIL);
}

