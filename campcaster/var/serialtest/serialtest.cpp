/**
 *  testing the serial ports
 *
 *  compile with: g++ -I $CC_DIR/usr/include -L $CC_DIR/usr/lib -lserial \
 *                    -o serialtest serialtest.cpp
 *  run with: LD_LIBRARY_PATH=$CC_DIR/usr/lib ./serialtest in /dev/ttyS0
 *        or: LD_LIBRARY_PATH=$CC_DIR/usr/lib ./serialtest out /dev/ttyUSB0
 *  (etc)
 */

#include <SerialStream.h>
#include <iostream>

int main (int       argc,
          char *    argv[])
{
    bool            in;
    std::string     sn;
    
    if (argc > 2) {
        if (argv[1] == "in") {
            in = true;
        } else {
            in = false;
        }
        
        sn = argv[2];
    } else {
        std::cerr << "Usage: setserial {in|out} devicename" << std::endl;
        std::exit(1);
    }

    LibSerial::SerialStream     s(sn);

    std::printf("Serial port %s is %s\n", sn.c_str(), s.bad() ? "bad" : "good");

    std::string     str;
    
    do {
        if (in) {
            std::getline(std::cin, str, '\n');
            s << str << std::endl;
        } else {
            std::getline(s, str, '\n');
            std::cerr << "received: " << str << std::endl;
        }
    } while (true);
}

