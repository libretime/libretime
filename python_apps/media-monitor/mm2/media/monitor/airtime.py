# -*- coding: utf-8 -*-
from kombu.messaging    import Exchange, Queue, Consumer
from kombu.connection   import BrokerConnection
from kombu.simple       import SimpleQueue
from os.path            import normpath

import json
import os
import copy
import time

from exceptions         import BadSongFile, InvalidMetadataElement, DirectoryIsNotListed
from metadata           import Metadata
from log                import Loggable
from syncdb             import AirtimeDB
from bootstrap          import Bootstrapper

from ..saas.thread      import apc, user

class AirtimeNotifier(Loggable):
    """
    AirtimeNotifier is responsible for interecepting RabbitMQ messages and
    feeding them to the event_handler object it was initialized with. The only
    thing it does to the messages is parse them from json
    """
    def __init__(self, cfg, message_receiver):
        self.cfg = cfg
        self.handler = message_receiver
        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

    def init_rabbit_mq(self):
        try:
            self.logger.info("Initializing RabbitMQ message consumer...")
            schedule_exchange = Exchange("airtime-media-monitor", "direct",
                    durable=True, auto_delete=True)
            schedule_queue = Queue("media-monitor", exchange=schedule_exchange,
                    key="filesystem")
            self.connection = BrokerConnection(self.cfg["rabbitmq"]["host"],
                    self.cfg["rabbitmq"]["user"], self.cfg["rabbitmq"]["password"],
                    self.cfg["rabbitmq"]["vhost"])
            channel  = self.connection.channel()

            self.simple_queue = SimpleQueue(channel, schedule_queue)

            self.logger.info("Initialized RabbitMQ consumer.")
        except Exception as e:
            self.logger.info("Failed to initialize RabbitMQ consumer")
            self.logger.error(e)
            return False

        return True


    def handle_message(self, message):
        """
        Messages received from RabbitMQ are handled here. These messages
        instruct media-monitor of events such as a new directory being watched,
        file metadata has been changed, or any other changes to the config of
        media-monitor via the web UI.
        """
        self.logger.info("Received md from RabbitMQ: %s" % str(message))
        m = json.loads(message)
        # TODO : normalize any other keys that could be used to pass
        # directories
        if 'directory' in m: m['directory'] = normpath(m['directory'])
        self.handler.message(m)

class AirtimeMessageReceiver(Loggable):
    def __init__(self, cfg, manager):
        self.dispatch_table = {
                'md_update'    : self.md_update,
                'new_watch'    : self.new_watch,
                'remove_watch' : self.remove_watch,
                'rescan_watch' : self.rescan_watch,
                'change_stor'  : self.change_storage,
                'file_delete'  : self.file_delete,
        }
        self.cfg     = cfg
        self.manager = manager

    def message(self, msg):
        """
        This method is called by an AirtimeNotifier instance that
        consumes the Rabbit MQ events that trigger this. The method
        return true when the event was executed and false when it wasn't.
        """
        msg = copy.deepcopy(msg)
        if msg['event_type'] in self.dispatch_table:
            evt = msg['event_type']
            del msg['event_type']
            self.logger.info("Handling RabbitMQ message: '%s'" % evt)
            self._execute_message(evt,msg)
            return True
        else:
            self.logger.info("Received invalid message with 'event_type': '%s'"
                    % msg['event_type'])
            self.logger.info("Message details: %s" % str(msg))
            return False
    def _execute_message(self,evt,message):
        self.dispatch_table[evt](message)

    def __request_now_bootstrap(self, directory_id=None, directory=None,
            all_files=True):
        if (not directory_id) and (not directory):
            raise ValueError("You must provide either directory_id or \
                    directory")
        sdb = AirtimeDB(apc())
        if directory            : directory = os.path.normpath(directory)
        if directory_id == None : directory_id = sdb.to_id(directory)
        if directory    == None : directory = sdb.to_directory(directory_id)
        try:
            bs = Bootstrapper( sdb, self.manager.watch_signal() )
            bs.flush_watch( directory=directory, last_ran=self.cfg.last_ran() )
        except Exception as e:
            self.fatal_exception("Exception bootstrapping: (dir,id)=(%s,%s)" %
                                 (directory, directory_id), e)
            raise DirectoryIsNotListed(directory, cause=e)

    def md_update(self, msg):
        self.logger.info("Updating metadata for: '%s'" %
                msg['MDATA_KEY_FILEPATH'])
        md_path = msg['MDATA_KEY_FILEPATH']
        try: Metadata.write_unsafe(path=md_path, md=msg)
        except BadSongFile as e:
            self.logger.info("Cannot find metadata file: '%s'" % e.path)
        except InvalidMetadataElement as e:
            self.logger.info("Metadata instance not supported for this file '%s'" \
                    % e.path)
            self.logger.info(str(e))
        except Exception as e:
            # TODO : add md_path to problem path or something?
            self.fatal_exception("Unknown error when writing metadata to: '%s'"
                    % md_path, e)

    def new_watch(self, msg, restart=False):
        msg['directory'] = normpath(msg['directory'])
        self.logger.info("Creating watch for directory: '%s'" %
                msg['directory'])
        if not os.path.exists(msg['directory']):
            try: os.makedirs(msg['directory'])
            except Exception as e:
                self.fatal_exception("Failed to create watched dir '%s'" %
                        msg['directory'],e)
            else:
                self.logger.info("Created new watch directory: '%s'" %
                        msg['directory'])
                self.new_watch(msg)
        else:
            self.__request_now_bootstrap( directory=msg['directory'],
                    all_files=restart)
            self.manager.add_watch_directory(msg['directory'])

    def remove_watch(self, msg):
        msg['directory'] = normpath(msg['directory'])
        self.logger.info("Removing watch from directory: '%s'" %
                msg['directory'])
        self.manager.remove_watch_directory(msg['directory'])

    def rescan_watch(self, msg):
        self.logger.info("Trying to rescan watched directory: '%s'" %
                         msg['directory'])
        try:
            # id is always an integer but in the dictionary the key is always a
            # string
            self.__request_now_bootstrap( unicode(msg['id']) )
        except DirectoryIsNotListed as e:
            self.fatal_exception("Bad rescan request", e)
        except Exception as e:
            self.fatal_exception("Bad rescan request. Unknown error.", e)
        else:
            self.logger.info("Successfully re-scanned: '%s'" % msg['directory'])

    def change_storage(self, msg):
        new_storage_directory = msg['directory']
        self.manager.change_storage_root(new_storage_directory)
        for to_bootstrap in [ self.manager.get_recorded_path(),
                self.manager.get_imported_path() ]:
            self.__request_now_bootstrap( directory=to_bootstrap )

    def file_delete(self, msg):
        # Deletes should be requested only from imported folder but we
        # don't verify that. Security risk perhaps?
        # we only delete if we are passed the special delete flag that is
        # necessary with every "delete_file" request
        if not msg['delete']:
            self.logger.info("No clippy confirmation, ignoring event. \
                    Out of curiousity we will print some details.")
            self.logger.info(msg)
            return
        # TODO : Add validation that we are deleting a file that's under our
        # surveillance. We don't to delete some random system file.
        if os.path.exists(msg['filepath']):
            try:
                self.logger.info("Attempting to delete '%s'" %
                        msg['filepath'])
                # We use FileMediator to ignore any paths with
                # msg['filepath'] so that we do not send a duplicate delete
                # request that we'd normally get form pyinotify. But right
                # now event contractor would take care of this sort of
                # thing anyway so this might not be necessary after all
                #user().file_mediator.ignore(msg['filepath'])
                os.unlink(msg['filepath'])
                # Verify deletion:
                if not os.path.exists(msg['filepath']):
                    self.logger.info("Successfully deleted: '%s'" %
                            msg['filepath'])
            except Exception as e:
                self.fatal_exception("Failed to delete '%s'" % msg['filepath'],
                        e)
        else: # validation for filepath existence failed
            self.logger.info("Attempting to delete file '%s' that does not \
                    exist. Full request:" % msg['filepath'])
            self.logger.info(msg)
