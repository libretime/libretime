# Shared

The `libretime_shared` package contains reusable functions and classes for the LibreTime project.

## Usage

This library assumes that:

- You will use [`Click`](https://github.com/pallets/click) to build a CLI for your app.
- You will use [`Pydantic`](https://github.com/samuelcolvin/pydantic/) to validate objects in your app.

### Configuration

First define a schema for your configuration in order to validate it. A schema is a class that inherit from `pydantic.BaseModel`. Some existing schemas can be reused such as `libretime_shared.config.RabbitMQ` or `libretime_shared.config.Database`.

Load your configuration using a subclass of `libretime_shared.config.BaseConfig`.

```py
from pydantic import BaseModel

from libretime_shared.config import RabbitMQConfig, BaseConfig

class AnalyzerConfig(BaseModel):
    bpm_enabled: bool = False
    bpm_track_max_length: int

class Config(BaseConfig):
    rabbitmq: RabbitMQConfig
    analyzer: AnalyzerConfig

config = Config("/etc/libretime/config.yml")
```

> Don't instantiate a sub model if it has a required field, otherwise the `Config` class import will raise a `ValidationError`.

### CLI

Decorate your CLI commands with the shared decorators to add extra flags.

```py
import click
from libretime_shared.cli import cli_logging_options, cli_config_options

from .app import App

@click.group()
def cli():
    pass

@cli.command()
@cli_config_options()
@cli_logging_options()
def run(**kwargs):
    app = App(**kwargs)
    return app.run()
```
