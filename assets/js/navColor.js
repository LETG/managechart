$('button.changeNavColor').on('click', function() {
	if ($(this).hasClass('navChangeColorButton')) {
		$('button.changeNavColor').removeClass('navChangeColorButton');
		$('nav').removeClass('navbar-inverse');
		Cookies.remove('Nav');
	} else {
		$('button.changeNavColor').addClass('navChangeColorButton');
		$('nav').addClass('navbar-inverse');
		Cookies.set('Nav', 'black');
	}
});