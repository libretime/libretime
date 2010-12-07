# Define a transition that fades out the
# old source, adds a single, and then 
# plays the new source
def to_live(jingle,old,new) = 
  # Fade out old source
  old = fade.final(old)
  # Supperpose the jingle
  s = add([jingle,old])
  # Compose this in sequence with
  # the new source
  sequence([s,new])
end

def to_scheduler(old,new) = 
  # We skip the file
  # currently in new
  # in order to being with
  # a fresh file
  # source.skip(new) 
  sequence([old,new])
end

# A transition when switching back to files:
def to_file(old,new) =
  # We skip the file
  # currently in new
  # in order to being with
  # a fresh file
  # source.skip(new) 
  sequence([old,new])
end


def dp_to_scheduler(old,new) = 
  old = fade.final(type='log',duration=2.1,old)
  sequence([old,new])
end