from queue import Queue

from libretime_playout.config import Config
from libretime_playout.message_handler import MessageListener


def test_message_listener(config: Config):
    MessageListener(config, Queue(), Queue())
