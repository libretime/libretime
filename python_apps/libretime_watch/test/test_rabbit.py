#!/usr/bin/python
import pika, os, logging
import json

#EXCHANGE = "airtime-watch"
#EXCHANGE_TYPE = "topic"
#ROUTING_KEY = ""
#QUEUE = "airtime-watch"

EXCHANGE="airtime-media-monitor"
EXCHANGE_TYPE = "direct"
ROUTING_KEY="filesystem"
QUEUE="media-monitor"



message = { 'cmd' : 'rescan_watch', 'id' : '34', }
json_encoded = json.dumps(message)

# connect to rabbitmq
credentials = pika.PlainCredentials('airtime', 'airtime')
# connect to rabbitmq
connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='localhost',virtual_host='/airtime',credentials=credentials))
channel = connection.channel()

channel.exchange_declare(exchange=EXCHANGE,type=EXCHANGE_TYPE,durable=True, auto_delete=True )

channel.basic_publish(exchange=EXCHANGE,
                      routing_key=ROUTING_KEY,
                      body=json_encoded)

connection.close()


