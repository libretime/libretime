#!/usr/bin/python
import pika, os, logging
import json
import psycopg2
from libretime_watch import readconfig as airtime

# initialize logging
logfile= "/var/log/airtime/libretime_watch_cron.log"
logging.basicConfig(format='%(asctime)s %(message)s',filename=logfile,level=logging.INFO)

EXCHANGE="airtime-media-monitor"
EXCHANGE_TYPE = "direct"
ROUTING_KEY="filesystem"
QUEUE="media-monitor"

config = {}

def main():
  # get the config data
  airtime.read_config(config)

  # user/password rabbitmq
  credentials=pika.credentials.PlainCredentials(config["rm_user"],config["rm_pass"])
  
  # connect to rabbitmq
  connection = pika.BlockingConnection(pika.ConnectionParameters(host=config["rm_host"],
              virtual_host=config["rm_vhost"],credentials=credentials))
  channel = connection.channel()

  # declare exchange
  channel.exchange_declare(exchange=EXCHANGE,exchange_type=EXCHANGE_TYPE,durable=True, auto_delete=True )
  
  # Delete queue
  channel.queue_delete(queue=QUEUE)
  connection.close()

  logging.info("Deleted watch folder queue.")
  exit()

if __name__ == "__main__":
  main()
