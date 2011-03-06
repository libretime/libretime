#ifndef INCLUDED_ECA_VERSION_H
#define INCLUDED_ECA_VERSION_H

/**
 * Ecasound library version as a formatted std::string.
 *
 * "vX.Y[.Z[.R]][-extraT]" :
 *
 * X = major version  - the overall development status
 *
 * Y = minor version  - represents a set of planned features (see TODO)
 *
 * Z = micro version  - small changes to major.minor version
 *
 * R = revision       - urgent fixes to normal releases (optional)
 *
 * extraT             - beta, pre and rc releases that are in 
 *                      preparation of major releases
 */
extern const char* ecasound_library_version;

/**
 * Ecasound library libtool version number (current:revision:age)
 */
extern const long int ecasound_library_version_current;
extern const long int ecasound_library_version_revision;
extern const long int ecasound_library_version_age;

#endif
