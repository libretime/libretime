#!/usr/bin/python
# -*- coding: utf-8 -*-

import codecs
import datetime
import json
import logging
import os
import pika
import psycopg2
import select
import signal
import subprocess
import sys
import time
import types

import readconfig as airtime
import metadata as airtime_md

EXCHANGE="airtime-media-monitor"
EXCHANGE_TYPE = "direct"
ROUTING_KEY="filesystem"
QUEUE="media-monitor"

timestamp_file = "/var/tmp/airtime/media-monitor/last_index"
logfile= "/var/log/airtime/libretime_watch.log"

# create empty dictionary 
database = {}

# keep the program running
shutdown=False

config = {}

logging.basicConfig(format='%(asctime)s %(message)s',filename=logfile,level=logging.INFO)

def update_database (conn):
  """Update database dictionary to cc_files
  """
  cur = conn.cursor()
  cols = database.keys()
  cols_str = str(cols)
  #cut off enclosing []
  cols_str = cols_str[1:-1]
  cols_str = cols_str.replace("'","")
  vals = [database[x] for x in cols]
  vals_str_list = ["%s"] * len(vals)
  vals_str = ", ".join(vals_str_list)
  try:
    cur.execute ("UPDATE cc_files set ({cols}) = ({vals_str}) where directory = {dir} and filepath ='{file}'"
       .format( cols = cols_str, vals_str = vals_str, dir = database["directory"], file = database["filepath"] ), vals)
  except psycopg2.Error as e:
    logging.error("Database error: {}".format(e.pgerror))
  else:
    conn.commit()
    logging.info("Updated: "+database["filepath"])
  finally:
    cur.close()

def insert_database (conn):
  cur = conn.cursor()
  cols = database.keys()
  cols_str = str(cols)
  #cut off enclosing []
  cols_str = cols_str[1:-1]
  cols_str = cols_str.replace("'","")
  vals = [database[x] for x in cols]
  vals_str_list = ["%s"] * len(vals)
  vals_str = ", ".join(vals_str_list)
  try:
    cur.execute ("INSERT INTO cc_files ({cols}) VALUES ({vals_str})".format(
           cols = cols_str, vals_str = vals_str), vals)
  except psycopg2.Error as e:
    logging.error("Database error: {}".format(e.pgerror))
  else:
    conn.commit()
    logging.info("Inserted: "+database["filepath"])
  finally:
    cur.close()

def touch_timestamp():
  """Returns the timestamp of the last run"""
  try:
     timestamp = time.strftime("%Y-%m-%d %H:%M:%S",time.localtime(int(os.path.getmtime(timestamp_file))))
  except OSError as e:
     # create directory if not exists
     folder=os.path.dirname(timestamp_file)
     if not os.path.exists(folder):
       os.makedirs(folder)
     timestamp="1974-01-01 00:00:00"
  #Update mtime
  file = open (timestamp_file,"w")
  file.write("")
  file.close()

  return timestamp

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
    logging.critical('Unable to connect to the database') 
  return conn


def watch (dir_id, directory):
    timestamp = touch_timestamp()
    logging.info ("Start scanning directory "+directory+ " for new files since "+ timestamp)
    conn = connect_database()
    watch_dir=str(directory)
    len_watch_dir=len(watch_dir) 
    # so now scan all directories
    for curroot, dirs, files in os.walk(watch_dir):
        if files == None:
          continue
        for curFile in files:
          database["directory"] = dir_id
          curFilePath = os.path.join(curroot, curFile)
          # cut off the watch_dir
          database["filepath"] = curFilePath[len_watch_dir:]
          # get modification date
          database["mtime"] = time.strftime("%Y-%m-%d %H:%M:%S",time.localtime(int(os.path.getmtime(curFilePath))))

          cur = conn.cursor()
          try:
            query = "SELECT count(*) FROM cc_files WHERE filepath = %s AND directory = %s"
            cur.execute(query, (database["filepath"], database["directory"]))            
          except: 
            logging.warning ("I can't SELECT count(*) ... from cc_files")
            logging.info ("Skipping: {}".format(curFilePath))
            continue
          row = cur.fetchone()
          counter = row[0]
          if counter == 0:
            # new file
            logging.info("--> New audio: "+database["filepath"])
            database["utime"] = datetime.datetime.now()
            if airtime_md.analyse_file (curFilePath,database):
              insert_database (conn)
            else:
              logging.warning("Problematic file: {}".format(database["filepath"]))
          elif counter >= 1:
            logging.info("--> Existing audio: "+database["filepath"])
            try:
              query = "SELECT mtime from cc_files WHERE filepath = %s AND directory = %s"
              cur.execute(query, (database["filepath"], database["directory"]))
            except:
              logging.warning ("I can't SELECT mtime ... from cc_files")
              continue
            row = cur.fetchone()
            fdate = row[0].strftime("%Y-%m-%d %H:%M:%S")
            # update needs only called, if new since last run
            if fdate < database["mtime"]:
               database["utime"] = datetime.datetime.now()
               if airtime_md.analyse_file (curFilePath,database):
                 update_database (conn)
    # close database session
    conn.close() 
    logging.info ("Scan finished..")

################################################################
# RabbitMQ parts
################################################################
def graceful_shutdown(self, signum, frame):
   '''Disconnect and break out of the message listening loop'''
   shutdown = True

def connect_to_messaging_server():
  """Connect to RabbitMQ Server and start listening for messages. 
     Returns RabbitMQ connection and channel
  """
  credentials=pika.credentials.PlainCredentials(config["rm_user"],config["rm_pass"])
  connection = pika.BlockingConnection(pika.ConnectionParameters(host=config["rm_host"],
            virtual_host=config["rm_vhost"],credentials=credentials))
  channel = connection.channel()
#  channel.exchange_delete (exchange=EXCHANGE)
  channel.exchange_declare(exchange=EXCHANGE, type=EXCHANGE_TYPE, durable=True, auto_delete=True)
#  channel.queue_delete(queue=QUEUE)
  result = channel.queue_declare(queue=QUEUE, durable=True)
  channel.queue_bind(exchange=EXCHANGE, queue=QUEUE, routing_key=ROUTING_KEY)

  logging.info("Listening for messages...")
  channel.basic_consume(msg_received_callback,queue=QUEUE, no_ack=False)

  return connection, channel

def msg_received_callback (channel, method, properties,body):
  '''Message reader'''
  try:
    msg_dict = json.loads(body)
    api_key         = msg_dict["api_key"]
    #callback_url    = msg_dict["callback_url"]

    #audio_file_path = msg_dict["tmp_file_path"]
    #import_directory = msg_dict["import_directory"]
    #original_filename = msg_dict["original_filename"]
    #file_prefix = msg_dict["file_prefix"]
    #storage_backend = msg_dict["storage_backend"]
  except Exception as e:
    logging.error("No JSON received: "+body+ str(e))

  if "rescan_watch" in msg_dict["cmd"]: 
       # now call the watching routine 
       logging.info ("Got message: "+msg_dict["cmd"]+" ID: "+str(msg_dict["id"])+" directory: "+msg_dict["directory"])
       watch(str(msg_dict["id"]),msg_dict["directory"]) 
  else :
       logging.info ("Got unhandled message: "+body)
  channel.basic_ack(delivery_tag = method.delivery_tag)

def wait_for_messages(channel):
  """Waiting for messages comming from RabbitMQ
  """
  channel.start_consuming()

def disconnect_from_messaging_server(connection):
  """Disconnect RabbitMQ"""
  connection.close()

def main():
  logging.info("Program started..")
  config = airtime.read_config()

  # Set up a signal handler so we can shutdown gracefully
  # For some reason, this signal handler must be set up here. I'd rather 
  # put it in AirtimeAnalyzerServer, but it doesn't work there (something to do
  # with pika's SIGTERM handler interfering with it, I think...)
  signal.signal(signal.SIGTERM, graceful_shutdown)

  while not shutdown:
    try:
       connection, channel = connect_to_messaging_server()
       wait_for_messages(channel)
    except (KeyboardInterrupt, SystemExit):
       break # Break out of the while loop and exit the application
    except select.error:
      pass
    except pika.exceptions.AMQPError as e:
       if shutdown:
          break
       logging.error("Connection to message queue failed. ")
       logging.error(e)
       logging.info("Retrying in 5 seconds...")
       time.sleep(5)
  # end of loop
  disconnect_from_messaging_server(connection)
  logging.info("Exiting cleanly.")


if __name__ == "__main__":
    main()
