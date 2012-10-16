# -*- coding: utf-8 -*-
import mutagen
import os
import copy
from collections     import namedtuple
from mutagen.easymp4 import EasyMP4KeyError
from mutagen.easyid3 import EasyID3KeyError

from media.monitor.exceptions import BadSongFile, InvalidMetadataElement
from media.monitor.log        import Loggable
from media.monitor.pure       import format_length, truncate_to_length
import media.monitor.pure as mmp

# emf related stuff
from media.metadata.process import global_reader
import media.metadata.definitions as defs
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
}

class FakeMutagen(dict):
    """
    Need this fake mutagen object so that airtime_special functions
    return a proper default value instead of throwing an exceptions for
    files that mutagen doesn't recognize
    """
    FakeInfo = namedtuple('FakeInfo','length bitrate')
    def __init__(self,path):
        self.path = path
        self.mime = ['audio/wav']
        self.info = FakeMutagen.FakeInfo(0.0, '')
        dict.__init__(self)
    def set_length(self,l):
        old_bitrate = self.info.bitrate
        self.info = FakeMutagen.FakeInfo(l, old_bitrate)

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
    def airtime_dict(d):
        """
        Converts mutagen dictionary 'd' into airtime dictionary
        """
        temp_dict = {}
        for m_key, m_val in d.iteritems():
            # TODO : some files have multiple fields for the same metadata.
            # genre is one example. In that case mutagen will return a list
            # of values

            if isinstance(m_val, list):
                # TODO : does it make more sense to just skip the element in
                # this case?
                if len(m_val) == 0: assign_val = ''
                else: assign_val = m_val[0]
            else: assign_val = m_val

            temp_dict[ m_key ] = assign_val
        airtime_dictionary = {}
        for muta_k, muta_v in temp_dict.iteritems():
            # We must check if we can actually translate the mutagen key into
            # an airtime key before doing the conversion
            if muta_k in mutagen2airtime:
                airtime_key = mutagen2airtime[muta_k]
                # Apply truncation in the case where airtime_key is in our
                # truncation table
                muta_v =  \
                        truncate_to_length(muta_v, truncate_table[airtime_key])\
                        if airtime_key in truncate_table else muta_v
                airtime_dictionary[ airtime_key ] = muta_v
        return airtime_dictionary

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
            if airtime_k in airtime2mutagen:
                # The unicode cast here is mostly for integers that need to be
                # strings
                try:
                    song_file[ airtime2mutagen[airtime_k] ] = unicode(airtime_v)
                except (EasyMP4KeyError, EasyID3KeyError) as e:
                    exceptions.append(InvalidMetadataElement(e, airtime_k,
                        path))
        song_file.save()
        # bubble dem up so that user knows that something is wrong
        for e in exceptions: raise e

    def __init__(self, fpath):
        self.__metadata = global_reader.read_mutagen(fpath)

    def __init__2(self, fpath):
        # Forcing the unicode through
        try    : fpath = fpath.decode("utf-8")
        except : pass

        if not mmp.file_playable(fpath): raise BadSongFile(fpath)

        try              : full_mutagen  = mutagen.File(fpath, easy=True)
        except Exception : raise BadSongFile(fpath)

        self.path = fpath
        if not os.path.exists(self.path):
            self.logger.info("Attempting to read metadata of file \
                    that does not exist. Setting metadata to {}")
            self.__metadata = {}
            return
        # TODO : Simplify the way all of these rules are handled right now it's
        # extremely unclear and needs to be refactored.
        #if full_mutagen is None: raise BadSongFile(fpath)

        if full_mutagen is None: full_mutagen = FakeMutagen(fpath)
        self.__metadata = Metadata.airtime_dict(full_mutagen)
        # Now we extra the special values that are calculated from the mutagen
        # object itself:

        if mmp.extension(fpath) == 'wav':
            full_mutagen.set_length(mmp.read_wave_duration(fpath))

        for special_key,f in airtime_special.iteritems():
            try:
                new_val = f(full_mutagen)
                if new_val is not None:
                    self.__metadata[special_key] = new_val
            except Exception as e:
                self.logger.info("Could not get special key %s for %s" %
                        (special_key, fpath))
                self.logger.info(str(e))
        # Finally, we "normalize" all the metadata here:
        self.__metadata = mmp.normalized_metadata(self.__metadata, fpath)
        # Now we must load the md5:
        # TODO : perhaps we shouldn't hard code how many bytes we're reading
        # from the file?
        self.__metadata['MDATA_KEY_MD5'] = mmp.file_md5(fpath,max_length=100)

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
