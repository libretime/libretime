%include "library/pervasives.liq"
%include "ls_config.liq"

set("log.file.path", log_file)
set("log.stdout", true)
set("server.telnet", true)

queue = request.queue(conservative=true,length=600.,id="queue")
queue = audio_to_stereo(queue)

pypo_data = ref '0'

def notify(m)
  system("./notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']}")
  print("./notify.sh --data='#{!pypo_data}' --media-id=#{m['schedule_table_id']}")
end

def crossfade(s)
  s = fade.in(type="log", s)
  s = fade.out(type="log", s)
  fader = fun (a,b) -> add(normalize=false,[b,a])
  cross(fader,s)
end

# Add a skip function to a source
# when it does not have one
# by default
def add_skip_command(s) =
 # A command to skip
 def skip(_) =
   source.skip(s)
   "Done!"
 end
 # Register the command:
 server.register(namespace="source",
                 usage="skip",
                 description="Skip the current song.",
                 "skip",skip)
end

server.register(namespace="vars", "pypo_data", fun (s) ->  begin pypo_data := s "Done" end)

default = single("/opt/pypo/files/basic/silence.mp3")
default = rewrite_metadata([("artist","Airtime"), ("title", "offline")],default)

s = fallback(track_sensitive=false, [queue, default])

s = on_metadata(notify, s)
s = crossfade(s)

# Attach a skip command to the source s:
add_skip_command(s)


if output_sound_device then
	out_device = out(s)
end

if output_icecast_mp3 then
	out_mp3 = output.icecast(%mp3,
			host = icecast_host, port = icecast_port,
			password = icecast_pass, mount = mount_point_mp3,
			fallible = true,
			restart = true,
			restart_delay = 5,
			url = icecast_url,
			description = icecast_description,
			genre = icecast_genre,
			s)
end

if output_icecast_vorbis then
	out_vorbis = output.icecast(%vorbis,
			host = icecast_host, port = icecast_port,
			password = icecast_pass, mount = mount_point_vorbis,
			fallible = true,
			restart = true,
			restart_delay = 5,
			url = icecast_url,
			description = icecast_description,
			genre = icecast_genre,
			s)
end
