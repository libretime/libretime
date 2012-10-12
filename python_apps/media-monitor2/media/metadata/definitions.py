# -*- coding: utf-8 -*-
import media.metadata.process as md
from os.path import normpath
from media.monitor.pure import format_length, file_md5

defs_loaded = False

def is_defs_loaded():
    global defs_loaded
    return defs_loaded

def load_definitions():
    with md.metadata('MDATA_KEY_DURATION') as t:
        t.default(u'0.0')
        t.depends('length')
        t.translate(lambda k: format_length(k['length']))

    with md.metadata('MDATA_KEY_MIME') as t:
        t.default(u'')
        t.depends('mime')
        t.translate(lambda k: k['mime'].replace('-','/'))

    with md.metadata('MDATA_KEY_BITRATE') as t:
        t.default(u'')
        t.depends('bitrate')
        t.translate(lambda k: k['bitrate'])

    with md.metadata('MDATA_KEY_SAMPLERATE') as t:
        t.default(u'0')
        t.depends('sample_rate')
        t.translate(lambda k: k['sample_rate'])

    with md.metadata('MDATA_KEY_FTYPE') as t:
        t.depends('ftype') # i don't think this field even exists
        t.default(u'audioclip')
        t.translate(lambda k: k['ftype']) # but just in case

    with md.metadata("MDATA_KEY_CREATOR") as t:
        t.depends("artist")
        # A little kludge to make sure that we have some value for when we parse
        # MDATA_KEY_TITLE
        t.default(u"")
        t.max_length(512)

    with md.metadata("MDATA_KEY_SOURCE") as t:
        t.depends("album")
        t.max_length(512)

    with md.metadata("MDATA_KEY_GENRE") as t:
        t.depends("genre")
        t.max_length(64)

    with md.metadata("MDATA_KEY_MOOD") as t:
        t.depends("mood")
        t.max_length(64)

    with md.metadata("MDATA_KEY_TRACKNUMBER") as t:
        t.depends("tracknumber")

    with md.metadata("MDATA_KEY_BPM") as t:
        t.depends("bpm")
        t.max_length(8)

    with md.metadata("MDATA_KEY_LABEL") as t:
        t.depends("organization")
        t.max_length(512)

    with md.metadata("MDATA_KEY_COMPOSER") as t:
        t.depends("composer")
        t.max_length(512)

    with md.metadata("MDATA_KEY_ENCODER") as t:
        t.depends("encodedby")
        t.max_length(512)

    with md.metadata("MDATA_KEY_CONDUCTOR") as t:
        t.depends("conductor")
        t.max_length(512)

    with md.metadata("MDATA_KEY_YEAR") as t:
        t.depends("date")
        t.max_length(16)

    with md.metadata("MDATA_KEY_URL") as t:
        t.depends("website")

    with md.metadata("MDATA_KEY_ISRC") as t:
        t.depends("isrc")
        t.max_length(512)

    with md.metadata("MDATA_KEY_COPYRIGHT") as t:
        t.depends("copyright")
        t.max_length(512)

    with md.metadata("MDATA_KEY_ORIGINAL_PATH") as t:
        t.depends('path')
        t.translate(lambda k: unicode(normpath(k['path'])))

    #with md.metadata("MDATA_KEY_MD5") as t:
        #t.depends('path')
        #t.optional(False)
        #t.translate(lambda k: file_md5(k['path'], max_length=100))

    # owner is handled differently by (by events.py)

    # MDATA_KEY_TITLE is the annoying special case b/c we sometimes read it
    # from file name
    with md.metadata('MDATA_KEY_TITLE') as t:
        # Need to know MDATA_KEY_CREATOR to know if show was recorded. Value is
        # defaulted to "" from definitions above
        t.depends('title','MDATA_KEY_CREATOR')
        t.translate(lambda k: k['title'])
        t.max_length(512)

    with md.metadata('MDATA_KEY_LABEL') as t:
        t.depends('label')
        t.max_length(512)
