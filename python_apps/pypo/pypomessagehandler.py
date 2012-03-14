import logging
import logging.config
import sys
from configobj import ConfigObj
from threading import Thread
import time
# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer
from kombu.exceptions import MessageStateError
from kombu.simple import SimpleQueue
import json

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    POLL_INTERVAL = int(config['poll_interval'])

except Exception, e:
    logger = logging.getLogger('message_h')
    logger.error('Error loading config file: %s', e)
    sys.exit()

class PypoMessageHandler(Thread):
    def __init__(self, pq, rq):
        Thread.__init__(self)
        self.logger = logging.getLogger('message_h')
        self.pypo_queue = pq
        self.recorder_queue = rq
        
    def init_rabbit_mq(self):
        self.logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], config["rabbitmq_vhost"])
            channel = connection.channel()
            self.simple_queue = SimpleQueue(channel, schedule_queue)
        except Exception, e:
            self.logger.error(e)
            return False
            
        return True
    
    """
    Handle a message from RabbitMQ, put it into our yucky global var.
    Hopefully there is a better way to do this.
    """
    def handle_message(self, message):
        try:        
            self.logger.info("Received event from RabbitMQ: %s" % message)
            
            m =  json.loads(message)
            command = m['event_type']
            self.logger.info("Handling command: " + command)
        
            if command == 'update_schedule':
                self.logger.info("Updating schdule...")
                self.pypo_queue.put(message)
            elif command == 'update_stream_setting':
                self.logger.info("Updating stream setting...")
                self.pypo_queue.put(message)
            elif command == 'update_stream_format':
                self.logger.info("Updating stream format...")
                self.pypo_queue.put(message)
            elif command == 'update_station_name':
                self.logger.info("Updating station name...")
                self.pypo_queue.put(message)
            elif command == 'cancel_current_show':
                self.logger.info("Cancel current show command received...")
                self.pypo_queue.put(message)
            elif command == 'switch_source':
                self.logger.info("switch_source command received...")
                self.pypo_queue.put(message)
            elif command == 'disconnect_source':
                self.logger.info("disconnect_source command received...")
                self.pypo_queue.put(message)
            elif command == 'update_recorder_schedule':
                self.recorder_queue.put(message)
            elif command == 'cancel_recording':
                self.recorder_queue.put(message)
        except Exception, e:
            self.logger.error("Exception in handling RabbitMQ message: %s", e)

    def main(self):
        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

        loops = 1
        while True:
            self.logger.info("Loop #%s", loops)
            try:
                message = self.simple_queue.get(block=True)
                self.handle_message(message.payload)
                # ACK the message to take it off the queue
                message.ack()
            except Exception, e:
                """
                sleep 5 seconds so that we don't spin inside this
                while loop and eat all the CPU
                """
                time.sleep(5)
                
                """
                There is a problem with the RabbitMq messenger service. Let's
                log the error and get the schedule via HTTP polling
                """
                self.logger.error("Exception, %s", e)

            loops += 1

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        while True:
            self.main()

