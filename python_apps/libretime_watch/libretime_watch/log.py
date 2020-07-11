#!/usr/bin/python
import logging

def setup(log_file, level=logging.INFO):
    log_handler = logging.handlers.RotatingFileHandler(log_file, mode='a', maxBytes=512, backupCount=0)
    formatter = logging.Formatter('%(asctime)s [%(levelname)s]: %(message)s')
    log_handler.setFormatter(formatter)
    logger = logging.getLogger()
    logger.addHandler(log_handler)
    logger.setLevel(level)
