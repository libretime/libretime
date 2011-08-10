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
        if type == "mp3" then
            if bitrate == 64 then 
                ignore(output.icecast(%mp3(bitrate = 64),
                    host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    restart = true,
                    restart_delay = 5,
                    url = url,
                    description = description,
                    genre = genre,
                    s)
                )
            elsif bitrate == 128 then
                ignore(output.icecast(%mp3(bitrate = 128),
                    host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    restart = true,
                    restart_delay = 5,
                    url = url,
                    description = description,
                    genre = genre,
                    s)
                )
            end
        else
            if bitrate == 64 then 
                if output_icecast_vorbis_metadata then
                    ignore(output.icecast(%vorbis,
                        host = host,
                        port = port,
                        password = pass,
                        mount = mount_point,
                        fallible = true,
                        restart = true,
                        restart_delay = 5,
                        url = url,
                        description = description,
                        genre = genre,
                        s)
                    )
                else
                    s = add(normalize=false, [amplify(0.00001, noise()),s])
                    ignore(output.icecast(%vorbis,
                        host = host,
                        port = port,
                        password = pass,
                        mount = mount_point,
                        fallible = true,
                        restart = true,
                        restart_delay = 5,
                        url = url,
                        description = description,
                        genre = genre,
                        s)
                    )
                end
            elsif bitrate == 128 then
                if output_icecast_vorbis_metadata then
                    ignore(output.icecast(%vorbis,
                        host = host,
                        port = port,
                        password = pass,
                        mount = mount_point,
                        fallible = true,
                        restart = true,
                        restart_delay = 5,
                        url = url,
                        description = description,
                        genre = genre,
                        s)
                    )
                else
                    s = add(normalize=false, [amplify(0.00001, noise()),s])
                    ignore(output.icecast(%vorbis,
                        host = host,
                        port = port,
                        password = pass,
                        mount = mount_point,
                        fallible = true,
                        restart = true,
                        restart_delay = 5,
                        url = url,
                        description = description,
                        genre = genre,
                        s)
                    )
                end
            end
        end
    else
        if bitrate == 64 then 
            ignore(output.shoutcast(%mp3(bitrate = 64),
                host = host,
                port = port,
                password = pass,
                fallible = true,
                restart = true,
                restart_delay = 5,
                url = url,
                genre = genre,
                s)
            )
        elsif bitrate == 128 then
            ignore(output.shoutcast(%mp3(bitrate = 128),
                host = host,
                port = port,
                password = pass,
                fallible = true,
                restart = true,
                restart_delay = 5,
                url = url,
                genre = genre,
                s)
            )
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
