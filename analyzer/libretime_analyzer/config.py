from libretime_shared.config import BaseConfig, RabbitMQConfig


class Config(BaseConfig):
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
