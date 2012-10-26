# -*- coding: utf-8 -*-

import replaygain

import os
import hashlib
import mutagen
import logging
import math
import traceback


"""
list of supported easy tags in mutagen version 1.20
['albumartistsort', 'musicbrainz_albumstatus', 'lyricist', 'releasecountry',
'date', 'performer', 'musicbrainz_albumartistid', 'composer', 'encodedby',
'tracknumber', 'musicbrainz_albumid', 'album', 'asin', 'musicbrainz_artistid',
'mood', 'copyright', 'author', 'media', 'length', 'version', 'artistsort',
'titlesort', 'discsubtitle', 'website', 'musicip_fingerprint', 'conductor',
'compilation', 'barcode', 'performer:*', 'composersort', 'musicbrainz_discid',
'musicbrainz_albumtype', 'genre', 'isrc', 'discnumber', 'musicbrainz_trmid',
'replaygain_*_gain', 'musicip_puid', 'artist', 'title', 'bpm', 'musicbrainz_trackid',
'arranger', 'albumsort', 'replaygain_*_peak', 'organization']
"""

class AirtimeMetadata:

    def __init__(self):

        self.airtime2mutagen = {\
        "MDATA_KEY_TITLE": "title", \
        "MDATA_KEY_CREATOR": "artist", \
        "MDATA_KEY_SOURCE": "album", \
        "MDATA_KEY_GENRE": "genre", \
        "MDATA_KEY_MOOD": "mood", \
        "MDATA_KEY_TRACKNUMBER": "tracknumber", \
        "MDATA_KEY_BPM": "bpm", \
        "MDATA_KEY_LABEL": "organization", \
        "MDATA_KEY_COMPOSER": "composer", \
        "MDATA_KEY_ENCODER": "encodedby", \
        "MDATA_KEY_CONDUCTOR": "conductor", \
        "MDATA_KEY_YEAR": "date", \
        "MDATA_KEY_URL": "website", \
        "MDATA_KEY_ISRC": "isrc", \
        "MDATA_KEY_COPYRIGHT": "copyright", \
        }

        self.mutagen2airtime = {\
        "title": "MDATA_KEY_TITLE", \
        "artist": "MDATA_KEY_CREATOR", \
        "album": "MDATA_KEY_SOURCE", \
        "genre": "MDATA_KEY_GENRE", \
        "mood": "MDATA_KEY_MOOD", \
        "tracknumber": "MDATA_KEY_TRACKNUMBER", \
        "bpm": "MDATA_KEY_BPM", \
        "organization": "MDATA_KEY_LABEL", \
        "composer": "MDATA_KEY_COMPOSER", \
        "encodedby": "MDATA_KEY_ENCODER", \
        "conductor": "MDATA_KEY_CONDUCTOR", \
        "date": "MDATA_KEY_YEAR", \
        "website": "MDATA_KEY_URL", \
        "isrc": "MDATA_KEY_ISRC", \
        "copyright": "MDATA_KEY_COPYRIGHT", \
        }

        self.logger = logging.getLogger()

    def get_md5(self, filepath):
        """
        Returns an md5 of the file located at filepath. Returns an empty string
        if there was an error reading the file.
        """
        try:
            f = open(filepath, 'rb')
            m = hashlib.md5()
            m.update(f.read())
            md5 = m.hexdigest()
        except Exception, e:
            return ""

        return md5

    ## mutagen_length is in seconds with the format (d+).dd
    ## return format hh:mm:ss.uuu
    def format_length(self, mutagen_length):
        t = float(mutagen_length)
        h = int(math.floor(t / 3600))
        t = t % 3600
        m = int(math.floor(t / 60))

        s = t % 60
        # will be ss.uuu
        s = str(s)
        seconds = s.split(".")
        s = seconds[0]

        # have a maximum of 6 subseconds.
        if len(seconds[1]) >= 6:
            ss = seconds[1][0:6]
        else:
            ss = seconds[1][0:]

        length = "%s:%s:%s.%s" % (h, m, s, ss)

        return length

    def save_md_to_file(self, m):
        try:
            airtime_file = mutagen.File(m['MDATA_KEY_FILEPATH'], easy=True)

            for key in m:
                if key in self.airtime2mutagen:
                    value = m[key]

                    if value is not None:
                        value = unicode(value)
                    else:
                        value = unicode('');

                        #if len(value) > 0:
                    self.logger.debug("Saving key '%s' with value '%s' to file", key, value)
                    airtime_file[self.airtime2mutagen[key]] = value

            airtime_file.save()
        except Exception, e:
            self.logger.error('Trying to save md')
            self.logger.error('Exception: %s', e)
            self.logger.error('Filepath %s', m['MDATA_KEY_FILEPATH'])

    def truncate_to_length(self, item, length):
        if isinstance(item, int):
            item = str(item)
        if isinstance(item, basestring):
            if len(item) > length:
                return item[0:length]
            else:
                return item

    def get_md_from_file(self, filepath):
        """
        Returns None if error retrieving metadata. Otherwise returns a dictionary
        representing the file's metadata
        """

        self.logger.info("getting info from filepath %s", filepath)

        md = {}

        replay_gain_val = replaygain.calculate_replay_gain(filepath)
        self.logger.info('ReplayGain calculated as %s for %s' % (replay_gain_val, filepath))
        md['MDATA_KEY_REPLAYGAIN'] = replay_gain_val

        try:

            md5 = self.get_md5(filepath)
            md['MDATA_KEY_MD5'] = md5

            file_info = mutagen.File(filepath, easy=True)
        except Exception, e:
            self.logger.error("failed getting metadata from %s", filepath)
            self.logger.error("Exception %s", e)
            return None


        #check if file has any metadata
        if file_info is None:
            return None

        for key in file_info.keys() :
            if key in self.mutagen2airtime:
                val = file_info[key]
                try:
                    if val is not None and len(val) > 0 and val[0] is not None and len(val[0]) > 0:
                        md[self.mutagen2airtime[key]] = val[0]
                except Exception, e:
                    self.logger.error('Exception: %s', e)
                    self.logger.error("traceback: %s", traceback.format_exc())
        if 'MDATA_KEY_TITLE' not in md:
            #get rid of file extension from original name, name might have more than 1 '.' in it.
            original_name = os.path.basename(filepath)
            original_name = original_name.split(".")[0:-1]
            original_name = ''.join(original_name)
            md['MDATA_KEY_TITLE'] = original_name

        #incase track number is in format u'4/11'
        #need to also check that the tracknumber is even a tracknumber (cc-2582)
        if 'MDATA_KEY_TRACKNUMBER' in md:
            try:
                md['MDATA_KEY_TRACKNUMBER'] = int(md['MDATA_KEY_TRACKNUMBER'])
            except Exception, e:
                pass

            if isinstance(md['MDATA_KEY_TRACKNUMBER'], basestring):
                try:
                    md['MDATA_KEY_TRACKNUMBER'] = int(md['MDATA_KEY_TRACKNUMBER'].split("/")[0], 10)
                except Exception, e:
                    del md['MDATA_KEY_TRACKNUMBER']

        #make sure bpm is valid, need to check more types of formats for this tag to assure correct parsing.
        if 'MDATA_KEY_BPM' in md:
            if isinstance(md['MDATA_KEY_BPM'], basestring):
                try:
                    md['MDATA_KEY_BPM'] = int(md['MDATA_KEY_BPM'])
                except Exception, e:
                    del md['MDATA_KEY_BPM']

        #following metadata is truncated if needed to fit db requirements.
        if 'MDATA_KEY_GENRE' in md:
            md['MDATA_KEY_GENRE'] = self.truncate_to_length(md['MDATA_KEY_GENRE'], 64)

        if 'MDATA_KEY_TITLE' in md:
            md['MDATA_KEY_TITLE'] = self.truncate_to_length(md['MDATA_KEY_TITLE'], 512)

        if 'MDATA_KEY_CREATOR' in md:
            md['MDATA_KEY_CREATOR'] = self.truncate_to_length(md['MDATA_KEY_CREATOR'], 512)

        if 'MDATA_KEY_SOURCE' in md:
            md['MDATA_KEY_SOURCE'] = self.truncate_to_length(md['MDATA_KEY_SOURCE'], 512)

        if 'MDATA_KEY_MOOD' in md:
            md['MDATA_KEY_MOOD'] = self.truncate_to_length(md['MDATA_KEY_MOOD'], 64)

        if 'MDATA_KEY_LABEL' in md:
            md['MDATA_KEY_LABEL'] = self.truncate_to_length(md['MDATA_KEY_LABEL'], 512)

        if 'MDATA_KEY_COMPOSER' in md:
            md['MDATA_KEY_COMPOSER'] = self.truncate_to_length(md['MDATA_KEY_COMPOSER'], 512)

        if 'MDATA_KEY_ENCODER' in md:
            md['MDATA_KEY_ENCODER'] = self.truncate_to_length(md['MDATA_KEY_ENCODER'], 255)

        if 'MDATA_KEY_CONDUCTOR' in md:
            md['MDATA_KEY_CONDUCTOR'] = self.truncate_to_length(md['MDATA_KEY_CONDUCTOR'], 512)

        if 'MDATA_KEY_YEAR' in md:
            md['MDATA_KEY_YEAR'] = self.truncate_to_length(md['MDATA_KEY_YEAR'], 16)

        if 'MDATA_KEY_URL' in md:
            md['MDATA_KEY_URL'] = self.truncate_to_length(md['MDATA_KEY_URL'], 512)

        if 'MDATA_KEY_ISRC' in md:
            md['MDATA_KEY_ISRC'] = self.truncate_to_length(md['MDATA_KEY_ISRC'], 512)

        if 'MDATA_KEY_COPYRIGHT' in md:
            md['MDATA_KEY_COPYRIGHT'] = self.truncate_to_length(md['MDATA_KEY_COPYRIGHT'], 512)
        #end of db truncation checks.

        try:
            md['MDATA_KEY_BITRATE'] = getattr(file_info.info, "bitrate", 0)
            md['MDATA_KEY_SAMPLERATE'] = getattr(file_info.info, "sample_rate", 0)

            md['MDATA_KEY_DURATION'] = self.format_length(getattr(file_info.info, "length", 0.0))

            md['MDATA_KEY_MIME'] = ""
            if len(file_info.mime) > 0:
                md['MDATA_KEY_MIME'] = file_info.mime[0]
        except Exception as e:
            self.logger.warn(e)

        if "mp3" in md['MDATA_KEY_MIME']:
            md['MDATA_KEY_FTYPE'] = "audioclip"
        elif "vorbis" in md['MDATA_KEY_MIME']:
            md['MDATA_KEY_FTYPE'] = "audioclip"
        else:
            self.logger.error("File %s of mime type %s does not appear to be a valid vorbis or mp3 file." % (filepath, md['MDATA_KEY_MIME']))
            return None

        return md
