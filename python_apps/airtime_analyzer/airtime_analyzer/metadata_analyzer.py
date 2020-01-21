import time
import datetime
import mutagen
import magic
import wave
import logging
import os
import hashlib
from .analyzer import Analyzer

class MetadataAnalyzer(Analyzer):

    @staticmethod
    def analyze(filename, metadata):
        ''' Extract audio metadata from tags embedded in the file (eg. ID3 tags)

            Keyword arguments:
                filename: The path to the audio file to extract metadata from.
                metadata: A dictionary that the extracted metadata will be added to.
        '''
        if not isinstance(filename, str):
            raise TypeError("filename must be string. Was of type " + type(filename).__name__)
        if not isinstance(metadata, dict):
            raise TypeError("metadata must be a dict. Was of type " + type(metadata).__name__)
        if not os.path.exists(filename):
            raise FileNotFoundError("audio file not found: {}".format(filename))

        #Airtime <= 2.5.x nonsense:
        metadata["ftype"] = "audioclip"
        #Other fields we'll want to set for Airtime:
        metadata["hidden"] = False

        # Get file size and md5 hash of the file
        metadata["filesize"] = os.path.getsize(filename)

        with open(filename, 'rb') as fh:
            m = hashlib.md5()
            while True:
                data = fh.read(8192)
                if not data:
                    break
                m.update(data)
            metadata["md5"] = m.hexdigest()

        # Mutagen doesn't handle WAVE files so we use a different package
        ms = magic.open(magic.MIME_TYPE)
        ms.load()
        with open(filename, 'rb') as fh:
            mime_check = ms.buffer(fh.read(2014))
        metadata["mime"] = mime_check
        if mime_check == 'audio/x-wav':
            return MetadataAnalyzer._analyze_wave(filename, metadata)

        #Extract metadata from an audio file using mutagen
        audio_file = mutagen.File(filename, easy=True)

        #Bail if the file couldn't be parsed. The title should stay as the filename
        #inside Airtime.
        if audio_file == None:  # Don't use "if not" here. It is wrong due to mutagen's design.
            return metadata
        # Note that audio_file can equal {} if the file is valid but there's no metadata tags.
        # We can still try to grab the info variables below.

        #Grab other file information that isn't encoded in a tag, but instead usually
        #in the file header. Mutagen breaks that out into a separate "info" object:
        info = audio_file.info
        if hasattr(info, "sample_rate"): # Mutagen is annoying and inconsistent
            metadata["sample_rate"] = info.sample_rate
        if hasattr(info, "length"):
            metadata["length_seconds"] = info.length
            #Converting the length in seconds (float) to a formatted time string
            track_length = datetime.timedelta(seconds=info.length)
            metadata["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
            # Other fields for Airtime
            metadata["cueout"] = metadata["length"]

        # Set a default cue in time in seconds
        metadata["cuein"] = 0.0;

        if hasattr(info, "bitrate"):
            metadata["bit_rate"] = info.bitrate

        # Use the mutagen to get the MIME type, if it has one. This is more reliable and
        # consistent for certain types of MP3s or MPEG files than the MIMEs returned by magic.
        if audio_file.mime:
            metadata["mime"] = audio_file.mime[0]

        #Try to get the number of channels if mutagen can...
        try:
            #Special handling for getting the # of channels from MP3s. It's in the "mode" field
            #which is 0=Stereo, 1=Joint Stereo, 2=Dual Channel, 3=Mono. Part of the ID3 spec...
            if metadata["mime"] in ["audio/mpeg", 'audio/mp3']:
                if info.mode == 3:
                    metadata["channels"] = 1
                else:
                    metadata["channels"] = 2
            else:
                metadata["channels"] = info.channels
        except (AttributeError, KeyError):
            #If mutagen can't figure out the number of channels, we'll just leave it out...
            pass

        #Try to extract the number of tracks on the album if we can (the "track total")
        try:
            track_number = audio_file["tracknumber"]
            if isinstance(track_number, list): # Sometimes tracknumber is a list, ugh
                track_number = track_number[0]
            track_number_tokens = track_number
            if '/' in track_number:
                track_number_tokens = track_number.split('/')
                track_number = track_number_tokens[0]
            elif '-' in track_number:
                track_number_tokens = track_number.split('-')
                track_number = track_number_tokens[0]
            metadata["track_number"] = track_number
            track_total = track_number_tokens[1]
            metadata["track_total"] = track_total
        except (AttributeError, KeyError, IndexError):
            #If we couldn't figure out the track_number or track_total, just ignore it...
            pass

        #We normalize the mutagen tags slightly here, so in case mutagen changes,
        #we find the
        mutagen_to_airtime_mapping = {
            'title':        'track_title',
            'artist':       'artist_name',
            'album':        'album_title',
            'bpm':          'bpm',
            'composer':     'composer',
            'conductor':    'conductor',
            'copyright':    'copyright',
            'comment':      'comment',
            'encoded_by':   'encoder',
            'genre':        'genre',
            'isrc':         'isrc',
            'label':        'label',
            'organization': 'label',
            #'length':       'length',
            'language':     'language',
            'last_modified':'last_modified',
            'mood':         'mood',
            'bit_rate':     'bit_rate',
            'replay_gain':  'replaygain',
            #'tracknumber':  'track_number',
            #'track_total':  'track_total',
            'website':      'website',
            'date':         'year',
            #'mime_type':    'mime',
        }

        for mutagen_tag, airtime_tag in mutagen_to_airtime_mapping.items():
            try:
                metadata[airtime_tag] = audio_file[mutagen_tag]

                # Some tags are returned as lists because there could be multiple values.
                # This is unusual so we're going to always just take the first item in the list.
                if isinstance(metadata[airtime_tag], list):
                    if metadata[airtime_tag]:
                        metadata[airtime_tag] = metadata[airtime_tag][0]
                    else: # Handle empty lists
                        metadata[airtime_tag] = ""

            except KeyError:
                continue

        return metadata

    @staticmethod
    def _analyze_wave(filename, metadata):
        try:
            reader = wave.open(filename, 'rb')
            metadata["channels"] = reader.getnchannels()
            metadata["sample_rate"] = reader.getframerate()
            length_seconds = float(reader.getnframes()) / float(metadata["sample_rate"])
            #Converting the length in seconds (float) to a formatted time string
            track_length = datetime.timedelta(seconds=length_seconds)
            metadata["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
            metadata["length_seconds"] = length_seconds
            metadata["cueout"] = metadata["length"]
        except wave.Error as ex:
            logging.error("Invalid WAVE file: {}".format(str(ex)))
            raise
        return metadata
