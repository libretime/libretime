#!/usr/bin/env python
# -*- coding: utf-8 -*-

# author Jonas Ohrstrom <jonas@digris.ch>

import sys
import time

import logging

import os
import socket


        
class DlsClient():

    def __init__(self, dls_host, dls_port, dls_user, dls_pass):
        self.dls_host = dls_host
        self.dls_port = dls_port
        self.dls_user = dls_user
        self.dls_pass = dls_pass

    def set_txt(self, txt):
        
        logger = logging.getLogger("DlsClient.set_txt")

        try:

            print 'trying to update dls'

            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((self.dls_host, self.dls_port))
            
            s.send('client_zzzz')
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.send('RS_DLS_VERSION' + ' ' + '1')
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.send('SERVICE' + ' ' + self.dls_user)
            s.send("\r\n")
            
            s.send('PASSWORD' + ' ' + self.dls_pass)
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.send('SET_DLS_CHARSET' + ' ' + '4')
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.send('CLEAR_DLS')
            s.send("\r\n")
            
            s.send('SET_DLS' + ' ' + txt)
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.send('CLOSE_DLS')
            s.send("\r\n")
            data = s.recv(1024)
            print data;
            
            s.close()
            
            
            print 'OK'   
    
        except Exception, e:
            #print e
            print 'did not work out.'
            dls_status = False
            logger.info("Unable to connect to the update metadata - %s", e)
    
        
        return 
    
    
    
    
    
