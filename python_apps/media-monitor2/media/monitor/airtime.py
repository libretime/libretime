# -*- coding: utf-8 -*-
from kombu.messaging import Exchange, Queue, Consumer
from kombu.connection import BrokerConnection
import json
import copy

from media.monitor.log import Loggable

# Do not confuse with media monitor 1's AirtimeNotifier class that more related
# to pyinotify's Notifier class. AirtimeNotifier just notifies when events come
# from Airtime itself. I.E. changes made in the web UI that must be updated
# through media monitor

class AirtimeNotifier(Loggable):
    """
    AirtimeNotifier is responsible for interecepting RabbitMQ messages and feeding them to the
    event_handler object it was initialized with. The only thing it does to the messages is parse
    them from json
    """
    def __init__(self, cfg, message_receiver):
        self.cfg = cfg
        try:
            self.handler = message_receiver
            self.logger.info("Initializing RabbitMQ message consumer...")
            schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
            #self.connection = BrokerConnection(cfg["rabbitmq_host"], cfg["rabbitmq_user"],
                    #cfg["rabbitmq_password"], cfg["rabbitmq_vhost"])
            connection = BrokerConnection(cfg["rabbitmq_host"], cfg["rabbitmq_user"],
                    cfg["rabbitmq_password"], cfg["rabbitmq_vhost"])
            channel = connection.channel()
            consumer = Consumer(channel, schedule_queue)
            consumer.register_callback(self.handle_message)
            consumer.consume()
        except Exception as e:
            self.logger.info("Failed to initialize RabbitMQ consumer")
            self.logger.error(e)
            raise

    def handle_message(self, body, message):
        """
        Messages received from RabbitMQ are handled here. These messages
        instruct media-monitor of events such as a new directory being watched,
        file metadata has been changed, or any other changes to the config of
        media-monitor via the web UI.
        """
        message.ack()
        self.logger.info("Received md from RabbitMQ: %s" % str(body))
        m = json.loads(message.body)
        self.handler.message(m)


class AirtimeMessageReceiver(Loggable):
    def __init__(self, cfg):
        self.dispatch_table = {
                'md_update' : self.md_update,
                'new_watch' : self.new_watch,
                'remove_watch' : self.remove_watch,
                'rescan_watch' : self.rescan_watch,
                'change_stor' : self.change_storage,
                'file_delete' : self.file_delete,
        }
        self.cfg = cfg
    def message(self, msg):
        """
        This method is called by an AirtimeNotifier instance that consumes the Rabbit MQ events
        that trigger this. The method return true when the event was executed and false when it
        wasn't
        """
        msg = copy.deepcopy(msg)
        if msg['event_type'] in self.dispatch_table:
            evt = msg['event_type']
            del msg['event_type']
            self.logger.info("Handling RabbitMQ message: '%s'" % evt)
            self._execute_message(evt,msg)
            return True
        else:
            self.logger.info("Received invalid message with 'event_type': '%s'" % msg['event_type'])
            self.logger.info("Message details: %s" % str(msg))
            return False
    def _execute_message(self,evt,message):
        self.dispatch_table[evt](message)

    def supported_messages(self):
        return self.dispatch_table.keys()

    # TODO : Handler methods - Should either fire the events directly with
    # pydispatcher or do the necessary changes on the filesystem that will fire
    # the events
    def md_update(self, msg):
        pass
    def new_watch(self, msg):
        pass
    def remove_watch(self, msg):
        pass
    def rescan_watch(self, msg):
        pass
    def change_storage(self, msg):
        pass
    def file_delete(self, msg):
        pass
