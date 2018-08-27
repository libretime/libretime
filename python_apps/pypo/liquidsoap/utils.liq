# -*- coding: utf-8 -*-

def legal_name_fix(s)
  string.replace(pattern='[^abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789]+',
                 fun (_) -> '_',s)
end

def format_save(m)
  [("title", list.nth(get_process_lines("date '+%Y-%m-%d'"),0)),
   ('album', m['artist_clean']),
   ('artist', legal_name_fix(m['artist_clean']))]
end

# returns a or b, like the clojure or function or ternary operator in c
def orn(a,b) 
  if a != "" then
    a 
  else 
    b 
  end
end

# Pattern used everywhere in Airtime: for side effects or sinking, 
# send output of a stream to an ignored output.dummy
# @category Liquidsoap
# @param ~s A source
def dummy_up(s)
  ignore(output.dummy(fallible=true, s))
end


def post2_metadata(m)
  mj = json_of(m)
  log("posting: #{mj}")
  ## horrible place for this, but this is such a mess, just want to get this done.
  ignore(http.post(data=mj, "http://localhost:8080/now-playing"))
end


def post_metadata(s)
  dummy_up(on_metadata(post2_metadata, s))
end


def log2_metadata(msg, m)
  mj = json_of(m)
  log(msg ^ " now: #{mj}")
end

def log_metadata(msg, s)
  dummy_up(on_metadata(log2_metadata(msg), s))
end

