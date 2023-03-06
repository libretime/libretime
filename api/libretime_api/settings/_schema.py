from libretime_shared.config import (
    BaseConfig,
    DatabaseConfig,
    EmailConfig,
    GeneralConfig,
    RabbitMQConfig,
    StorageConfig,
)


class Config(BaseConfig):
    general: GeneralConfig
    email: EmailConfig = EmailConfig()
    database: DatabaseConfig = DatabaseConfig()
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
    storage: StorageConfig = StorageConfig()
