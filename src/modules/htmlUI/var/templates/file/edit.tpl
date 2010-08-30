<div class="content">
    {if $_REQUEST.act == addFileMData || $_REQUEST.act == addWebstreamMData || $_REQUEST.act == editItem}
        {include file="file/tabswitch.js.tpl"}
    {/if}

    <div class="container_elements" style="width: 607px;">
        <h1>
        {if $_REQUEST.act == addFileData || $_REQUEST.act == addWebstreamData || $_REQUEST.act == addWebstreamMData}
            ##New##
        {else}
            ##Edit##
        {/if}
        {$editItem.type|capitalize}
        </h1>     

    {if $editItem.type == 'audioclip' || $editItem.type == 'file'}
   
        <div id="div_Data">
        {if $_REQUEST.act == 'addFileData'}
        
        <form id="plupload_form">
        	<div id="plupload_files"></div>
        	<div id="plupload_error"><table></table></div>
       </form>
        
        {literal}
	        <script type="text/javascript">
	       
	        $("#plupload_files").pluploadQueue({
	        	// General settings
	        	runtimes : 'html5',
	        	url : 'ui_handler.php?act=plupload',
	        	filters : [
	        		{title: "Audio Files", extensions: "ogg,mp3"}
	       	    ]
	    	});

	    	var uploader = $("#plupload_files").pluploadQueue();
	    	var files_error = new Array();
	    	
	        uploader.bind('FileUploaded', function(up, file, json) {

		        if (!json.response) {
			        //alert("problem");
			        return;
			    }

	        	var j = eval("(" + json.response + ")");
	        	
	        	if(j.error.message) {  

		        	var row = $("<tr/>")
		        		.append('<td>' + file.name +'</td>')
		        		.append('<td>' + j.error.message + '</td>');
		        		
	        		$("#plupload_error").find("table").append(row);
	        		files_error.push(file);  

	        		if(files_error.length % 2 === 0){
						row.addClass("blue1");
		        	}	
	        		else {
	        			row.addClass("blue2");
		        	}  
		        	      
	        	}

	        	if(up.state === plupload.STOPPED){ 
		        	var i;      	
		        	for( i=0; i< files_error.length; i++ ){
						up.removeFile(files_error[i]);
				    }	
	        	}        	
	        });

	        uploader.bind('Error', function(up, err) {
	        	console.log(err);
	        });
	        		    	
	        </script>        
    	{/literal}
        
        {*
            {UIBROWSER->fileForm id=$editItem.id folderId=$editItem.folderId assign="dynform"}
            {include file="sub/dynForm_plain.tpl}
            {assign var="_uploadform" value=null}
         *}
        {/if}
        </div>

        <div id="div_MData">
            {include file="file/metadataform.tpl"}
        </div>
    {/if}
    
    

    {if $editItem.type == 'webstream'}
    
        <div id="div_Data">
            {UIBROWSER->webstreamForm id=$editItem.id folderId=$editItem.folderId assign="dynform"}
            {include file="sub/dynForm_plain.tpl}
            {assign var="_uploadform" value=null}
        </div>

        <div id="div_MData">
            {include file="file/metadataform.tpl"}
        </div>
    {/if}

    {if $editItem.type == 'playlist'}   
        {include file="file/metadataform.tpl"}
    {/if}
    

    </div>
</div>


