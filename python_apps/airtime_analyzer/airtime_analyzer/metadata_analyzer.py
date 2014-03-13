import time
import datetime
import mutagen
import magic # For MIME type detection
from analyzer import Analyzer

class MetadataAnalyzer(Analyzer):

    def __init__(self):
        pass

    @staticmethod
    def analyze(filename):

        metadata = dict()
        #Extract metadata from an audio file using mutagen
        audio_file = mutagen.File(filename, easy=True)

        #Grab other file information that isn't encoded in a tag, but instead usually
        #in the file header. Mutagen breaks that out into a separate "info" object:
        info = audio_file.info
        metadata["sample_rate"] = info.sample_rate
        #Converting the length in seconds (float) to a formatted time string
        track_length = datetime.timedelta(seconds=info.length)
        metadata["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
        metadata["bit_rate"] = info.bitrate
        #metadata["channels"] = info.channels
      
        #Use the python-magic module to get the MIME type.
        mime_magic = magic.Magic(mime=True)
        metadata["mime"] = mime_magic.from_file(filename)

        #Try to get the number of channels if mutagen can...
        try:
            #Special handling for getting the # of channels from MP3s. It's in the "mode" field
            #which is 0=Stereo, 1=Joint Stereo, 2=Dual Channel, 3=Mono. Part of the ID3 spec...
            if metadata["mime"] == "audio/mpeg":
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
            track_number_tokens = track_number.split(u'/')
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
            'length':       'length',
            'language':     'language',
            'last_modified':'last_modified',
            'mood':         'mood',
            'replay_gain':  'replaygain',
            #'tracknumber':  'track_number',
            #'track_total':  'track_total',
            'website':      'website',
            'date':         'year',
            #'mime_type':    'mime',
        }

        for mutagen_tag, airtime_tag in mutagen_to_airtime_mapping.iteritems():
            try:
                metadata[airtime_tag] = audio_file[mutagen_tag]

                # Some tags are returned as lists because there could be multiple values.
                # This is unusual so we're going to always just take the first item in the list.
                if isinstance(metadata[airtime_tag], list):
                    metadata[airtime_tag] = metadata[airtime_tag][0]

            except KeyError:
                continue 

        #Airtime <= 2.5.x nonsense:
        metadata["ftype"] = "audioclip"
        metadata["cueout"] = metadata["length"] 

        return metadata



'''
For reference, the Airtime metadata fields are:
            title
            artist ("Creator" in Airtime)
            album
            bit rate
            BPM
            composer
            conductor
            copyright
            cue in
            cue out
            encoded by
            genre
            ISRC
            label
            language
            last modified
            length
            mime
            mood
            owner
            replay gain
            sample rate
            track number
            website
            year
'''
