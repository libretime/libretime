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
