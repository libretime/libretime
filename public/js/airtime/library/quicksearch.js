function setUpQuickSearch() {

	$("#library_quick_search input").keyup(function(ev){
		var url, string;
		//alert(x);

		url = "/Library/quick-search/format/json";
		string = $(this).val();

		$.post(url, {search: string}, function(json){
			var html, data;
			//hacky way until I can figure out paginator better.
			html = json.html.replace(/quick-search\/format\/json/g, "index");

			var el = $(html);
			
			data = el.find("tr:not(:first-child)");
			setLibraryContents(data);

			var lib = $("#library_content");

			lib.find("div.paginationControl").remove();
			lib.append(el[2]);

		});
	});

}
