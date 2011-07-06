%include "library/pervasives.liq"
%include "/etc/airtime/liquidsoap.cfg"

set("log.file.path", log_file)
set("log.stdout", true)
set("server.telnet", true)
set("server.telnet.port", 1234)

queue = request.queue(id="queue", length=0.5)
queue = cue_cut(queue)
queue = audio_to_stereo(queue)

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
    ignore(out(s))
end

if output_icecast_mp3 then
    ignore(output.icecast(%mp3,
            host = icecast_host,
            port = icecast_port,
            password = icecast_pass,
            mount = mount_point_mp3,
            fallible = true,
            restart = true,
            restart_delay = 5,
            url = icecast_url,
            description = icecast_description,
            genre = icecast_genre,
            s)
        )
end

if output_icecast_vorbis then
    if output_icecast_vorbis_metadata then
        ignore(output.icecast(%vorbis,
                host = icecast_host,
                port = icecast_port,
                password = icecast_pass,
                mount = mount_point_vorbis,
                fallible = true,
                restart = true,
                restart_delay = 5,
                url = icecast_url,
                description = icecast_description,
                genre = icecast_genre,
                s)
            )
    else
        #remove metadata from ogg source and merge tracks to fix bug
        #with vlc and mplayer disconnecting at the end of every track
        s = add(normalize=false, [amplify(0.00001, noise()),s])
        ignore(output.icecast(%vorbis,
                host = icecast_host,
                port = icecast_port,
                password = icecast_pass,
                mount = mount_point_vorbis,
                fallible = true,
                restart = true,
                restart_delay = 5,
                url = icecast_url,
                description = icecast_description,
                genre = icecast_genre,
                s)
            )
    end
end

if output_shoutcast then
    ignore(output.shoutcast(%mp3,
            host = shoutcast_host,
            port = shoutcast_port,
            password = shoutcast_pass,
            fallible = true,
            restart = true,
            restart_delay = 5,
            url = shoutcast_url,
            genre = shoutcast_genre,
            s)
        )
end

