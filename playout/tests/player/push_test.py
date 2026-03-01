from queue import Queue
from unittest.mock import MagicMock

from libretime_playout.config import Config
from libretime_playout.player.push import PypoPush


def test_push_thread(config: Config):
    PypoPush(
        Queue(),
        MagicMock(),
        config,
    )
