#!/bin/sh
#
#

LIVESUPPORT_DIR=/opt/livesupport
LIVESUPPORT_BIN=$LIVESUPPORT_DIR/bin
LIVESUPPORT_ETC=$LIVESUPPORT_DIR/etc
LIVESUPPORT_LIB=$LIVESUPPORT_DIR/lib

PATH=/sbin:/bin:/usr/sbin:/usr/bin:$LIVESUPPORT_BIN
LD_LIBRARY_PATH=$LIVESUPPORT_LIB:$LD_LIBRARY_PATH
DAEMON=$LIVESUPPORT_BIN/scheduler
NAME=livesupport-scheduler
DESC="livesupport scheduler"

test -x $DAEMON || exit 0

export PATH
export LD_LIBRARY_PATH

DAEMON_OPTS="-c $LIVESUPPORT_ETC/scheduler.xml"

set -e

case "$1" in
  start)
	echo -n "Starting $DESC: "
    $DAEMON -c $LIVESUPPORT_ETC/scheduler.xml start > /dev/null
	echo "$NAME."
	;;
  stop)
	echo -n "Stopping $DESC: "
    $DAEMON -c $LIVESUPPORT_ETC/scheduler.xml stop > /dev/null
	echo "$NAME."
	;;
  restart|force-reload)
	echo -n "Restarting $DESC: "
    $DAEMON -c $LIVESUPPORT_ETC/scheduler.xml stop > /dev/null
	sleep 1
    $DAEMON -c $LIVESUPPORT_ETC/scheduler.xml start > /dev/null
	echo "$NAME."
	;;
  *)
	N=/etc/init.d/$NAME
	echo "Usage: $N {start|stop|restart|force-reload|kill}" >&2
	exit 1
	;;
esac

exit 0

