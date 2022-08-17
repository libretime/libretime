import threading


def __timeout(func, timeout_duration, default, args, kwargs):
    class InterruptableThread(threading.Thread):
        name = "liquidsoap_timeout"

        def __init__(self):
            threading.Thread.__init__(self)
            self.result = default

        def run(self):
            self.result = func(*args, **kwargs)

    while True:
        thread = InterruptableThread()
        thread.start()
        thread.join(timeout_duration)

        if thread.is_alive():
            raise Exception("Thread did not terminate")

        return thread.result


def ls_timeout(func, timeout=15, default=None):
    def new_f(*args, **kwargs):
        return __timeout(func, timeout, default, args, kwargs)

    return new_f
