airtime-celery
==============

airtime-celery is a Celery_ daemon for handling backend tasks asynchronously.
Communication and the Celery results backend are both handled with amqp (RabbitMQ).

Installation
============

    $ sudo python setup.py install

Each instance of airtime-celery has its own worker, and multiple instances can be run in parallel.
`Celery is thread-safe`_, so this parallelization won't cause conflicts.

.. _Celery: http://www.celeryproject.org/
.. _Celery is thread-safe: http://celery.readthedocs.org/en/latest/userguide/application.html

Usage
=====

This program must be run with sudo:

    $ sudo service airtime-celery {start | stop | restart | graceful | kill | dryrun | create-paths}

Developers
==========

To debug, you can run celery directly from the command line:

    $ cd /my/airtime/root/python_apps/airtime-celery
    $ RMQ_CONFIG_FILE=/etc/airtime/airtime.conf celery -A airtime-celery.tasks worker --loglevel=info

This worker can be run alongside the service without issue.

You may want to use the setuptools develop target to install:

    $ cd /my/airtime/root/python_apps/airtime-celery
    $ sudo python setup.py develop

You will need to allow the "airtime" RabbitMQ user to access all exchanges and queues within the /airtime vhost:

    $ sudo rabbitmqctl set_permissions -p /airtime airtime .\* .\* .\*

Logging
=======

By default, logs are saved to:

    /var/log/airtime/airtime-celery[-DEV_ENV].log

Troubleshooting
===============

If you run into issues getting Celery to accept tasks from Airtime:

    1) Make sure Celery is running ($ sudo service airtime-celery status).

    2) Check the log file (/var/log/airtime/airtime-celery[-DEV_ENV].log) to make sure Celery started correctly.

    3) Check your /etc/airtime/airtime.conf rabbitmq settings. Make sure the settings here align with
       /etc/airtime-saas/production/rabbitmq.ini.

    4) Check RabbitMQ to make sure the celeryresults and task queues were created in the correct vhost.

    5) Make sure the RabbitMQ user (the default is airtime) has permissions on all vhosts being used.
