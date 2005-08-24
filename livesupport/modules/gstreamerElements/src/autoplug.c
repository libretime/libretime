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
 

    This code is based on the examples/manual/dynamic.c sample file
    provided in the gstreamer-0.8.10 source tarball, which is published
    under the GNU LGPL license.

 
    Author   : $Author: maroy $
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/src/autoplug.c,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#include <gst/gst.h>

#include "LiveSupport/GstreamerElements/autoplug.h"


/* ===================================================  local data structures */

typedef struct _Typefind Typefind;

/**
 *  Data structure to hold information related to typefindinf.
 */
struct _Typefind {
    GList             * factories;

    GstElement        * pipeline;
    GstElement        * bin;
    GstElement        * source;
    GstElement        * typefind;
    GstElement        * audiosink;
    GstElement        * sink;

    const GstCaps     * caps;

    gulong              typefindSignal;

    gboolean            done;
};


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */

/**
 *  Handle typefinding error.
 *
 *  @param pipeline the pipeline generating the error.
 *  @param source the source element with the error
 *  @param error the error itself.
 *  @param message the error message.
 *  @param userData user-specific data.
 */
static void
autoplug_error_handler(GstElement * pipeline,
                       GstElement * source,
                       GError     * error,
                       gchar      * message,
                       gpointer     userData);

/**
 *  Handle event of the typefinder finding a type.
 *
 *  @param typefind the typefind element that found the type.
 *  @param probability the probability of the find.
 *  @param caps the found capabilities.
 *  @param userData user-specific data, a pointer to a related Typefind
 *         structure.
 */
static void
autoplug_typefound_handler(GstElement * typefind,
                           gint         probability,
                           GstCaps    * caps,
                           gpointer     userData);

/**
 *  Initialize a typefind object.
 *
 *  @param typefind the Typefind structure to init.
 *  @param name the name of the topmost bin element, that will
 *         be returned at the end of autoplugging.
 *  @param caps the capabilities expected from the returned element,
 *         on its src pad.
 */
static void
autoplug_init(Typefind        * typefind,
              const gchar     * name,
              const GstCaps   * caps);

/**
 *  De-initialize a typefind object.
 *
 *  @param typefind the Typefind structure to de-init.
 */
static void
autoplug_deinit(Typefind      * typefind);

/**
 *  A filter specifying the kind of factories we're interested in.
 *
 *  @param feature the feature to test
 *  @param userData user-specific data
 *  @return TRUE if we're interested in the supplied feature, FALSE otherwise
 */
static gboolean
autoplug_feature_filter(GstPluginFeature      * feature,
                        gpointer                userData);

/**
 *  A comparison function based on the ranks of two features.
 *
 *  @param feature1 one of the features to compare.
 *  @param feature2 the other feature to compare.
 *  @return 0 if the two features match in terms of their ranks,
 *          <0 if feature1 is higher, >0 if feature2 is higher in
 *          their ranks.
 */
static gint
autoplug_compare_ranks(GstPluginFeature   * feature1,
                       GstPluginFeature   * feature2);

/**
 *  Type to plug an appropriate element to a pad, according to the specified
 *  capabilities.
 *
 *  @param typefind the Typefind structure to do the plugging for
 *  @param pad the pad to plug.
 *  @param caps the capabilities to plug with.
 */
static void
autoplug_try_to_plug(Typefind         * typefind,
                     GstPad           * pad,
                     const GstCaps    * caps);

/**
 *  Close a found link.
 *
 *  @param typefind the Typefind structure to do close the link for.
 *  @param srcpad the source pad to close linking for.
 *  @param sinkelement the sink element to link the src pad to.
 *  @param padname the name of sink pad in sinkelement to link srcpad to.
 *  @param templlist a pad template list (TODO: what's this for?)
 */
static void
autoplug_close_link(Typefind      * typefind,
                    GstPad        * srcpad,
	                GstElement    * sinkelement,
	                const gchar   * padname,
	                const GList   * templlist);

/**
 *  Handle the event of new pads created on elements with dynamic pads.
 *
 *  @param element the element that the new pad was created on.
 *  @param pad the new pad.
 *  @param userData user-specific data.
 */
static void
autoplug_newpad(GstElement    * element,
	            GstPad        * pad,
	            gpointer        data);


/**
 *  Remove all typefind elements inside the bin, traversing to lower binds
 *  if necessary. The pads linked through the removed typefind elements are
 *  linked directly instead.
 *  The typefind member of the supplied Typefind object is also removed,
 *  and changed to NULL.
 *
 *  @param typefind the typefind object to work on.
 *  @param bin the bin to remove the typefind elements from.
 */
static void
autoplug_remove_typefind_elements(Typefind    * typefind,
                                  GstBin      * bin);


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Filter the features that we're interested in.
 *----------------------------------------------------------------------------*/
static gboolean
autoplug_feature_filter(GstPluginFeature      * feature,
                        gpointer                userData)
{
    const gchar   * klass;
    guint           rank;

    /* we only care about element factories */
    if (!GST_IS_ELEMENT_FACTORY(feature)) {
        return FALSE;
    }

    /* only parsers, demuxers and decoders */
    klass = gst_element_factory_get_klass(GST_ELEMENT_FACTORY(feature));
    if (g_strrstr(klass, "Demux") == NULL &&
        g_strrstr(klass, "Decoder") == NULL &&
        g_strrstr(klass, "Parse") == NULL) {

        return FALSE;
    }

    /* only select elements with autoplugging rank */
    rank = gst_plugin_feature_get_rank(feature);
    if (rank < GST_RANK_MARGINAL) {
        return FALSE;
    }

    return TRUE;
}


/*------------------------------------------------------------------------------
 *  Compare the ranks of two features.
 *----------------------------------------------------------------------------*/
static gint
autoplug_compare_ranks(GstPluginFeature   * feature1,
                       GstPluginFeature   * feature2)
{
    return gst_plugin_feature_get_rank(feature2)
         - gst_plugin_feature_get_rank(feature1);
}


/*------------------------------------------------------------------------------
 *  Initialize a Typefind object, like the factories that we care about.
 *----------------------------------------------------------------------------*/
static void
autoplug_init(Typefind        * typefind,
              const gchar     * name,
              const GstCaps   * caps)
{
    /* first filter out the interesting element factories */
    typefind->factories = gst_registry_pool_feature_filter(
                             (GstPluginFeatureFilter) autoplug_feature_filter,
                             FALSE, NULL);

    /* sort them according to their ranks */
    typefind->factories = g_list_sort(typefind->factories,
                                      (GCompareFunc) autoplug_compare_ranks);

    typefind->pipeline  = gst_pipeline_new("pipeline");
    typefind->bin       = gst_bin_new(name);
    typefind->typefind  = gst_element_factory_make("typefind", "tf");
    typefind->audiosink = gst_element_factory_make("audioconvert", "audiosink");
    typefind->sink      = gst_element_factory_make("fakesink", "fakesink");

    typefind->caps      = caps;

    gst_element_add_ghost_pad(typefind->bin,
                              gst_element_get_pad(typefind->typefind, "sink"),
                              "sink");
    gst_bin_add_many(GST_BIN(typefind->bin),
                     typefind->typefind,
                     NULL);

    g_signal_connect(typefind->bin,
                     "error",
                     G_CALLBACK(autoplug_error_handler),
                     NULL);
    typefind->typefindSignal = g_signal_connect(typefind->typefind,
                                         "have-type",
                                         G_CALLBACK(autoplug_typefound_handler),
                                         typefind);

    gst_element_link(typefind->source, typefind->bin);
    gst_bin_add_many(GST_BIN(typefind->pipeline),
                     typefind->source,
                     typefind->bin,
                     NULL);

    typefind->done = FALSE;
}

/*------------------------------------------------------------------------------
 *  De-initialize a Typefind object.
 *----------------------------------------------------------------------------*/
static void
autoplug_deinit(Typefind      * typefind)
{
    g_list_free(typefind->factories);

    gst_element_set_state(typefind->pipeline, GST_STATE_NULL);
    if (typefind->typefind) {
        g_signal_handler_disconnect(typefind->typefind,
                                    typefind->typefindSignal);
    }

    if (typefind->audiosink && !gst_element_get_parent(typefind->audiosink)) {
        gst_object_unref(GST_OBJECT(typefind->audiosink));
    }
    if (typefind->sink && !gst_element_get_parent(typefind->sink)) {
        gst_object_unref(GST_OBJECT(typefind->sink));
    }
    gst_object_unref(GST_OBJECT(typefind->pipeline));
}


/*------------------------------------------------------------------------------
 *  Handle the event of a new pad being created on an element with
 *  request pads.
 *----------------------------------------------------------------------------*/
static void
autoplug_newpad(GstElement    * element,
	            GstPad        * pad,
	            gpointer        userData)
{
    GstCaps       * caps;
    Typefind      * typefind = (Typefind*) userData;

    g_return_if_fail(typefind != NULL);

    GST_DEBUG("created new pad %s for element %s",
            gst_pad_get_name(pad), gst_element_get_name(element));
    caps = gst_pad_get_caps(pad);
    autoplug_try_to_plug(typefind, pad, caps);
    gst_caps_free(caps);
}



/*------------------------------------------------------------------------------
 *  Close the link.
 *----------------------------------------------------------------------------*/
static void
autoplug_close_link(Typefind      * typefind,
                    GstPad        * srcpad,
	                GstElement    * sinkelement,
	                const gchar   * padname,
	                const GList   * templlist)
{
    GstPad        * pad;
    gboolean        has_dynamic_pads = FALSE;
    GstElement    * srcelement;

    srcelement = GST_ELEMENT(gst_pad_get_parent(srcpad));

    GST_DEBUG("Plugging pad %s:%s to newly created %s:%s",
	          gst_object_get_name(GST_OBJECT(srcelement)),
	          gst_pad_get_name(srcpad),
	          gst_object_get_name(GST_OBJECT(sinkelement)), padname);

    /* add the element to the pipeline and set correct state */
    gst_bin_add(GST_BIN(typefind->bin), sinkelement);
    pad = gst_element_get_pad(sinkelement, padname);

    if (g_strrstr(gst_object_get_name(GST_OBJECT(sinkelement)), "audiosink")) {
        if (!gst_pad_link_filtered(srcpad, pad, typefind->caps)) {
            gst_pad_link(srcpad, pad);
        }
    } else {
        gst_pad_link(srcpad, pad);
    }

    /* FIXME: this is a nasty workaround for lack of time
     *        the minimalaudiosmil will try to read the input immediately
     *        from it sink pad as its set to PLAYING state,
     *        but that will result in a zillion such gstreamer warnings:
     *        "deadlock detected, disabling group 0xXXXXXX"
     *        but for example the vorbis demuxer needs to be in PLAYING
     *        state so that it can dynamically connect its request pads.
     *        fix this as soon as possible!
     */
    if (!(g_strrstr(gst_object_get_name(GST_OBJECT(srcelement)),
                  "minimalaudiosmil")
       || g_strrstr(gst_object_get_name(GST_OBJECT(sinkelement)),
                  "minimalaudiosmil"))) {

        gst_bin_sync_children_state(GST_BIN(typefind->bin));
    }

    /* if we have static source pads, link those. If we have dynamic
     * source pads, listen for new-pad signals on the element */
    for ( ; templlist != NULL; templlist = templlist->next) {
        GstPadTemplate *templ = GST_PAD_TEMPLATE (templlist->data);

        /* only sourcepads, no request pads */
        if (templ->direction != GST_PAD_SRC ||
            templ->presence == GST_PAD_REQUEST) {
            continue;
        }

        switch (templ->presence) {
            case GST_PAD_ALWAYS: {
                GstPad    * pad = gst_element_get_pad(sinkelement,
                                                  templ->name_template);
                GstCaps   * caps = gst_pad_get_caps(pad);

                /* link */
                autoplug_try_to_plug(typefind, pad, caps);
                gst_caps_free(caps);
            } break;

            case GST_PAD_SOMETIMES:
                has_dynamic_pads = TRUE;
                break;

            default:
                break;
        }
    }

    /* listen for newly created pads if this element supports that */
    if (has_dynamic_pads) {
        g_signal_connect(sinkelement,
                         "new-pad",
                         G_CALLBACK(autoplug_newpad),
                         typefind);
    }
}


/*------------------------------------------------------------------------------
 *  Try to plug a pad with the specified capabilities.
 *----------------------------------------------------------------------------*/
static void
autoplug_try_to_plug(Typefind         * typefind,
                     GstPad           * pad,
                     const GstCaps    * caps)
{
    GstObject     * parent = GST_OBJECT(gst_pad_get_parent(pad));
    const gchar   * mime;
    const GList   * item;
    GstCaps       * res;
    GstCaps       * audiocaps;

    g_return_if_fail(typefind != NULL);

    /* don't plug if we're already plugged */
    if (GST_PAD_IS_LINKED(gst_element_get_pad(typefind->audiosink, "sink"))) {
        GST_DEBUG("Omitting link for pad %s:%s because we're already linked",
	              gst_object_get_name(parent), gst_pad_get_name(pad));
        return;
    }

    /* as said above, we only try to plug audio... Omit video */
    mime = gst_structure_get_name(gst_caps_get_structure(caps, 0));
    if (g_strrstr(mime, "video")) {
        GST_DEBUG("Omitting link for pad %s:%s because "
                  "mimetype %s is non-audio\n",
	              gst_object_get_name(parent), gst_pad_get_name(pad), mime);
        return;
    }

    /* can it link to the audiopad? */
    audiocaps = gst_pad_get_caps(gst_element_get_pad(typefind->audiosink,
                                                     "sink"));
    res = gst_caps_intersect(caps, audiocaps);
    if (res && !gst_caps_is_empty(res)) {
        GST_DEBUG("Found pad to link to audiosink - plugging is now done");
        typefind->done = TRUE;

        autoplug_close_link(typefind, pad, typefind->audiosink, "sink", NULL);

        gst_element_add_ghost_pad(typefind->bin,
                                gst_element_get_pad(typefind->audiosink, "src"),
                                "src");
        gst_element_link_filtered(typefind->bin, typefind->sink,
                                  typefind->caps);
        gst_bin_add(GST_BIN(typefind->pipeline), typefind->sink);
        gst_bin_sync_children_state(GST_BIN(typefind->pipeline));

        gst_caps_free(audiocaps);
        gst_caps_free(res);
        return;
    }
    gst_caps_free(audiocaps);
    gst_caps_free(res);

    /* try to plug from our list */
    for (item = typefind->factories; item != NULL; item = item->next) {
        GstElementFactory * factory = GST_ELEMENT_FACTORY(item->data);
        const GList       * pads;

        for (pads = gst_element_factory_get_pad_templates(factory);
             pads != NULL;
             pads = pads->next) {
            GstPadTemplate * templ = GST_PAD_TEMPLATE(pads->data);

            if (!GST_IS_PAD_TEMPLATE(templ)) {
                continue;
            }
            /* find the sink template - need an always pad*/
            if (templ->direction != GST_PAD_SINK ||
                templ->presence != GST_PAD_ALWAYS) {
                continue;
            }

            /* can it link? */
            res = gst_caps_intersect(caps, templ->caps);
            if (res && !gst_caps_is_empty(res)) {
                GstElement    * element;
                const GList   * padTemplates;
                gchar         * templateName;

                /* close link and return */
                gst_caps_free(res);
                templateName = g_strdup(templ->name_template);
                element      = gst_element_factory_create(factory, NULL);
                padTemplates = gst_element_factory_get_pad_templates(factory);
                autoplug_close_link(typefind,
                                    pad,
                                    element,
                                    templateName,
		                            padTemplates);
                g_free(templateName);
                return;
            }
            gst_caps_free(res);

            /* we only check one sink template per factory, so move on to the
             * next factory now */
            break;
        }
    }

    /* if we get here, no item was found */
    GST_DEBUG("No compatible pad found to decode %s on %s:%s",
	          mime, gst_object_get_name(parent), gst_pad_get_name(pad));
}


/*------------------------------------------------------------------------------
 *  Handle the event when a new type was found.
 *----------------------------------------------------------------------------*/
static void
autoplug_typefound_handler(GstElement * typefind,
                           gint         probability,
                           GstCaps    * caps,
                           gpointer     userData)
{
    gchar     * str;
    Typefind  * tf = (Typefind*) userData;

    g_return_if_fail(tf != NULL);

    str = gst_caps_to_string(caps);
    GST_DEBUG("Detected media type %s", str);
    g_free(str);

    /* actually plug now */
    autoplug_try_to_plug(tf, gst_element_get_pad(typefind, "src"), caps);
}


/*------------------------------------------------------------------------------
 *  Filter the features that we're interested in.
 *----------------------------------------------------------------------------*/
static void
autoplug_error_handler(GstElement * pipeline,
                       GstElement * source,
                       GError     * error,
                       gchar      * message,
                       gpointer     userData)
{
    /* TODO: handle error somehow */
    GST_DEBUG("error: %s", message);
}


/*------------------------------------------------------------------------------
 *  Remove all typefind elements inside the bin, traversing to lower binds
 *  if necessary. The pads linked to the removed typefind elements are
 *  linked directly instead.
 *----------------------------------------------------------------------------*/
static void
autoplug_remove_typefind_elements(Typefind    * typefind,
                                  GstBin      * bin)
{
    GstElement    * element;
    const GList   * elements;

    elements = gst_bin_get_list(GST_BIN(bin));
    while (elements) {
        GstElementFactory     * factory;
        GType                   type;

        element = (GstElement*) elements->data;
        factory = gst_element_get_factory(element);
        type    = gst_element_factory_get_element_type(factory);

        GST_DEBUG("found factory: %s of type %s, is bin: %d",
                  gst_element_factory_get_longname(factory),
                  g_type_name(type),
                  g_type_is_a(type, GST_TYPE_BIN));

        if (GST_IS_BIN(element)) {
            autoplug_remove_typefind_elements(typefind, GST_BIN(element));
        } else if (g_strrstr(gst_element_factory_get_longname(factory),
                             "TypeFind")) {
            GstPad        * tfSinkPad;
            GstPad        * tfSrcPad;
            GstPad        * sinkPad;
            GstElement    * sinkElement;
            GstPad        * srcPad;
            GstElement    * srcElement;
            GstElement    * parent;
            GstPad        * parentSrcPad;
            GstPad        * parentSinkPad;

            tfSinkPad     = gst_element_get_pad(element, "sink");
            tfSrcPad      = gst_element_get_pad(element, "src");
            sinkPad       = gst_pad_get_peer(tfSrcPad);
            sinkElement   = gst_pad_get_parent(sinkPad);
            srcPad        = gst_pad_get_peer(tfSinkPad);
            srcElement    = gst_pad_get_parent(srcPad);
            parent        = (GstElement*) gst_element_get_parent(element);
            parentSrcPad  = gst_element_get_pad(parent, "src");
            parentSinkPad = gst_element_get_pad(parent, "sink");

            gst_element_unlink(srcElement, element);
            gst_element_unlink(element, sinkElement);

            if (GST_PAD_REALIZE(parentSrcPad) == (GstRealPad*) tfSrcPad) {
                /* if the pad we want to relink is ghosted by the container */
                gst_element_remove_pad(parent, parentSrcPad);
                gst_element_add_ghost_pad(parent, srcPad, "src");
                gst_element_link(parent, sinkElement);
            } else if (GST_PAD_REALIZE(parentSinkPad) ==
                       (GstRealPad*) tfSinkPad) {
                /* if the pad we want to relink is ghosted by the container */
                gst_element_remove_pad(parent, parentSinkPad);
                gst_element_add_ghost_pad(parent, sinkPad, "sink");
                gst_element_link(srcElement, parent);
            } else {
                gst_element_link(srcElement, sinkElement);
            }

            gst_bin_remove(bin, element);

            if (element == typefind->typefind) {
                typefind->typefind = NULL;
            }

            /* start iteration from the beginning, as probably the element
             * list is invalidated with us removing the typefind element */
            elements = gst_bin_get_list(GST_BIN(bin));
            continue;
        }

        elements = elements->next;
    }
}


/*------------------------------------------------------------------------------
 *  Filter the features that we're interested in.
 *----------------------------------------------------------------------------*/
GstElement *
ls_gst_autoplug_plug_source(GstElement        * source,
                            const gchar       * name,
                            const GstCaps     * caps)
{
    Typefind        typefind;
    GstElement    * bin;

    /* add an additional ref on the source, as we'll put it in a bin
     * and remove it from the bin later, which will decrease the ref by one */
    g_object_ref(source);

    typefind.source = source;
    autoplug_init(&typefind, name, caps);

    gst_element_set_state(typefind.audiosink, GST_STATE_PAUSED);
    gst_element_set_state(typefind.sink, GST_STATE_PAUSED);
    gst_element_set_state(typefind.bin, GST_STATE_PLAYING);
    gst_element_set_state(typefind.pipeline, GST_STATE_PLAYING);

    /* run */
    while (!typefind.done && gst_bin_iterate(GST_BIN(typefind.pipeline)));

    /* do an extra iteration, otherwise some gstreamer elements don't get
     * properly initialized, like the vorbis element.
     * see http://bugs.campware.org/view.php?id=1421 for details */
    gst_bin_iterate(GST_BIN(typefind.pipeline));

    if (!typefind.done) {
        autoplug_deinit(&typefind);
        return NULL;
    }

    /* remove the sink element */
    gst_element_unlink(typefind.bin, typefind.sink);
    gst_bin_remove(GST_BIN(typefind.pipeline), typefind.sink);
    typefind.sink = NULL;

    /* remove the typefind elements, and re-link with the source */
    autoplug_remove_typefind_elements(&typefind, GST_BIN(typefind.bin));
    gst_element_link(typefind.source, typefind.bin);

    /* destory the pipeline, but keep source and bin */
    g_object_ref(typefind.bin);
    gst_bin_remove(GST_BIN(typefind.pipeline), typefind.bin);

    bin = typefind.bin;

    autoplug_deinit(&typefind);

    gst_element_set_state(bin, GST_STATE_PAUSED);
    gst_bin_sync_children_state(GST_BIN(bin));

    return bin;
}

