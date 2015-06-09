from configobj import ConfigObj
from kombu import Exchange, Queue

# Get the broker string from airtime.conf
DEFAULT_RMQ_CONFIG_PATH = '/etc/airtime/airtime.conf'
RMQ_CONFIG_SECTION = "rabbitmq"


def parse_rmq_config(rmq_config):
    return {
        'host'     : rmq_config[RMQ_CONFIG_SECTION]['host'],
        'port'     : rmq_config[RMQ_CONFIG_SECTION]['port'],
        'username' : rmq_config[RMQ_CONFIG_SECTION]['user'],
        'password' : rmq_config[RMQ_CONFIG_SECTION]['password'],
        'vhost'    : rmq_config[RMQ_CONFIG_SECTION]['vhost']
    }


def get_rmq_broker():
    rmq_config = ConfigObj(DEFAULT_RMQ_CONFIG_PATH)
    rmq_settings = parse_rmq_config(rmq_config)
    return 'amqp://{username}:{password}@{host}:{port}/{vhost}'.format(**rmq_settings)

# Celery amqp settings
BROKER_URL = get_rmq_broker()
CELERY_RESULT_BACKEND = 'amqp'     # Use RabbitMQ as the celery backend
# CELERY_RESULT_EXCHANGE = 'upload-results'
CELERY_RESULT_PERSISTENT = True    # Persist through a broker restart
CELERY_TASK_RESULT_EXPIRES = None  # Don't expire tasks
CELERY_TRACK_STARTED = False
CELERY_QUEUES = (
    Queue('soundcloud-uploads', Exchange('soundcloud-uploads'), routing_key='celery'),
)
CELERY_DEFAULT_QUEUE = 'soundcloud-uploads'
CELERY_DEFAULT_EXCHANGE_TYPE = 'topic'

# Celery task settings
CELERY_TASK_SERIALIZER = 'json'
CELERY_RESULT_SERIALIZER = 'json'
CELERY_ACCEPT_CONTENT = ['json']
CELERY_TIMEZONE = 'Europe/Berlin'
CELERY_ENABLE_UTC = True
