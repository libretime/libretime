# run this file through "liquidsoap -c -i"
# to see if pretty printing looks pretty enough
# for more checks, pass --twidth 78

x = (blank() : source(2,3,4))
pair = (x,x)
x = (x,x)
x = (x,x)
x = (x,x)

def output.shoutcast(
  ~id="output.shoutcast",~start=true,
  ~restart=false,~restart_delay=3,
  ~host="localhost",~port=8000,
  ~user="source",~password="hackme",
  ~genre="Misc",~url="http://savonet.sf.net/",
  ~name="OCaml Radio!",~public=true, ~format="",
  ~dumpfile="", ~icy_metadata="guess",
  ~on_connect={()}, ~on_disconnect={()},
  ~aim="",~icq="",~irc="",~icy_reset=true,
  ~fallible=false,~on_start={()},~on_stop={()},
  e,s)
=
  42
end

x = (output.shoutcast,pair)

def output.shoutcast(
  ~id="output.shoutcast",~start=true,
  ~restart=false,~restart_delay=3,
  ~host="localhost",~port=8000,
  ~user="source",~password="hackme",
  ~genre="Misc",~url="http://savonet.sf.net/",
  ~name="OCaml Radio!",~public=true, ~format="",
  ~dumpfile="", ~icy_metadata="guess",
  ~on_connect={()}, ~on_disconnect={()},
  ~aim="",~icq="",~irc="",~icy_reset=true,
  ~fallible=false,~on_start={()},~on_stop={()},
  e,s)
=
  (pair,42)
end

def output.shoutcast(
  ~id="output.shoutcast",~start=true,
  ~restart=false,~restart_delay=3,
  ~host="localhost",~port=8000,
  ~user="source",~password="hackme",
  ~genre="Misc",~url="http://savonet.sf.net/",
  ~name="OCaml Radio!",~public=true, ~format="",
  ~dumpfile="", ~icy_metadata="guess",
  ~on_connect={()}, ~on_disconnect={()},
  ~aim="",~icq="",~irc="",~icy_reset=true,
  ~fallible=false,~on_start={()},~on_stop={()},
  e,s)
=
  (blank() : source(3,0,0))
end

x = (output.shoutcast,blank())
