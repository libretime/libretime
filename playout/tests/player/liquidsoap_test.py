from unittest.mock import MagicMock

from libretime_playout.player.liquidsoap import PypoLiquidsoap


def test_liquidsoap():
    PypoLiquidsoap(MagicMock())
