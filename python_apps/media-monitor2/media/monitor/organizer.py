# -*- coding: utf-8 -*-

from media.monitor.handler import ReportHandler
import media.monitor.pure as mmp
from media.monitor.log import Loggable
from media.monitor.exceptions import BadSongFile

class Organizer(ReportHandler,Loggable):
    """
    Organizer is responsible to to listening to OrganizeListener events and
    committing the appropriate changes to the filesystem. It does not in any
    interact with WatchSyncer's even when the the WatchSyncer is a "storage
    directory". The "storage" directory picks up all of its events through
    pyinotify. (These events are fed to it through StoreWatchListener)
    """

    _instance = None
    def __new__(cls, channel, target_path, recorded_path):
        if cls._instance:
            cls._instance.channel = channel
            cls._instance.target_path = target_path
            cls._instance.recorded_path = recorded_path
        else:
            cls._instance = super(Organizer, cls).__new__( cls, channel,
                    target_path, recorded_path)
        return cls._instance

    def __init__(self, channel, target_path, recorded_path):
        self.channel = channel
        self.target_path = target_path
        self.recorded_path = recorded_path
        super(Organizer, self).__init__(signal=self.channel, weak=False)

    def handle(self, sender, event):
        """
        Intercept events where a new file has been added to the organize
        directory and place it in the correct path (starting with
        self.target_path)
        """
        try:
            # We must select the target_path based on whether file was recorded
            # by airtime or not.
            # Do we need to "massage" the path using mmp.organized_path?
            target_path = self.recorded_path if event.metadata.is_recorded() \
                                             else self.target_path
            new_path = mmp.organized_path(event.path, target_path,
                    event.metadata.extract())
            mmp.magic_move(event.path, new_path)
            self.logger.info('Organized: "%s" into "%s"' %
                    (event.path, new_path))
        except BadSongFile as e:
            self.report_problem_file(event=event, exception=e)
        # probably general error in mmp.magic.move...
        except Exception as e:
            self.unexpected_exception( e )
            self.report_problem_file(event=event, exception=e)

