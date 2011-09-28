try:
    import amqplib
    import anyjson
    import configobj
    import kombu
    import mutagen
    import poster
    import pyinotify
    print 0
except ImportError, e:
    print 1