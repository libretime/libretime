from os import getenv

from kombu import Exchange, Queue
from libretime_shared.config import BaseConfig, RabbitMQConfig


class Config(BaseConfig):
    rabbitmq: RabbitMQConfig = RabbitMQConfig()


LIBRETIME_CONFIG_FILEPATH = getenv("LIBRETIME_CONFIG_FILEPATH")

config = Config(filepath=LIBRETIME_CONFIG_FILEPATH)

# Celery settings
# See https://docs.celeryproject.org/en/stable/userguide/configuration.html
broker_url = config.rabbitmq.url
worker_concurrency = 1
event_queue_expires = 900

result_backend = "rpc://"
result_persistent = True
result_expires = 900
result_exchange = "celeryresults"  # needed due to php-celery

task_time_limit = 1800
task_queues = (
    Queue("podcast", exchange=Exchange("podcast"), routing_key="podcast"),
    Queue(exchange=Exchange("celeryresults"), auto_delete=True),
)
