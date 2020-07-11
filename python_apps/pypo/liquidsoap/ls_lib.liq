def notify(m)
  command = "timeout --signal=KILL 45 pyponotify --media-id=#{m['schedule_table_id']} &"
  log(command)
  system(command)
end

def notify_queue(m)
  f = !dynamic_metadata_callback
  ignore(f(m))
  notify(m)
end

def notify_stream(m)
  json_str = string.replace(pattern="\n",(fun (s) -> ""), json_of(m))
  #if a string has a single apostrophe in it, let's comment it out by ending the string before right before it
  #escaping the apostrophe, and then starting a new string right after it. This is why we use 3 apostrophes.
  json_str = string.replace(pattern="'",(fun (s) -> "'\''"), json_str)
  command = "timeout --signal=KILL 45 pyponotify --webstream='#{json_str}' --media-id=#{!current_dyn_id} &"
  
  if !current_dyn_id != "-1" then
    log(command)
    system(command)
  end
end

# A function applied to each metadata chunk
def append_title(m) =  
  log("Using stream_format #{!stream_metadata_type}")

  if list.mem_assoc("mapped", m) then
    #protection against applying this function twice. It shouldn't be happening
    #and bug file with Liquidsoap.
    m
  else
      if !stream_metadata_type == 1 then
        [("title", "#{!show_name} - #{m['artist']} - #{m['title']}"), ("mapped", "true")]
      elsif !stream_metadata_type == 2 then
        [("title", "#{!station_name} - #{!show_name}"), ("mapped", "true")]
      else
        if "#{m['artist']}" == "" then
          [("title", "#{m['title']}"), ("mapped", "true")]
        else
          [("title", "#{m['artist']} - #{m['title']}"), ("mapped", "true")]
        end
     end
  end
end

def crossfade_airtime(s)
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


def output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, user, s, stream, connected, name, channels) =
    source = ref s
    def on_error(msg)
        connected := "false"
        command = "timeout --signal=KILL 45 pyponotify --error='#{msg}' --stream-id=#{stream} --time=#{!time} &"
        system(command)
        log(command)
        5.
    end
    def on_connect()
        connected := "true"
        command = "timeout --signal=KILL 45 pyponotify --connect --stream-id=#{stream} --time=#{!time} &"
        system(command)
        log(command)
    end

    stereo = (channels == "stereo")

    if output_type == "icecast" then
        user_ref = ref user
        if user == "" then
            user_ref := "source"
        end
        output_mono = output.icecast(host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    url = url,
                    description = description,
                    name = name,
                    genre = genre,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect)

        output_stereo = output.icecast(host = host,
                    port = port,
                    password = pass,
                    mount = mount_point,
                    fallible = true,
                    url = url,
                    description = description,
                    name = name,
                    genre = genre,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect)
        if type == "mp3" then
            %include "mp3.liq"
        end
        if type == "ogg" then
            %include "ogg.liq"
        end

        %ifencoder %opus
        if type == "opus" then
            %include "opus.liq"
        end
        %endif

        %ifencoder %fdkaac
        if type == "aac" then
            %include "fdkaac.liq"
        end
        %endif
    else
        user_ref = ref user
        if user == "" then
            user_ref := "source"
        end

        output_mono = output.shoutcast(id = "shoutcast_stream_#{stream}",
                    host = host,
                    port = port,
                    password = pass,
                    fallible = true,
                    url = url,
                    genre = genre,
                    name = description,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect)

        output_stereo = output.shoutcast(id = "shoutcast_stream_#{stream}",
                    host = host,
                    port = port,
                    password = pass,
                    fallible = true,
                    url = url,
                    genre = genre,
                    name = description,
                    user = !user_ref,
                    on_error = on_error,
                    on_connect = on_connect)

        if type == "mp3" then
            %include "mp3.liq"
        end

        %ifencoder %fdkaac
        if type == "aac" then
            %include "fdkaac.liq"
        end
        %endif
    end
end

# Add a skip function to a source
# when it does not have one
# by default
#def add_skip_command(s)
# # A command to skip
#  def skip(_)
#    # get playing (active) queue and flush it
#    l = list.hd(server.execute("queue.secondary_queue"))
#    l = string.split(separator=" ",l)
#    list.iter(fun (rid) -> ignore(server.execute("queue.remove #{rid}")), l)
#
#    l = list.hd(server.execute("queue.primary_queue"))
#    l = string.split(separator=" ", l)
#    if list.length(l) > 0 then
#      source.skip(s)
#      "Skipped"
#    else
#      "Not skipped"
#    end
#  end
# # Register the command:
# server.register(namespace="source",
#                 usage="skip",
#                 description="Skip the current song.",
#                 "skip",fun(s) -> begin log("source.skip") skip(s) end)
#end

def clear_queue(s)
    source.skip(s)
end

def set_dynamic_source_id(id) =
    current_dyn_id := id 
    string_of(!current_dyn_id)
end

def get_dynamic_source_id() =
    string_of(!current_dyn_id)
end

#cc-4633


# NOTE
# A few values are hardcoded and may be dependent:
#  - the delay in gracetime is linked with the buffer duration of input.http
#    (delay should be a bit less than buffer)
#  - crossing duration should be less than buffer length
#    (at best, a higher duration will be ineffective)

# HTTP input with "restart" command that waits for "stop" to be effected
# before "start" command is issued. Optionally it takes a new URL to play,
# which makes it a convenient replacement for "url".
# In the future, this may become a core feature of the HTTP input.
# TODO If we stop and restart quickly several times in a row,
#   the data bursts accumulate and create buffer overflow.
#   Flushing the buffer on restart could be a good idea, but
#   it would also create an interruptions while the buffer is
#   refilling... on the other hand, this would avoid having to
#   fade using both cross() and switch().
def input.http_restart(~id,~initial_url="http://dummy/url")

  source = audio_to_stereo(input.http(buffer=5.,max=15.,id=id,autostart=false,initial_url))

  def stopped()
    "stopped" == list.hd(server.execute("#{id}.status"), default="")
  end

  server.register(namespace=id,
                  "restart",
                  usage="restart [url]",
                  fun (url) -> begin
                    if url != "" then
                      log(string_of(server.execute("#{id}.url #{url}")))
                    end
                    log(string_of(server.execute("#{id}.stop")))
                    add_timeout(0.5,
                      { if stopped() then
                          log(string_of(server.execute("#{id}.start"))) ;
                          (-1.)
                        else 0.5 end})
                    "OK"
                  end)

  # Dummy output should be useless if HTTP stream is meant
  # to be listened to immediately. Otherwise, apply it.
  #
  # output.dummy(fallible=true,source)

  source

end

# Transitions between URL changes in HTTP streams.
def cross_http(~debug=true,~http_input_id,source)

  id = http_input_id
  last_url = ref ""
  change = ref false

  def on_m(m)
    notify_stream(m)
    changed = m["source_url"] != !last_url
    log("URL now #{m['source_url']} (change: #{changed})")
    if changed then
      if !last_url != "" then change := true end
      last_url := m["source_url"]
    end
  end

  # We use both metadata and status to know about the current URL.
  # Using only metadata may be more precise is crazy corner cases,
  # but it's also asking too much: the metadata may not pass through
  # before the crosser is instantiated.
  # Using only status in crosser misses some info, eg. on first URL.
  source = on_metadata(on_m,source)

  cross_d = 3.

  def crosser(a,b)
    url = list.hd(server.execute('#{id}.url'), default="")
    status = list.hd(server.execute('#{id}.status'))
    on_m([("source_url",url)])
    if debug then
      log("New track inside HTTP stream")
      log("  status: #{status}")
      log("  need to cross: #{!change}")
      log("  remaining #{source.remaining(a)} sec before, \
             #{source.remaining(b)} sec after")
    end
    if !change then
      change := false
      # In principle one should avoid crossing on a live stream
      # it'd be okay to do it here (eg. use add instead of sequence)
      # because it's only once per URL, but be cautious.
      sequence([fade.out(duration=cross_d,a),fade.in(b)])
    else
      # This is done on tracks inside a single stream.
      # Do NOT cross here or you'll gradually empty the buffer!
      sequence([a,b])
    end
  end

  # Setting conservative=true would mess with the delayed switch below
  cross(duration=cross_d,conservative=false,crosser,source)

end

# Custom fallback between http and default source with fading of
# beginning and end of HTTP stream.
# It does not take potential URL changes into account, as long as
# they do not interrupt streaming (thanks to the HTTP buffer).
def http_fallback(~http_input_id,~http,~default)

  id = http_input_id

  # We use a custom switching predicate to trigger switching (and thus,
  # transitions) before the end of a track (rather, end of HTTP stream).
  # It is complexified because we don't want to trigger switching when
  # HTTP disconnects for just an instant, when changing URL: for that
  # we use gracetime below.

  def gracetime(~delay=3.,f)
    last_true = ref 0.
    { if f() then
        last_true := gettimeofday()
        true
      else
        gettimeofday() < !last_true+delay
      end }
  end

  def connected()
    status = list.hd(server.execute("#{id}.status"), default="")
    not(list.mem(status,["polling","stopped"]))
  end
  connected = gracetime(connected)

  def to_live(a,b) =
    log("TRANSITION to live")
    add(normalize=false,
        [fade.initial(b),fade.final(a)])
  end
  def to_static(a,b) =
    log("TRANSITION to static")
    sequence([fade.out(a),fade.initial(b)])
  end

  switch(
    track_sensitive=false,
    transitions=[to_live,to_static],
    [(# make sure it is connected, and not buffering
      {connected() and source.is_ready(http) and !webstream_enabled}, http),
     ({true},default)])

end
