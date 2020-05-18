---
sidebar: main
---

# Troubleshooting

Having trouble with your LibreTime installation? We've got you covered!

Since LibreTime is effectively a web site running on a LAPP stack, individual components of the system can be started, stopped, restarted or checked in the server console using the **systemctl** command:

    sudo systemctl start|stop|restart|status libretime-liquidsoap
    sudo systemctl start|stop|restart|status libretime-playout
    sudo systemctl start|stop|restart|status libretime-celery
    sudo systemctl start|stop|restart|status libretime-analyzer
    sudo systemctl start|stop|restart|status apache2
    sudo systemctl start|stop|restart|status rabbitmq-server

For example, to restart the Airtime playout engine, you could enter the command:

    sudo systemctl restart libretime-playout

Log files
---------

Airtime stores log files under the directory path */var/log/airtime/* which can be useful for diagnosing the cause of any problems. Copies of these log files may be requested by LibreTime developers while they are providing technical support for your Airtime deployment.

Test tones
----------

If you need to test your computer's soundcard, you can use `speaker-test`, a tone generator for ALSA.
This does not come installed with LibreTime but can be installed with `sudo apt install speaker-test`.

   speaker-test [-D] [-f]
   
   Where:
        -D device name
        -f frequency of test tone

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

    sudo systemctl restart rabbitmq-server

3. Enter the following commands to set up authentication and grant permissions. The *rabbitmqctl add\_user* command requires the RabbitMQ password from the /etc/airtime/airtime.conf file as an argument. The *rabbitmqctl set\_permissions* command should be entered on one line, with the list of Airtime services repeated three times:

    rabbitmqctl add_vhost /airtime
    rabbitmqctl add_user airtime XXXXXXXXXXXXXXXXXXXX
    rabbitmqctl set_permissions -p /airtime airtime
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
       "airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"
