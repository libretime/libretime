import threading

tc = threading.local()

class HasUser(object):
    def user(self): 
        return self._user

class InstanceThread(threading.Thread, HasUser):
    def __init__(self,user, *args, **kwargs):
        super(InstanceThread, self).__init__(*args, **kwargs)
        self._user = user
        
class InstanceInheritingThread(threading.Thread, HasUser):
    def __init__(self, *args, **kwargs):
        self._user = threading.current_thread().user()
        super(InstanceInheritingThread, self).__init__(*args, **kwargs)
