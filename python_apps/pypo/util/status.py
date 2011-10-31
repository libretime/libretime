# -*- coding: utf-8 -*-
import sys
import time
import urllib

import logging

import telnetlib
import json

import os

ALLOWED_EXTS = ('mp3') 
    
class Callable:
    def __init__(self, anycallable):
        self.__call__ = anycallable

class Status:
    def __init__(self, status_url):
        self.status_url = status_url
    def get_obp_version(self):
        logger = logging.getLogger("status.get_obp_version")
        # lookup OBP version
        try:
            response = urllib.urlopen(self.status_url)
            response_json = json.loads(response.read())
            obp_version = int(response_json['version'])
            logger.debug("OBP Version %s detected", obp_version)
    
        except Exception, e:
            print e
            obp_version = 0
            logger.error("Unable to detect OBP Version - %s", e)

        
        return obp_version
    
    
    def check_ls(self, ls_host, ls_port):
        logger = logging.getLogger("status.get_ls_version")
        # lookup OBP version
        try:
            tn = telnetlib.Telnet(ls_host, ls_port)
            tn.write("\n")
            tn.write("version\n")
            tn.write("exit\n")
            print tn.read_all()
            logger.info("liquidsoap connection ok")
            return 1
    
        except Exception, e:
            obp_version = 0
            logger.error("Unable to connect to liquidsoap")
            return 0
    
    
