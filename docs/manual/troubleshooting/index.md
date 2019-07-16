If your Airtime server is not working as expected, individual components of the system can be started, stopped, restarted or checked in the server console using the <span style="font-weight: bold;">invok</span>**e-rc.d** command:

    sudo invoke-rc.d airtime-liquidsoap     start|stop|restart|status
    sudo invoke-rc.d airtime-playout        start|stop|restart|status
    sudo invoke-rc.d airtime-analyzer       start|stop|restart|status
    sudo invoke-rc.d apache2                start|stop|restart|status
    sudo invoke-rc.d rabbitmq-server        start|stop|restart|status

For example, to restart the Airtime playout engine, you could enter the command:

    sudo invoke-rc.d airtime-playout restart

The server should respond:

    Restarting Airtime Playout: Done.

The **status** option for **airtime-playout** and **airtime-analyzer** runs the **airtime-check-system** script to confirm that all of Airtime's dependencies are installed and running correctly.

Log files
---------

Airtime stores log files under the directory path */var/log/airtime/* which can be useful for diagnosing the cause of any problems. Copies of these log files may be requested by Sourcefabric engineers while they are providing technical support for your Airtime deployment. See the chapter *The airtime-log command* for more details.

Test tones
----------

Liquidsoap output can be tested using two commands provided by Airtime. The **airtime-test-soundcard** command enables you to send a test tone to the default sound card on the system, so you can check that your audio equipment is working. Press **Ctrl+C** on your keyboard to stop the tone.

    airtime-test-soundcard [-v]
                     [-o alsa | ao | oss | portaudio | pulseaudio ]
                     [-h]
    Where:
         -v verbose mode
         -o Linux Sound API (default: alsa)
         -h show help menu

The **airtime-test-stream** command enables you to send a test tone to a local or remote streaming media server. Press **Ctrl+C** on your keyboard to stop the tone being streamed.

    airtime-test-stream [-v]
                   [-o icecast | shoutcast ] [-H hostname] [-P port]
                   [-u username] [-p password] [-m mount]
                   [-h]
    Where:
         -v verbose mode
         -o stream server type (default: icecast)
         -H hostname (default: localhost)
         -P port (default: 8000)
         -u user (default: source)
         -p password (default: hackme)
         -m mount (default: test)
         -h show help menu

RabbitMQ hostname changes
-------------------------

If the Airtime logs indicate failures to connect to the RabbitMQ server, such as:

    2013-10-31 08:21:11,255 ERROR - [pypomessagehandler.py : main() : line 
    99] - Error connecting to RabbitMQ Server. Trying again in few seconds

2013-10-31 08:21:11,255 ERROR - \[pypomessagehandler.py : main() : line 99\] - Error connecting to RabbitMQ Server. Trying again in few seconds - See more at: http://forum.sourcefabric.org/discussion/16050/\#sthash.W8OJrNFm.dpuf

but the RabbitMQ server is running normally, this error might be due to a change in the server's hostname since Airtime installation. Directory names under */var/lib/rabbitmq/mnesia/* indicate that RabbitMQ's database files are organised according to the hostname of the server, for example:

    rabbit@airtime

where the hostname is *airtime.example.com*. If the hostname has changed, it may be necessary to reconfigure RabbitMQ manually, as follows:

1. Delete the files in */var/lib/rabbitmq/mnesia/*

    sudo rm -r /var/lib/rabbitmq/mnesia/*

2. Restart RabbitMQ:

    sudo invoke-rc.d rabbitmq-server restart

3. Enter the following commands to set up authentication and grant permissions. The *rabbitmqctl add\_user* command requires the RabbitMQ password from the /etc/airtime/airtime.conf file as an argument. The *rabbitmqctl set\_permissions* command should be entered on one line, with the list of Airtime services repeated three times:

    rabbitmqctl add_vhost /airtime
    rabbitmqctl add_user airtime XXXXXXXXXXXXXXXXXXXX 
    rabbitmqctl set_permissions -p /airtime airtime 
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
