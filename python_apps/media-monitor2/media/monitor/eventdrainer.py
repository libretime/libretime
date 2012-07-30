import socket
from media.monitor.log import Loggable
from media.monitor.toucher import RepeatTimer

class EventDrainer(Loggable):
    def __init__(self, connection, interval=1):
        def cb():
            try: connection.drain_events(timeout=0.3)
            except socket.timeout: pass
            except Exception as e:
                self.logger.error("Error flushing events")
                self.logger.error( str(e) )
        t = RepeatTimer(interval, cb)
        t.daemon = True
        t.start()
