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
#ifndef SmilHandler_h
#define SmilHandler_h

#ifndef __cplusplus
#error This is a C++ include file
#endif

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_STRING_H
#include <string.h>
#else
#error need string.h
#endif

#include <libxml/parser.h>
#include <libxml/tree.h>

#include <list>

#define NSEC_PER_SEC        1000000000LL
#define SEC_PER_MIN         60
#define SEC_PER_HOUR        3600
#define NSEC_PER_SEC_FLOAT  1000000000.0
#define SEC_PER_MIN_FLOAT   60.0
#define SEC_PER_HOUR_FLOAT  3600.0

/* ===================================================  local data structures */

/**
 *  The AnimationDescription class.
 *
 *  It is assumed that thic class supports only animations that have
 *
 *  attributeName="soundLevel",
 *  calcMode="linear" and
 *  fill="freeze"
 *
 *  If these conditions are not met, the entire animate entry in xml will
 *  be ignored and this object will never be created.
 */
class AnimationDescription {
public:
    double m_from;
    double m_to;
    guint64 m_begin;
    guint64 m_end;
    
    AnimationDescription():
        m_from(0.0),
        m_to(0.0),
        m_begin(0),
        m_end(0)
    {
    }
    ~AnimationDescription()
    {
    }
};

/**
 *  The AudioDescription class.
 */
class AudioDescription {
public:
    gchar *m_src;
    gint64 m_begin;
    gint64 m_clipBegin;
    gint64 m_clipEnd;
    gint64 m_clipLength;
	gint64 m_Id;
    std::vector<AnimationDescription*> m_animations;
    
    AudioDescription():
        m_src(NULL),
        m_begin(0),
        m_clipBegin(0),
        m_clipEnd(0),
		m_clipLength(0),
		m_Id(0)
    {
    }
    ~AudioDescription()
    {
    }
    
    void release()
    {
        for(int i = m_animations.size(); i > 0; i--){
            delete m_animations[i-1];
        }
        m_animations.clear();
    }
};


/**
 *  A class to parse SMIL file and return playback info in sequence.
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
class SmilHandler
{
    xmlDocPtr m_document;
    xmlNode *m_bodyChildren;
    xmlNode *m_parChildren;
    SmilHandler *m_subSmil;
	gint64 m_smilOffset;

public:

    SmilHandler(){
        m_document = NULL;
        m_bodyChildren = NULL;
        m_parChildren = NULL;
        m_subSmil = NULL;
		m_smilOffset = 0L;
    }

    ~SmilHandler(){
        xmlCleanupParser();
        if(m_document != NULL){
            xmlFreeDoc(m_document);
        }
    }

    /**
    *  Process the sink input as a SMIL file.
    *  The bin container inside the MinimalAudioSmil object will be filled
    *  with gstreamer elements, playing audio as described by the SMIL file.
    *
    *  @para smil a MinimalAudioSmil object.
    *  @return TRUE if processing was successful, FALSE otherwise.
    */
    gboolean openSmilFile(const gchar *xmlFile, gint64 offset){
        xmlNode *node;
		m_smilOffset = offset;

        /* parse the XML files */
        m_document = xmlReadFile(xmlFile, NULL, XML_PARSE_RECOVER);
        if (!m_document || !(node = getBodyElement(m_document))) {
            return false;
        }

        m_bodyChildren = node->children;
        return true;
    }

    /**
    *  Fetch next audio entry in sequence.
    *  @return AudioDescription object if processing was successful, NULL otherwise.
    */
    AudioDescription *getNext(){
emptysmilrecovery:
        AudioDescription *audioDescription = NULL;
        if(m_subSmil != NULL){
            audioDescription = m_subSmil->getNextInternal();
            if(audioDescription == NULL){
                delete m_subSmil;
                m_subSmil = NULL;
            }else{
				gint64 actualLength = audioDescription->m_clipEnd != -1 ? 
					audioDescription->m_clipEnd - audioDescription->m_clipBegin :
					audioDescription->m_clipLength - audioDescription->m_clipBegin;
				if(m_smilOffset >= actualLength)
				{
					m_smilOffset -= actualLength;
					goto emptysmilrecovery;
				}
                return audioDescription;
            }
        }

        if(m_parChildren){//we are currently traversing par segment
            audioDescription = getNextPar();
        }

        if(audioDescription == NULL && m_bodyChildren){//par exaused, see if there is more in the body segment
            for (; m_bodyChildren; m_bodyChildren = m_bodyChildren->next) {
                if (m_bodyChildren->type == XML_ELEMENT_NODE) {
                    if (!strcmp((const char*)m_bodyChildren->name, "par")) {
                        m_parChildren = m_bodyChildren->children;
                        audioDescription = getNextPar();
                        if(audioDescription != NULL){
                            m_bodyChildren = m_bodyChildren->next;
                            break;
                        }
                    } else {
                        GST_WARNING("unsupported SMIL element %s found", m_bodyChildren->name);
                    }
                }
            }
        }

        if(audioDescription != NULL && std::string(audioDescription->m_src).find(".smil") != std::string::npos){//we have a sub smil
            m_subSmil = new SmilHandler();
            m_subSmil->openSmilFile(audioDescription->m_src, m_smilOffset);
            delete audioDescription;
            audioDescription = m_subSmil->getNextInternal();
            if(audioDescription == NULL){
                delete m_subSmil;
                m_subSmil = NULL;
                goto emptysmilrecovery;
            }
        }
		if(audioDescription != NULL)
		{
			gint64 actualLength = audioDescription->m_clipEnd != -1 ? 
				audioDescription->m_clipEnd - audioDescription->m_clipBegin :
				audioDescription->m_clipLength - audioDescription->m_clipBegin;
			if(m_smilOffset >= actualLength)
			{
				m_smilOffset -= actualLength;
				goto emptysmilrecovery;
			}
		}

        return audioDescription;
    }
	
	gint64 getClipOffset() {
        gint64 offset = m_smilOffset;
		m_smilOffset = 0L;//offset only valid after the first getNext
		return offset;
	}
    
    gint64 getPlayLength() throw() {
        gint64 ns = 0LL;
        //TODO: calculate proper playlist length
        return ns;
    }

private:

    /**
    *  Fetch next audio entry in sequence.
    *  @return AudioDescription object if processing was successful, NULL otherwise.
    */
    AudioDescription *getNextInternal(){
emptysmilrecoveryint:
        AudioDescription *audioDescription = NULL;
        if(m_subSmil != NULL){
            audioDescription = m_subSmil->getNextInternal();
            if(audioDescription == NULL){
                delete m_subSmil;
                m_subSmil = NULL;
            }else{
                return audioDescription;
            }
        }

        if(m_parChildren){//we are currently traversing par segment
            audioDescription = getNextPar();
        }

        if(audioDescription == NULL && m_bodyChildren){//par exaused, see if there is more in the body segment
            for (; m_bodyChildren; m_bodyChildren = m_bodyChildren->next) {
                if (m_bodyChildren->type == XML_ELEMENT_NODE) {
                    if (!strcmp((const char*)m_bodyChildren->name, "par")) {
                        m_parChildren = m_bodyChildren->children;
                        audioDescription = getNextPar();
                        if(audioDescription != NULL){
                            m_bodyChildren = m_bodyChildren->next;
                            break;
                        }
                    } else {
                        GST_WARNING("unsupported SMIL element %s found", m_bodyChildren->name);
                    }
                }
            }
        }

        if(audioDescription != NULL && std::string(audioDescription->m_src).find(".smil") != std::string::npos){//we have a sub smil
            m_subSmil = new SmilHandler();
            m_subSmil->openSmilFile(audioDescription->m_src, m_smilOffset);
            delete audioDescription;
            audioDescription = m_subSmil->getNextInternal();
            if(audioDescription == NULL){
                delete m_subSmil;
                m_subSmil = NULL;
                goto emptysmilrecoveryint;
            }
        }

        return audioDescription;
    }
	
    /**
    *  Fetch next audio entry from "<par>" SMIL segment.
    *
    *  @return AudioDescription object if processing was successful, NULL otherwise..
    */
    AudioDescription *getNextPar(){
        AudioDescription *audioDescription = NULL;
        for (; m_parChildren; m_parChildren = m_parChildren->next) {
            if (m_parChildren->type == XML_ELEMENT_NODE) {
                if (!strcmp((const char*)m_parChildren->name, "audio")) {
                    audioDescription = getNextAudio(m_parChildren);
                    if(audioDescription != NULL){
                        m_parChildren = m_parChildren->next;
                        break;
                    }
                } else {
                    GST_WARNING("unsupported SMIL element %s found inside a par", m_parChildren->name);
                }
            }
        }
        return audioDescription;
    }

    /**
    *  Fetch next audio entry from "<audio>" SMIL segment.
    *
    *  @param audio an "<audio>" SMIL element.
    *  @return AudioDescription object if processing was successful, NULL otherwise..
    */
    AudioDescription *getNextAudio(xmlNode *audio){
        AudioDescription *audioDescription = NULL;

        xmlNode           * node;
        xmlAttribute      * attr;
        gchar             * src       	= 0;
        gchar             * begin     	= 0;
        gchar             * clipBegin 	= 0;
        gchar             * clipEnd   	= 0;
        gchar             * clipLength	= 0;
        gchar             * idStr     	= 0;
    
        /* handle the attributes */
        for (attr = ((xmlElement*)audio)->attributes; attr; attr = (xmlAttribute*) attr->next) {
            /* TODO: support attribute values that are represented with
            *       more than one text node, in all content assignments below */
            if (!strcmp((const char*)attr->name, "src")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    src = (gchar*) node->content;
                }
            } else if (!strcmp((const char*)attr->name, "id")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    idStr = (gchar*) node->content;
                }
            } else if (!strcmp((const char*)attr->name, "begin")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    begin = (gchar*) node->content;
                }
            } else if (!strcmp((const char*)attr->name, "clipBegin")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    clipBegin = (gchar*) node->content;
                }
            } else if (!strcmp((const char*)attr->name, "clipEnd")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    clipEnd = (gchar*) node->content;
                }
            } else if (!strcmp((const char*)attr->name, "clipLength")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    clipLength = (gchar*) node->content;
                }
            } else {
                GST_WARNING("unsupported SMIL audio element attribute: %s",
                            attr->name);
            }
        }

		audioDescription = new AudioDescription();
		if(begin)
		{
			audioDescription->m_begin = su_smil_clock_value_to_nanosec(begin);
		}
		if(clipBegin)
		{
			audioDescription->m_clipBegin = su_smil_clock_value_to_nanosec(clipBegin);
		}
		if(clipEnd)
		{
			audioDescription->m_clipEnd = su_smil_clock_value_to_nanosec(clipEnd);
			if(audioDescription->m_clipEnd <= 0)//clip end can never be 0, force it to -1
			{
				audioDescription->m_clipEnd = -1;
			}
		}
		if(clipLength)
		{
			audioDescription->m_clipLength = su_smil_clock_value_to_nanosec(clipLength);
		}
		if(idStr)
		{
			std::stringstream    idReader(idStr);
			idReader >> audioDescription->m_Id;
		}
        audioDescription->m_src = src;
        // now handle the possible animate elements inside this audio element
        for (node = audio->children; node; node = node->next) {
            if (node->type == XML_ELEMENT_NODE) {
                if (!strcmp((const char*)node->name, "animate")) {
                    AnimationDescription *anim = getNextAnimate(audioDescription->m_begin, node);
                    if(anim != NULL){
                        audioDescription->m_animations.push_back(anim);
                    }
                } else {
                    GST_WARNING("unsupported SMIL element %s found inside a audio", node->name);
                }
            }
        }
        return audioDescription;
    }

    /**
    *  Handle an "<animate>" element.
    *
    *  @param offset the offset in nanoseconds that the animation should
    *         begin at. this is usually the begin="xx" attribute value
    *         of the containing element.
    *  @param animate the "<animate>" element to handle.
    *  @return AnimationDescription object if processing was successful, NULL otherwise..
    */
    AnimationDescription *getNextAnimate(gint64 offset, xmlNode *animate){
        xmlAttribute      * attr;
        double              from  = 0.0;
        double              to    = 0.0;
        guint64              begin = 0;
        guint64              end   = 0;
    
        /* handle the attributes */
        for (attr = ((xmlElement*)animate)->attributes; attr; attr = (xmlAttribute*) attr->next) {
    
            xmlNode * node;
    
            /* TODO: support attribute values that are represented with
            *       more than one text node, in all content assignments below */
            if (!strcmp((const char*)attr->name, "attributeName")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gchar* cstr = (gchar*) node->content;
                    /* we only support soundLevel animation at the moment */
                    if (strcmp(cstr, "soundLevel")) {
                        GST_WARNING("unsupported animate attribute: %s", cstr);
                        return 0;
                    }
                }
            } else if (!strcmp((const char*)attr->name, "calcMode")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gchar* cstr = (gchar*) node->content;
                    /* we only support linear calc mode at the moment */
                    if (strcmp(cstr, "linear")) {
                        GST_WARNING("unsupported animate calcMode: %s", cstr);
                        return 0;
                    }
                }
            } else if (!strcmp((const char*)attr->name, "fill")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gchar* cstr = (gchar*) node->content;
                    /* we only support freeze fill at the moment */
                    if (strcmp(cstr, "freeze")) {
                        GST_WARNING("unsupported animate fill: %s", cstr);
                        return 0;
                    }
                }
            } else if (!strcmp((const char*)attr->name, "from")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gchar* cstr = (gchar*) node->content;
                    if (!su_smil_parse_percent(cstr, &from)) {
                        GST_WARNING("bad from value: %s", cstr);
                        return 0;
                    }
                }
            } else if (!strcmp((const char*)attr->name, "to")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gchar* cstr = (gchar*) node->content;
                    if (!su_smil_parse_percent(cstr, &to)) {
                        GST_WARNING("bad to value: %s", cstr);
                        return 0;
                    }
                }
            } else if (!strcmp((const char*)attr->name, "begin")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gint64  i;
                    gchar* cstr  = (gchar*) node->content;
                    begin     = su_smil_clock_value_to_nanosec(cstr);// + offset;
//                    begin = ((double) i) / NSEC_PER_SEC_FLOAT;
                }
            } else if (!strcmp((const char*)attr->name, "end")) {
                if ((node = attr->children) && node->type == XML_TEXT_NODE) {
                    gint64  i;
                    gchar* cstr = (gchar*) node->content;
                    end   = su_smil_clock_value_to_nanosec(cstr);// + offset;
//                    end = ((double) i) / NSEC_PER_SEC_FLOAT;
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
        AnimationDescription *animDesc = new AnimationDescription();
        animDesc->m_from = from;
        animDesc->m_to = to;
        animDesc->m_begin = begin;
        animDesc->m_end = end;
        return animDesc;
    }

    xmlNode *getBodyElement(xmlDocPtr  document){
        xmlNode * node = document->children;
        if (!node || strcmp((const char*)node->name, "smil")) {
            return 0;
        }
        for (node = node->children; node; node = node->next) {
            if (node->type == XML_ELEMENT_NODE
            && !strcmp((const char*)node->name, "body")) {
                return node;
            }
        }
        return 0;
    }
    /*------------------------------------------------------------------------------
    *  Convert a hour-minute-second triplet to nanoseconds
    *----------------------------------------------------------------------------*/
    static gint64
    su_hms_to_nanosec(int      hours,
                int      minutes,
                double   seconds)
    {
        gint64  nanosec;
        double  nsec;
        
        nsec = seconds * NSEC_PER_SEC_FLOAT;
        
        nanosec  = (gint64) nsec;
        nanosec += ((gint64) hours) * NSEC_PER_SEC;
        nanosec += ((gint64) minutes) * SEC_PER_MIN * NSEC_PER_SEC;
        
        return nanosec;
    }
    /**
    *  Parse the clock value according to the SMIL clock spec
    *
    *  see http://www.w3.org/TR/2005/REC-SMIL2-20050107/smil-timing.html#Timing-ClockValueSyntax
    *
    *  the BNF for the value is:
    *  
    *  <pre><code>
    *  Clock-value         ::= ( Full-clock-value | Partial-clock-value
    *                          | Timecount-value )
    *  Full-clock-value    ::= Hours ":" Minutes ":" Seconds ("." Fraction)?
    *  Partial-clock-value ::= Minutes ":" Seconds ("." Fraction)?
    *  Timecount-value     ::= Timecount ("." Fraction)? (Metric)?
    *  Metric              ::= "h" | "min" | "s" | "ms"
    *  Hours               ::= DIGIT+; any positive number
    *  Minutes             ::= 2DIGIT; range from 00 to 59
    *  Seconds             ::= 2DIGIT; range from 00 to 59
    *  Fraction            ::= DIGIT+
    *  Timecount           ::= DIGIT+
    *  2DIGIT              ::= DIGIT DIGIT
    *  DIGIT               ::= [0-9]
    *  </code></pre>
    *
    *  @param value the SMIL clock value in string form
    *  @return the clock value in nanoseconds
    */
    guint64
    su_smil_clock_value_to_nanosec(const gchar    * value)
    {
        int     hours;
        int     minutes;
        double  seconds;
    
        /* see if it's a full-clock-value */
        if (sscanf(value, "%2d:%2d:%lf", &hours, &minutes, &seconds) == 3) {
            return su_hms_to_nanosec(hours, minutes, seconds);
        }
    
        /* see if it's a partial-clock-value */
        if (sscanf(value, "%2d:%lf", &minutes, &seconds) == 2) {
        return su_hms_to_nanosec(0, minutes, seconds);
        }
    
        /* see if it's a timecount-value, in hours */
        if (g_str_has_suffix(value, "h")
        && sscanf(value, "%lfh", &seconds) == 1) {
        return su_hms_to_nanosec(0, 0, seconds * SEC_PER_HOUR_FLOAT);
        }
    
        /* see if it's a timecount-value, in minutes */
        if (g_str_has_suffix(value, "min")
        && sscanf(value, "%lfmin", &seconds) == 1) {
        return su_hms_to_nanosec(0, 0, seconds * SEC_PER_MIN_FLOAT);
        }
    
        /* see if it's a timecount-value, in millisecs */
        if (g_str_has_suffix(value, "ms")
        && sscanf(value, "%lfms", &seconds) == 1) {
        return su_hms_to_nanosec(0, 0, seconds / 100.0);
        }
    
        /* it's a timecount-value, either with no metric, or explicit seconds */
        if (sscanf(value, "%lfs", &seconds) == 1) {
        return su_hms_to_nanosec(0, 0, seconds);
        }
    
        return -1LL;
    }
    
    
    /**
    *  Parse a string as a percentage value, and return the result as a
    *  float. Indicate parse error.
    *
    *  @param str the string to parse.
    *  @param value the parsed value (out parameter).
    *  @return TRUE if parsing went OK, FALSE otherwise.
    */
    gboolean
    su_smil_parse_percent(const gchar    * str,
                    double         * value)
    {
        double  val;
    
        if (g_str_has_suffix(str, "%")
        && sscanf(str, "%lf%%", &val) == 1) {
            *value = val / 100.0;
            return TRUE;
        }
    
        return FALSE;
    }
};

#endif // SmilHandler_h

