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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision$
    Location : $URL: svn+ssh://fgerlits@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/core/src/FileTools.cxx $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_TIME_H
#include <time.h>
#else
#error need time.h
#endif


#include <fstream>
#include <curl/curl.h>
#include <curl/easy.h>

#include "LiveSupport/Core/FileTools.h"


using namespace LiveSupport;
using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Copy the contents of a URL to a local file.
 *----------------------------------------------------------------------------*/
void
FileTools :: copyUrlToFile(const std::string &      url,
                           const std::string &      path)
                                                throw (std::runtime_error)
{
    FILE*   file    = fopen(path.c_str(), "wb");
    if (!file) {
        throw std::runtime_error("File location is not writable.");
    }

    CURL*    handle     = curl_easy_init();
    if (!handle) {
        fclose(file);
        throw std::runtime_error("Could not obtain curl handle.");
    }
    
    int    status =   curl_easy_setopt(handle, CURLOPT_URL, url.c_str()); 
    status |=   curl_easy_setopt(handle, CURLOPT_WRITEDATA, file);
    status |=   curl_easy_setopt(handle, CURLOPT_HTTPGET);

    if (status) {
        fclose(file);
        throw std::runtime_error("Could not set curl options.");
    }

    status =    curl_easy_perform(handle);

    if (status) {
        fclose(file);
        throw std::runtime_error("Error downloading file.");
    }

    curl_easy_cleanup(handle);
    fclose(file);
}


/*------------------------------------------------------------------------------
 *  Upload the contents of a local file to a writable URL.
 *----------------------------------------------------------------------------*/
void
FileTools :: copyFileToUrl(const std::string &      path,
                           const std::string &      url)
                                                throw (std::runtime_error)
{
    FILE*   file    = fopen(path.c_str(), "rb");
    if (!file) {
        throw std::runtime_error("File not found.");
    }
    fseek(file, 0, SEEK_END);
    long    fileSize  = ftell(file);
    rewind(file);

    CURL*   handle  = curl_easy_init();
    if (!handle) {
        throw std::runtime_error("Could not obtain curl handle.");
    }
    
    int    status = curl_easy_setopt(handle, CURLOPT_READDATA, file);
    status |=   curl_easy_setopt(handle, CURLOPT_INFILESIZE, fileSize); 
                                         // works for files of size up to 2 GB
    status |=   curl_easy_setopt(handle, CURLOPT_PUT, 1); 
    status |=   curl_easy_setopt(handle, CURLOPT_URL, url.c_str()); 
//  status |=   curl_easy_setopt(handle, CURLOPT_HEADER, 1);
//  status |=   curl_easy_setopt(handle, CURLOPT_ENCODING, "deflate");

    if (status) {
        throw std::runtime_error("Could not set curl options.");
    }

    status = curl_easy_perform(handle);

    if (status) {
        throw std::runtime_error("Error uploading file.");
    }

    curl_easy_cleanup(handle);
    fclose(file);
}

