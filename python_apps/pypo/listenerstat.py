from threading import Thread
import urllib2
import xml.dom.minidom
import base64
from datetime import datetime
import traceback
import logging
import time

from api_clients import api_client

class ListenerStat(Thread):
    def __init__(self, logger=None):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        if logger is None:
            self.logger = logging.getLogger()
        else:
            self.logger = logger

    def get_node_text(self, nodelist):
        rc = []
        for node in nodelist:
            if node.nodeType == node.TEXT_NODE:
                rc.append(node.data)
        return ''.join(rc)

    def get_stream_parameters(self):
        #[{"user":"", "password":"", "url":"", "port":""},{},{}]
        return self.api_client.get_stream_parameters()


    def get_icecast_xml(self, ip):
        encoded = base64.b64encode("%(admin_user)s:%(admin_password)s" % ip)

        header = {"Authorization":"Basic %s" % encoded}
        self.logger.debug(ip)
        url = 'http://%(host)s:%(port)s/admin/stats.xml' % ip
        self.logger.debug(url)
        req = urllib2.Request(
            #assuming that the icecast stats path is /admin/stats.xml
            #need to fix this
            url=url,
            headers=header)

        f = urllib2.urlopen(req)
        document = f.read()
        return document


    def get_icecast_stats(self, ip):
        document = self.get_icecast_xml(ip)
        dom = xml.dom.minidom.parseString(document)
        sources = dom.getElementsByTagName("source")

        mount_stats = {}
        for s in sources:
            #drop the leading '/' character
            mount_name = s.getAttribute("mount")[1:]
            if mount_name == ip["mount"]:
                timestamp = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
                listeners = s.getElementsByTagName("listeners")
                num_listeners = 0
                if len(listeners):
                    num_listeners = self.get_node_text(listeners[0].childNodes)

                mount_stats = {"timestamp":timestamp, \
                            "num_listeners": num_listeners, \
                            "mount_name": mount_name}
        return mount_stats

    def get_stream_stats(self, stream_parameters):
        stats = []

        #iterate over stream_parameters which is a list of dicts. Each dict
        #represents one Airtime stream (currently this limit is 3).
        #Note that there can be optimizations done, since if all three
        #streams are the same server, we will still initiate 3 separate
        #connections
        for k, v in stream_parameters.items():
            v["admin_user"] = "admin"
            v["admin_password"] = "hackme"
            if v["enable"] == 'true':
                stats.append(self.get_icecast_stats(v))
            #stats.append(get_shoutcast_stats(ip))

        return stats

    def push_stream_stats(self, stats):
        self.api_client.push_stream_stats(stats)

    def run(self):
        #Wake up every 120 seconds and gather icecast statistics. Note that we
        #are currently querying the server every 2 minutes for list of
        #mountpoints as well. We could remove this query if we hooked into
        #rabbitmq events, and listened for these changes instead.
        while True:
            try:
                stream_parameters = self.get_stream_parameters()

                stats = self.get_stream_stats(stream_parameters["stream_params"])
                self.logger.debug(stats)

                self.push_stream_stats(stats)
                time.sleep(120)
            except Exception, e:
                top = traceback.format_exc()
                self.logger.error('Exception: %s', top)
                time.sleep(120)


if __name__ == "__main__":
    # create logger
    logger = logging.getLogger('std_out')
    logger.setLevel(logging.DEBUG)
    # create console handler and set level to debug
    #ch = logging.StreamHandler()
    #ch.setLevel(logging.DEBUG)
    # create formatter
    formatter = logging.Formatter('%(asctime)s - %(name)s - %(lineno)s - %(levelname)s - %(message)s')
    # add formatter to ch
    #ch.setFormatter(formatter)
    # add ch to logger
    #logger.addHandler(ch)

    ls = ListenerStat(logger)
    ls.run()
