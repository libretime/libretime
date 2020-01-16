from threading import Thread
import urllib.request, urllib.error, urllib.parse
import defusedxml.minidom
import base64
from datetime import datetime
import traceback
import logging
import time

from api_clients import api_client

class ListenerStat(Thread):

    HTTP_REQUEST_TIMEOUT = 30 # 30 second HTTP request timeout

    def __init__(self, config, logger=None):
        Thread.__init__(self)
        self.config = config
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


    def get_stream_server_xml(self, ip, url, is_shoutcast=False):
        encoded = base64.b64encode("%(admin_user)s:%(admin_pass)s" % ip)

        header = {"Authorization":"Basic %s" % encoded}

        if is_shoutcast:
            #user agent is required for shoutcast auth, otherwise it returns 404.
            user_agent = "Mozilla/5.0 (Linux; rv:22.0) Gecko/20130405 Firefox/22.0"
            header["User-Agent"] = user_agent

        req = urllib.request.Request(
            #assuming that the icecast stats path is /admin/stats.xml
            #need to fix this
            url=url,
            headers=header)

        f = urllib.request.urlopen(req, timeout=ListenerStat.HTTP_REQUEST_TIMEOUT)
        document = f.read()

        return document


    def get_icecast_stats(self, ip):
        document = None
        if "airtime.pro" in ip["host"].lower():
            url = 'http://%(host)s:%(port)s/stats.xsl' % ip
            document = self.get_stream_server_xml(ip, url)
        else:
            url = 'http://%(host)s:%(port)s/admin/stats.xml' % ip
            document = self.get_stream_server_xml(ip, url)
        dom = defusedxml.minidom.parseString(document)
        sources = dom.getElementsByTagName("source")

        mount_stats = None
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

    def get_shoutcast_stats(self, ip):
        url = 'http://%(host)s:%(port)s/admin.cgi?sid=1&mode=viewxml' % ip
        document = self.get_stream_server_xml(ip, url, is_shoutcast=True)
        dom = defusedxml.minidom.parseString(document)
        current_listeners = dom.getElementsByTagName("CURRENTLISTENERS")

        timestamp = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
        num_listeners = 0
        if len(current_listeners):
            num_listeners = self.get_node_text(current_listeners[0].childNodes)

        mount_stats = {"timestamp":timestamp, \
                    "num_listeners": num_listeners, \
                    "mount_name": "shoutcast"}

        return mount_stats

    def get_stream_stats(self, stream_parameters):
        stats = []

        #iterate over stream_parameters which is a list of dicts. Each dict
        #represents one Airtime stream (currently this limit is 3).
        #Note that there can be optimizations done, since if all three
        #streams are the same server, we will still initiate 3 separate
        #connections
        for k, v in list(stream_parameters.items()):
            if v["enable"] == 'true':
                try:
                    if v["output"] == "icecast":
                        mount_stats = self.get_icecast_stats(v)
                        if mount_stats: stats.append(mount_stats)
                    else:
                        stats.append(self.get_shoutcast_stats(v))
                    self.update_listener_stat_error(v["mount"], 'OK')
                except Exception as e:
                    try:
                        self.update_listener_stat_error(v["mount"], str(e))
                    except Exception as e:
                        self.logger.error('Exception: %s', e)

        return stats

    def push_stream_stats(self, stats):
        self.api_client.push_stream_stats(stats)

    def update_listener_stat_error(self, stream_id, error):
        keyname = '%s_listener_stat_error' % stream_id
        data = {keyname: error}
        self.api_client.update_stream_setting_table(data)

    def run(self):
        #Wake up every 120 seconds and gather icecast statistics. Note that we
        #are currently querying the server every 2 minutes for list of
        #mountpoints as well. We could remove this query if we hooked into
        #rabbitmq events, and listened for these changes instead.
        while True:
            try:
                stream_parameters = self.get_stream_parameters()
                stats = self.get_stream_stats(stream_parameters["stream_params"])

                if stats:
                    self.push_stream_stats(stats)
            except Exception as e:
                self.logger.error('Exception: %s', e)

            time.sleep(120)
        self.logger.info('ListenerStat thread exiting')


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

    #ls = ListenerStat(logger=logger)
    #ls.run()
