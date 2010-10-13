
$(document).ready(function() {
	
	$('#search_results').find('tr').not('.blue_head').draggable({ 
		helper: 'clone' 
	});
	
	
	$('#cc_right_panel').find('h1').click(function(){
		$(this).siblings().toggle();
	});
    
    function movePLItem(event, ui) {	
    	var ul, li, newPos, oldPos, id_prefix;
    	
    	ul = $(this);
    	li = ui.item;
    	
    	var temp = li.attr('id').split("_");
    	
        newPos = ul.children().index(li);
        oldPos = temp[1]; 
        id_prefix = temp[0];
        
        $.post('ui_handler.php',
        		
        	{ 'act': 'PL.moveItem', 'oldPos': oldPos, 'newPos': newPos },
        	
        	function(data){
        		
        		if(data.error) {
        			var size,
        				tmp_ul;
        			
        			size = $(ul).children().size();
        			
        			tmp_ul = $('<ul/>');
        			for(var i=0; i<size; i++)
        			{
        				tmp_ul.append(ul.find('#'+id_prefix+'_'+i));
        			}
        		
        			//restore the UI to the previous order.
        			$(ul).html(tmp_ul.contents());
        			
        			alert(data.error);	
        		}
        		else {
	        		//redo playlist positional ids, input names.
        			$(ul).children().each(function(index){	
	                	var li = $(this);
	                	li.attr('id', id_prefix+'_'+index);	
	                	li.find('.'+id_prefix+'_input').find('input').attr('name', index);
	                });
        		}	
        	},
        	
        	"json"
        );
   	}
    
    function addPLItem(event, ui){
    	var tr, id, ul;
    	
    	ul = $(this);
    	tr = ui.helper; 	
    	
    	if(tr.get(0).tagName === 'LI')
    		return;
    	
    	id = tr.find("input").attr('name');
    	
    	hpopup('ui_handler.php?act=PL.addItem&id='+id);

    }
  
    //PL main editor.
    $("#pl_sortable").sortable();
    $("#pl_sortable" ).bind( "sortstop", movePLItem);
    
    //PL side bar editor.
    $("#spl_sortable").sortable();
    $("#spl_sortable" ).bind( "sortstop", movePLItem);
    
    $("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addPLItem);
    
    function removeCueInput(){
    	var span = $(this).parent();
    	var pos = span.parent().attr('id').split("_").pop();
    	
    	var cueIn, cueOut;
    	
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
    	
    	$.post("ui_handler.php",
        		
        	{ 'act': 'PL.setClipLength', 'pos': pos, 'cueIn': cueIn, 'cueOut': cueOut },
        	
        	function(data){
        		var li, span;
        		
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
        	},
        	
        	"json"
        );	
    }
    
    function removeFadeInput(){
    	var span = $(this).parent();
    	var pos = span.parent().parent().attr('id').split("_").pop();
    	
    	var fadeIn, fadeOut;
    	
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

    	$.post("ui_handler.php",
        		
        	{ 'act': 'PL.setFadeLength', 'pos': pos, 'fadeIn': fadeIn, 'fadeOut': fadeOut },
        	
        	function(data){
        		var li, span;
        		
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
        	},
        	
        	"json"
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
    		if (ev.keyCode == '13') {
    			ev.preventDefault();
    			$(this).blur();
    		}
    	});
    	
    	input = $("<input type='hidden' value="+time+" size='10' id='pl_tmp_time'/>");
    	$(this).append(input);
    	
    	$(this).unbind('click');
    }
    
    $(".pl_time").click(addTextInput);

    
});
