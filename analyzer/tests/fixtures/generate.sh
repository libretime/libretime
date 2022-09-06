#!/usr/bin/env bash

set -u

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v ffmpeg > /dev/null || error "ffmpeg command not found!"

cd "$(dirname "${BASH_SOURCE[0]}")" || error "could not change directory!"

ffmpeg_cmd() {
  ffmpeg -y "$@" 2> /dev/null
}

# <metadata> <input> <output>
tag() {
  metadata="$1" && shift
  input="$1" && shift
  output="$1" && shift
  if [[ ! -f "$output" ]]; then
    echo "tagging $output from $input with $metadata"
    ffmpeg_cmd -i "$input" -f ffmetadata -i "$metadata" -c copy -map_metadata 1 "$output" ||
      error "could not tag $output"
  fi
}

# <input> <output> <flags...>
generate() {
  input="$1" && shift
  output="$1" && shift
  if [[ ! -f "$output" ]]; then
    echo "generating $output from $input"
    ffmpeg_cmd  -i "$input" -vn "$@" "$output" ||
      error "could not generate $output"
  fi
}

# Generate sample 1
generate  s1.flac s1-mono.flac         -ac 1   -acodec flac
generate  s1.flac s1-mono.wav          -ac 1
generate  s1.flac s1-mono.m4a          -ac 1   -acodec aac
generate  s1.flac s1-mono.mp3          -ac 1   -acodec libmp3lame
generate  s1.flac s1-mono.ogg          -ac 1   -acodec libvorbis
generate  s1.flac s1-stereo.flac       -ac 2   -acodec flac
generate  s1.flac s1-stereo.wav        -ac 2
generate  s1.flac s1-stereo.m4a        -ac 2   -acodec aac
generate  s1.flac s1-stereo.mp3        -ac 2   -acodec libmp3lame
generate  s1.flac s1-stereo.ogg        -ac 2   -acodec libvorbis
generate  s1.flac s1-jointstereo.mp3   -ac 2   -acodec libmp3lame    -joint_stereo 1

# Generate sample 1 +/-12dB
generate  s1.flac s1-mono-12.flac         -ac 1   -acodec flac          -af volume=-12dB
generate  s1.flac s1-stereo-12.flac       -ac 2   -acodec flac          -af volume=-12dB
generate  s1.flac s1-mono-12.mp3          -ac 1   -acodec libmp3lame    -af volume=-12dB
generate  s1.flac s1-stereo-12.mp3        -ac 2   -acodec libmp3lame    -af volume=-12dB

generate  s1.flac s1-mono+12.flac         -ac 1   -acodec flac          -af volume=+12dB
generate  s1.flac s1-stereo+12.flac       -ac 2   -acodec flac          -af volume=+12dB
generate  s1.flac s1-mono+12.mp3          -ac 1   -acodec libmp3lame    -af volume=+12dB
generate  s1.flac s1-stereo+12.mp3        -ac 2   -acodec libmp3lame    -af volume=+12dB

# Generate sample 1 large
if [[ ! -f s1-large.flac ]]; then
  echo "generating s1-large.flac from s1.flac"
  ffmpeg_cmd -stream_loop -1 -t $((3600 * 2)) -i s1.flac -vn s1-large.flac
fi

# Generate sample 2
generate  s2.flac s2-mono.flac         -ac 1   -acodec flac
generate  s2.flac s2-mono.m4a          -ac 1   -acodec aac
generate  s2.flac s2-mono.mp3          -ac 1   -acodec libmp3lame
generate  s2.flac s2-mono.ogg          -ac 1   -acodec libvorbis
generate  s2.flac s2-stereo.flac       -ac 2   -acodec flac
generate  s2.flac s2-stereo.m4a        -ac 2   -acodec aac
generate  s2.flac s2-stereo.mp3        -ac 2   -acodec libmp3lame
generate  s2.flac s2-stereo.ogg        -ac 2   -acodec libvorbis
generate  s2.flac s2-jointstereo.mp3   -ac 2   -acodec libmp3lame    -joint_stereo 1

# Generate sample 3
generate  s3.flac s3-stereo.flac       -ac 2   -acodec flac
generate  s3.flac s3-stereo.m4a        -ac 2   -acodec aac
generate  s3.flac s3-stereo.mp3        -ac 2   -acodec libmp3lame
generate  s3.flac s3-stereo.ogg        -ac 2   -acodec libvorbis

# Tag sample 1
tag metadata.txt  s1-mono.flac         s1-mono-tagged.flac
tag metadata.txt  s1-mono.wav          s1-mono-tagged.wav
tag metadata.txt  s1-mono.m4a          s1-mono-tagged.m4a
tag metadata.txt  s1-mono.mp3          s1-mono-tagged.mp3
tag metadata.txt  s1-mono.ogg          s1-mono-tagged.ogg
tag metadata.txt  s1-stereo.flac       s1-stereo-tagged.flac
tag metadata.txt  s1-stereo.wav        s1-stereo-tagged.wav
tag metadata.txt  s1-stereo.m4a        s1-stereo-tagged.m4a
tag metadata.txt  s1-stereo.mp3        s1-stereo-tagged.mp3
tag metadata.txt  s1-stereo.ogg        s1-stereo-tagged.ogg
tag metadata.txt  s1-jointstereo.mp3   s1-jointstereo-tagged.mp3

# Tag utf8 sample 1
tag metadata-utf8.txt   s1-mono.flac       s1-mono-tagged-utf8.flac
tag metadata-utf8.txt   s1-mono.wav        s1-mono-tagged-utf8.wav
tag metadata-utf8.txt   s1-mono.m4a        s1-mono-tagged-utf8.m4a
tag metadata-utf8.txt   s1-mono.mp3        s1-mono-tagged-utf8.mp3
tag metadata-utf8.txt   s1-mono.ogg        s1-mono-tagged-utf8.ogg
tag metadata-utf8.txt   s1-stereo.flac     s1-stereo-tagged-utf8.flac
tag metadata-utf8.txt   s1-stereo.wav      s1-stereo-tagged-utf8.wav
tag metadata-utf8.txt   s1-stereo.m4a      s1-stereo-tagged-utf8.m4a
tag metadata-utf8.txt   s1-stereo.mp3      s1-stereo-tagged-utf8.mp3
tag metadata-utf8.txt   s1-stereo.ogg      s1-stereo-tagged-utf8.ogg
tag metadata-utf8.txt   s1-jointstereo.mp3 s1-jointstereo-tagged-utf8.mp3

# Extension less files
cp s1-stereo.ogg s1-stereo
cp s1-stereo-tagged.ogg s1-stereo-tagged
cp s1-stereo-tagged-utf8.ogg s1-stereo-tagged-utf8
