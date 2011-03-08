#!/usr/bin/env ruby

# -----------------------------------------------------------------------
# Runs a stress test using the ruby-ecasound ControlInterface
# Jan Weil
# Adapted from original code for ecacontrol.py/pyecasound:
# Copyright (C) 2003 Kai Vehmanen
# Licensed under GPL. See the file 'COPYING' for more information.
# -----------------------------------------------------------------------

if test(?x, "../ecasound/ecasound_debug")
    ENV['ECASOUND'] = "../ecasound/ecasound_debug"
end

if test(?x, "../ecasound/ecasound")
    ENV['ECASOUND'] = "../ecasound/ecasound"
end

require "ecasound"

# ---
# configuration variables

# run for how many seconds
runlen = 5
# debug level (0, 1, 2)
debuglevel = 2

# if above tests fail, the default ecasound binary
# will be used

# main program
e = Ecasound::ControlInterface.new()

result = 0

e.command("cs-add play_chainsetup")
e.command("c-add 1st_chain")
e.command("ai-add rtnull")
e.command("ao-add null")
e.command("cop-add -ezx:1,0.0")
e.command("ctrl-add -kos:2,-1,1,300,0")
e.command("cop-add -efl:300")
e.command("cop-add -evp")
e.command("cop-select 3")
e.command("copp-select 1")
e.command("cs-connect")
e.command("start")

total_cmds = 0
curpos = 0  # not a block var!
copp = 0

puts "Test1"

while true
    curpos = e.command("get-position")
    break if curpos > runlen
    copp = e.command("copp-get")
    if debuglevel == 2
        #print curpos, e.last_float()
        #if curpos == None:
        #    curpos = 0.0
        $stderr << "#{curpos} #{copp}\r"
        # sys.stderr.write('%6.2f %6.4f\r' % (curpos,e.last_float()))
    else
        $stderr << '.' if debuglevel == 1
    end
            
    total_cmds += 2
end

$stderr << "\nprocessing speed: #{total_cmds / runlen} cmds/second.\n" if debuglevel == 2
$stderr << "\n" if debuglevel > 0

e.command("stop")
e.command("cs-disconnect")

# Test 2
puts "Test2"

e.command("cs-remove")
e.command("cs-add play_chainsetup")
e.command("c-add 1st_chain")
e.command("ai-add rtnull")
e.command("ao-add null")
e.command("cop-add -ezx:1,0.0")
e.command("ctrl-add -kos:2,-1,1,300,0")
e.command("cop-add -efl:300")
e.command("cop-add -evp")
e.command("cop-select 3")
e.command("copp-select 1")
e.command("cs-connect")
e.command("start")

total_cmds = 0
curpos = 0  # not a block var!
copp = 0

while true
    curpos = e.command("get-position")
    break if curpos > runlen
    e.command("copp-get")
    # some commands that return a lot
    # of return data
    e.command("cop-register")
    e.command("aio-register")
    e.command("int-cmd-list")
    
    total_cmds = total_cmds + 4
    $stderr << '.' if debuglevel > 0
end

e.command("stop")
e.command("cs-disconnect")

$stderr << "\nprocessing speed: #{total_cmds / runlen} cmds/second.\n" if debuglevel == 2
$stderr << "\n" if debuglevel > 0

e.command("quit")

exit(result)

