
$(document).ready(function() {
	  
    $("#pl_sortable").sortable();
    
    $("#pl_sortable" ).bind( "sortstop", function(event, ui) {	
    	var li, newPos, oldPos;
    	
    	li = ui.item;
        newPos = $(this).children().index(li);
        oldPos = li.attr('id').split("_").pop();  
        
        $.post("ui_handler.php",
        		
        	{ 'act': 'PL.moveItem', 'oldPos': oldPos, 'newPos': newPos },
        	
        	function(data){
        		var ul =  $("#pl_sortable");
        		
        		if(data.error) {
        			var size,
        				tmp_ul;
        			
        			size = $(ul).children().size();
        			
        			tmp_ul = $("<ul/>");
        			for(var i=0; i<size; i++)
        			{
        				tmp_ul.append(ul.find("#pl_"+i));
        			}
        		
        			//restore the UI to the previous order.
        			$(ul).html(tmp_ul.contents());
        			
        			alert(data.error);	
        		}
        		else {
	        		//redo playlist positional ids, input names.
        			$(ul).children().each(function(index){	
	                	var li = $(this);
	                	li.attr('id', 'pl_'+index);	
	                	li.find(".pl_input").find("input").attr('name', index);
	                });
        		}	
        	},
        	
        	"json"
        );
   	});
    
    function removeTextInput(){
    	var span = $(this).parent();
    	var pos = span.parent().attr('id').split("_").pop();
    	
    	var cueIn, cueOut;
    	
    	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,4})?$");
    	var oldValue = $("#pl_tmp_time").val();
    	var newValue = $(this).val().trim();
    	
    	//test that input is a time.
	    if (!regExpr.test(newValue)) {
	    	span.empty();
	    	span.append(oldValue);
	    	span.click(addTextInput);
	    	alert("please put in a time '00:00:00 (.0000)'");
	    	return;
	    }
    	
    	if(span.hasClass('pl_cue_in')){
    		cueIn = $(this).val();
    	}
    	else if(span.hasClass('pl_cue_out')){
    		cueOut = $(this).val();
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
        		}
        		else if(data.type==="cue"){
        			span = li.find(".pl_playlength");
        			span.empty();
        			span.append(data.cliplength);
        			
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
        		}
        	},
        	
        	"json"
        );	
    }
    
    function addTextInput(){
    	var time = $(this).text().trim();
    	var input = $("<input type='text' value="+time+" size='10' maxlength='13'/>");
    	
    	//Firefox seems to have problems losing focus otherwise, Chrome is fine.
    	$(":input").blur();
    	$(this).empty();
    	
    	$(this).append(input);
    	input.focus();
    	input.blur(removeTextInput);
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
