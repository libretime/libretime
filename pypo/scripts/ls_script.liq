######################################
# main liquidsoap development script #
######################################
# author Jonas Ohrstrom <jonas@digris.ch>

########################################
# include configuration                #
########################################

%include "library/pervasives.liq"
%include "ls_config.liq"
%include "library.liq"
%include "include_dynamic_vars.liq"
%include "include_notify.liq"

silence_threshold = -50.
silence_time = 3.

# log
set("log.file.path",log_file)
set("log.stdout", true)
set("log.level",log_level)

# telnet server
set("server.telnet", true)

######################################
# some functions needed              #
######################################
def fcross(a,b) =
    add(normalize=false,[b,a])
end

######################################
# live recording functions
######################################
def live_start() =
  log("got live source")
  ignore(execute("archives.start"))
end

def live_stop() =
  log("live source has gone")
  ignore(execute("archives.stop"))
end


#######################################################################
# File locations / sources
#######################################################################
silence = single("/opt/pypo/files/basic/silence.mp3")
jingles_cc = playlist("/opt/pypo/files/jingles/jcc")
fallback_airtime = playlist("/opt/pypo/files/basic/silence-playlist.lsp")
fallback_airtime = audio_to_stereo(fallback_airtime)


# default
default = silence

special = request.queue(id="special")


#######################################################################
# Includeing two A/B Queues, daypart & scheduler
# this will give us the sources 'daypart' & 'scheduler' 
#######################################################################
%include "include_daypart.liq"
%include "include_scheduler.liq"

source = fallback(track_sensitive=false,transitions=[dp_to_scheduler],[strip_blank(threshold=silence_threshold,length=silence_time,scheduler),daypart])

%include "include_live_in.liq"

live = fallback(track_sensitive=false,[strip_blank(threshold=silence_threshold,length=silence_time,live),fallback_airtime])
live = switch(track_sensitive=false, [({!live_active},live)])

source = fallback(track_sensitive=false,transitions=[to_live_s, to_scheduler_s],[live, source]) 

# handle the annotate fades
faded = fade.in(type="log", fade.out(type="log", source))

# add up with a crossfade function (defined above)
source = cross(fcross,faded)

# track start detection (for notifications)
source = on_metadata(notify, source)
#source = on_track(notify, source)

# special to mix with final source
source = smooth_add(normal=source,special=special)


#####################################
# Stream Output
#####################################
# finally the output | mp3
#clock(id="clock_icecast",
#  output.icecast(%mp3,
#   host = icecast_host, port = icecast_port,
#    password = icecast_pass, mount = mount_scheduler,
#    fallible = true,
#    restart = true,
#    restart_delay = 5,
#    buffer(source)))

out(source)
