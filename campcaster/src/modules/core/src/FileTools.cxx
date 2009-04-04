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

#ifdef HAVE_TIME_H
#include <stdio.h>
#else
#error need stdio.h
#endif


#include <fcntl.h>
#include <libtar.h>

#include <iostream>

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
    status |=   curl_easy_setopt(handle, CURLOPT_HTTPGET, 1);

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


/*------------------------------------------------------------------------------
 *  Create a temporary file name
 *----------------------------------------------------------------------------*/
const std::string
FileTools :: tempnam(void)                                      throw ()
{
    std::string     fileName(::tempnam(NULL, NULL));

    return fileName;
}


/*------------------------------------------------------------------------------
 *  Append a file to an existing tarball
 *----------------------------------------------------------------------------*/
void
FileTools :: appendFileToTarball(const std::string & tarFileName,
                                 const std::string & newFileRealName,
                                 const std::string & newFileInTarball)
                                                    throw (std::runtime_error)
{
    TAR   * tar;
    off_t   tarFileEnd; // keeps read position it tarball

    // first chop off the existing EOF from the tarball

    // open for reading first to determine where EOT block begins  
    if (tar_open(&tar,
                 (char*) tarFileName.c_str(),
                 NULL,
                 O_RDONLY,
                 0,
                 0) == -1) {
        throw std::runtime_error("can't open tarball");
    }

    // go through all files in tarball and record end position
    // of the last file read
    tarFileEnd = 0;
    while (th_read(tar) == 0) {
        if (TH_ISREG(tar)) {
            tar_skip_regfile(tar);
        }
        tarFileEnd = lseek(tar->fd, 0, SEEK_CUR);
    }

    // at this point, tarFileEnd is position where EOT block begins
    tar_close(tar); // close for reading

    //truncate EOT from the tarball
    if (truncate(tarFileName.c_str(), tarFileEnd) == -1) {
        throw std::runtime_error("can't truncate tarball");
    }

    // and now append the new file, and put an EOF at the end
    
    // open truncated tarball (without EOT block) for writing and append
    if (tar_open(&tar,
                 (char*) tarFileName.c_str(),
                 NULL,
                 O_WRONLY | O_APPEND,
                 0666,
                 0) == -1) {
        throw std::runtime_error("can't open tarball");
    }

    // add the new file
    if (tar_append_file(tar,
                        (char*) newFileRealName.c_str(),
                        (char*) newFileInTarball.c_str()) == -1) {
        tar_close(tar);
        throw std::runtime_error("can't append file to tarball");
    }

    // add EOT at the end and close tarball
    tar_append_eof(tar);
    tar_close(tar);
}


/*------------------------------------------------------------------------------
 *  Check if a file is in the tarball
 *----------------------------------------------------------------------------*/
bool
FileTools :: existsInTarball(const std::string & tarFileName,
                             const std::string & fileName)
                                                    throw (std::runtime_error)
{
    TAR   * tar;
    bool    result = false;

    if (tar_open(&tar,
                 (char*) tarFileName.c_str(),
                 NULL,
                 O_RDONLY,
                 0,
                 0) == -1) {
        throw std::runtime_error("can't open tarball");
    }

    while (th_read(tar) == 0) {
        if (TH_ISREG(tar)) {
            char  * path = th_get_pathname(tar);

            if (fileName == path) {
                result = true;
                break;
            }

            tar_skip_regfile(tar);
        }
    }

    // at this point, tarFileEnd is position where EOT block begins
    tar_close(tar); // close for reading

    return result;
}


/*------------------------------------------------------------------------------
 *  Extract a file from a tarball.
 *----------------------------------------------------------------------------*/
void
FileTools :: extractFileFromTarball(const std::string &     tarFileName,
                                    const std::string &     fileInTarball,
                                    const std::string &     fileExtracted)
                                                    throw (std::runtime_error)
{
    TAR   * tar;
    bool    found = false;

    if (tar_open(&tar,
                 (char*) tarFileName.c_str(),
                 NULL,
                 O_RDONLY,
                 0,
                 0) == -1) {
        throw std::runtime_error("can't open tarball");
    }

    while (th_read(tar) == 0) {
        if (TH_ISREG(tar)) {
            char  * path = th_get_pathname(tar);

            if (fileInTarball == path) {
                found = true;
                if (tar_extract_file(tar,
                                     (char *) fileExtracted.c_str()) != 0) {
                    std::string     errorMsg = "can't extract file ";
                    errorMsg += fileInTarball;
                    errorMsg += " from tarball ";
                    errorMsg += tarFileName;
                    throw std::runtime_error(errorMsg);
                }
                break;
            }

            tar_skip_regfile(tar);
        }
    }

    // at this point, tarFileEnd is position where EOT block begins
    tar_close(tar); // close for reading
    
    if (!found) {
        std::string     errorMsg = "could not find file ";
        errorMsg += fileInTarball;
        errorMsg += " in the tarball ";
        errorMsg += tarFileName;
        throw std::runtime_error(errorMsg);
    }
}

