#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys,fileinput,time,types,os
import logging
import codecs
import datetime
import psycopg2

#for libretime, there is no interactive way to define the watch dir
#insert into cc_music_dirs (directory,type,exists,watched) values ('/srv/airtime/watch','watched','t','t');


# definitions RabbitMQ
EXCHANGE = "airtime-watch"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = ""
QUEUE = "airtime-watch"


# create empty dictionary 
database = {}
# keep the program running
run=1

#
# logging
#
logging.basicConfig(format='%(asctime)s %(message)s',filename='/var/log/airtime/libretime_watch.log',level=logging.INFO)

def connect_rabbitmq():
   import pika

   credentials = pika.PlainCredentials('airtime', 'airtime')
   # connect to rabbitmq
   connection = pika.BlockingConnection(pika.ConnectionParameters(
        host='localhost',virtual_host='/airtime',credentials=credentials))
   return (connection)

#   channel = connection.channel()
   
   #channel.queue_declare(queue='hello')

#def callback(ch, method, properties, body):
#    print(" [x] Received %r" % body)

#channel.basic_consume(callback,
#                      queue='hello',
#                      no_ack=True)


def update_database (conn):
   cur = conn.cursor()
   cols = database.keys()
   cols_str = str(cols)
   #cut off enclosing []
   cols_str = cols_str[1:-1]
   cols_str = cols_str.replace("'","")
   vals = [database[x] for x in cols]
   vals_str_list = ["%s"] * len(vals)
   vals_str = ", ".join(vals_str_list)
   cur.execute ("UPDATE cc_files set ({cols}) = ({vals_str}) where directory = {dir} and filepath ='{file}'"
       .format( cols = cols_str, vals_str = vals_str, dir = database["directory"], file = database["filepath"] ), vals)
   conn.commit()
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
   cur.execute ("INSERT INTO cc_files ({cols}) VALUES ({vals_str})".format(
           cols = cols_str, vals_str = vals_str), vals)
   conn.commit()
   cur.close()

def analyse_file (filename):
   import hashlib
   import magic
   # test
   from mimetypes import MimeTypes
   from mutagen.easyid3 import EasyID3
   from mutagen.mp3 import MP3

   analyse_ok=False
   logging.info ("analyse Filename: "+filename)
   #try to determin the filetype 
   mime_check = magic.from_file(filename, mime=True)
   database["mime"] = mime_check
   # test
   f = MP3(filename)
   mime_mutagen = f.mime[0]
   mime = MimeTypes()
   type, a = mime.guess_type(filename)
   logging.info ("mime_check :"+database["mime"]+ " mime: "+type+" mutagen: " +mime_mutagen )
   #
   database["ftype"] = "audioclip"
   database["filesize"] = os.path.getsize(filename) 
   database["import_status"]=0
   #md5
   with open(filename, 'rb') as fh:
       m = hashlib.md5()
       while True:
           data = fh.read(8192)
           if not data:
              break
           m.update(data)
       database["md5"] = m.hexdigest()
   # MP3 file ?
   if database["mime"] in ['audio/mpeg','audio/mp3','application/octet-stream']:
     try:
       audio = EasyID3(filename)
       database["track_title"]=audio['title'][0]
       try:
         database["artist_name"]=audio['artist'][0]
       except StandardError, err:
         logging.warning('no title ID3 for '+filename) 
         database["artist_name"]= ""       
       try:
         database["genre"]=audio['genre'][0]
       except StandardError, err:
         logging.warning('no genre ID3 for '+filename) 
         database["genre"]= ""
       try:
         database["album_title"]=audio['album'][0]
       except StandardError, err:
         database["album_title"]= ""
       # get data encoded into file
       f = MP3(filename)
       database["bit_rate"]=f.info.bitrate
       database["sample_rate"]=f.info.sample_rate
       if hasattr(f.info, "length"):
         #Converting the length in seconds (float) to a formatted time string
         track_length = datetime.timedelta(seconds=f.info.length)
         database["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
         # Other fields for Airtime
         database["cueout"] = database["length"]
       database["cuein"]= "00:00:00.0"
       # use mutage to get better mime 
       if  f.mime:
            database["mime"] = f.mime[0]
       if database["mime"] in ["audio/mpeg", 'audio/mp3']:
          if f.info.mode == 3:
                database["channels"] = 1
          else:
                database["channels"] = 2
       else:
            database["channels"] = f.info.channels
       analyse_ok=True

     except StandardError, err:
          logging.error('Error ',str(err),filename) 
          #print "Error: ",str(err),filename
   return analyse_ok

def connect_database(): 
  try:
    conn = psycopg2.connect("dbname='airtime' user='airtime' host='localhost' password='airtime'")
  except:
    logging.critical('Unable to connect to the database') 
    #print "I am unable to connect to the database"
  return conn


def main (run):
  while (run) :
    # Connect to airtime rabbitmq
    rabbit=connect_rabbitmq()
    # look for what dir we've to watch
    conn = connect_database()
    cur = conn.cursor()
    try:
      cur.execute ("SELECT directory,id from cc_music_dirs where type = 'watched'")
      row = cur.fetchone()
      watch_dir = row[0]+"/"
      directory=row[1]
      len_watch_dir = len(watch_dir) 
      cur.close()
    except:
      logging.critical("Can't get directory for watching") 
      #print ("Can't get directory for watching")
      exit()
 
    # so now scan all directories
    for curroot, dirs, files in os.walk(watch_dir):
        if files == None:
          continue
        for curFile in files:
          #database = {}
          database["directory"] = directory 
          curFilePath = os.path.join(curroot,curFile)
          # cut off the watch_dir
          database["filepath"] = curFilePath[len_watch_dir:]
          # get modification date
          database["mtime"] = time.strftime("%Y-%m-%d %H:%M:%S",time.localtime(int(os.path.getmtime(curFilePath))))
          # prepare database 
          cur = conn.cursor()
          #file already in database
          try:
            cur.execute ("SELECT count(*) from cc_files where"
                +" filepath = '"+database["filepath"]+"'" 
                +" and directory = "+str(database["directory"]))
          except: 
            logging.warning ("I can't SELECT count(*) ... from cc_files")
            print "I can't SELECT from cc_files"
          row = cur.fetchone()
          # is there already a record
          if row[0] == 0:
            logging.info("Insert: "+database["filepath"])
            #print ("Insert: "+database["filepath"])
            database["utime"] = datetime.datetime.now()
            if analyse_file (curFilePath):
              insert_database (conn)
            #let's sleep
#            time.sleep(1)
          else :
            cur1 = conn.cursor()
            try:
              # look for mtime
              cur1.execute ("SELECT mtime from cc_files where"
                +" filepath = '"+database["filepath"]+"'" 
                +" and directory = "+str(database["directory"]))
            except:
              logging.warning ("I can't SELECT mtime ... from cc_files")
              #print "I can't SELECT from cc_files"
            row = cur1.fetchone()
            # update needs only called, if mtime different
            if str(row[0]) != database["mtime"]:
               logging.info("Update: "+database["filepath"])
               #print ("Update "+database["filepath"])
               database["utime"] = datetime.datetime.now()
               if analyse_file (curFilePath):
                 update_database (conn)
            cur1.close()
          cur.close()
    #
    # to do... exit
    # close database session
    conn.close() 
    # time.sleep (300)	
    rabbit.close()    
    logging.info("Finished")
    run=0

if __name__ == "__main__":
    logging.info("Woke up")
    main(run)
