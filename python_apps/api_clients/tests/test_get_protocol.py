import unittest
import configparser
from api_clients.api_client import get_protocol

def get_force_ssl(value, useConfigParser):
    config = {}
    if useConfigParser:
        config = configparser.ConfigParser()
    config['general'] = {
        'base_port': 80,
        'force_ssl': value,
    }
    return get_protocol(config)

class TestGetProtocol(unittest.TestCase):
    def test_dict_config_empty_http(self):
        config = {'general': {}}
        protocol = get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_dict_config_http(self):
        config = {
            'general': {
                'base_port': 80,
            },
        }
        protocol = get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_dict_config_https(self):
        config = {
            'general': {
                'base_port': 443,
            },
        }
        protocol = get_protocol(config)
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
        protocol = get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_configparser_config_http(self):
        config = configparser.ConfigParser()
        config['general'] = {
            'base_port': 80,
        }
        protocol = get_protocol(config)
        self.assertEqual(protocol, 'http')

    def test_configparser_config_https(self):
        config = configparser.ConfigParser()
        config['general'] = {
            'base_port': 443,
        }
        protocol = get_protocol(config)
        self.assertEqual(protocol, 'https')

    def test_configparser_config_force_https(self):
        postive_values = ['yes', 'Yes', 'True', 'true', True]
        negative_values = ['no', 'No', 'False', 'false', False]
        for value in postive_values:
            self.assertEqual(get_force_ssl(value, True), 'https')
        for value in negative_values:
            self.assertEqual(get_force_ssl(value, True), 'http')
