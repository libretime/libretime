import random
import string
import sys
from configparser import ConfigParser


def read_config_file(config_filepath):
    """Parse the application's config file located at config_path."""
    config = ConfigParser()
    try:
        with open(config_filepath, encoding="utf-8") as config_file:
            config.read_file(config_file)
    except OSError as error:
        print(
            f"Unable to read config file at {config_filepath}: {error.strerror}",
            file=sys.stderr,
        )
        return ConfigParser()
    except Exception as error:
        print(error, file=sys.stderr)
        raise error
    return config


def get_random_string(seed):
    """Generates a random string based on the given seed"""
    choices = string.ascii_letters + string.digits + string.punctuation
    seed = seed.encode("utf-8")
    rand = random.Random(seed)
    return [rand.choice(choices) for i in range(16)]
