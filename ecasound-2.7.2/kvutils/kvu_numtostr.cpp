// ------------------------------------------------------------------------
// kvu_numtostr.cpp: Routines for converting string objects to numbers. 
// Copyright (C) 1999,2001 Kai Vehmanen
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#include <cstdio>
#include <string>

#include "kvu_numtostr.h"

std::string kvu_numtostr (unsigned char c) { return kvu_numtostr((int)c); }
std::string kvu_numtostr (signed char c) {  return kvu_numtostr((int)c); }
std::string kvu_numtostr (bool b) { return kvu_numtostr((int)b); }
std::string kvu_numtostr(unsigned short i) { return kvu_numtostr((int)i); }

std::string kvu_numtostr (float n, int flo_prec) { return kvu_numtostr(static_cast<double>(n), flo_prec); }

std::string kvu_numtostr (int n) {
  char ctmp[12];
  snprintf(ctmp, 12, "%d",n);
  ctmp[11] = 0;
  return(std::string(ctmp));
}

std::string kvu_numtostr (const void *p) {
  char ctmp[12];
  snprintf(ctmp, 12, "%p",p);
  ctmp[11] = 0;
  return(std::string(ctmp));
}

std::string kvu_numtostr (unsigned int n) {
  char ctmp[12];
  snprintf(ctmp, 12, "%u",n);
  ctmp[11] = 0;
  return(std::string(ctmp));
}

std::string kvu_numtostr (long int n) {
  char ctmp[12];
  snprintf(ctmp, 12, "%ld",n);
  ctmp[11] = 0;
  return(std::string(ctmp));
}

std::string kvu_numtostr (unsigned long int n) {
  char ctmp[12];
  snprintf(ctmp, 12, "%lu",n);
  ctmp[11] = 0;
  return(std::string(ctmp));
}

#if defined _ISOC99_SOURCE || defined _ISOC9X_SOURCE || defined _LARGEFILE_SOURCE || defined __GNUC__
std::string kvu_numtostr (long long int n) {
  char ctmp[24];
  snprintf(ctmp, 24, "%lli", n);
  ctmp[23] = 0;
  return (std::string(ctmp));    
}

std::string kvu_numtostr (unsigned long long int n) {
  char ctmp[24];
  snprintf(ctmp, 24, "%llu", n);
  ctmp[23] = 0;
  return (std::string(ctmp));    
}
#endif


std::string kvu_numtostr (double n, int flo_prec) {
  char ctmp[32];
  snprintf(ctmp, 32, "%.*f",flo_prec, n);
  ctmp[31] = 0;
  return(std::string(ctmp));
}
