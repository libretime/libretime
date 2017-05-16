The airtime-log command provides convenient access to the logging output from the services which make up the Airtime system: *media-monitor*, *recorder*, *playout*, *liquidsoap* and *web*.

Using this command requires root privileges (**sudo** on Ubuntu). Entering the command without any options returns a list of options that you can specify:

    sudo airtime-log

    Usage: airtime-log [options]

    --view|-v <string> Display log file
            media-monitor|recorder|playout|liquidsoap|web

    --dump|-d <string> Collect all log files and compress into a tarball
            media-monitor|recorder|playout|liquidsoap|web (ALL by default)

    --tail|-t <string> View any new entries appended to log files in real-time
            media-monitor|recorder|playout|liquidsoap|web (ALL by default)

For example, to view the media-monitor log, you could use the command:

    sudo airtime-log -v media-monitor

Use the **PageUp** and **PageDown** keys on your keyboard to navigate through the log file, or press the **q** key to quit the viewer.

To dump all log files and compress them into a tarball placed in the working directory, you could add the -d switch to the command:

    sudo airtime-log -d

    Creating Airtime logs tgz file at /root/logs/airtime-log-all-2012-11-14-16-22-02.tgz

To view just the Liquidsoap log output in real-time, you could enter the command:

    sudo airtime-log -t liquidsoap

    Tail liquidsoap log 2012/11/14 15:47:20 [server:3] New client: localhost.
    2012/11/14 15:47:20 [server:3] Client localhost disconnected.
    2012/11/14 15:47:20 [server:3] New client: localhost.
    2012/11/14 15:47:20 [lang:3] dynamic_source.get_id
    2012/11/14 15:47:20 [server:3] Client localhost disconnected.
    2012/11/14 16:17:20 [server:3] New client: localhost.
    2012/11/14 16:17:20 [server:3] Client localhost disconnected.
    2012/11/14 16:17:20 [server:3] New client: localhost.
    2012/11/14 16:17:20 [lang:3] dynamic_source.get_id
    2012/11/14 16:17:20 [server:3] Client localhost disconnected.

Press the **Ctrl+C** keys to interrupt the real-time log output and return to the server console.
