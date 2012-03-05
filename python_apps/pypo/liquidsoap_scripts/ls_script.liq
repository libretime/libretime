%include "library/pervasives.liq"
%include "/etc/airtime/liquidsoap.cfg"

set("log.file.path", log_file)
set("log.stdout", true)
set("server.telnet", true)
set("server.telnet.port", 1234)

time = ref string_of(gettimeofday())

queue = audio_to_stereo(request.queue(id="queue", length=0.5))
queue = cue_cut(queue)

pypo_data = ref '0'
web_stream_enabled = ref false
stream_metadata_type = ref 0
station_name = ref ''
show_name = ref ''

s1_connected = ref ''
s2_connected = ref ''
s3_connected = ref ''
s1_namespace = ref ''
s2_namespace = ref ''
s3_namespace = ref ''

%include "ls_lib.liq"

server.register(namespace="vars", "pypo_data", fun (s) -> begin pypo_data := s "Done" end)
server.register(namespace="vars", "web_stream_enabled", fun (s) -> begin web_stream_enabled := (s == "true") string_of(!web_stream_enabled) end)
server.register(namespace="vars", "stream_metadata_type", fun (s) -> begin stream_metadata_type := int_of_string(s) s end)
server.register(namespace="vars", "show_name", fun (s) -> begin show_name := s s end)
server.register(namespace="vars", "station_name", fun (s) -> begin station_name := s s end)
server.register(namespace="vars", "bootup_time", fun (s) -> begin time := s s end)
server.register(namespace="streams", "connection_status", fun (s) -> begin "1:#{!s1_connected},2:#{!s2_connected},3:#{!s3_connected}" end)


default = amplify(0.00001, noise())
default = rewrite_metadata([("artist","Airtime"), ("title", "offline")],default)

#live stream setup
set("harbor.bind_addr", "0.0.0.0")

#auth function for live stream
def check_master_dj_client(user,password) =
        #get the output of the php script
        ret = get_process_lines("python /usr/lib/airtime/pypo/bin/liquidsoap_scripts/liquidsoap_auth.py --master #{user} #{password}")
        #ret has now the value of the live client (dj1,dj2, or djx), or "ERROR"/"unknown" ...
        ret = list.hd(ret)
        #return true to let the client transmit data, or false to tell harbor to decline
        if (ret == "True") then
            true
        else
            false
        end
end

def check_dj_client(user,password) =
        #get the output of the php script
        ret = get_process_lines("python /usr/lib/airtime/pypo/bin/liquidsoap_scripts/liquidsoap_auth.py --dj #{user} #{password}")
        #ret has now the value of the live client (dj1,dj2, or djx), or "ERROR"/"unknown" ...
        ret = list.hd(ret)
        #return true to let the client transmit data, or false to tell harbor to decline
        if (ret == "True") then
            true
        else
            false
        end
end

#master_dj = ref default
#dj_live = ref default

def append_master_dj_input(master_harbor_input_port, master_harbor_input_mount_point, s) =
    if master_harbor_input_port != 0 and master_harbor_input_mount_point != "" then
        master_dj = input.harbor(master_harbor_input_mount_point, port=master_harbor_input_port, auth=check_master_dj_client, buffer=0.5,max=15.)
        
        ignore(output.dummy(master_dj, fallible=true))
        fallback(track_sensitive=false, [master_dj, s])
    else
        s
    end
end

def append_dj_input(dj_harbor_input_port, dj_harbor_input_mount_point, s) =
    if dj_harbor_input_port != 0 and dj_harbor_input_mount_point != "" then
        dj_live = input.harbor(dj_harbor_input_mount_point, port=dj_harbor_input_port, auth=check_dj_client, buffer=0.5,max=15.)
        
        ignore(output.dummy(dj_live, fallible=true))
        fallback(track_sensitive=false, [dj_live, s])
    else
        s
    end
end

s = fallback(track_sensitive=false, [queue, default])
s = on_metadata(notify, s)
s = map_metadata(append_title, s)
s = append_dj_input(dj_live_stream_port, dj_live_stream_mp, s)
s = append_master_dj_input(master_live_stream_port, master_live_stream_mp, s)


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
    server.register(namespace=!s1_namespace, "connected", fun (s) -> begin !s1_connected end)
    output_to(s1_output, s1_type, s1_bitrate, s1_host, s1_port, s1_pass, s1_mount, s1_url, s1_description, s1_genre, s1_user, s, "1", s1_connected)
end

if s2_enable == true then
    if s2_output == 'shoutcast' then
        s2_namespace := "shoutcast_stream_2"
    else
        s2_namespace := s2_mount
    end
    server.register(namespace=!s2_namespace, "connected", fun (s) -> begin !s2_connected end)
    output_to(s2_output, s2_type, s2_bitrate, s2_host, s2_port, s2_pass, s2_mount, s2_url, s2_description, s2_genre, s2_user, s, "2", s2_connected)
    
end

if s3_enable == true then
    if s3_output == 'shoutcast' then
        s3_namespace := "shoutcast_stream_3"
    else
        s3_namespace := s3_mount
    end
    server.register(namespace=!s3_namespace, "connected", fun (s) -> begin !s3_connected end)
    output_to(s3_output, s3_type, s3_bitrate, s3_host, s3_port, s3_pass, s3_mount, s3_url, s3_description, s3_genre, s3_user, s, "3", s3_connected)
end

#output.dummy(fallible=true, !master_dj)
#output.dummy(fallible=true, !dj_live)
ignore(output.dummy(blank()))
