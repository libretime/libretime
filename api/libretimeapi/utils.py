import configparser
import sys
import string
import random

def read_config_file(config_path):
    """Parse the application's config file located at config_path."""
    config = configparser.ConfigParser()
    try:
        config.readfp(open(config_path))
    except IOError as e:
        print("Failed to open config file at {}: {}".format(config_path, e.strerror),
              file=sys.stderr)
        raise e
    except Exception as e:
        print(e.strerror, file=sys.stderr)
        raise e
    return config

def get_random_string(seed):
    """Generates a random string based on the given seed"""
    choices = string.ascii_letters + string.digits + string.punctuation
    seed = seed.encode('utf-8')
    rand = random.Random(seed)
    return [rand.choice(choices) for i in range(16)]
