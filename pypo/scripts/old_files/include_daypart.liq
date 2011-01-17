
#########################################
# A/B queue-setup daypart
#########################################

# a/b queue setup
daypart_q0 = request.queue(conservative=true,length=600.,id="daypart_q0")
daypart_q1 = request.queue(conservative=true,length=600.,id="daypart_q1")

daypart_q0 = audio_to_stereo(daypart_q0)
daypart_q1 = audio_to_stereo(daypart_q1)

daypart_active = ref 0
daypart_queue = ref 1
daypart_q0_enabled = ref false
daypart_q1_enabled = ref false

# push function, enqueues file in inactive queue (does not start automatically)
def daypart_push(s)
  list.hd(server.execute("daypart_q#{!daypart_queue}.push #{s}"))
  print('push to #{!daypart_queue} - #{s}')
  "Done"
end


# flips the queues
def daypart_flip()

  # set a/b-queue corresponding to active, see fallback below
  if !daypart_active==1 then daypart_q0_enabled:=true else daypart_q0_enabled:=false end
  if !daypart_active==0 then daypart_q1_enabled:=true else daypart_q1_enabled:=false end

  # get playing (active) queue and flush it  
  l = list.hd(server.execute("daypart_q#{!daypart_active}.queue"))
  l = string.split(separator=" ",l)
  list.iter(fun (rid) -> ignore(server.execute("daypart_q#{!daypart_active}.ignore #{rid}")), l)
  
  # skip the playing item
  # source.skip(if !daypart_active==0 then daypart_q0 else daypart_q1 end)

  # flip variables
  daypart_active := 1-!daypart_active
  daypart_queue := 1-!daypart_active

  "Done"
end


# print status
def daypart_status()
  print('daypart_active: #{!daypart_active}')
  print('daypart_queue : #{!daypart_queue}')
  "Done"
end

# register for telnet access
server.register(namespace="daypart","push", daypart_push)
server.register(namespace="daypart","flip", fun (_) -> daypart_flip())
server.register(namespace="daypart","status", fun (_) -> daypart_status())


# activate / deactivate queues, needed for fallback to work
daypart_q0 = switch(track_sensitive=true, [({!daypart_q0_enabled},daypart_q0)])
daypart_q1 = switch(track_sensitive=true, [({!daypart_q1_enabled},daypart_q1)])
daypart_q_holder = fallback(track_sensitive=true, [daypart_q0, daypart_q1])


# finally the resulting daypart source
daypart = fallback(track_sensitive=false, [daypart_q_holder, default])

