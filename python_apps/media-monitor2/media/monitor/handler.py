# -*- coding: utf-8 -*-
from pydispatch import dispatcher
import abc

from media.monitor.log import Loggable

# Defines the handle interface
class Handles(object):
    __metaclass__ = abc.ABCMeta
    @abc.abstractmethod
    def handle(self, sender, event, *args, **kwargs): pass


# TODO : remove the code duplication between ReportHandler and
# ProblemFileHandler. Namely the part where both initialize pydispatch
# TODO : Investigate whether weak reffing in dispatcher.connect could possibly
# cause a memory leak

class ReportHandler(Handles):
    __metaclass__ = abc.ABCMeta
    def __init__(self, signal):
        self.signal = signal
        self.report_signal = "badfile"
        def dummy(sender, event): self.handle(sender,event)
        dispatcher.connect(dummy, signal=signal, sender=dispatcher.Any, weak=False)

    def report_problem_file(self, event, exception=None):
        dispatcher.send(signal=self.report_signal, sender=self, event=event, exception=exception)

class ProblemFileHandler(Handles, Loggable):
    def __init__(self, channel, **kwargs):
        self.channel = channel
        self.signal = self.channel.signal
        self.problem_dir = self.channel.path
        def dummy(sender, event, exception): self.handle(sender, event, exception)
        dispatcher.connect(dummy, signal=self.signal, sender=dispatcher.Any, weak=False)

    def handle(self, sender, event, exception=None):
        self.logger.info("Received problem file: '%s'. Supposed to move it somewhere", event.path)
        # TODO : not actually moving it anywhere yet

