import threading
from . import pypofetch

def __timeout(func, timeout_duration, default, args, kwargs):

    class InterruptableThread(threading.Thread):
        def __init__(self):
            threading.Thread.__init__(self)
            self.result = default
        def run(self):
            self.result = func(*args, **kwargs)

    first_attempt = True

    while True:
        it = InterruptableThread()
        it.start()
        if not first_attempt:
            timeout_duration = timeout_duration * 2
        it.join(timeout_duration)

        if it.isAlive():
            """Restart Liquidsoap and try the command one more time. If it 
            fails again then there is something critically wrong..."""
            if first_attempt:
                #restart liquidsoap
                pypofetch.PypoFetch.ref.restart_liquidsoap()
            else:
                raise Exception("Thread did not terminate")
        else:
            return it.result

        first_attempt = False

def ls_timeout(f, timeout=15, default=None):
    def new_f(*args, **kwargs):
        return __timeout(f, timeout, default, args, kwargs)
    return new_f
