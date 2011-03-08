/* config.h.  Generated from config.h.in by configure.  */
/* config.h.in.  Generated from configure.in by autoheader.  */

/* disable all use of shared libs */
/* #undef ECA_ALL_STATIC */

/* enable ALSA support */
#define ECA_COMPILE_ALSA 1

/* enable aRts support */
/* #undef ECA_COMPILE_ARTS */

/* enable libaudiofile support */
#define ECA_COMPILE_AUDIOFILE 1

/* enable JACK support */
/* #undef ECA_COMPILE_JACK */

/* enable OSS audio input/output */
#define ECA_COMPILE_OSS 1

/* enable libsamplerate support */
#define ECA_COMPILE_SAMPLERATE 1

/* enable libsndfile support */
#define ECA_COMPILE_SNDFILE 1

/* debugging mode build */
/* #undef ECA_DEBUG_MODE */

/* disable all effects */
/* #undef ECA_DISABLE_EFFECTS */

/* disable use of OSS trigger API */
/* #undef ECA_DISABLE_OSS_TRIGGER */

/* enable experimental features */
/* #undef ECA_FEELING_EXPERIMENTAL */

/* version of JACK transport API to use */
#define ECA_JACK_TRANSPORT_API 2

/* enable ecasound curses console interface */
#define ECA_PLATFORM_CURSES 1

/* Ecasound configure script prefix */
#define ECA_PREFIX "/usr/local"

/* use ncurses.h for curses interface */
/* #undef ECA_USE_CURSES_H */

/* use C++ std namespace */
#define ECA_USE_CXX_STD_NAMESPACE 1

/* Use liblo for OSC support */
/* #undef ECA_USE_LIBLO */

/* Use liboil */
/* #undef ECA_USE_LIBOIL */

/* use curses.h for curses interface */
#define ECA_USE_NCURSES_H 1

/* ncurses headers are installed in ncurses subdir <ncurses/ncurses.h> */
/* #undef ECA_USE_NCURSES_NCURSES_H */

/* Define to 1 if you have the `clock_gettime' function. */
#define HAVE_CLOCK_GETTIME 1

/* Define to 1 if you have the <dlfcn.h> header file. */
#define HAVE_DLFCN_H 1

/* Define to 1 if you have the <errno.h> header file. */
#define HAVE_ERRNO_H 1

/* Define to 1 if you have the <execinfo.h> header file. */
#define HAVE_EXECINFO_H 1

/* Define to 1 if you have the `execvp' function. */
#define HAVE_EXECVP 1

/* Define to 1 if you have the <fcntl.h> header file. */
#define HAVE_FCNTL_H 1

/* Define to 1 if you have the <features.h> header file. */
#define HAVE_FEATURES_H 1

/* Define to 1 if you have the `getpagesize' function. */
#define HAVE_GETPAGESIZE 1

/* Define to 1 if you have the `gettimeofday' function. */
#define HAVE_GETTIMEOFDAY 1

/* Define to 1 if you have the <inttypes.h> header file. */
#define HAVE_INTTYPES_H 1

/* Define to 1 if you have the <ladspa.h> header file. */
/* #undef HAVE_LADSPA_H */

/* Define to 1 if you have the <locale.h> header file. */
#define HAVE_LOCALE_H 1

/* Define to 1 if you have the <memory.h> header file. */
#define HAVE_MEMORY_H 1

/* Define to 1 if you have the `mlockall' function. */
#define HAVE_MLOCKALL 1

/* Define to 1 if you have a working `mmap' system call. */
#define HAVE_MMAP 1

/* Define to 1 if you have the `munlockall' function. */
#define HAVE_MUNLOCKALL 1

/* Define to 1 if you have the `nanosleep' function. */
#define HAVE_NANOSLEEP 1

/* Define to 1 if you have the `pause' function. */
#define HAVE_PAUSE 1

/* Define to 1 if you have the `posix_memalign' function. */
#define HAVE_POSIX_MEMALIGN 1

/* Define to 1 if you have the `pthread_getschedparam' function. */
#define HAVE_PTHREAD_GETSCHEDPARAM 1

/* Define to 1 if you have the `pthread_kill' function. */
#define HAVE_PTHREAD_KILL 1

/* Define to 1 if you have the `pthread_mutexattr_init' function. */
#define HAVE_PTHREAD_MUTEXATTR_INIT 1

/* Define to 1 if you have the `pthread_self' function. */
#define HAVE_PTHREAD_SELF 1

/* Define to 1 if you have the `pthread_setschedparam' function. */
#define HAVE_PTHREAD_SETSCHEDPARAM 1

/* Define to 1 if you have the `pthread_sigmask' function. */
#define HAVE_PTHREAD_SIGMASK 1

/* Define to 1 if you have the <regex.h> header file. */
#define HAVE_REGEX_H 1

/* Define to 1 if you have the `sched_getparam' function. */
#define HAVE_SCHED_GETPARAM 1

/* Define to 1 if you have the `sched_getscheduler' function. */
#define HAVE_SCHED_GETSCHEDULER 1

/* Define to 1 if you have the `sched_get_priority_max' function. */
#define HAVE_SCHED_GET_PRIORITY_MAX 1

/* Define to 1 if you have the <sched.h> header file. */
#define HAVE_SCHED_H 1

/* Define to 1 if you have the `sched_setscheduler' function. */
#define HAVE_SCHED_SETSCHEDULER 1

/* Define to 1 if you have the `setlocale' function. */
#define HAVE_SETLOCALE 1

/* Define to 1 if you have the <signal.h> header file. */
#define HAVE_SIGNAL_H 1

/* Define to 1 if you have the `sigprocmask' function. */
#define HAVE_SIGPROCMASK 1

/* Define to 1 if you have the `sigwait' function. */
#define HAVE_SIGWAIT 1

/* Define to 1 if you have the <stdint.h> header file. */
#define HAVE_STDINT_H 1

/* Define to 1 if you have the <stdlib.h> header file. */
#define HAVE_STDLIB_H 1

/* Define to 1 if you have the <strings.h> header file. */
#define HAVE_STRINGS_H 1

/* Define to 1 if you have the <string.h> header file. */
#define HAVE_STRING_H 1

/* Define to 1 if you have the <sys/mman.h> header file. */
#define HAVE_SYS_MMAN_H 1

/* Define to 1 if you have the <sys/poll.h> header file. */
#define HAVE_SYS_POLL_H 1

/* Define to 1 if you have the <sys/select.h> header file. */
#define HAVE_SYS_SELECT_H 1

/* Define to 1 if you have the <sys/socket.h> header file. */
#define HAVE_SYS_SOCKET_H 1

/* Define to 1 if you have the <sys/stat.h> header file. */
#define HAVE_SYS_STAT_H 1

/* Define to 1 if you have the <sys/time.h> header file. */
#define HAVE_SYS_TIME_H 1

/* Define to 1 if you have the <sys/types.h> header file. */
#define HAVE_SYS_TYPES_H 1

/* Define to 1 if you have the <sys/wait.h> header file. */
#define HAVE_SYS_WAIT_H 1

/* Define to 1 if you have the <termios.h> header file. */
#define HAVE_TERMIOS_H 1

/* Define to 1 if you have the <unistd.h> header file. */
#define HAVE_UNISTD_H 1

/* Define to 1 if you have the `usleep' function. */
#define HAVE_USLEEP 1

/* libecasoundc interface version */
#define LIBECASOUNDC_VERSION 2

/* libecasound interface version */
#define LIBECASOUND_VERSION 22

/* libecasound interface age */
#define LIBECASOUND_VERSION_AGE 0

/* libkvutils interface version */
#define LIBKVUTILS_VERSION 9

/* libkvutils interface age */
#define LIBKVUTILS_VERSION_AGE 5

/* Name of package */
#define PACKAGE "ecasound"

/* Define to the address where bug reports for this package should be sent. */
#define PACKAGE_BUGREPORT ""

/* Define to the full name of this package. */
#define PACKAGE_NAME "ecasound"

/* Define to the full name and version of this package. */
#define PACKAGE_STRING "ecasound 2.7.2"

/* Define to the one symbol short name of this package. */
#define PACKAGE_TARNAME "ecasound"

/* Define to the version of this package. */
#define PACKAGE_VERSION "2.7.2"

/* Define to 1 if you have the ANSI C header files. */
#define STDC_HEADERS 1

/* Define to 1 if you can safely include both <sys/time.h> and <time.h>. */
#define TIME_WITH_SYS_TIME 1

/* Version number of package */
#define VERSION "2.7.2"

/* Define to 1 if your processor stores words with the most significant byte
   first (like Motorola and SPARC, unlike Intel and VAX). */
/* #undef WORDS_BIGENDIAN */

/* Number of bits in a file offset, on hosts where this is settable. */
/* #undef _FILE_OFFSET_BITS */

/* Define for large files, on AIX-style hosts. */
/* #undef _LARGE_FILES */

/* Define to `unsigned int' if <sys/types.h> does not define. */
/* #undef size_t */
