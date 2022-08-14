import threading

from .player import fetch


def __timeout(func, timeout_duration, default, args, kwargs):
    class InterruptableThread(threading.Thread):
        name = "liquidsoap_timeout"

        def __init__(self):
            threading.Thread.__init__(self)
            self.result = default

        def run(self):
            self.result = func(*args, **kwargs)

    first_attempt = True

    while True:
        thread = InterruptableThread()
        thread.start()
        if not first_attempt:
            timeout_duration = timeout_duration * 2
        thread.join(timeout_duration)

        if thread.is_alive():
            # Restart Liquidsoap and try the command one more time. If it
            # fails again then there is something critically wrong...
            if first_attempt:
                # restart liquidsoap
                fetch.PypoFetch.ref.restart_liquidsoap()
            else:
                raise Exception("Thread did not terminate")
        else:
            return thread.result

        first_attempt = False


def ls_timeout(func, timeout=15, default=None):
    def new_f(*args, **kwargs):
        return __timeout(func, timeout, default, args, kwargs)

    return new_f
