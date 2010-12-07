
# Register the cut protocol
def cue_protocol(arg,delay)
	# The extraction program
	# cut_file = "#{configure.libdir}/cut-file.py"
	cue_script = "./cue_file.py"
	# Parse args 
	ret = string.extract(pattern="cue_in=(\d+)",arg)
	start = 
		if list.length(ret) == 0 then 
			"0"
		else
			ret["1"]
		end
	ret = string.extract(pattern="cue_out=(\d+)",arg)
	stop =
		if list.length(ret) == 0 then
			"0"
		else
			ret["1"]
		end
	ret = string.extract(pattern=":(.*)$",arg)
	uri = 
	   if list.length(ret) == 0 then
			""
		else
			ret["1"]
		end
	x = get_process_lines("#{cue_script} #{quote(uri)} #{start} #{stop}")
	if list.hd(x) != "" then
		([list.hd(x)],[])
	else
		([uri],[])
	end
end
add_post_processor("cue_file", temporary=true, cue_protocol)