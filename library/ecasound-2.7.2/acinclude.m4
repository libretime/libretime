dnl ---
dnl acinclude.m4 for ecasound
dnl last modified: 20050816-13
dnl ---

## ------------------------------------------------------------------------
## Check for JACK support
##
## defines: ECA_AM_COMPILE_JACK, ECA_S_JACK_LIBS, ECA_S_JACK_INCLUDES,
##          ECA_COMPILE_JACK, ECA_JACK_TRANSPORT_API
## ------------------------------------------------------------------------

AC_DEFUN([AC_CHECK_JACK],
[
AC_CHECK_HEADER(jack/jack.h,jack_support=yes,jack_support=no)

AC_ARG_WITH(jack,
  [  --with-jack=DIR	  Compile against JACK installed in DIR],
  [
    ECA_S_JACK_LIBS="-L${withval}/lib"
    ECA_S_JACK_INCLUDES="-I${withval}/include"
    jack_support=yes
  ])

AC_ARG_ENABLE(jack,
  [  --enable-jack		  Enable JACK support (default=yes, if found)],
  [
    case "$enableval" in
      y | yes)
        AC_MSG_RESULT(yes)
	jack_support=yes
      ;;

      n | no)
        AC_MSG_RESULT(no)
	jack_support=no
      ;;
        
      *)
        AC_MSG_ERROR([Invalid parameter value for --enable-jack: $enableval])
      ;;
    esac
 ])

AM_CONDITIONAL(ECA_AM_COMPILE_JACK, test x$jack_support = xyes)

if test x$jack_support = xyes; then
    AC_DEFINE([ECA_COMPILE_JACK], 1, [enable JACK support])
    ECA_S_JACK_LIBS="${ECA_S_JACK_LIBS} -ljack"
    case "$host" in
	*darwin*)
	    AM_LDFLAGS="$AM_LDFLAGS -framework CoreAudio"
	    ;;
    esac
fi                                     

AC_LANG_C
old_cppflags=$CPPFLAGS
old_ldflags=$LDFLAGS
old_INCLUDES=$INCLUDES
CPPFLAGS="$CPPFLAGS $ECA_S_JACK_INCLUDES"
LDFLAGS="$LDFLAGS $ECA_S_JACK_LIBS"
INCLUDES="--host=a.out-i386-linux"

AC_TRY_LINK(
[ #include <jack/transport.h> ],
[
	jack_position_t t;
	int *a = (void*)&jack_transport_query;
	int *b = (void*)&jack_transport_start;
	int *c = (void*)&jack_transport_stop;
	int *d = (void*)&jack_transport_locate;
	t.frame = 0;
	t.valid = 0;
	return 0;
],
[ ECA_JACK_TRANSPORT_API="3" ],
[ ECA_JACK_TRANSPORT_API="2" ]
)

AC_TRY_LINK(
[ #include <jack/transport.h> ],
[
	jack_transport_info_t t;
	t.state = 0;
	return 0;
],
[ ECA_JACK_TRANSPORT_API="1" ],
[ true ]
)

CPPFLAGS="$old_cppflags"
LDFLAGS="$old_ldflags"
INCLUDES="$old_INCLUDES"

echo "Using JACK transport API version:" ${ECA_JACK_TRANSPORT_API}
AC_DEFINE_UNQUOTED([ECA_JACK_TRANSPORT_API], ${ECA_JACK_TRANSPORT_API}, [version of JACK transport API to use])

AC_SUBST(ECA_S_JACK_LIBS)
AC_SUBST(ECA_S_JACK_INCLUDES)
])

## ------------------------------------------------------------------------
## Check for LFS (now deprecated, v3 is only a stub that doesn't
## peform any checks)
## 
## version: 3
##
## refs:
##  - http://www.gnu.org/software/libtool/manual/libc/Feature-Test-Macros.html
##  - http://www.suse.de/~aj/linux_lfs.html
##  - http://en.wikipedia.org/wiki/Large_file_support
##
## modifies: AM_CXXFLAGS, AM_CFLAGS
## defines: enable_largefile
## ------------------------------------------------------------------------
##

AC_DEFUN([AC_CHECK_LARGEFILE],
[
  echo "checking for largefile support (>2GB files)..."

  dnl note: this is only for backwards compatibility
  AC_ARG_WITH(largefile,
    [  --with-largefile        deprecated option, now used by default], [])

  AC_SYS_LARGEFILE
  if test x$ac_cv_sys_file_offset_bits = x64 ; then
    dnl note: Just to be sure that the define is there even
    dnl       if config.h is not included in right order w.r.t.
    dnl       the system headers.
    AM_CXXFLAGS="$AM_CXXFLAGS -D_FILE_OFFSET_BITS=64"
    AM_CFLAGS="$AM_CFLAGS -D_FILE_OFFSET_BITS=64"

    enable_largefile=yes
  fi

  dnl old checks
  dnl ----------
  dnl  [ if test "x$withval" = "xyes" ; then
  dnl      enable_largefile="yes"
  dnl    fi 
  dnl  ]
  dnl if test "x$enable_largefile" = "xyes"; then
  dnl   dnl AC_DEFINE(_FILE_OFFSET_BITS, 64)
  dnl   dnl AC_DEFINE(_LARGEFILE_SOURCE)
  dnl   AM_CXXFLAGS="$AM_CXXFLAGS -D_FILE_OFFSET_BITS=64 -D_LARGEFILE_SOURCE"
  dnl   AM_CFLAGS="$AM_CFLAGS -D_FILE_OFFSET_BITS=64 -D_LARGEFILE_SOURCE"
  dnl   AC_MSG_RESULT(yes.)
  dnl else
  dnl   AC_MSG_RESULT(no.)
  dnl fi

])

## ------------------------------------------------------------------------
## Check whether namespaces are supported.
##
## version: 3
##
## defines: ECA_USE_CXX_STD_NAMESPACE
## ------------------------------------------------------------------------
##
AC_DEFUN([AC_CHECK_CXX_NAMESPACE_SUPPORT],
[
AC_MSG_CHECKING(if C++ compiler supports namespaces)
AC_LANG_CPLUSPLUS
old_cxx_flags=$CXXFLAGS
CXXFLAGS="-fno-exceptions $CXXFLAGS" # hack around gcc3.x feature
AC_TRY_RUN(
[
#include <string>
#include <vector>

using std::string;

int main(void)
{	
	string s ("foo");
 	std::vector<string> v;
	return(0);
}
],
[ 	
	AC_MSG_RESULT(yes.)
	AC_DEFINE([ECA_USE_CXX_STD_NAMESPACE], 1, [use C++ std namespace])
],
[
	AC_MSG_RESULT(no.)
	AC_MSG_WARN([C++ compiler has problems with namespaces. Build process can fail because of this.])
]
,
[
	AC_MSG_RESULT(no.)
]
)
CXXFLAGS=$old_cxx_flags
])

## ------------------------------------------------------------------------
## Find a file (or one of more files in a list of dirs)
##
## version: 1
## ------------------------------------------------------------------------
##
AC_DEFUN([AC_FIND_FILE],
[
$3=NO
for i in $2;
do
  for j in $1;
  do
    if test -r "$i/$j"; then
      $3=$i
      break 2
    fi
  done
done
])
