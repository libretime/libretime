#######################################################################
# Dynamic variables
#######################################################################

playlist_id = ref '0'
user_id = ref '0'
transmission_id = ref '0'
playlist_type = ref '0'
pypo_data = ref '0'

def set_pypo_data(s)
  pypo_data := s
end

def set_user_id(s)
  user_id := s
end


def set_playlist_id(s)
  playlist_id := s
end


def set_transmission_id(s)
  transmission_id := s
end

def set_playlist_type(s)
  playlist_type := s
end

server.register(namespace="vars", "user_id", fun (s) ->  begin set_user_id(s) "Done!" end)
server.register(namespace="vars", "playlist_id", fun (s) ->  begin set_playlist_id(s) "Done!" end)
server.register(namespace="vars", "transmission_id", fun (s) ->  begin set_transmission_id(s) "Done!" end)
server.register(namespace="vars", "playlist_type", fun (s) ->  begin set_playlist_type(s) "Done!" end)
server.register(namespace="vars", "pypo_data", fun (s) ->  begin set_pypo_data(s) "Done!" end)