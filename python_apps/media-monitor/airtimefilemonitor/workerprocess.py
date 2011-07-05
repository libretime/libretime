class MediaMonitorWorkerProcess:

    #this function is run in its own process, and continuously
    #checks the queue for any new file events.
    def process_file_events(self, queue, notifier):

        while True:
            event = queue.get()
            notifier.logger.info("received event %s", event)
            notifier.update_airtime(event)

    