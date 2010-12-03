
#########################################
# A/B queue-setup Scheduler
#########################################

# a/b queue setup
scheduler_q0 = request.queue(conservative=true,length=600.,id="scheduler_q0")
scheduler_q1 = request.queue(conservative=true,length=600.,id="scheduler_q1")

scheduler_q0 = audio_to_stereo(scheduler_q0)
scheduler_q1 = audio_to_stereo(scheduler_q1)

scheduler_active = ref 0
scheduler_queue = ref 1
scheduler_q0_enabled = ref false
scheduler_q1_enabled = ref false

# push function, enqueues file in inactive queue (does not start automatically)
def scheduler_push(s)
  list.hd(server.execute("scheduler_q#{!scheduler_queue}.push #{s}"))
  print('push to #{!scheduler_queue} - #{s}')
  "Done"
end


# flips the queues
def scheduler_flip()

  # set a/b-queue corresponding to active, see fallback below
  if !scheduler_active==1 then scheduler_q0_enabled:=true else scheduler_q0_enabled:=false end
  if !scheduler_active==0 then scheduler_q1_enabled:=true else scheduler_q1_enabled:=false end

  # get playing (active) queue and flush it  
  l = list.hd(server.execute("scheduler_q#{!scheduler_active}.queue"))
  l = string.split(separator=" ",l)
  list.iter(fun (rid) -> ignore(server.execute("scheduler_q#{!scheduler_active}.ignore #{rid}")), l)
  
  # skip the playing item
  source.skip(if !scheduler_active==0 then scheduler_q0 else scheduler_q1 end)

  # flip variables
  scheduler_active := 1-!scheduler_active
  scheduler_queue := 1-!scheduler_active


  "Done"
end


# print status
def scheduler_status()
  print('scheduler_active: #{!scheduler_active}')
  print('scheduler_queue : #{!scheduler_queue}')
  print('pypo_data: #{!pypo_data}')

  #print('user_id: #{!user_id}')
  #print('playlist_id: #{!playlist_id}')
  #print('transmission_id: #{!transmission_id}')  
  #print('playlist_type: #{!playlist_type}')  
  
  "Done"
end

# register for telnet access
server.register(namespace="scheduler","push", scheduler_push)
server.register(namespace="scheduler","flip", fun (_) -> scheduler_flip())
server.register(namespace="scheduler","status", fun (_) -> scheduler_status())


# activate / deactivate queues, needed for fallback to work
scheduler_q0 = switch(track_sensitive=true, [({!scheduler_q0_enabled},scheduler_q0)])
scheduler_q1 = switch(track_sensitive=true, [({!scheduler_q1_enabled},scheduler_q1)])
scheduler_q_holder = fallback(track_sensitive=true, [scheduler_q0, scheduler_q1])


# finally the resulting scheduler source
scheduler = fallback(track_sensitive=false, [scheduler_q_holder, default])

