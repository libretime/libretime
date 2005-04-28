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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/src/Uuid.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_SYS_TIME_H
#include <sys/time.h>
#else
#error need sys/time.h
#endif

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#else
#error need unistd.h
#endif


#include <cstdlib>
#include <iomanip>
#include <sstream>

#include "LiveSupport/Core/Uuid.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  Set the following to the number of 100ns ticks of the actual
 *  resolution of your system's clock
 *----------------------------------------------------------------------------*/
#define UUIDS_PER_TICK 1024


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Generate a globally unique id.
 *----------------------------------------------------------------------------*/
Ptr<Uuid>::Ref
Uuid :: generateId(void)                        throw ()
{
    Ptr<Uuid>::Ref  id(new Uuid());

    UuidTime    timestamp;
    UuidTime    lastTime;
    uint16_t    clockseq;
    UuidNode    node;
    UuidNode    lastNode;
    int         f;

    /* acquire system wide lock so we're alone */
    //LOCK;

    /* get current time */
    getCurrentTime(&timestamp);

    /* get node ID */
    getIeeeNodeIdentifier(&node);

    /* get saved state from NV storage */
    f = id->readState(&clockseq, &lastTime, &lastNode);

    /* if no NV state, or if clock went backwards, or node ID changed
       (e.g., net card swap) change clockseq */
    if (!f || memcmp(&node, &lastNode, sizeof(UuidNode))) {
        clockseq = trueRandom();
    } else if (timestamp < lastTime) {
        clockseq++;
    }

    /* stuff fields into the UUID */
    id->format(clockseq, timestamp, node);
    id->representAsString();

    /* save the state for next time */
    id->writeState(clockseq, timestamp, node);

    //UNLOCK;

    return id;
}


/*------------------------------------------------------------------------------
 *  Format the UUID
 *----------------------------------------------------------------------------*/
void
Uuid :: format(uint16_t     clockSeq,
               UuidTime     timestamp,
               UuidNode     node)                               throw ()
{
    /* Construct a version 1 uuid with the information we've gathered
     * plus a few constants. */
    timeLow                = (unsigned long)(timestamp & 0xFFFFFFFF);
    timeMid                = (unsigned short)((timestamp >> 32) & 0xFFFF);
    timeHiAndVersion       = (unsigned short)((timestamp >> 48) & 0x0FFF);
    timeHiAndVersion      |= (1 << 12);
    clockSeqLow            = clockSeq & 0xFF;
    clockSeqHiAndReserved  = (clockSeq & 0x3F00) >> 8;
    clockSeqHiAndReserved |= 0x80;

    for (int i = 0; i < 6; ++i) {
        this->node[i] = node.nodeId[i];
    }
}


/*------------------------------------------------------------------------------
 *  Create a string representation of the UUID
 *----------------------------------------------------------------------------*/
void
Uuid :: representAsString(void)                                 throw ()
{
    std::stringstream  sstr;

    sstr << std::hex << std::setw(8) << std::setfill('0') << timeLow << '-'
         << std::hex << std::setw(4) << std::setfill('0') << timeMid << '-'
         << std::hex << std::setw(4) << std::setfill('0')
                     << timeHiAndVersion << '-'
         << std::hex << std::setw(2) << std::setfill('0') 
                     << (unsigned short) clockSeqHiAndReserved << '-'
         << std::hex << std::setw(2) << std::setfill('0') 
                     << (unsigned short) clockSeqLow << '-';
    for (int i = 0; i < 6; ++i) {
        sstr << std::hex << std::setw(2) << std::setfill('0')
             << (unsigned short) this->node[i];
    }

    idAsString = sstr.str();
}


/*------------------------------------------------------------------------------
 *  Read the current state from non-volatile storage
 *----------------------------------------------------------------------------*/
int
Uuid :: readState(uint16_t    * clockSeq,
                  UuidTime    * timestamp,
                  UuidNode    * node)                           throw ()
{
    // TODO: read the state from non-volatile storage

    return 0;
}


/*------------------------------------------------------------------------------
 *  Write the current state to non-volatile storage
 *----------------------------------------------------------------------------*/
void
Uuid :: writeState(uint16_t     clockSeq,
                   UuidTime     timestamp,
                   UuidNode     node)                       throw ()
{
    // TODO: write the current state to non-volatile storage
}


/*------------------------------------------------------------------------------
 *  Get the current time into a timestamp
 *----------------------------------------------------------------------------*/
void
Uuid :: getCurrentTime(UuidTime  *timestamp)                throw ()
{
    UuidTime             timeNow;
    static UuidTime      timeLast;
    static uint16_t      uuidsThisTick;
    static bool          inited = false;

    if (!inited) {
        getSystemTime(&timeNow);
        uuidsThisTick = UUIDS_PER_TICK;
        inited = true;
    };

    while (true) {
        getSystemTime(&timeNow);

        /* if clock reading changed since last UUID generated... */
        if (timeLast != timeNow) {
            /* reset count of uuids gen'd with this clock reading */
            uuidsThisTick = 0;
            break;
        };
        if (uuidsThisTick < UUIDS_PER_TICK) {
            uuidsThisTick++;
            break;
        };
        /* going too fast for our clock; spin */
    };

    /* add the count of uuids to low order bits of the clock reading */
    *timestamp = timeNow + uuidsThisTick;
}


/*------------------------------------------------------------------------------
 *  Get the system time in the UUID UTC base time, which is October 15, 1582
 *----------------------------------------------------------------------------*/
void
Uuid :: getSystemTime(UuidTime * uuidTime)                      throw ()
{
    struct timeval tp;

    gettimeofday(&tp, (struct timezone *)0);

    /* Offset between UUID formatted times and Unix formatted times.
       UUID UTC base time is October 15, 1582.
       Unix base time is January 1, 1970.
    */
    *uuidTime = (tp.tv_sec * 10000000)
              + (tp.tv_usec * 10)
              + 0x01B21DD213814000LL;
}


/*------------------------------------------------------------------------------
 *  Get the IEEE node identifier
 *----------------------------------------------------------------------------*/
void
Uuid :: getIeeeNodeIdentifier(UuidNode    * node)           throw ()
{
    long    hostId = gethostid();

    node->nodeId[5] = (char) (hostId & 0x0000000000ffL);
    node->nodeId[4] = (char) ((hostId & 0x00000000ff00L) >> 8);
    node->nodeId[3] = (char) ((hostId & 0x000000ff0000L) >> 16);
    node->nodeId[2] = (char) ((hostId & 0x0000ff000000L) >> 24);
    // these will be 0, as the returned node is only 32 bits
    node->nodeId[1] = 0;
    node->nodeId[0] = 0;
}


/*------------------------------------------------------------------------------
 *  Generate a random number
 *----------------------------------------------------------------------------*/
uint16_t
Uuid :: trueRandom(void)                                    throw ()
{
    static bool inited = false;
    UuidTime    timeNow;

    if (!inited) {
        getSystemTime(&timeNow);
        timeNow = timeNow/UUIDS_PER_TICK;
        srand((unsigned int)(((timeNow >> 32) ^ timeNow)&0xffffffff));
        inited = true;
    };

    return rand();
}


/*------------------------------------------------------------------------------
 *  Compare two ids.
 *----------------------------------------------------------------------------*/
bool
Uuid :: compare(const Uuid    & id1,
                const Uuid    & id2)                        throw ()
{
    if (!(id1.timeLow == id2.timeLow
       && id1.timeMid == id2.timeMid
       && id1.timeHiAndVersion == id2.timeHiAndVersion
       && id1.clockSeqHiAndReserved == id2.clockSeqHiAndReserved
       && id1.clockSeqLow == id2.clockSeqLow)) {
        
        return false;
    }

    for (int i = 0; i < 6; ++i) {
        if (id1.node[i] != id2.node[i]) {
            return false;
        }
    }

    return true;
}

