#!/usr/bin/python
import ConfigParser
import pika, os, logging
import json
import psycopg2

# initialize config
logging.basicConfig()

EXCHANGE="airtime-media-monitor"
EXCHANGE_TYPE = "direct"
ROUTING_KEY="filesystem"
QUEUE="media-monitor"

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
    config["api_key"]=Config.get('general','api_key')

  except:
    print ("can't open the configfile")  
  return config

def connect_database():
  """Connect database
     return: connection
  """
  try:
     conn = psycopg2.connect("dbname='"
          +config["db_name"]+"' user='"
          +config["db_user"]+"' host='"
          +config["db_host"]+"' password='"
          +config["db_pass"]+"'")
  except:
    print "I am unable to connect to the database"
  return conn


# get the config data
libretime_config =read_config()

# connect to database
conn=connect_database()
cur = conn.cursor()
try:
   cur.execute ("SELECT id,directory from cc_music_dirs where type = 'watched'")
   row = cur.fetchone()
   id = row[0]
   watch_dir = row[1]
   cur.close()
except:
   print ("Can't get directory for watching")
   exit()

#print ("ID: "+str(id)+" Dir: "+str(watch_dir)+ " "+config['api_key'])

#message = { 'cmd' : 'rescan_watch', 'id' : '34', 'directory' : '/srv/airtime/watch/'}
message = { 'cmd' : 'rescan_watch', 'api_key' : str(config['api_key']), 'id' : str(id), 'directory' : str(watch_dir)}

json_encoded = json.dumps(message)

# user/password rabbitmq
credentials=pika.credentials.PlainCredentials(config["rm_user"],config["rm_pass"])
# connect to rabbitmq
connection = pika.BlockingConnection(pika.ConnectionParameters(host=config["rm_host"],
            virtual_host=config["rm_vhost"],credentials=credentials))
channel = connection.channel()

# declare exchange
channel.exchange_declare(exchange=EXCHANGE,type=EXCHANGE_TYPE,durable=True, auto_delete=True )

# .. and send message
channel.basic_publish(exchange=EXCHANGE,
                      routing_key=ROUTING_KEY,
                      body=json_encoded)
# close rabbitmq
connection.close()


