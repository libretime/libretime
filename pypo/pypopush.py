import os
import sys
import time
import logging
import logging.config
import pickle
import telnetlib
import calendar
import json

from api_clients import api_client
from util import CueFile

from configobj import ConfigObj

# loading config file
try:
    config = ConfigObj('config.cfg')
    POLL_INTERVAL = float(config['poll_interval'])
    PUSH_INTERVAL = 0.5
    #PUSH_INTERVAL = float(config['push_interval'])
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

class PypoPush:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        self.cue_file = CueFile()
        self.set_export_source('scheduler')

        """
        push_ahead2 MUST be < push_ahead. The difference in these two values
        gives the number of seconds of the window of opportunity for the scheduler
        to catch when a playlist is to be played.
        """
        self.push_ahead = 10
        self.push_ahead2 = self.push_ahead -5

    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        self.schedule_file = self.cache_dir + 'schedule.pickle'
        self.schedule_tracker_file = self.cache_dir + "schedule_tracker.pickle"
        
    """
    The Push Loop - the push loop periodically (minimal 1/2 of the playlist-grid)
    checks if there is a playlist that should be scheduled at the current time.
    If yes, the temporary liquidsoap playlist gets replaced with the corresponding one,
    then liquidsoap is asked (via telnet) to reload and immediately play it.
    """
    def push(self, export_source):
        logger = logging.getLogger()

        self.schedule = self.load_schedule()
        playedItems = self.load_schedule_tracker()

        tcoming = time.localtime(time.time() + self.push_ahead)
        tcoming2 = time.localtime(time.time() + self.push_ahead2)
        

        str_tcoming_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming[0], tcoming[1], tcoming[2], tcoming[3], tcoming[4], tcoming[5])
        str_tcoming2_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming2[0], tcoming2[1], tcoming2[2], tcoming2[3], tcoming2[4], tcoming2[5])

        currently_on_air = False
        if self.schedule == None:
            logger.warn('Unable to loop schedule - maybe write in progress?')
            logger.warn('Will try again in next loop.')

        else:
            for pkey in self.schedule:
                plstart = pkey[0:19]
                start = self.schedule[pkey]['start']
                end = self.schedule[pkey]['end']
          
                playedFlag = (pkey in playedItems) and playedItems[pkey].get("played", 0)
                
                if plstart == str_tcoming_s or (plstart < str_tcoming_s and plstart > str_tcoming2_s and not playedFlag):
                    logger.debug('Preparing to push playlist scheduled at: %s', pkey)
                    playlist = self.schedule[pkey]

                    ptype = playlist['subtype']
                    currently_on_air = True

                    # We have a match, replace the current playlist and
                    # force liquidsoap to refresh.
                    if (self.push_liquidsoap(pkey, self.schedule, ptype) == 1):
                        logger.debug("Pushed to liquidsoap, updating 'played' status.")
                        # Marked the current playlist as 'played' in the schedule tracker
                        # so it is not called again in the next push loop.
                        # Write changes back to tracker file.
                        playedItems[pkey] = playlist
                        playedItems[pkey]['played'] = 1
                        schedule_tracker = open(self.schedule_tracker_file, "w")
                        pickle.dump(playedItems, schedule_tracker)
                        schedule_tracker.close()
                        logger.debug("Wrote schedule to disk: "+str(json.dumps(playedItems)))

                        # Call API to update schedule states
                        logger.debug("Doing callback to server to update 'played' status.")
                        self.api_client.notify_scheduled_item_start_playing(pkey, self.schedule)

        if self.schedule != None:
            tnow = time.localtime(time.time())
            str_tnow_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tnow[0], tnow[1], tnow[2], tnow[3], tnow[4], tnow[5])
            for pkey in self.schedule:
                start = self.schedule[pkey]['start']
                end = self.schedule[pkey]['end']

                if start <= str_tnow_s and str_tnow_s < end:
                    currently_on_air = True

        if not currently_on_air:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write('source.skip\n'.encode('latin-1'))
            tn.write('exit\n')
            tn.read_all()
            #logger.info('source.skip')
            #logger.debug(tn.read_all())

    def push_liquidsoap(self, pkey, schedule, ptype):
        logger = logging.getLogger()
        src = self.cache_dir + str(pkey) + '/list.lsp'

        try:
            if True == os.access(src, os.R_OK):
                logger.debug('OK - Can read playlist file')

            pl_file = open(src, "r")
            file_content = pl_file.read()
            pl_file.close()
            logger.debug('file content: %s' % (file_content))
            playlist = json.loads(file_content)

            #strptime returns struct_time in local time
            #mktime takes a time_struct and returns a floating point
            #gmtime Convert a time expressed in seconds since the epoch to a struct_time in UTC
            #mktime: expresses the time in local time, not UTC. It returns a floating point number, for compatibility with time().
            epoch_start = calendar.timegm(time.gmtime(time.mktime(time.strptime(pkey, '%Y-%m-%d-%H-%M-%S'))))

            #Return the time as a floating point number expressed in seconds since the epoch, in UTC.
            epoch_now = time.time()

            logger.debug("Epoch start: " + str(epoch_start))
            logger.debug("Epoch now: " + str(epoch_now))

            sleep_time = epoch_start - epoch_now;

            if sleep_time < 0:
                sleep_time = 0

            logger.debug('sleeping for %s s' % (sleep_time))
            time.sleep(sleep_time)

            tn = telnetlib.Telnet(LS_HOST, LS_PORT)

            #skip the currently playing song if any.
            logger.debug("source.skip\n")
            tn.write("source.skip\n")

            # Get any extra information for liquidsoap (which will be sent back to us)
            liquidsoap_data = self.api_client.get_liquidsoap_data(pkey, schedule)

            #Sending schedule table row id string.
            logger.debug("vars.pypo_data %s\n"%(str(liquidsoap_data["schedule_id"])))
            tn.write(("vars.pypo_data %s\n"%str(liquidsoap_data["schedule_id"])).encode('latin-1'))

            for item in playlist:
                annotate = str(item['annotate'])
                logger.debug(annotate)
                tn.write(('queue.push %s\n' % annotate).encode('latin-1'))
                tn.write(('vars.show_name %s\n' % item['show_name']).encode('latin-1'))

            tn.write("exit\n")
            logger.debug(tn.read_all())

            status = 1
        except Exception, e:
            logger.error('%s', e)
            status = 0

        return status


    def load_schedule(self):
        logger = logging.getLogger()
        schedule = None

        # create the file if it doesnt exist
        if (not os.path.exists(self.schedule_file)):
            logger.debug('creating file ' + self.schedule_file)
            open(self.schedule_file, 'w').close()
        else:
            # load the schedule from cache
            #logger.debug('loading schedule file '+self.schedule_file)
            try:
                schedule_file = open(self.schedule_file, "r")
                schedule = pickle.load(schedule_file)
                schedule_file.close()

            except Exception, e:
                logger.error('%s', e)

        return schedule


    def load_schedule_tracker(self):
        logger = logging.getLogger()
        playedItems = dict()

        # create the file if it doesnt exist
        if (not os.path.exists(self.schedule_tracker_file)):
            logger.debug('creating file ' + self.schedule_tracker_file)
            schedule_tracker = open(self.schedule_tracker_file, 'w')
            pickle.dump(playedItems, schedule_tracker)
            schedule_tracker.close()
        else:
            try:
                schedule_tracker = open(self.schedule_tracker_file, "r")
                playedItems = pickle.load(schedule_tracker)
                schedule_tracker.close()
            except Exception, e:
                logger.error('Unable to load schedule tracker file: %s', e)

        return playedItems

