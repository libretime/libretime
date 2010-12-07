#######################################################################
# Dynamic variables
#######################################################################

pypo_data = ref '0'

def set_pypo_data(s)
  pypo_data := s
end

server.register(namespace="vars", "pypo_data", fun (s) ->  begin set_pypo_data(s) "Done!" end)