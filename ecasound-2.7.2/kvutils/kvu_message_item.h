// -*- mode: C++; -*-
#ifndef INCLUDED_KVU_MESSAGE_ITEM_H
#define INCLUDED_KVU_MESSAGE_ITEM_H

#include <string>

/**
 * A simple version of C++ stringstream
 */
class MESSAGE_ITEM {
  
  std::string stringtemp;
  int flo_prec;

public:

    MESSAGE_ITEM(void) { flo_prec = 2; }

    void setprecision (int prec) { flo_prec = prec; }
    const std::string& to_string(void) const { return(stringtemp); }

    MESSAGE_ITEM& operator<< (std::string& c) { stringtemp = stringtemp + c; return (*this); }
    MESSAGE_ITEM& operator<< (const std::string& c) { stringtemp = stringtemp + c; return (*this); }
    MESSAGE_ITEM& operator<< (char c);
    MESSAGE_ITEM& operator<< (unsigned char c) { return (*this) << (char)c; }
    MESSAGE_ITEM& operator<< (signed char c) { return (*this) << (char)c; }
    MESSAGE_ITEM& operator<< (const char *s) { stringtemp = stringtemp + std::string(s); return (*this); }
    MESSAGE_ITEM& operator<< (const unsigned char *s)
	{ return (*this) << (const char*)s; }
    MESSAGE_ITEM& operator<< (const signed char *s)
	{ return (*this) << (const char*)s; }
    MESSAGE_ITEM& operator<< (const void *p);
    MESSAGE_ITEM& operator<< (int n);
    MESSAGE_ITEM& operator<< (unsigned int n);
    MESSAGE_ITEM& operator<< (long int n);
    MESSAGE_ITEM& operator<< (unsigned long n);

#if defined _ISOC99_SOURCE || defined _ISOC9X_SOURCE || defined _LARGEFILE_SOURCE || defined __GNUC__
    MESSAGE_ITEM& operator<< (long long int n);
    MESSAGE_ITEM& operator<< (unsigned long long int n);
#endif

    MESSAGE_ITEM& operator<< (short n) {return operator<<((int)n);}
    MESSAGE_ITEM& operator<< (unsigned short n) {return operator<<((unsigned int)n);}
    MESSAGE_ITEM& operator<< (bool b) { return operator<<((int)b); }
    MESSAGE_ITEM& operator<< (double n);
    MESSAGE_ITEM& operator<< (float n) { return operator<<((double)n); }
};

#endif
