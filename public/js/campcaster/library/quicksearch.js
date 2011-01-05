function setUpQuickSearch() {

	$("#library_quick_search input").keyup(function(ev){
		var url, string;
		//alert(x);

		url = "/Library/quick-search/format/json";
		string = $(this).val();

		$.post(url, {search: string}, function(json){
			var x;
		});
	});

}
