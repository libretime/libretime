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
    Version  : $Revision: 1.8 $
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

#include "smil-util.h"
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

#define NSEC_PER_SEC_FLOAT  1000000000.0


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
                  "$Revision: 1.8 $",
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
 *  Handle an "<animate>" element.
 *
 *  @param smil a MinimalAudioSmil object.
 *  @param bin the container to put all the generated elements in.
 *  @param offset the offset in nanoseconds that the animation should
 *         begin at. this is usually the begin="xx" attribute value
 *         of the containing element.
 *  @param animate the "<animate>" element to handle.
 *  @param namePrefix name prefix to use for generated gstreamer element
 *         names
 *  @param index the index of the "<animate>" element with respect to it's
 *         containing element.
 *  @return a gstreamer element, that if linked after the containing SMIL
 *          element, performs the animation described by the "<animate>"
 *          SMIL element
 */
static GstElement *
handle_animate_element(LivesupportMinimalAudioSmil  * smil,
                       GstBin                       * bin,
                       gint64                         offset,
                       xmlNode                      * animate,
                       const gchar                  * namePrefix,
                       int                            index);

/**
 *  Signal handler for the eos event of a gstreamer element.
 *  The handler will set the gstreamer element pointed to by userData
 *  to eos as well.
 *
 *  @param element the element emitting the eos signal
 *  @param userData pointer to a gstreamer element to  put into eos state.
 */
static void
element_eos_signal_handler(GstElement     * element,
                           gpointer         userData);

/**
 *  Handle an "<audio>" SMIL element.
 *  A series of elements will be generated, all linked and added to the
 *  supplied bin container. The last element in the series will be returned,
 *  that can be linked further to produce the desired output.
 *
 *  @param smil a MinimalAudioSmil object.
 *  @param bin the container to put all the generated elements in.
 *  @param audio an "<audio>" SMIL element.
 *  @param index the index of the "<audio>" element with respect to it's
 *         containing element.
 *  @return a gstreamer element that will play audio as described by the
 *          "<audio>" SMIL element. the element will be named using the
 *          supplied indexes, thus is will be uniquely named with respect
 *          to it's parent.
 */
static GstElement *
handle_audio_element(LivesupportMinimalAudioSmil  * smil,
                     GstBin                       * bin,
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
 *  Handle an "<animate>" SMIL element, which is supposedly inside
 *  an "<audio>" element
 *----------------------------------------------------------------------------*/
static GstElement *
handle_animate_element(LivesupportMinimalAudioSmil  * smil,
                       GstBin                       * bin,
                       gint64                         offset,
                       xmlNode                      * animate,
                       const gchar                  * namePrefix,
                       int                            index)
{
    GstElement        * volenv;
    xmlAttribute      * attr;
    double              from  = 0.0;
    double              to    = 0.0;
    double              begin = 0.0;
    double              end   = 0.0;
    const gchar       * cstr;
    gchar             * str;
    guint               len;

    /* handle the attributes */
    for (attr = ((xmlElement*)animate)->attributes;
         attr;
         attr = (xmlAttribute*) attr->next) {

        xmlNode * node;

        /* TODO: support attribute values that are represented with
         *       more than one text node, in all content assignments below */
        if (!strcmp(attr->name, "attributeName")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                cstr = (gchar*) node->content;
                /* we only support soundLevel animation at the moment */
                if (strcmp(cstr, "soundLevel")) {
                    GST_WARNING("unsupported animate attribute: %s", cstr);
                    return 0;
                }
            }
        } else if (!strcmp(attr->name, "calcMode")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                cstr = (gchar*) node->content;
                /* we only support linear calc mode at the moment */
                if (strcmp(cstr, "linear")) {
                    GST_WARNING("unsupported animate calcMode: %s", cstr);
                    return 0;
                }
            }
        } else if (!strcmp(attr->name, "fill")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                cstr = (gchar*) node->content;
                /* we only support freeze fill at the moment */
                if (strcmp(cstr, "freeze")) {
                    GST_WARNING("unsupported animate fill: %s", cstr);
                    return 0;
                }
            }
        } else if (!strcmp(attr->name, "from")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                cstr = (gchar*) node->content;
                if (!smil_parse_percent(cstr, &from)) {
                    GST_WARNING("bad from value: %s", cstr);
                    return 0;
                }
            }
        } else if (!strcmp(attr->name, "to")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                cstr = (gchar*) node->content;
                if (!smil_parse_percent(cstr, &to)) {
                    GST_WARNING("bad to value: %s", cstr);
                    return 0;
                }
            }
        } else if (!strcmp(attr->name, "begin")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                gint64  i;

                cstr  = (gchar*) node->content;
                i     = smil_clock_value_to_nanosec(cstr) + offset;
                begin = ((double) i) / NSEC_PER_SEC_FLOAT;
            }
        } else if (!strcmp(attr->name, "end")) {
            if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                gint64  i;

                cstr = (gchar*) node->content;
                i   = smil_clock_value_to_nanosec(cstr) + offset;
                end = ((double) i) / NSEC_PER_SEC_FLOAT;
            }
        } else {
            GST_WARNING("unsupported SMIL audio element attribute: %s",
                        attr->name);
        }
    }

    if (from == 0.0 && to == 0.0 && begin == 0.0 && end == 0.0) {
        GST_WARNING("some required animate attribute missing");
        return 0;
    }

    if (begin >= end) {
        GST_WARNING("begin value in animate greater than end value");
        return 0;
    }

    /* now create a volenv element */
    len = strlen(namePrefix) + strlen("_volenv_XXXXXXXXXX") + 1;
    str = g_malloc(len);
    g_snprintf(str, len, "%s_volenv_%d", namePrefix, index);
    if (!(volenv = gst_element_factory_make("volenv", str))) {
        GST_WARNING("can't create a required gstreamer element");
        g_free(str);
        return 0;
    }
    g_free(str);

    /* insert the control points */
    str = g_malloc(64);
    /* insert an initial volume level at 0.0 */
    if (begin <= 0.0) {
        g_snprintf(str, 64, "0.0:%lf", from);
        g_object_set(G_OBJECT(volenv), "controlpoint", str, NULL);
        g_snprintf(str, 64, "0.01:%lf", from);
        g_object_set(G_OBJECT(volenv), "controlpoint", str, NULL);
    } else {
        g_object_set(G_OBJECT(volenv), "controlpoint", "0.0:1.0", NULL);
        g_snprintf(str, 64, "%lf:%lf", begin, from);
        g_object_set(G_OBJECT(volenv), "controlpoint", str, NULL);
    }
    g_snprintf(str, 64, "%lf:%lf", end, to);
    g_object_set(G_OBJECT(volenv), "controlpoint", str, NULL);
    g_free(str);

    return volenv;
}


/*------------------------------------------------------------------------------
 *  eos signal handler for the partial play element
 *----------------------------------------------------------------------------*/
static void
element_eos_signal_handler(GstElement     * element,
                           gpointer         userData)
{
    GstElement    * elem= GST_ELEMENT(userData);

    g_return_if_fail(elem!= NULL);
    g_return_if_fail(GST_IS_ELEMENT(elem));

    /* set the element into eos state */

    GST_INFO("setting element %p to eos", elem);
    gst_element_set_eos(elem);
}


/*------------------------------------------------------------------------------
 *  Handle an "<audio>" SMIL element.
 *----------------------------------------------------------------------------*/
static GstElement *
handle_audio_element(LivesupportMinimalAudioSmil  * smil,
                     GstBin                       * bin,
                     xmlNode                      * audio,
                     int                            index)
{
    xmlAttribute      * attr;
    gchar             * src       = 0;
    gchar             * begin     = 0;
    gchar             * clipBegin = 0;
    gchar             * clipEnd   = 0;
    gint64              nsBegin;
    gchar             * name;
    gchar             * str;
    guint               len;
    GstElement        * pplay;
    GstElement        * element;
    xmlNode           * node;
    int                 ix;

    /* handle the attributes */
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
    nsBegin = smil_clock_value_to_nanosec(begin);
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
    name = g_malloc(len);
    g_snprintf(name, len, "partialplay_%d", index);
    if (!(pplay = gst_element_factory_make("partialplay", name))) {
        GST_WARNING("can't create a required gstreamer element");
        g_free(name);
        return 0;
    }
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

    gst_bin_add(bin, pplay);

    /* now handle the possible animate elements inside this audio element */
    element = pplay;
    for (ix = 0, node = audio->children; node; node = node->next, ++ix) {
        if (node->type == XML_ELEMENT_NODE) {
            GstElement    * elem = 0;

            if (!strcmp(node->name, "animate")) {
                elem = handle_animate_element(smil, bin, nsBegin,
                                              node, name, ix);
            } else {
                GST_WARNING("unsupported SMIL element %s found inside a audio",
                            node->name);
            }

            if (elem) {
                gst_element_link(element, elem);
                gst_bin_add(bin, elem);

                /* FIXME: this is an ugly workaround.
                 *        for some reason, the EOS event does not get
                 *        propagated to the volenv elements from the
                 *        partial play element. so we catch the EOS
                 *        event from each element, and set the next in
                 *        line to EOS explicitly */
                g_signal_connect(element,
                                 "eos",
                                 G_CALLBACK(element_eos_signal_handler),
                                 elem);

                element = elem;
            }
        }
    }

    g_free(name);

    return element;
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
    g_value_unset(&gvalue);

    g_value_init(&gvalue, G_TYPE_POINTER);
    g_value_set_pointer(&gvalue, smil->caps);
    gst_element_set_property(adder, "caps", &gvalue);
    g_value_unset(&gvalue);


    for (index = 0, node = par->children; node; node = node->next, ++index) {
        if (node->type == XML_ELEMENT_NODE) {
            GstElement    * element = 0;

            if (!strcmp(node->name, "audio")) {
                element = handle_audio_element(smil,
                                               GST_BIN(pipeline),
                                               node,
                                               index);
            } else {
                GST_WARNING("unsupported SMIL element %s found inside a par",
                            node->name);
            }

            if (element) {
                if (!gst_element_link(element, adder)) {
                    GST_WARNING("can't link par child to adder");
                }
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
    gst_caps_free(smil->caps);
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
    GstPad    * oneshotReaderSink;

    smil->bin    = GST_BIN(gst_bin_new("smilbin"));

    /* TODO: don't hardcode capability values */
    smil->caps = gst_caps_new_simple("audio/x-raw-int",
                                     "width", G_TYPE_INT, 16,
                                     "depth", G_TYPE_INT, 16,
                                     "endianness", G_TYPE_INT, G_BYTE_ORDER,
                                     "signed", G_TYPE_BOOLEAN, TRUE,
                                     "channels", G_TYPE_INT, 2,
                                     "rate", G_TYPE_INT, 44100,
                                     NULL);

    smil->finalAdder = gst_element_factory_make("adder", "finalAdder");

    g_value_init(&gvalue, G_TYPE_BOOLEAN);
    g_value_set_boolean(&gvalue, TRUE);
    gst_element_set_property(smil->finalAdder, "eos", &gvalue);
    g_value_unset(&gvalue);

    g_value_init(&gvalue, G_TYPE_POINTER);
    g_value_set_pointer(&gvalue, smil->caps);
    gst_element_set_property(smil->finalAdder, "caps", &gvalue);
    g_value_unset(&gvalue);


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
    oneshotReaderSink = gst_element_get_pad(smil->oneshotReader, "sink");
    gst_bin_add(GST_BIN(smil), smil->oneshotReader);
    smil->sinkpad = gst_element_add_ghost_pad(GST_ELEMENT(smil),
                                              oneshotReaderSink,
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

