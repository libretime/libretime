import threading

class UserlessThread(Exception):
    def __str__(self):
        return "Current thread: %s is not an instance of InstanceThread \
                of InstanceInheritingThread" % str(threading.current_thread())

class HasUser(object):
    def user(self): return self._user
    def assign_user(self): self._user = threading.current_thread().user()

class InstanceThread(threading.Thread, HasUser):
    def __init__(self,user, *args, **kwargs):
        super(InstanceThread, self).__init__(*args, **kwargs)
        self._user = user
        
class InstanceInheritingThread(threading.Thread, HasUser):
    def __init__(self, *args, **kwargs):
        self.assign_user()
        super(InstanceInheritingThread, self).__init__(*args, **kwargs)

def user():
    try: return threading.current_thread().user()
    except AttributeError: raise UserlessThread()

def apc(): return user().api_client

def getsig(s): return user().signal(s)
