function topNavToSelect() {
    ss.i18n.init();
	// building select menu
	$('<select class="upper-nav" />').appendTo('#pre-header .container');

	// building an option for select menu
	$('<option />', {
		'selected': 'selected',
		'value' : '',
		'text': ss.i18n._t('Mobile.ChoosePage', 'Choose Page...')
	}).appendTo('#pre-header .container select');

	$('#pre-header ul.inline li a').each(function(){
		var target = $(this);

		$('<option />', {
			'value' : target.attr('href'),
			'text': target.text()
		}).appendTo('#pre-header .container select');
	});
	// on clicking on link
	$('#pre-header .container select').on('change',function(){
		window.location = $(this).find('option:selected').val();
	});
}

// building select .navbar for mobile width only
function NavToSelect() {
    ss.i18n.init();
	// building select menu
	$('<select />').appendTo('.navbar');

	// building an option for select menu
	$('<option />', {
		'selected': 'selected',
		'value' : '',
		'text': ss.i18n._t('Mobile.ChoosePage', 'Choose Page...')
	}).appendTo('.navbar select');
	$('.navbar ul li a').each(function(){
		var target = $(this);

		$('<option />', {
			'value' : target.attr('href'),
			'text': target.text()
		}).appendTo('.navbar select');
	});
	// on clicking on link
	$('.navbar select').on('change',function(){
		window.location = $(this).find('option:selected').val();
	});

}

function showtooltip() {
	$('a[data-toggle=tooltip], button[data-toggle=tooltip], input[data-toggle=tooltip]')
	.tooltip({
		animation:false
	});
}

function cartContent() {
	var $cartContent = $('.cart-content');
	$cartContent.find('table').click(function(e){
		e.stopPropagation();
	});
}

// flexslider on home page
function flexSlideShow() {
    if (typeof imageSliderBuildNavigation === 'undefined') {
        var imageSliderBuildNavigation = true;
    }
	$('.flexslider').flexslider({
		 animation: "slide",
		 slideshowSpeed: 4000,
		 directionNav: false,
		 pauseOnHover: true,
		 controlNav: imageSliderBuildNavigation
	});
}

// bootstrap carousel in caregories grid and list
function productSlider() {
	$('.carousel').carousel();
}


// link fancybox plugin on product detail
function productFancyBox() {
	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
}

// dropdown mainnav
function dropdownMainNav() {
	var navLis = $('div.navbar > ul.nav > li');
	navLis.hover(
		function () {
			// hide the css default behavir
			$(this).children('div').css('display', 'none');
			//show its submenu
			$(this).children('div').slideDown(150);
		}, 
		function () {
			//hide its submenu
			$(this).children('div').slideUp(350);		
		}
	);

}

$(document).ready(function(){
	topNavToSelect();
	NavToSelect();
	cartContent();
	flexSlideShow();
	productSlider();
	productFancyBox();
	dropdownMainNav();
    
    if (!window.matchMedia || (window.matchMedia("(min-width: 768px)").matches)) {
        // this is only for desktop (screen width higher than 767px
        showtooltip();
    }
});