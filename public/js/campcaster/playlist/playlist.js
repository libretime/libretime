function removeFadeInput(){
	var span = $(this).parent();
	var pos = span.parent().parent().attr('id').split("_").pop();
	
	var fadeIn, fadeOut, url;
	
	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");
	var oldValue = $("#pl_tmp_time").val();
	var newValue = $(this).val().trim();
	
	if(newValue === "")
		newValue = '00:00:00';
	
	if(span.parent().hasClass('pl_fade_in')){	
		fadeIn = newValue;
	}
	else if(span.parent().hasClass('pl_fade_out')){
		fadeOut = newValue;
	}
	
	//test that input is a time.
    if (!regExpr.test(newValue)) {
    	span.empty();
    	span.append(oldValue);
    	span.click(addTextInput);
    	alert("please put in a time '00:00:00 (.0000)'");
    	return;
    }

	url = '/Playlist/set-fade';
	url = url + '/format/json';
	url = url + '/pos/' + pos;

	if (fadeIn !== undefined)
		url = url + '/fadeIn/' + fadeIn;
	if (fadeOut !== undefined)
		url = url + '/fadeOut/' + fadeOut;

	$.post(

		url,
    	
    	function(json){
    		var li, span, data;
			data = jQuery.parseJSON(json),
    		
    		li = $("#pl_"+pos);
    		if(data.error){
    			var hidden = $("#pl_tmp_time");
    			var time = hidden.val();
    			
    			span = hidden.parent();
    			span.empty();
    			span.append(time);
    			span.click(addTextInput);
    			alert(data.error);
    		}
			if(data.fadeIn){
				span = li.find(".pl_fade_in").find(".pl_time");
				span.empty();
    			span.append(data.fadeIn);
    			span.click(addTextInput);
			}
			if(data.fadeOut){
				span = li.find(".pl_fade_out").find(".pl_time");
				span.empty();
    			span.append(data.fadeOut);
    			span.click(addTextInput);
			}
    	}
    );	
}

function removeCueInput(){
	var span = $(this).parent();
	var pos = span.parent().attr('id').split("_").pop();
	
	var cueIn, cueOut, url;
	
	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");
	var oldValue = $("#pl_tmp_time").val();
	var newValue = $(this).val().trim();
	
	if(span.hasClass('pl_cue_in')){
		if(newValue === "")
    		newValue = '00:00:00';
		cueIn = newValue;
	}
	else if(span.hasClass('pl_cue_out')){
		cueOut = newValue;
	}
	
	//test that input is a time.
    if (newValue!=="" && !regExpr.test(newValue)) {
    	span.empty();
    	span.append(oldValue);
    	span.click(addTextInput);
    	alert("please put in a time '00:00:00 (.0000)'");
    	return;
    }
	
	url = '/Playlist/set-cue';
	url = url + '/format/json';
	url = url + '/pos/' + pos;

	if (cueIn !== undefined)
		url = url + '/cueIn/' + cueIn;
	if (cueOut !== undefined)
		url = url + '/cueOut/' + cueOut;

	$.post(
		url,
    	
    	function(json){
    		var li, span, data;
			data = jQuery.parseJSON(json),
    		
    		li = $("#pl_"+pos);
    		if(data.error){
    			var hidden = $("#pl_tmp_time");
    			var time = hidden.val();
    			
    			span = hidden.parent();
    			span.empty();
    			span.append(time);
    			span.click(addTextInput);
    			alert(data.error);
    			return;
    		}
			span = li.find(".pl_playlength");
			span.empty();
			span.append(data.cliplength);
			
			span = $(".pl_duration");
			span.empty();
			span.append(data.length);
			
			if(data.cueIn){
				span = li.find(".pl_cue_in");
				span.empty();
    			span.append(data.cueIn);
    			span.click(addTextInput);
			}
			if(data.cueOut){
				span = li.find(".pl_cue_out");
				span.empty();
    			span.append(data.cueOut);
    			span.click(addTextInput);
			}
			
			span = li.find(".pl_fade_in").find(".pl_time");
			span.empty();
			span.append(data.fadeIn);
			
			span = li.find(".pl_fade_out").find(".pl_time");
			span.empty();
			span.append(data.fadeOut);
    	}
    );	
}

function addTextInput(){
	var time = $(this).text().trim();
	var input = $("<input type='text' value="+time+" size='13' maxlength='15'/>");
	
	//Firefox seems to have problems losing focus otherwise, Chrome is fine.
	$(":input").blur();
	$(this).empty();
	
	$(this).append(input);
	input.focus();
	
	if($(this).hasClass('pl_cue_in') || $(this).hasClass('pl_cue_out')) {
		input.blur(removeCueInput);
	}
	else if($(this).parent().hasClass('pl_fade_in') || $(this).parent().hasClass('pl_fade_out')){
		input.blur(removeFadeInput);
	}
	
	input.keypress(function(ev){
		//don't want enter to submit.
		if (ev.keyCode === 13) {
			ev.preventDefault();
			$(this).blur();
		}
	});
	
	input = $("<input type='hidden' value="+time+" size='10' id='pl_tmp_time'/>");
	$(this).append(input);
	
	$(this).unbind('click');
}


function setPLContent(json) {
	
	$('#pl_name').empty()
		.append(json.name);
	$('#pl_length').empty()
		.append(json.length);		
	$('#pl_sortable').empty()
		.append(json.html);	

	$(".pl_time").click(addTextInput);	
}

function deletePLItem(){

	var url, pos;

	url = '/Playlist/delete-item/format/json/view/pl';

	pos = $('form[name="PL"]').find(':checked').not('input[name="all"]').map(function() {
		return "/pos/" + $(this).attr('name');
	}).get().join("");

	url = url + pos;

	$.post(url, setPLContent);
}

function movePLItem(event, ui) {	
	var li, newPos, oldPos, url;
	
	li = ui.item;
	
    newPos = li.index();
    oldPos = li.attr('id').split("_").pop(); 

	url = '/Playlist/move-item'
	url = url + '/format/json';
	url = url + '/view/pl';
	url = url + '/oldPos/' + oldPos;
	url = url + '/newPos/' + newPos;

	$.post(url, setPLContent);
}

$(document).ready(function() {

	$("#pl_sortable").sortable();
    $("#pl_sortable" ).bind( "sortstop", movePLItem);

	$(".pl_time").click(addTextInput);

	$("#pl_remove_selected").click(deletePLItem);

	$('input[name="all"]').click(function(){
		$('form[name="PL"]').find('input').attr("checked", $(this).attr("checked"));
	});

});
