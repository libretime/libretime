import datetime
import configparser
import unittest
from api_clients import utils

def get_force_ssl(value, useConfigParser):
    config = {}
    if useConfigParser:
        config = configparser.ConfigParser()
    config['general'] = {
        'base_port': 80,
        'force_ssl': value,
    }
    return utils.get_protocol(config)


class TestTime(unittest.TestCase):
    def test_time_in_seconds(self):
        time = datetime.time(hour=0, minute=3, second=34, microsecond=649600)
        self.assertTrue(abs(utils.time_in_seconds(time) - 214.65) < 0.009)

    def test_time_in_milliseconds(self):
        time = datetime.time(hour=0, minute=0, second=0, microsecond=500000)
        self.assertEqual(utils.time_in_milliseconds(time), 500)


class TestGetProtocol(unittest.TestCase):
    def test_dict_config_empty_http(self):
        config = {'general': {}}
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_dict_config_http(self):
        config = {
            'general': {
                'base_port': 80,
            },
        }
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_dict_config_https(self):
        config = {
            'general': {
                'base_port': 443,
            },
        }
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'https')

    def test_dict_config_force_https(self):
        postive_values = ['yes', 'Yes', 'True', 'true', True]
        negative_values = ['no', 'No', 'False', 'false', False]
        for value in postive_values:
            self.assertEqual(get_force_ssl(value, False), 'https')
        for value in negative_values:
            self.assertEqual(get_force_ssl(value, False), 'http')

    def test_configparser_config_empty_http(self):
        config = configparser.ConfigParser()
        config['general'] = {}
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_configparser_config_http(self):
        config = configparser.ConfigParser()
        config['general'] = {
            'base_port': 80,
        }
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_configparser_config_https(self):
        config = configparser.ConfigParser()
        config['general'] = {
            'base_port': 443,
        }
        protocol = utils.get_protocol(config)
        self.assertEqual(protocol, 'https')

    def test_configparser_config_force_https(self):
        postive_values = ['yes', 'Yes', 'True', 'true', True]
        negative_values = ['no', 'No', 'False', 'false', False]
        for value in postive_values:
            self.assertEqual(get_force_ssl(value, True), 'https')
        for value in negative_values:
            self.assertEqual(get_force_ssl(value, True), 'http')

    def test_fromisoformat(self):
        time = {
            "00:00:00.500000": datetime.time(microsecond=500000),
            "00:04:30.092540": datetime.time(minute=4, second=30, microsecond=92540),
        }
        for time_string, expected in time.items():
            result = utils.fromisoformat(time_string)
            self.assertEqual(result, expected)

if __name__ == '__main__': unittest.main()
