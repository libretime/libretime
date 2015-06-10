# -*- coding: utf-8 -*-
from pydispatch import dispatcher
import abc

from log import Loggable
from ..saas.thread import getsig
import pure as mmp

# Defines the handle interface
class Handles(object):
    __metaclass__ = abc.ABCMeta
    @abc.abstractmethod
    def handle(self, sender, event, *args, **kwargs): pass

# TODO : Investigate whether weak reffing in dispatcher.connect could possibly
# cause a memory leak

class ReportHandler(Handles):
    """
    A handler that can also report problem files when things go wrong
    through the report_problem_file routine
    """
    __metaclass__ = abc.ABCMeta
    def __init__(self, signal, weak=False):
        self.signal = getsig(signal)
        self.report_signal = getsig("badfile")
        def dummy(sender, event): self.handle(sender,event)
        dispatcher.connect(dummy, signal=self.signal, sender=dispatcher.Any,
                weak=weak)

    def report_problem_file(self, event, exception=None):
        dispatcher.send(signal=self.report_signal, sender=self, event=event,
                exception=exception)

class ProblemFileHandler(Handles, Loggable):
    """
    Responsible for answering to events passed through the 'badfile'
    signal. Moves the problem file passed to the designated directory.
    """
    def __init__(self, channel, **kwargs):
        self.channel = channel
        self.signal = getsig(self.channel.signal)
        self.problem_dir = self.channel.path
        def dummy(sender, event, exception):
            self.handle(sender, event, exception)
        dispatcher.connect(dummy, signal=self.signal, sender=dispatcher.Any,
                weak=False)
        mmp.create_dir( self.problem_dir )
        self.logger.info("Initialized problem file handler. Problem dir: '%s'" %
                self.problem_dir)

    def handle(self, sender, event, exception=None):
        # TODO : use the exception parameter for something
        self.logger.info("Received problem file: '%s'. Supposed to move it to \
                problem dir", event.path)
        try: mmp.move_to_dir(dir_path=self.problem_dir, file_path=event.path)
        except Exception as e:
            self.logger.info("Could not move file: '%s' to problem dir: '%s'" %
                    (event.path, self.problem_dir))
            self.logger.info("Exception: %s" % str(e))
