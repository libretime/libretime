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

#include <SerialStream.h>
#include <iostream>

/**
 *  testing the serial ports
 *
 *  compile with: g++ -I $CC_DIR/usr/include -L $CC_DIR/usr/lib -lserial \
 *                    -o serialtest serialtest.cpp
 *  run with: LD_LIBRARY_PATH=$CC_DIR/usr/lib ./serialtest in /dev/ttyS0
 *        or: LD_LIBRARY_PATH=$CC_DIR/usr/lib ./serialtest out /dev/ttyUSB0
 *  (etc)
 */
int main (int       argc,
          char *    argv[])
{
    bool            in;
    std::string     sn;
    
    if (argc > 2) {
        if (std::string(argv[1]) == "in") {
            in = true;
        } else {
            in = false;
        }
        
        sn = argv[2];

    } else {
        std::cerr << "Usage: serialtest {in|out} devicename" << std::endl;
        std::exit(1);
    }

    LibSerial::SerialStream     s(sn);

    std::cout << "Serial port "
              << sn
              << " is "
              << (s.bad() ? "bad" : "good") << std::endl
              << (in ? "Receiving." : "Sending.") << std::endl;

    std::string     str;
    
    do {
        if (in) {
            std::getline(s, str, '\n');
            if (str.length() > 0) {
                std::cout << str << std::endl;
            }
        } else {
            std::getline(std::cin, str, '\n');
            s << str << std::endl;
        }
    } while (true);
}

