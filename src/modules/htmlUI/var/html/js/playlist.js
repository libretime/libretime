
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
        			$(ul).empty();
        			$(ul).html(tmp_ul.contents());
        			
        			alert(data.error);	
        		}
        		else {
	        		//redo playlist positional ids.
        			$(ul).children().each(function(index){	
	                	var li = $(this);
	                	li.attr('id', 'pl_'+index);
	                });
        		}	
        	},
        	
        	"json"
        );
   	});

    
});
