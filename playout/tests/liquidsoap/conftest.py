from libretime_playout.liquidsoap.version import get_liquidsoap_version

LIQ_VERSION = get_liquidsoap_version()
LIQ_VERSION_STR = ".".join(map(str, LIQ_VERSION))
