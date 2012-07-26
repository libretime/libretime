def notify(m)
  log("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']} &")
  system("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']} &")
end

# A function applied to each metadata chunk
def append_title(m) =  
  log("Using stream_format #{!stream_metadata_type}")
  if !stream_metadata_type == 1 then
    [("artist","#{!show_name} - #{m['artist']}")]
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

def transition(a,b) =
  log("transition called...")
  add(normalize=false,
     [ sequence([ blank(duration=0.01),
                   fade.initial(duration=!default_dj_fade, b) ]),
        fade.final(duration=!default_dj_fade, a) ])
end

# we need this function for special transition case(from default to queue)
# we don't want the trasition fade to have effect on the first song that would
# be played siwtching out of the default(silent) source
def transition_default(a,b) =
  log("transition called...")
  if !just_switched then
      just_switched := false
      add(normalize=false,
         [ sequence([ blank(duration=0.01),
                       fade.initial(duration=!default_dj_fade, b) ]),
            fade.final(duration=!default_dj_fade, a) ])
  else
    just_switched := false
    b
  end
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


def output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, user, s, stream, connected, name) =
    def on_error(msg)
        connected := "false"
        system("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --error='#{msg}' --stream-id=#{stream} --time=#{!time} &")
        log("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --error='#{msg}' --stream-id=#{stream} --time=#{!time} &")
        5.
    end
    def on_connect()
        connected := "true"
        system("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --connect --stream-id=#{stream} --time=#{!time} &")
        log("/usr/lib/airtime/pypo/bin/liquidsoap_scripts/notify.sh --connect --stream-id=#{stream} --time=#{!time} &")
    end
    if output_type == "icecast" then
        user_ref = ref user
        if user == "" then
            user_ref := "source"
        end
        output = output.icecast(host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    url = url,
                    description = description,
                    genre = genre,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect,
                    name = name)
        if type == "mp3" then
            if bitrate == 24 then 
                ignore(output(%mp3(bitrate = 24),s))
            elsif bitrate == 32 then
                ignore(output(%mp3(bitrate = 32),s))
            elsif bitrate == 48 then
                ignore(output(%mp3(bitrate = 48),s))
            elsif bitrate == 64 then
                ignore(output(%mp3(bitrate = 64),s))
            elsif bitrate == 96 then
                ignore(output(%mp3(bitrate = 96),s))
            elsif bitrate == 128 then
                ignore(output(%mp3(bitrate = 128),s))
            elsif bitrate == 160 then
                ignore(output(%mp3(bitrate = 160),s))
            elsif bitrate == 192 then
                ignore(output(%mp3(bitrate = 192),s))
            elsif bitrate == 224 then
                ignore(output(%mp3(bitrate = 224),s))
            elsif bitrate == 256 then
                ignore(output(%mp3(bitrate = 256),s))
            elsif bitrate == 320 then
                ignore(output(%mp3(bitrate = 320),s))
            end
        else
            source = ref s
            if not icecast_vorbis_metadata then
                source := add(normalize=false, [amplify(0.00001, noise()),s])
            end
            if bitrate == 24 then 
                ignore(output(%vorbis(quality=-0.1),!source))
            elsif bitrate == 32 then
                ignore(output(%vorbis(quality=-0.1),!source))
            elsif bitrate == 48 then
                ignore(output(%vorbis(quality=-0.1),!source))
            elsif bitrate == 64 then
                ignore(output(%vorbis(quality=0),!source))
            elsif bitrate == 96 then
                ignore(output(%vorbis(quality=0.2),!source))
            elsif bitrate == 128 then
                ignore(output(%vorbis(quality=0.4),!source))
            elsif bitrate == 160 then
                ignore(output(%vorbis(quality=0.5),!source))
            elsif bitrate == 192 then
                ignore(output(%vorbis(quality=0.6),!source))
            elsif bitrate == 224 then
                ignore(output(%vorbis(quality=0.7),!source))
            elsif bitrate == 256 then
                ignore(output(%vorbis(quality=0.8),!source))
            elsif bitrate == 320 then
                ignore(output(%vorbis(quality=0.9),!source))
            end
        end
    else
        user_ref = ref user
        if user == "" then
            user_ref := "source"
        end
        
        description_ref = ref description
        if description == "" then
            description_ref := "N/A"
        end
        
        genre_ref = ref genre
        if genre == "" then
            genre_ref := "N/A"
        end
        
        url_ref = ref url
        if url == "" then
            url_ref := "N/A"
        end
        output.shoutcast = output.shoutcast(id = "shoutcast_stream_#{stream}",
                    host = host,
                    port = port,
                    password = pass,
                    fallible = true,
                    url = !url_ref,
                    genre = !genre_ref,
                    name = !description_ref,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect)
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
    list.iter(fun (rid) -> ignore(server.execute("queue.remove #{rid}")), l)

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

dyn_out = output.icecast(%wav,
                     host="localhost",
                     port=8999,
                     password="hackme",
                     mount="test-harbor",
                     fallible=true)

# Function to create a playlist source and output it.
def create_dynamic_source(uri) =
  # The playlist source 
  s = input.http(uri)

  # The output
  active_dyn_out = dyn_out(s)

  # We register both source and output 
  # in the list of sources
  dyn_sources := 
      list.append( [(uri,s),(uri,active_dyn_out)],
                    !dyn_sources )
  "Done!"
end

# A function to destroy a dynamic source
def destroy_dynamic_source(uri) = 
  # We need to find the source in the list,
  # remove it and destroy it. Currently, the language
  # lacks some nice operators for that so we do it
  # the functional way

  # This function is executed on every item in the list
  # of dynamic sources
  def parse_list(ret, current_element) = 
    # ret is of the form: (matching_sources, remaining_sources)
    # We extract those two:
    matching_sources = fst(ret)
    remaining_sources = snd(ret)

    # current_element is of the form: ("uri", source) so 
    # we check the first element
    current_uri = fst(current_element)
    if current_uri == uri then
      # In this case, we add the source to the list of
      # matched sources
      (list.append( [snd(current_element)], 
                     matching_sources),
       remaining_sources)
    else
      # In this case, we put the element in the list of remaining
      # sources
      (matching_sources,
       list.append([current_element], 
                    remaining_sources))
    end
  end
    
  # Now we execute the function:
  result = list.fold(parse_list, ([], []), !dyn_sources)
  matching_sources = fst(result)
  remaining_sources = snd(result)

  # We store the remaining sources in dyn_sources
  dyn_sources := remaining_sources

  # If no source matched, we return an error
  if list.length(matching_sources) == 0 then
    "Error: no matching sources!"
  else
    # We stop all sources
    list.iter(source.shutdown, matching_sources)
    # And return
    "Done!"
  end
end


