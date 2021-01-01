%include "/etc/airtime/liquidsoap.cfg"

set("log.file.path", log_file)
set("server.telnet", true)
set("server.telnet.port", 1234)
# set("init.daemon.pidfile.path", "/var/run/airtime/airtime-liquidsoap.pid")


#Dynamic source list
#dyn_sources = ref []
webstream_enabled = ref false

time = ref string_of(gettimeofday())

#live stream setup
set("harbor.bind_addr", "0.0.0.0")

current_dyn_id = ref '-1'

pypo_data = ref '0'
stream_metadata_type = ref 0
default_dj_fade = ref 0.
station_name = ref ''
show_name = ref ''

dynamic_metadata_callback = ref fun (s) -> begin () end

s1_connected = ref ''
s2_connected = ref ''
s3_connected = ref ''
s4_connected = ref ''
s1_namespace = ref ''
s2_namespace = ref ''
s3_namespace = ref ''
just_switched = ref false

%include "ls_lib.liq"

sources = ref []
source_id = ref 0

def check_version(~version=liquidsoap.version, major, minor) =
    v = list.map(int_of_string, string.split(separator="\.", version))
    list.nth(v,0) > major or list.nth(v,0) == major and list.nth(v,1) >= minor
end

# cue cut fix for liquidsoap <1.2.2
#
# This was most likely broken on 1.1.1 (debian) as well.
# 
# adapted from https://github.com/savonet/liquidsoap/issues/390#issuecomment-277562081
#
def fix_cue_in(~cue_in_metadata='liq_cue_in', m) =
    # 0.04 might need to be adjusted according to your frame size
    if float_of_string(m[cue_in_metadata]) < 0.04 then
        [(cue_in_metadata, "0")]
    else
        []
    end
end

def create_source()
    l = request.equeue(id="s#{!source_id}", length=0.5)

    l = audio_to_stereo(id="queue_src", l)

    l = if not check_version(1, 3) then
        map_metadata(fix_cue_in, l)
    else
        l
    end
    l = cue_cut(l)
    l = amplify(1., override="replay_gain", l)

    # the crossfade function controls fade in/out
    l = crossfade_airtime(l)

    l = on_metadata(notify_queue, l)

    sources := list.append([l], !sources)
    server.register(namespace="queues",
                "s#{!source_id}_skip",
                fun (s) -> begin log("queues.s#{!source_id}_skip") 
                    clear_queue(l) 
                    "Done" 
                end)
    source_id := !source_id + 1
end

create_source()
create_source()
create_source()
create_source()

create_source()
create_source()
create_source()
create_source()

queue = add(!sources, normalize=false)
pair = insert_metadata(queue)
dynamic_metadata_callback := fst(pair)
queue = snd(pair)

output.dummy(fallible=true, queue)

http = input.http_restart(id="http")
http = cross_http(http_input_id="http",http)
output.dummy(fallible=true, http)
stream_queue = http_fallback(http_input_id="http", http=http, default=queue)
stream_queue = map_metadata(update=false, append_title, stream_queue)

ignore(output.dummy(stream_queue, fallible=true))

server.register(namespace="vars",
                "pypo_data",
                fun (s) -> begin log("vars.pypo_data") pypo_data := s "Done" end)
server.register(namespace="vars",
                "stream_metadata_type",
                fun (s) -> begin log("vars.stream_metadata_type") stream_metadata_type := int_of_string(s) s end)
server.register(namespace="vars",
                "show_name",
                fun (s) -> begin log("vars.show_name") show_name := s s end)
server.register(namespace="vars",
                "station_name",
                fun (s) -> begin log("vars.station_name") station_name := s s end)
server.register(namespace="vars",
                "bootup_time",
                fun (s) -> begin log("vars.bootup_time") time := s s end)
server.register(namespace="streams",
                "connection_status",
                fun (s) -> begin log("streams.connection_status") "1:#{!s1_connected},2:#{!s2_connected},3:#{!s3_connected},4:#{!s4_connected}" end)
server.register(namespace="vars",
                "default_dj_fade",
                fun (s) -> begin log("vars.default_dj_fade") default_dj_fade := float_of_string(s) s end)

server.register(namespace="dynamic_source",
                description="Enable webstream output",
                usage='start',
                "output_start",
                fun (s) -> begin log("dynamic_source.output_start")
                    notify([("schedule_table_id", !current_dyn_id)])
                    webstream_enabled := true "enabled" end)
server.register(namespace="dynamic_source",
                description="Enable webstream output",
                usage='stop',
                "output_stop",
                fun (s) -> begin log("dynamic_source.output_stop") webstream_enabled := false "disabled" end)

server.register(namespace="dynamic_source",
                description="Set the streams cc_schedule row id",
                usage="id <id>",
                "id",
                fun (s) -> begin log("dynamic_source.id") set_dynamic_source_id(s) end)

server.register(namespace="dynamic_source",
                description="Get the streams cc_schedule row id",
                usage="get_id",
                "get_id",
                fun (s) -> begin log("dynamic_source.get_id") get_dynamic_source_id() end)

#server.register(namespace="dynamic_source",
#                description="Start a new dynamic source.",
#                usage="start <uri>",
#                "read_start",
#                fun (uri) -> begin log("dynamic_source.read_start") begin_stream_read(uri) end)
#server.register(namespace="dynamic_source",
#                description="Stop a dynamic source.",
#                usage="stop <id>",
#                "read_stop",
#                fun (s) -> begin log("dynamic_source.read_stop") stop_stream_read(s) end)

#server.register(namespace="dynamic_source",
#                description="Stop a dynamic source.",
#                usage="stop <id>",
#                "read_stop_all",
#                fun (s) -> begin log("dynamic_source.read_stop") destroy_dynamic_source_all() end)

default = amplify(id="silence_src", 0.00001, noise())
ref_off_air_meta = ref off_air_meta
if !ref_off_air_meta == "" then
    ref_off_air_meta := "LibreTime - offline"
end
default = rewrite_metadata([("title", !ref_off_air_meta)], default)
ignore(output.dummy(default, fallible=true))

master_dj_enabled = ref false
live_dj_enabled = ref false
scheduled_play_enabled = ref false

def make_master_dj_available()
    master_dj_enabled := true
end

def make_master_dj_unavailable()
    master_dj_enabled := false
end

def make_live_dj_available()
    live_dj_enabled := true
end

def make_live_dj_unavailable()
    live_dj_enabled := false
end

def make_scheduled_play_available()
    scheduled_play_enabled := true
    just_switched := true
end

def make_scheduled_play_unavailable()
    scheduled_play_enabled := false
end

def update_source_status(sourcename, status) =
    command = "timeout --signal=KILL 45 pyponotify --source-name=#{sourcename} --source-status=#{status} &"
    system(command)
    log(command)
end

def live_dj_connect(header) =
    update_source_status("live_dj", true)
end

def live_dj_disconnect() =
    update_source_status("live_dj", false)
end

def master_dj_connect(header) =
    update_source_status("master_dj", true)
end

def master_dj_disconnect() =
    update_source_status("master_dj", false)
end

#auth function for live stream
def check_master_dj_client(user,password) =
    log("master connected")
    #get the output of the php script
    ret = get_process_lines("python3 #{auth_path} --master #{user} #{password}")
    #ret has now the value of the live client (dj1,dj2, or djx), or "ERROR"/"unknown" ...
    ret = list.hd(ret)

    #return true to let the client transmit data, or false to tell harbor to decline
    ret == "True"
end

def check_dj_client(user,password) =
    log("live dj connected")
    #get the output of the php script
    ret = get_process_lines("python3 #{auth_path} --dj #{user} #{password}")
    #ret has now the value of the live client (dj1,dj2, or djx), or "ERROR"/"unknown" ...
    hd = list.hd(ret)
    log("Live DJ authenticated: #{hd}")
    hd == "True"
end

s = switch(id="schedule_noise_switch",
            track_sensitive=false,
            transitions=[transition_default, transition],
            [({!scheduled_play_enabled}, stream_queue), ({true}, default)]
    )

s = if dj_live_stream_port != 0 and dj_live_stream_mp != "" then
    dj_live =
        audio_to_stereo(
            input.harbor(id="live_dj_harbor",
                dj_live_stream_mp,
                port=dj_live_stream_port,
                auth=check_dj_client,
                max=40.,
                on_connect=live_dj_connect,
                on_disconnect=live_dj_disconnect))

    ignore(output.dummy(dj_live, fallible=true))

    switch(id="show_schedule_noise_switch",
            track_sensitive=false,
            transitions=[transition, transition],
            [({!live_dj_enabled}, dj_live), ({true}, s)]
        )
else
    s
end

s = if master_live_stream_port != 0 and master_live_stream_mp != "" then
    master_dj =
        audio_to_stereo(
            input.harbor(id="master_harbor",
                master_live_stream_mp,
                port=master_live_stream_port,
                auth=check_master_dj_client,
                max=40.,
                on_connect=master_dj_connect,
                on_disconnect=master_dj_disconnect))

    ignore(output.dummy(master_dj, fallible=true))

    switch(id="master_show_schedule_noise_switch",
            track_sensitive=false,
            transitions=[transition, transition],
            [({!master_dj_enabled}, master_dj), ({true}, s)]
        )
else
    s
end


# Attach a skip command to the source s:
#add_skip_command(s)

server.register(namespace="streams",
    description="Stop Master DJ source.",
    usage="master_dj_stop",
    "master_dj_stop",
    fun (s) -> begin log("streams.master_dj_stop") make_master_dj_unavailable() "Done." end)
server.register(namespace="streams",
    description="Start Master DJ source.",
    usage="master_dj_start",
    "master_dj_start",
    fun (s) -> begin log("streams.master_dj_start") make_master_dj_available() "Done." end)
server.register(namespace="streams",
    description="Stop Live DJ source.",
    usage="live_dj_stop",
    "live_dj_stop",
    fun (s) -> begin log("streams.live_dj_stop") make_live_dj_unavailable() "Done." end)
server.register(namespace="streams",
    description="Start Live DJ source.",
    usage="live_dj_start",
    "live_dj_start",
    fun (s) -> begin log("streams.live_dj_start") make_live_dj_available() "Done." end)
server.register(namespace="streams",
    description="Stop Scheduled Play source.",
    usage="scheduled_play_stop",
    "scheduled_play_stop",
    fun (s) -> begin log("streams.scheduled_play_stop") make_scheduled_play_unavailable() "Done." end)
server.register(namespace="streams",
    description="Start Scheduled Play source.",
    usage="scheduled_play_start",
    "scheduled_play_start",
    fun (s) -> begin log("streams.scheduled_play_start") make_scheduled_play_available() "Done." end)

if output_sound_device then
    success = ref false

    log(output_sound_device_type)

    %ifdef output.alsa
	if output_sound_device_type == "ALSA" then
		ignore(output.alsa(s))
        success := true
	end
	%endif

	%ifdef output.ao
	if output_sound_device_type == "AO" then
		ignore(output.ao(s))
        success := true
	end
	%endif

	%ifdef output.oss
	if output_sound_device_type == "OSS" then
        ignore(output.oss(s))
        success := true
	end
	%endif

	%ifdef output.portaudio
	if output_sound_device_type == "Portaudio" then
        ignore(output.portaudio(s))
        success := true
	end
	%endif

	%ifdef output.pulseaudio
	if output_sound_device_type == "Pulseaudio" then
        ignore(output.pulseaudio(s))
        success := true
	end
	%endif

    if (!success == false) then
        ignore(output.prefered(s))
	end

end

if s1_enable == true then
    if s1_output == 'shoutcast' then
        s1_namespace := "shoutcast_stream_1"
    else
        s1_namespace := s1_mount
    end
    server.register(namespace=!s1_namespace, "connected", fun (s) -> begin log("#{!s1_namespace}.connected") !s1_connected end)
    output_to(s1_output, s1_type, s1_bitrate, s1_host, s1_port, s1_pass,
                s1_mount, s1_url, s1_description, s1_genre, s1_user, s, "1",
                s1_connected, s1_name, s1_channels)
end

if s2_enable == true then
    if s2_output == 'shoutcast' then
        s2_namespace := "shoutcast_stream_2"
    else
        s2_namespace := s2_mount
    end
    server.register(namespace=!s2_namespace, "connected", fun (s) -> begin log("#{!s2_namespace}.connected") !s2_connected end)
    output_to(s2_output, s2_type, s2_bitrate, s2_host, s2_port, s2_pass,
                s2_mount, s2_url, s2_description, s2_genre, s2_user, s, "2",
                s2_connected, s2_name, s2_channels)

end

if s3_enable == true then
    if s3_output == 'shoutcast' then
        s3_namespace := "shoutcast_stream_3"
    else
        s3_namespace := s3_mount
    end
    server.register(namespace=!s3_namespace, "connected", fun (s) -> begin log("#{!s3_namespace}.connected") !s3_connected end)
    output_to(s3_output, s3_type, s3_bitrate, s3_host, s3_port, s3_pass,
                s3_mount, s3_url, s3_description, s3_genre, s3_user, s, "3",
                s3_connected, s3_name, s3_channels)
end

s4_namespace = ref ''
if s4_enable == true then
    log("Stream 4 Enabled")
    if s4_output == 'shoutcast' then
        s4_namespace := "shoutcast_stream_4"
    else
        s4_namespace := s4_mount
    end
    server.register(namespace=!s4_namespace, "connected", fun (s) -> begin log("#{!s4_namespace}.connected") !s4_connected end)
    output_to(s4_output, s4_type, s4_bitrate, s4_host, s4_port, s4_pass,
                s4_mount, s4_url, s4_name, s4_genre, s4_user, s, "4",
                s4_connected, s4_description, s4_channels)
end


command = "timeout --signal=KILL 45 pyponotify --liquidsoap-started &"
log(command)
system(command)
