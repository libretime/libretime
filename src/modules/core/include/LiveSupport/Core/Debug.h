/*------------------------------------------------------------------------------

    Copyright   (c) 2003-2005 Max Howell <max.howell@methylblue.com>
                (c) 2006 Media Development Loan Fund
 
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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 2329 $
    Location : $URL: svn://code.campware.org/campcaster/trunk/campcaster/src/products/scheduler/src/PlaylistEvent.cxx $

------------------------------------------------------------------------------*/

#ifndef CAMPCASTER_DEBUG_H
#define CAMPCASTER_DEBUG_H

#include "configure.h"

#include <iostream>
#include <sys/time.h>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcValue.h>

#ifndef DEBUG_PREFIX
  #define CMP_PREFIX ""
#else
  #define CMP_PREFIX "[" DEBUG_PREFIX "] "
#endif

/**
 *  @namespace LiveSupport::Core::Debug
 *  @short A debug output API with indentation and block timing.
 *  @author Max Howell <max.howell@methylblue.com>
 *  @author Ian Monroe <ian@monroe.nu>
 */
namespace LiveSupport {
    namespace Core {
        namespace Debug {
            static int indentAmount = 0;
        }
    }
}
using namespace LiveSupport;
using namespace LiveSupport::Core;

#ifndef YDEBUG
    class NoDebugStream;
    
    typedef NoDebugStream & (*NDBGFUNC)(NoDebugStream &);
    
    class NoDebugStream {
    public:
        /// Default constructor.
        NoDebugStream() {}
        ~NoDebugStream() {}
        NoDebugStream& operator<<(NDBGFUNC) { return *this; }
        
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(short int )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(unsigned short int )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(char )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(unsigned char )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(int )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(unsigned int )  { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream &operator<<(const char *) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(const void *) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(void *) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(double) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(long) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(unsigned long) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(std::string) { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(boost::posix_time::ptime)
        { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(boost::posix_time::time_duration)
        { return *this; }
        /**
        * Does nothing.
        * @return this stream
        */
        NoDebugStream& operator<<(XmlRpc::XmlRpcValue)
        { return *this; }
    };
    static inline NoDebugStream debug()   { return NoDebugStream(); }
    static inline NoDebugStream warning() { return NoDebugStream(); }
    static inline NoDebugStream error()   { return NoDebugStream(); }
    inline NoDebugStream &endl( NoDebugStream & s) { return s; }
#else
    static inline std::ostream& debug()   { return std::cout << std::string(Debug::indentAmount, ' ') << CMP_PREFIX; }
    static inline std::ostream& warning() { return std::cout << std::string(Debug::indentAmount, ' ') << CMP_PREFIX << "[WARNING!] "; }
    static inline std::ostream& error()   { return std::cout << std::string(Debug::indentAmount, ' ') << CMP_PREFIX << "[ERROR!] "; }
#endif

using std::endl;

namespace LiveSupport { namespace Core {
namespace Debug
{
    /**
     * @class Debug::Block
     * @short Use this to label sections of your code
     *
     * Usage:
     *     #define DEBUG_PREFIX "Prefix"
     *     #include "LiveSupport/Core/Debug.h"
     *
     *     void function()
     *     {
     *         ...
     *         {
     *            Debug::Block myBlock( "section" );
     *
     *            debug() << "output1" << endl;
     *            debug() << "output2" << endl;
     *         }
     *         ...
     *     }
     *
     * Will output:
     *
     *     BEGIN: section
     *       [prefix] output1
     *       [prefix] output2
     *     END: section - Took 0.1s
     *
     * Its not thread-safe with the indentation count. But a race condition 
     * involving indentation width isn't a big deal.
     */

    class Block
    {
        public:
            Block( std::string label )
                : m_label( label )
            {
                gettimeofday( &m_start, 0 );
                debug() << "BEGIN: " << m_label << endl;
                indentAmount += 2; //critical section
            }
            ~Block()
            {
                timeval end;
                gettimeofday( &end, 0 );

                end.tv_sec -= m_start.tv_sec;
                if( end.tv_usec < m_start.tv_usec) {
                    // Manually carry a one from the seconds field.
                    end.tv_usec += 1000000;
                    end.tv_sec--;
                }
                end.tv_usec -= m_start.tv_usec;

                double duration = double(end.tv_sec) + (double(end.tv_usec) / 1000000.0);

                indentAmount -= 2; //critical section
                debug() << "END__: " << m_label << " - Took " << duration << "s\n";
            }
        private:
            const std::string m_label;
            timeval m_start;
    };
     
     /**
     * @name Debug::stamp()
     * @short To facilitate crash/freeze bugs, by making it easy to mark code that has been processed
     *
     * Usage:
     *
     *     {
     *         Debug::stamp();
     *         function1();
     *         Debug::stamp();
     *         function2();
     *         Debug::stamp();
     *     }
     *
     * Will output (assuming the crash occurs in function2()
     *
     *     app: Stamp: 1
     *     app: Stamp: 2
     *
     */

    inline void stamp()
    {
        static int n = 0;
        debug() << "| Stamp: " << ++n << endl;
    }
}
} } //LiveSupport and Core namespaces
#define DEBUG_BLOCK Debug::Block uniquelyNamedStackAllocatedStandardBlock( __PRETTY_FUNCTION__ );

/// Standard function announcer
#define DEBUG_FUNC_INFO { debug() << '[' << __PRETTY_FUNCTION__ << ']' << endl; }

/// Announce a line
#define DEBUG_LINE_INFO {  debug() << '[' << __PRETTY_FUNCTION__ << ']' << "Line: " << __LINE__ << endl; }


#endif //CAMPCASTER_DEBUG_H
