#######################################################################
# Dynamic variables
#######################################################################

playlist_type = ref '0'
pypo_data = ref '0'

def set_playlist_type(s)
  playlist_type := s
end

def set_pypo_data(s)
  pypo_data := s
end

server.register(namespace="vars", "playlist_type", fun (s) ->  begin set_playlist_type(s) "Done!" end)
server.register(namespace="vars", "pypo_data", fun (s) ->  begin set_pypo_data(s) "Done!" end)