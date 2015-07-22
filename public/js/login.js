$(document).ready(function() {
	var lang = $.cookie('lang');

	if(lang == undefined)
		var popup = $('.popup').show();

	$('.lang-bt').click(function(event) {
		$.cookie('lang', $(this).attr('value'));
		location.reload();
	});
});