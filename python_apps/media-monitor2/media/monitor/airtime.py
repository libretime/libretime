# -*- coding: utf-8 -*-
from kombu.messaging import Exchange, Queue, Consumer
from kombu.connection import BrokerConnection
import json

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
        self.logger.info("Received md from RabbitMQ: " + body)
        m = json.loads(message.body)
        self.handler.message(m)


class AirtimeMessageReceiver(Loggable):
    def __init__(self, cfg):
        pass
    def message(self, msg):
        pass
