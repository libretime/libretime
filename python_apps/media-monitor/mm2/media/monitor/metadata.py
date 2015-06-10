# -*- coding: utf-8 -*-
import mutagen
import os
import copy
from mutagen.easymp4    import EasyMP4KeyError
from mutagen.easyid3    import EasyID3KeyError

from exceptions         import BadSongFile, InvalidMetadataElement
from log                import Loggable
from pure               import format_length
import pure             as mmp

# emf related stuff
from ..metadata.process import global_reader
from ..metadata         import definitions as defs
defs.load_definitions()

"""
list of supported easy tags in mutagen version 1.20
['albumartistsort', 'musicbrainz_albumstatus', 'lyricist', 'releasecountry',
'date', 'performer', 'musicbrainz_albumartistid', 'composer', 'encodedby',
'tracknumber', 'musicbrainz_albumid', 'album', 'asin', 'musicbrainz_artistid',
'mood', 'copyright', 'author', 'media', 'length', 'version', 'artistsort',
'titlesort', 'discsubtitle', 'website', 'musicip_fingerprint', 'conductor',
'compilation', 'barcode', 'performer:*', 'composersort', 'musicbrainz_discid',
'musicbrainz_albumtype', 'genre', 'isrc', 'discnumber', 'musicbrainz_trmid',
'replaygain_*_gain', 'musicip_puid', 'artist', 'title', 'bpm',
'musicbrainz_trackid', 'arranger', 'albumsort', 'replaygain_*_peak',
'organization']
"""

airtime2mutagen = {
    "MDATA_KEY_TITLE"       : "title",
    "MDATA_KEY_CREATOR"     : "artist",
    "MDATA_KEY_SOURCE"      : "album",
    "MDATA_KEY_GENRE"       : "genre",
    "MDATA_KEY_MOOD"        : "mood",
    "MDATA_KEY_TRACKNUMBER" : "tracknumber",
    "MDATA_KEY_BPM"         : "bpm",
    "MDATA_KEY_LABEL"       : "label",
    "MDATA_KEY_COMPOSER"    : "composer",
    "MDATA_KEY_ENCODER"     : "encodedby",
    "MDATA_KEY_CONDUCTOR"   : "conductor",
    "MDATA_KEY_YEAR"        : "date",
    "MDATA_KEY_URL"         : "website",
    "MDATA_KEY_ISRC"        : "isrc",
    "MDATA_KEY_COPYRIGHT"   : "copyright",
    "MDATA_KEY_CUE_IN"      : "cuein",
    "MDATA_KEY_CUE_OUT"     : "cueout",
}

#doesn't make sense for us to write these values to a track's metadata
mutagen_do_not_write = ["MDATA_KEY_CUE_IN", "MDATA_KEY_CUE_OUT"]

# Some airtime attributes are special because they must use the mutagen object
# itself to calculate the value that they need. The lambda associated with each
# key should attempt to extract the corresponding value from the mutagen object
# itself pass as 'm'. In the case when nothing can be extracted the lambda
# should return some default value to be assigned anyway or None so that the
# airtime metadata object will skip the attribute outright.

airtime_special = {
    "MDATA_KEY_DURATION" :
        lambda m: format_length(getattr(m.info, u'length', 0.0)),
    "MDATA_KEY_BITRATE" :
        lambda m: getattr(m.info, "bitrate", ''),
    "MDATA_KEY_SAMPLERATE" :
        lambda m: getattr(m.info, u'sample_rate', 0),
    "MDATA_KEY_MIME" :
        lambda m: m.mime[0] if len(m.mime) > 0 else u'',
}
mutagen2airtime = dict( (v,k) for k,v in airtime2mutagen.iteritems()
        if isinstance(v, str) )

truncate_table = {
        'MDATA_KEY_GENRE'     : 64,
        'MDATA_KEY_TITLE'     : 512,
        'MDATA_KEY_CREATOR'   : 512,
        'MDATA_KEY_SOURCE'    : 512,
        'MDATA_KEY_MOOD'      : 64,
        'MDATA_KEY_LABEL'     : 512,
        'MDATA_KEY_COMPOSER'  : 512,
        'MDATA_KEY_ENCODER'   : 255,
        'MDATA_KEY_CONDUCTOR' : 512,
        'MDATA_KEY_YEAR'      : 16,
        'MDATA_KEY_URL'       : 512,
        'MDATA_KEY_ISRC'      : 512,
        'MDATA_KEY_COPYRIGHT' : 512,
}

class Metadata(Loggable):
    # TODO : refactor the way metadata is being handled. Right now things are a
    # little bit messy. Some of the handling is in m.m.pure while the rest is
    # here. Also interface is not very consistent

    @staticmethod
    def fix_title(path):
        # If we have no title in path we will format it
        # TODO : this is very hacky so make sure to fix it
        m = mutagen.File(path, easy=True)
        if u'title' not in m:
            new_title = unicode( mmp.no_extension_basename(path) )
            m[u'title'] = new_title
            m.save()

    @staticmethod
    def write_unsafe(path,md):
        """
        Writes 'md' metadata into 'path' through mutagen. Converts all
        dictionary values to strings because mutagen will not write anything
        else
        """
        if not os.path.exists(path): raise BadSongFile(path)
        song_file = mutagen.File(path, easy=True)
        exceptions = [] # for bad keys
        for airtime_k, airtime_v in md.iteritems():
            if airtime_k in airtime2mutagen and \
                    airtime_k not in mutagen_do_not_write:
                # The unicode cast here is mostly for integers that need to be
                # strings
                if airtime_v is None: continue
                try:
                    song_file[ airtime2mutagen[airtime_k] ] = unicode(airtime_v)
                except (EasyMP4KeyError, EasyID3KeyError) as e:
                    exceptions.append(InvalidMetadataElement(e, airtime_k,
                        path))
        song_file.save()
        # bubble dem up so that user knows that something is wrong
        for e in exceptions: raise e

    def __init__(self, fpath):
        # Forcing the unicode through
        try    : fpath = fpath.decode("utf-8")
        except : pass
        self.__metadata = global_reader.read_mutagen(fpath)

    def is_recorded(self):
        """
        returns true if the file has been created by airtime through recording
        """
        return mmp.is_airtime_recorded( self.__metadata )

    def extract(self):
        """
        returns a copy of the metadata that was loaded when object was
        constructed
        """
        return copy.deepcopy(self.__metadata)

    def utf8(self):
        """
        Returns a unicode aware representation of the data that is compatible
        with what is spent to airtime
        """
        return mmp.convert_dict_value_to_utf8(self.extract())
