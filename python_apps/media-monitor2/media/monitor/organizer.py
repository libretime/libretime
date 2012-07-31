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
    def __init__(self, channel, target_path):
        self.channel = channel
        self.target_path = target_path
        super(Organizer, self).__init__(signal=self.channel)
    def handle(self, sender, event):
        """Intercept events where a new file has been added to the organize
        directory and place it in the correct path (starting with self.target_path)"""
        try:
            new_path = mmp.organized_path(event.path, self.target_path, event.metadata.extract())
            mmp.magic_move(event.path, new_path)
            self.logger.info('Organized: "%s" into "%s"' % (event.path, new_path))
        except BadSongFile as e:
            self.report_problem_file(event=event, exception=e)
        # probably general error in mmp.magic.move...
        except Exception as e:
            self.report_problem_file(event=event, exception=e)

