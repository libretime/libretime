import telnetlib

def create_liquidsoap_annotation(media):
    # We need liq_start_next value in the annotate. That is the value that controls overlap duration of crossfade.
    return 'annotate:media_id="%s",liq_start_next="0",liq_fade_in="%s",liq_fade_out="%s",liq_cue_in="%s",liq_cue_out="%s",schedule_table_id="%s",replay_gain="%s dB":%s' \
        % (media['id'], float(media['fade_in']) / 1000, float(media['fade_out']) / 1000, float(media['cue_in']), float(media['cue_out']), media['row_id'], media['replay_gain'], media['dst'])

class TelnetLiquidsoap:

    def __init__(self, telnet_lock, logger, ls_host, ls_port):
        self.telnet_lock = telnet_lock
        self.ls_host = ls_host
        self.ls_port = ls_port
        self.logger = logger

    def __connect(self):
        return telnetlib.Telnet(self.ls_host, self.ls_port)

    def __is_empty(self, tn, queue_id):
        return True


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

class QueueNotEmptyException(Exception):
    pass
