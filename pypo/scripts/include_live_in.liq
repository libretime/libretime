#######################################################################
# Live input - From external icecast server
#######################################################################
live_in = input.http(id="live_in",autostart=false,buffer=.1, max=12.,couchcaster_list)
live_in = buffer(id="buffer_live_in",buffer=.1,fallible=true,live_in)
live_in = mksafe(live_in)

live_active = ref false
def live_switch(i)
  print(i)
  if i=='1' then live_active:=true else live_active:=false end
  print(live_active)
  "Done"
end
server.register(namespace="live","active", live_switch)

live = switch(track_sensitive=false, [({!live_active},live_in)])

to_live_s = to_live(jingles_cc)
to_scheduler_s = to_scheduler()
