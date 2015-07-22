$(document).ready(function() {

	function init(){
		$('#bt_add_image').click(function(event) {
			popup.show('upload');
		});

		
	}

	var popup = {
       	show : function(popupName){
       		$('.popup').hide();
       		$('.popup_' + popupName).show();
       	},
       	hide : function(){
       		$('.popup').hide();
       	}
	};

	init();
});