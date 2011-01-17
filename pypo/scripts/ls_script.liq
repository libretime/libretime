%include "library/pervasives.liq"
%include "ls_config.liq"

set("log.file.path",log_file)
set("server.telnet", true)

active_queue = ref 0

scheduler_q0 = request.queue(conservative=true,length=600.,id="scheduler_q0")
scheduler_q1 = request.queue(conservative=true,length=600.,id="scheduler_q1")

scheduler_q0 = audio_to_stereo(scheduler_q0)
scheduler_q1 = audio_to_stereo(scheduler_q1)

pypo_data = ref '0'

# push function, enqueues file in inactive queue (does not start automatically)
def scheduler_push(s)
  print('s: #{s}')
  server.execute("scheduler_q#{!active_queue}.push #{s}")
  print('push to #{!active_queue} - #{s}')
  "Done"
end

# flips the queues
def scheduler_flip()
  # get playing (active) queue and flush it  
  l = list.hd(server.execute("scheduler_q#{!active_queue}.queue"))
  l = string.split(separator=" ",l)
  list.iter(fun (rid) -> ignore(server.execute("scheduler_q#{!active_queue}.ignore #{rid}")), l)
  
  # skip the playing item
  source.skip(if !active_queue==0 then scheduler_q0 else scheduler_q1 end)

  # flip variables
  active_queue := 1-!active_queue
  "Done"
end

def notify(m)
  system("./notify.sh --data='#{!pypo_data}' --media-id=#{m['media_id']}")
  #print("./notify.sh --data='#{!pypo_data}' --media-id=#{m['media_id']}")
end

# register for telnet access
server.register(namespace="scheduler","push", scheduler_push)
server.register(namespace="scheduler","flip", fun (s) -> begin scheduler_flip() end)
server.register(namespace="vars", "pypo_data", fun (s) ->  begin pypo_data := s "Done" end)

default = single("/opt/pypo/files/basic/silence.mp3")
radio = fallback([ switch(track_sensitive=false, [(fun () -> !active_queue==1, scheduler_q0), (fun () -> !active_queue==0, scheduler_q1)]), default])

radio = on_metadata(notify, radio)

out(radio)
