# -*- coding: utf-8 -*-
from media.saas.launcher import setup_logger, setup_global

def autoscan_instances(log_config):
    print("privet mang")

def main(log_config, log_path):
    log = setup_logger(log_config, log_path)
    setup_global(log)
    autoscan_instances(log_config)

if __name__ == '__main__': main('shen','sheni')
