import socket
import time
from log     import Loggable
from toucher import RepeatTimer
from amqplib.client_0_8.exceptions import AMQPConnectionException

class EventDrainer(Loggable):
    """
    Flushes events from RabbitMQ that are sent from airtime every
    certain amount of time
    """
    def __init__(self, airtime_notifier, interval=1):
        def cb():
            try:
                message = airtime_notifier.simple_queue.get(block=True)
                airtime_notifier.handle_message(message.payload)
                message.ack()
            except (IOError, AttributeError, AMQPConnectionException), e:
                self.logger.error('Exception: %s', e)
                while not airtime_notifier.init_rabbit_mq():
                    self.logger.error("Error connecting to RabbitMQ Server. \
                                      Trying again in few seconds")
                    time.sleep(5)

        t        = RepeatTimer(interval, cb)
        t.daemon = True
        t.start()
