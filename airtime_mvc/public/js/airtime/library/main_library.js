$(document).ready(function() {
	var viewport = AIRTIME.utilities.findViewportDimensions(),
		lib = $("#library_content"),
		pl = $("#side_playlist"),
		widgetHeight = viewport.height - 185,
		width = Math.floor(viewport.width - 110);
	
		lib.height(widgetHeight)
			.width(Math.floor(width * 0.55));
			
		pl.height(widgetHeight)
			.width(Math.floor(width * 0.45));
	
	AIRTIME.library.libraryInit();
		
});