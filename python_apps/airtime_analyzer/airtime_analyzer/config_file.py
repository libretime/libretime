
import configparser

def read_config_file(config_path):
    """Parse the application's config file located at config_path."""
    config = configparser.SafeConfigParser()
    try:
        config.readfp(open(config_path))
    except IOError as e:
        print("Failed to open config file at {}: {}".format(config_path, e.strerror))
        exit(-1)
    except Exception as e:
        print(e.strerror)
        exit(-1)

    return config
