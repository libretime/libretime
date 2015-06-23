airtime-celery
==============

airtime-celery is a Celery_ daemon for handling backend tasks asynchronously.
Communication and the Celery results backend are both handled with amqp (RabbitMQ).

Installation
============

    $ sudo adduser --system --no-create-home --disabled-login --disabled-password --group celery
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

You may want to use the setuptools develop target to install:

    $ sudo python setup.py develop

You will need to allow the "airtime" RabbitMQ user to access all exchanges and queues within the /airtime vhost:

    $ sudo rabbitmqctl set_permissions -p /airtime airtime .\* .\* .\*

Logging
=======

By default, logs are saved to:

    /var/log/airtime/airtime-celery[-DEV_ENV].log
