from libretime_playout.liquidsoap.version import get_liquidsoap_version

try:
    LIQ_VERSION = get_liquidsoap_version()
except FileNotFoundError:  # is liquidsoap installed?
    LIQ_VERSION = (0, 0, 0)

LIQ_VERSION_STR = ".".join(map(str, LIQ_VERSION))
