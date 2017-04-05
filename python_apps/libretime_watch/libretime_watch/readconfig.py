#!/usr/bin/python

import ConfigParser
import logging
import os
import libretime_watch

CONFIGFILE="/etc/airtime/airtime.conf"

config = {}

def read_config():
  """Read airtime configfile"""
  try: 
    Config = ConfigParser.ConfigParser()
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
  except:
    logging.error ("can't open the configfile")  
  return config
