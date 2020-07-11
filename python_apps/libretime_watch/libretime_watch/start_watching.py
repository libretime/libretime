#!/usr/bin/python
import pika, os, logging
import json
import psycopg2
from libretime_watch import readconfig as airtime
from libretime_watch import log

# initialize logging
logfile= "/var/log/airtime/libretime_watch_cron.log"
log.setup(logfile, logging.WARNING)

EXCHANGE="airtime-media-monitor"
EXCHANGE_TYPE = "direct"
ROUTING_KEY="filesystem"
QUEUE="media-monitor"

config = {}

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
    logging.critical("I am unable to connect to the database.")
    exit(1)

  return conn

def main():
  # get the config data
  airtime.read_config(config)

  # connect to database
  conn=connect_database()
  cur = conn.cursor()
  try:
    cur.execute ("SELECT id,directory from cc_music_dirs where type = 'watched'")
    rows = cur.fetchall()
    cur.close()
  except:
    cur.close()
    logging.critical("Can't get directory for watching.")
    exit(1)

  for row in rows:
    id = row[0]
    watch_dir = row[1]
    message = { 'cmd' : 'rescan_watch', 'api_key' : str(config['api_key']), 'id' : str(id), 'directory' : str(watch_dir)}
    json_encoded = json.dumps(message)

    # user/password rabbitmq
    credentials=pika.credentials.PlainCredentials(config["rm_user"],config["rm_pass"])

    # connect to rabbitmq
    connection = pika.BlockingConnection(pika.ConnectionParameters(host=config["rm_host"],
                virtual_host=config["rm_vhost"],credentials=credentials))
    channel = connection.channel()

    # declare exchange
    channel.exchange_declare(exchange=EXCHANGE,exchange_type=EXCHANGE_TYPE,durable=True, auto_delete=True )

    # .. and send message
    channel.basic_publish(exchange=EXCHANGE,
                          routing_key=ROUTING_KEY,
                          body=json_encoded)
    # close rabbitmq
    connection.close()
    logging.info("Triggered watching folder scan for {0}".format(str(watch_dir)))

  exit()

if __name__ == "__main__":
  main()
