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
#include <sstream>

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
        std::cerr << "Usage: serialtest {in|out} devicename [baud rate]"
                  << std::endl;
        std::exit(1);
    }

    LibSerial::SerialStream     s(sn);
    
    if (argc > 3) {
        int                 baudRate;
        std::stringstream   baudRateS(argv[3]);
        baudRateS >> baudRate;
        switch (baudRate) {
            case 50:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_50);
                    break;
            case 75:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_75);
                    break;
            case 110:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_110);
                    break;
            case 134:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_134);
                    break;
            case 150:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_150);
                    break;
            case 200:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_200);
                    break;
            case 300:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_300);
                    break;
            case 600:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_600);
                    break;
            case 1200:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_1200);
                    break;
            case 1800:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_1800);
                    break;
            case 2400:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_2400);
                    break;
            case 4800:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_4800);
                    break;
            case 9600:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_9600);
                    break;
            case 19200:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_19200);
                    break;
            case 38400:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_38400);
                    break;
            case 57600:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_57600);
                    break;
            case 115200:
                    s.SetBaudRate(LibSerial::SerialStreamBuf::BAUD_115200);
                    break;
            default:
                    std::cerr << "Invalid baud rate "
                              << baudRate << "." << std::endl;
                              << "Pick one of 50, 75, 110, 134, 150, 200, "
                                 "300, 600, 1200, 1800, 2400, 4800, 9600, "
                                 "19200, 38400, 57600, or 115200." << std::endl;
                    std::exit(1);
        }
    }

    std::cout << "Serial port "
              << sn
              << " is "
              << (s.bad() ? "bad." : "good.") << std::endl
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

