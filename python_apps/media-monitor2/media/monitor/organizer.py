from media.monitor.handler import Handler

class Organizer(Handler):
    def correct_path(self): pass
    def handle(self, sender, event):
        print("Handling event: %s" % str(event))


