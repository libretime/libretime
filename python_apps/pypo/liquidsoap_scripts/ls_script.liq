%include "library/pervasives.liq"
%include "/etc/airtime/liquidsoap.cfg"

set("log.file.path", log_file)
set("log.stdout", true)
set("server.telnet", true)
set("server.telnet.port", 1234)

queue = audio_to_stereo(request.queue(id="queue", length=0.5))
queue = cue_cut(queue)

pypo_data = ref '0'
web_stream_enabled = ref false
stream_metadata_type = ref 0
station_name = ref ''
show_name = ref ''

%include "ls_lib.liq"

server.register(namespace="vars", "pypo_data", fun (s) -> begin pypo_data := s "Done" end)
server.register(namespace="vars", "web_stream_enabled", fun (s) -> begin web_stream_enabled := (s == "true") string_of(!web_stream_enabled) end)
server.register(namespace="vars", "stream_metadata_type", fun (s) -> begin stream_metadata_type := int_of_string(s) s end)
server.register(namespace="vars", "show_name", fun (s) -> begin show_name := s s end)
server.register(namespace="vars", "station_name", fun (s) -> begin station_name := s s end)


default = amplify(0.00001, noise())
default = rewrite_metadata([("artist","Airtime"), ("title", "offline")],default)

s = fallback(track_sensitive=false, [queue, default])

s = on_metadata(notify, s)
s = crossfade(s)
# Attach a skip command to the source s:

#web_stream_source = input.http(id="web_stream", autostart = false, buffer=0.5, max=20., "")

#once the stream is started, give it a sink so that liquidsoap doesn't
#create buffer overflow warnings in the log file.
#output.dummy(fallible=true, web_stream_source)

#s = switch(track_sensitive = false,
#    transitions=[to_live,to_live],
#    [
#      ({ !web_stream_enabled }, web_stream_source),
#      ({ true }, s)
#    ]
#)

add_skip_command(s)
s = map_metadata(append_title, s)

if output_sound_device then
    ignore(output.alsa(s))
end

if s1_output != "disabled" then
    #output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, s)
    output_to(s1_output, s1_type, s1_bitrate, s1_host, s1_port, s1_pass, s1_mount, s1_url, s1_description, s1_genre, s1_user, s)
end

if s2_output != "disabled" then
    #output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, s)
    output_to(s2_output, s2_type, s2_bitrate, s2_host, s2_port, s2_pass, s2_mount, s2_url, s2_description, s2_genre, s2_user, s)
end

if s3_output != "disabled" then
    #output_to(output_type, type, bitrate, host, port, pass, mount_point, url, description, genre, s)
    output_to(s3_output, s3_type, s3_bitrate, s3_host, s3_port, s3_pass, s3_mount, s3_url, s3_description, s3_genre, s3_user, s)
end

ignore(output.dummy(blank()))
