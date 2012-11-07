import threading

tc = threading.local()

class InstanceThread(threading.Thread):
    def __init__(self,user, *args, **kwargs):
        super(InstanceThread, self).__init__(*args, **kwargs)
        self._user = user

    def run(self):
        tc._user = self._user
        
    def user(self):
        return tc._user

class InstanceInheritingThread(threading.Thread):
    def __init__(self, *args, **kwargs):
        super(InstanceInheritingThread, self).__init__(*args, **kwargs)
        self.user = tc._user
