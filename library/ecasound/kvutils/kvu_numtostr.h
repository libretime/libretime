#ifndef INCLUDED_KVU_NUMTOSTR_H
#define INCLUDED_KVU_NUMTOSTR_H

#include <string>

std::string kvu_numtostr(char c);
std::string kvu_numtostr(unsigned char c);
std::string kvu_numtostr(signed char c);
std::string kvu_numtostr(const void *p);
std::string kvu_numtostr(int n);
std::string kvu_numtostr(unsigned int n);
std::string kvu_numtostr(long int n);
std::string kvu_numtostr(unsigned long int n);
#if defined _ISOC99_SOURCE || defined _ISOC9X_SOURCE || defined _LARGEFILE_SOURCE || defined __GNUC__
std::string kvu_numtostr(long long int n);
std::string kvu_numtostr(unsigned long long int n);
#endif
std::string kvu_numtostr(short n);
std::string kvu_numtostr(unsigned short n);
std::string kvu_numtostr(bool b);
std::string kvu_numtostr(double n, int flo_prec = 2);
std::string kvu_numtostr(float n, int flo_prec = 2);

#endif
