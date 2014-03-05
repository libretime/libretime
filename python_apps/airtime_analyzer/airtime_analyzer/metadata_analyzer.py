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
        metadata["length_seconds"] = info.length
        metadata["bitrate"] = info.bitrate
      
        #Use the python-magic module to get the MIME type.
        mime_magic = magic.Magic(mime=True)
        metadata["mime_type"] = mime_magic.from_file(filename)

        #We normalize the mutagen tags slightly here, so in case mutagen changes,
        #we find the 
        mutagen_to_analyzer_mapping = {
            'title':        'title',
            'artist':       'artist',
            'album':        'album',
            'bpm':          'bpm',
            'composer':     'composer',
            'conductor':    'conductor',
            'copyright':    'copyright',
            'encoded_by':   'encoder',
            'genre':        'genre',
            'isrc':         'isrc',
            'label':        'label',
            'language':     'language',
            'last_modified':'last_modified',
            'mood':         'mood',
            'replay_gain':  'replaygain',
            'track_number': 'tracknumber',
            'track_total':  'tracktotal',
            'website':      'website',
            'year':         'year',
        }

        for mutagen_tag, analyzer_tag in mutagen_to_analyzer_mapping.iteritems():
            try:
                metadata[analyzer_tag] = audio_file[mutagen_tag]

                # Some tags are returned as lists because there could be multiple values.
                # This is unusual so we're going to always just take the first item in the list.
                if isinstance(metadata[analyzer_tag], list):
                    metadata[analyzer_tag] = metadata[analyzer_tag][0]

            except KeyError:
                pass

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
