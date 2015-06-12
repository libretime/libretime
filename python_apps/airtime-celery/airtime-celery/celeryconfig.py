import os
from configobj import ConfigObj
from kombu import Exchange, Queue

# Get the broker string from airtime.conf
RMQ_CONFIG_SECTION = "rabbitmq"


def get_rmq_broker():
    rmq_config = ConfigObj(os.environ['RMQ_CONFIG_FILE'])
    rmq_settings = parse_rmq_config(rmq_config)
    return 'amqp://{username}:{password}@{host}:{port}/{vhost}'.format(**rmq_settings)


def parse_rmq_config(rmq_config):
    return {
        'host'    : rmq_config[RMQ_CONFIG_SECTION]['host'],
        'port'    : rmq_config[RMQ_CONFIG_SECTION]['port'],
        'username': rmq_config[RMQ_CONFIG_SECTION]['user'],
        'password': rmq_config[RMQ_CONFIG_SECTION]['password'],
        'vhost'   : rmq_config[RMQ_CONFIG_SECTION]['vhost']
    }

# Celery amqp settings
BROKER_URL = get_rmq_broker()
CELERY_RESULT_BACKEND = 'amqp'     # Use RabbitMQ as the celery backend
CELERY_RESULT_PERSISTENT = True    # Persist through a broker restart
CELERY_TASK_RESULT_EXPIRES = 300   # Expire task results after 5 minutes
CELERY_TRACK_STARTED = False
CELERY_RESULT_EXCHANGE = 'airtime-results'
CELERY_QUEUES = (
    Queue('soundcloud-uploads', exchange=Exchange('soundcloud-uploads'), routing_key='soundcloud-uploads'),
    Queue('airtime-results.soundcloud-uploads', exchange=Exchange('airtime-results')),
)
CELERY_ROUTES = (
    {
        'soundcloud_uploads.tasks.upload_to_soundcloud': {
            'exchange': 'airtime-results',
            'queue': 'airtime-results.soundcloud-uploads',
        }
    },
)

# Celery task settings
CELERY_TASK_SERIALIZER = 'json'
CELERY_RESULT_SERIALIZER = 'json'
CELERY_ACCEPT_CONTENT = ['json']
CELERY_TIMEZONE = 'Europe/Berlin'
CELERY_ENABLE_UTC = True
