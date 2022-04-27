# airtime-celery

airtime-celery is a [Celery](https://docs.celeryq.dev/) daemon for handling backend tasks asynchronously. Communication and the Celery results backend are both handled with amqp (RabbitMQ).

# Installation

```sh
sudo python3 setup.py install
```

Each instance of airtime-celery has its own worker, and multiple instances can be run in parallel. [Celery is thread-safe](https://docs.celeryq.dev/en/latest/userguide/application.html), so this parallelization won't cause conflicts.

# Developers

To debug, you can run celery directly from the command line:

```sh
RMQ_CONFIG_FILE=${LIBRETIME_CONF_DIR}/config.yml celery -A libretime_worker.tasks worker --loglevel=info
```

This worker can be run alongside the service without issue.

You may want to use the setuptools develop target to install:

```sh
sudo python setup.py develop
```

You will need to allow the "airtime" RabbitMQ user to access all exchanges and queues within the /airtime vhost:

```sh
sudo rabbitmqctl set_permissions -p /airtime airtime .\* .\* .\*
```

# Logging

By default, logs are saved to:

```
/var/log/airtime/airtime-celery[-DEV_ENV].log
```

# Troubleshooting

If you run into issues getting Celery to accept tasks from Airtime:

1. Make sure Celery is running ($ sudo service airtime-celery status).
2. Check the log file (/var/log/airtime/airtime-celery[-DEV_ENV].log) to make sure Celery started correctly.
3. Check your $LIBRETIME_CONF_DIR/config.yml rabbitmq settings. Make sure the settings here align with $LIBRETIME_CONF_DIR/$ENVIRONMENT/rabbitmq.ini.
4. Check RabbitMQ to make sure the celeryresults and task queues were created in the correct vhost.
5. Make sure the RabbitMQ user (the default is airtime) has permissions on all vhosts being used.
