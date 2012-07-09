from pydispatch import dispatcher
import abc

class Handler(object):
    __metaclass__ = abc.ABCMeta
    def __init__(self, signal, target):
        self.target = target
        self.signal = signal
        def dummy(sender, event):
            self.handle(sender,event)
        dispatcher.connect(dummy, signal=signal, sender=dispatcher.Any, weak=False)
    @abc.abstractmethod
    def handle(self, sender, event): pass



