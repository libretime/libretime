#!/usr/bin/python

import configparser
import logging
import os

CONFIGFILE="/etc/airtime/airtime.conf"

def read_config(config):
  """Read airtime configfile"""
  try: 
    Config = configparser.ConfigParser()
    Config.read(CONFIGFILE)
    config["db_host"]=Config.get('database','host')
    config["db_name"]=Config.get('database','dbname')
    config["db_user"]=Config.get('database','dbuser')
    config["db_pass"]=Config.get('database','dbpass')
    config["rm_host"]=Config.get('rabbitmq','host')
    config["rm_vhost"]=Config.get('rabbitmq','vhost')
    config["rm_port"]=Config.get('rabbitmq','port')
    config["rm_user"]=Config.get('rabbitmq','user')
    config["rm_pass"]=Config.get('rabbitmq','password')
    config["api_key"]=Config.get('general','api_key')
    config["airtime_dir"]=Config.get('general', 'airtime_dir')

    # Is there a better way to do this?
    if not config["airtime_dir"]:
        config["airtime_dir"] = '/srv/airtime/stor/'

  except:
    logging.error("can't open the configfile")
    raise
