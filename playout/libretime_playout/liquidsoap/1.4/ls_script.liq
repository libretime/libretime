boot_timestamp = string_of(gettimeofday())

web_stream_enabled = ref false
web_stream_id = ref '-1'

show_name = interactive.string("show_name", "")

dynamic_metadata_callback = ref fun (~new_track=false, s) -> begin () end

just_switched = ref false

%include "ls_lib.liq"

sources = ref []
source_id = ref 0

def create_source()
    this_source_id = !source_id

    l = request.equeue(id="s#{this_source_id}", length=0.5)

    l = audio_to_stereo(id="queue_src", l)
    l = cue_cut(l)
    l = amplify(1., override="replay_gain", l)

    # Fade the tracks to avoid hard cuts in between two tracks and at the end of the shows.
    # Liquidsoap reads the fade in/out durations from the annotation "liq_fade_in/out" which
    # value can be set via Libretime settings.
    l = fade.in(l)
    l = fade.out(l)

    l = on_metadata(notify_queue, l)

    sources := list.append([l], !sources)
    server.register(namespace="queues",
                "s#{this_source_id}_skip",
                fun (s) -> begin log("queues.s#{this_source_id}_skip")
                    clear_queue(l)
                    "Done"
                end)
    source_id := !source_id + 1
end

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
stream_queue = map_metadata(id="map_metadata:schedule", update=false, append_title, stream_queue)

ignore(output.dummy(stream_queue, fallible=true))

def web_stream_set_id(value)
    web_stream_id := value
    string_of(!web_stream_id)
end

def web_stream_get_id()
    string_of(!web_stream_id)
end

server.register(namespace="sources",
                description="Start webstream source",
                "start_web_stream",
                fun (s) -> begin log("sources.start_web_stream")
                    notify([("schedule_table_id", !web_stream_id)])
                    web_stream_enabled := true "enabled" end)
server.register(namespace="sources",
                description="Stop webstream source",
                "stop_web_stream",
                fun (s) -> begin log("sources.stop_web_stream") web_stream_enabled := false "disabled" end)

server.register(namespace="web_stream",
                description="Set the web stream id",
                "set_id",
                fun (s) -> begin log("web_stream.set_id") web_stream_set_id(s) end)

server.register(namespace="web_stream",
                description="Get the web stream id",
                "get_id",
                fun (s) -> begin log("web_stream.get_id") web_stream_get_id() end)

default = amplify(id="silence_src", 0.00001, noise())

def map_message_offline(m) =
  [("title", message_offline())]
end

default = map_metadata(id="map_metadata:offline", map_message_offline, default)
ignore(output.dummy(default, fallible=true))

input_main_streaming = ref false
input_show_streaming = ref false
schedule_streaming = ref false

def start_input_main() input_main_streaming := true end
def stop_input_main() input_main_streaming := false end
def start_input_show() input_show_streaming := true end
def stop_input_show() input_show_streaming := false end
def start_schedule() schedule_streaming := true; just_switched := true end
def stop_schedule() schedule_streaming := false end

def update_source_status(sourcename, status) =
    gateway("live '#{sourcename}' '#{status}'")
end

def input_main_on_connect(header) update_source_status("master_dj", true) end
def input_main_on_disconnect() update_source_status("master_dj", false) end
def input_show_on_connect(header) update_source_status("live_dj", true) end
def input_show_on_disconnect() update_source_status("live_dj", false) end

# Auth function for live stream
# @Category LiveStream
# @param user     Username to check against LibreTime API
# @param password Password to check against LibreTime API
# @param ~type    Type of password to check, "dj" or "master, default: "master"
def input_check_auth(user="", password="", ~type="master") =
    log("#{type} user #{user} connected",label="#{type}_source")

    # Check auth based on return value from auth script
    ret = test_process("python3 #{auth_path} --#{type} #{user} #{password}")

    if ret then
        log("#{type} user #{user} authenticated",label="#{type}_source")
    else
        log("#{type} user #{user} auth failed",label="#{type}_source",level=2)
    end

    ret
end

def make_input_auth_handler(source_name)
    def auth_handler(user, password) =
        input_check_auth(user, password, type=source_name)
    end
    auth_handler
end

s = switch(id="switch:blank+schedule",
            track_sensitive=false,
            transitions=[transition_default, transition],
            [({!schedule_streaming}, stream_queue), ({true}, default)]
    )

s = if input_show_port != 0 and input_show_mount != "" then
    input_show_source =
        audio_to_stereo(
            input.harbor(id="harbor:input_show",
                input_show_mount,
                port=input_show_port,
                auth=make_input_auth_handler("dj"),
                max=40.,
                on_connect=input_show_on_connect,
                on_disconnect=input_show_on_disconnect))

    ignore(output.dummy(input_show_source, fallible=true))

    switch(id="switch:blank+schedule+show",
            track_sensitive=false,
            transitions=[transition, transition],
            [({!input_show_streaming}, input_show_source), ({true}, s)]
        )
else
    s
end

s = if input_main_port != 0 and input_main_mount != "" then
    input_main_source =
        audio_to_stereo(
            input.harbor(id="harbor:input_main",
                input_main_mount,
                port=input_main_port,
                auth=make_input_auth_handler("master"),
                max=40.,
                on_connect=input_main_on_connect,
                on_disconnect=input_main_on_disconnect))

    ignore(output.dummy(input_main_source, fallible=true))

    switch(id="switch:blank+schedule+show+main",
            track_sensitive=false,
            transitions=[transition, transition],
            [({!input_main_streaming}, input_main_source), ({true}, s)]
        )
else
    s
end

server.register(namespace="sources",
    description="Stop main input source.",
    usage="stop_input_main",
    "stop_input_main",
    fun (s) -> begin log("sources.stop_input_main") stop_input_main() "Done." end)
server.register(namespace="sources",
    description="Start main input source.",
    usage="start_input_main",
    "start_input_main",
    fun (s) -> begin log("sources.start_input_main") start_input_main() "Done." end)
server.register(namespace="sources",
    description="Stop show input source.",
    usage="stop_input_show",
    "stop_input_show",
    fun (s) -> begin log("sources.stop_input_show") stop_input_show() "Done." end)
server.register(namespace="sources",
    description="Start show input source.",
    usage="start_input_show",
    "start_input_show",
    fun (s) -> begin log("sources.start_input_show") start_input_show() "Done." end)
server.register(namespace="sources",
    description="Stop schedule source.",
    usage="stop_schedule",
    "stop_schedule",
    fun (s) -> begin log("sources.stop_schedule") stop_schedule() "Done." end)
server.register(namespace="sources",
    description="Start schedule source.",
    usage="start_schedule",
    "start_schedule",
    fun (s) -> begin log("sources.start_schedule") start_schedule() "Done." end)
