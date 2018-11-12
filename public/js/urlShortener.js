//Grab and Submit URL to Shorten
$(document).ready(function() {
	$("#longurl").keyup(function(ev){
		if(ev.keyCode === 13) { // 13 is the key code for enter.
			$("#grab-shorturl").click();
		}
	});
	
	
	
	$("#grab-shorturl").click(function(){
		var url_input=$("#longurl").val();
		
		//Browser side check for valid URL format
		var http_expression = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
		var base_url_expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;

		var http_regex = new RegExp(http_expression);
		var base_url_regex = new RegExp(base_url_expression);
		
		if (url_input.match(base_url_regex)) {
			if (url_input.match(http_regex)){
				//Submit the url
				$.ajax({
					url: "/ajaxAction", 
					data: {
						longurl:url_input
						},
					method: "POST",
					success: function(result){
						$("#url-reply").html(result);
						
						//Add Vanity functionality
						$("#add-vanity-link").click(function(event){
							event.preventDefault();
							$("#add-vanity-section").slideDown();
							
							//Prevent default click functionality
							return false;
						});
						
						//Update Vanity with AJAX
						$("#send-vanity-url").click(function(){
							
							var vanity_string=$("#vanity_url").val();
							var slug = $("#slug").val();
							
							var slug_regexp = /^[a-zA-Z0-9-_]+$/;
							var slug_regex = new RegExp(slug_regexp);
							if (vanity_string.match(slug_regex)){
								$.ajax({
								url: "/ajaxAction/vanity", 
								data: {
									vanity:vanity_string,
									slug_id:slug 
									},
								method: "POST",
								success: function(result){
										$("#vanity-wrapper").html(result);
									}
								});	
							}
							else{
								$("#vanity-notice").html('<div class="alert alert-danger" role="alert">Please enter a slug - only alphanumeric characters, dashes and underscores</div>');
							}
							
							
						});
						
					}
				});
			}
			else{
				$("#url-reply").html('<div class="alert alert-warning" role="alert">Please include http:// or https://</div>');
			}
		} else {
			$("#url-reply").html('<div class="alert alert-danger" role="alert">Please enter a valid url</div>');
		}

		
	});

});



