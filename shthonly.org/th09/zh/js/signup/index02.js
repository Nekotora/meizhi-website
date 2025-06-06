function scrollbar(){
	if ($(document).width() > $(window).width()) {
		$(document).scrollLeft(Math.floor(($(document).width() - $(window).width()) / 2));
	}
};

$(window).resize(function(){
	scrollbar();
});

$(document).ready(function(){
	scrollbar();
});