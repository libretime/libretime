import telnetlib

def create_liquidsoap_annotation(media):
    # We need liq_start_next value in the annotate. That is the value that controls overlap duration of crossfade.
    return 'annotate:media_id="%s",liq_start_next="0",liq_fade_in="%s",' + \
            'liq_fade_out="%s",liq_cue_in="%s",liq_cue_out="%s",' + \
            'schedule_table_id="%s",replay_gain="%s dB":%s' % \
            (media['id'], 
                    float(media['fade_in']) / 1000, 
                    float(media['fade_out']) / 1000, 
                    float(media['cue_in']), 
                    float(media['cue_out']), 
                    media['row_id'], 
                    media['replay_gain'], 
                    media['dst'])

class TelnetLiquidsoap:

    def __init__(self, telnet_lock, logger, ls_host, ls_port, queues):
        self.telnet_lock = telnet_lock
        self.ls_host = ls_host
        self.ls_port = ls_port
        self.logger = logger
        self.queues = queues
        self.current_prebuffering_stream_id = None

    def __connect(self):
        return telnetlib.Telnet(self.ls_host, self.ls_port)

    def __is_empty(self, tn, queue_id):
        return True

    def queue_clear_all(self):
        try:
            self.telnet_lock.acquire()
            tn = self.__connect()

            for i in self.queues:
                msg = 'queues.%s_skip\n' % i
                self.logger.debug(msg)
                tn.write(msg)
            
            tn.write("exit\n")
            self.logger.debug(tn.read_all())
        except Exception:
            raise
        finally:
            self.telnet_lock.release()

    def queue_remove(self, queue_id):
        try:
            self.telnet_lock.acquire()
            tn = self.__connect()

            msg = 'queues.%s_skip\n' % queue_id
            self.logger.debug(msg)
            tn.write(msg)
            
            tn.write("exit\n")
            self.logger.debug(tn.read_all())
        except Exception:
            raise
        finally:
            self.telnet_lock.release()


    def queue_push(self, queue_id, media_item):
        try:
            self.telnet_lock.acquire()
            tn = self.__connect()

            if not self.__is_empty(tn, queue_id):
                raise QueueNotEmptyException()

            annotation = create_liquidsoap_annotation(media_item)
            msg = '%s.push %s\n' % (queue_id, annotation.encode('utf-8'))
            self.logger.debug(msg)
            tn.write(msg)

            show_name = media_item['show_name']
            msg = 'vars.show_name %s\n' % show_name.encode('utf-8')
            tn.write(msg)
            self.logger.debug(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())
        except Exception:
            raise
        finally:
            self.telnet_lock.release()


    def stop_web_stream_buffer(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.ls_host, self.ls_port)
            #dynamic_source.stop http://87.230.101.24:80/top100station.mp3

            msg = 'http.stop\n'
            self.logger.debug(msg)
            tn.write(msg)

            msg = 'dynamic_source.id -1\n'
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())

        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def stop_web_stream_output(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.ls_host, self.ls_port)
            #dynamic_source.stop http://87.230.101.24:80/top100station.mp3

            msg = 'dynamic_source.output_stop\n'
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())

        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def start_web_stream(self, media_item):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.ls_host, self.ls_port)

            #TODO: DO we need this?
            msg = 'streams.scheduled_play_start\n'
            tn.write(msg)

            msg = 'dynamic_source.output_start\n'
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())

            self.current_prebuffering_stream_id = None
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()
        
    def start_web_stream_buffer(self, media_item):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.ls_host, self.ls_port)

            msg = 'dynamic_source.id %s\n' % media_item['row_id']
            self.logger.debug(msg)
            tn.write(msg)

            msg = 'http.restart %s\n' % media_item['uri'].encode('latin-1')
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            self.logger.debug(tn.read_all())

            self.current_prebuffering_stream_id = media_item['row_id']
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def get_current_stream_id(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.ls_host, self.ls_port)

            msg = 'dynamic_source.get_id\n'
            self.logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            stream_id = tn.read_all().splitlines()[0]
            self.logger.debug("stream_id: %s" % stream_id)

            return stream_id
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

class DummyTelnetLiquidsoap:

    def __init__(self, telnet_lock, logger):
        self.telnet_lock = telnet_lock
        self.liquidsoap_mock_queues = {}
        self.logger = logger

        for i in range(4):
            self.liquidsoap_mock_queues["s"+str(i)] = []

    def queue_push(self, queue_id, media_item):
        try:
            self.telnet_lock.acquire()

            self.logger.info("Pushing %s to queue %s" % (media_item, queue_id))
            from datetime import datetime
            print "Time now: %s" % datetime.utcnow()

            annotation = create_liquidsoap_annotation(media_item)
            self.liquidsoap_mock_queues[queue_id].append(annotation)
        except Exception:
            raise
        finally:
            self.telnet_lock.release()

    def queue_remove(self, queue_id):
        try:
            self.telnet_lock.acquire()

            self.logger.info("Purging queue %s" % queue_id)
            from datetime import datetime
            print "Time now: %s" % datetime.utcnow()

        except Exception:
            raise
        finally:
            self.telnet_lock.release()

class QueueNotEmptyException(Exception):
    pass
