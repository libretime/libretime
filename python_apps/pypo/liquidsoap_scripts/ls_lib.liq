def notify(m)
  system("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']}")
  print("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']}")
end

# A function applied to each metadata chunk
def append_title(m) =  
  if !stream_metadata_type == 1 then
    [("artist","#{!show_name} - #{m['artist']}")]
  #####elsif !stream_metadata_type == 2 then
  #####  [("artist", ""), ("title", !show_name)]
  elsif !stream_metadata_type == 2 then
    [("artist",!station_name), ("title", !show_name)]
  else
    []
  end
end

def crossfade(s)
  #duration is automatically overwritten by metadata fields passed in
  #with audio
  s = fade.in(type="log", duration=0., s)
  s = fade.out(type="log", duration=0., s)
  fader = fun (a,b) -> add(normalize=false,[b,a])
  cross(fader,s)
end

# Define a transition that fades out the
# old source, adds a single, and then 
# plays the new source
def to_live(old,new) = 
  # Fade out old source
  old = fade.final(old)
  # Compose this in sequence with
  # the new source
  sequence([old,new])
end


def output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, s) =
    if output_type == "icecast" then
        output.icecast = output.icecast(host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    restart = true,
                    restart_delay = 5,
                    url = url,
                    description = description,
                    genre = genre) 
        if type == "mp3" then
            if bitrate == 24 then 
                ignore(output.icecast(%mp3(bitrate = 24),s))
            elsif bitrate == 32 then
                ignore(output.icecast(%mp3(bitrate = 32),s))
            elsif bitrate == 48 then
                ignore(output.icecast(%mp3(bitrate = 48),s))
            elsif bitrate == 64 then
                ignore(output.icecast(%mp3(bitrate = 64),s))
            elsif bitrate == 96 then
                ignore(output.icecast(%mp3(bitrate = 96),s))
            elsif bitrate == 128 then
                ignore(output.icecast(%mp3(bitrate = 128),s))
            elsif bitrate == 160 then
                ignore(output.icecast(%mp3(bitrate = 160),s))
            elsif bitrate == 192 then
                ignore(output.icecast(%mp3(bitrate = 192),s))
            elsif bitrate == 224 then
                ignore(output.icecast(%mp3(bitrate = 224),s))
            elsif bitrate == 256 then
                ignore(output.icecast(%mp3(bitrate = 256),s))
            elsif bitrate == 320 then
                ignore(output.icecast(%mp3(bitrate = 320),s))
            end
        else
            source = ref s
            if not output_icecast_vorbis_metadata then
                source := add(normalize=false, [amplify(0.00001, noise()),s])
            end
            if bitrate == 24 then 
                ignore(output.icecast(%vorbis.cbr(bitrate = 24),!source))
            elsif bitrate == 32 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 32),!source))
            elsif bitrate == 48 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 48),!source))
            elsif bitrate == 64 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 64),!source))
            elsif bitrate == 96 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 96),!source))
            elsif bitrate == 128 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 128),!source))
            elsif bitrate == 160 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 160),!source))
            elsif bitrate == 192 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 192),!source))
            elsif bitrate == 224 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 224),!source))
            elsif bitrate == 256 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 256),!source))
            elsif bitrate == 320 then
                ignore(output.icecast(%vorbis.cbr(bitrate = 320),!source))
            end
        end
    else
        output.shoutcast = output.shoutcast(host = host,
                    port = port,
                    password = pass,
                    fallible = true,
                    restart = true,
                    restart_delay = 5,
                    url = url,
                    genre = genre)
         if bitrate == 24 then 
                ignore(output.shoutcast(%mp3(bitrate = 24),s))
            elsif bitrate == 32 then
                ignore(output.shoutcast(%mp3(bitrate = 32),s))
            elsif bitrate == 48 then
                ignore(output.shoutcast(%mp3(bitrate = 48),s))
            elsif bitrate == 64 then
                ignore(output.shoutcast(%mp3(bitrate = 64),s))
            elsif bitrate == 96 then
                ignore(output.shoutcast(%mp3(bitrate = 96),s))
            elsif bitrate == 128 then
                ignore(output.shoutcast(%mp3(bitrate = 128),s))
            elsif bitrate == 160 then
                ignore(output.shoutcast(%mp3(bitrate = 160),s))
            elsif bitrate == 192 then
                ignore(output.shoutcast(%mp3(bitrate = 192),s))
            elsif bitrate == 224 then
                ignore(output.shoutcast(%mp3(bitrate = 224),s))
            elsif bitrate == 256 then
                ignore(output.shoutcast(%mp3(bitrate = 256),s))
            elsif bitrate == 320 then
                ignore(output.shoutcast(%mp3(bitrate = 320),s))
            end
    end
end

# Add a skip function to a source
# when it does not have one
# by default
def add_skip_command(s)
 # A command to skip
  def skip(_)
    # get playing (active) queue and flush it
    l = list.hd(server.execute("queue.secondary_queue"))
    l = string.split(separator=" ",l)
    list.iter(fun (rid) -> ignore(server.execute("queue.ignore #{rid}")), l)

    l = list.hd(server.execute("queue.primary_queue"))
    l = string.split(separator=" ", l)
    if list.length(l) > 0 then
      source.skip(s)
      "Skipped"
    else
      "Not skipped"
    end
  end
 # Register the command:
 server.register(namespace="source",
                 usage="skip",
                 description="Skip the current song.",
                 "skip",skip)
end
