$(function() {

	function resetSearch() {
		window.location.replace( Routing.generate('shows') );
	}


	$("form[action='/search']").on('submit', function (e) {
		var inputValue = $(this).find("input[name='keyword']").val();
		
		if ("" == inputValue) {
		   	e.preventDefault();
			resetSearch();
		}
	});

});