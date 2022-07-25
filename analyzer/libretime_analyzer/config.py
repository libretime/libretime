from libretime_shared.config import BaseConfig, GeneralConfig, RabbitMQConfig


class Config(BaseConfig):
    general: GeneralConfig
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
