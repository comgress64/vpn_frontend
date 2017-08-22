/* ======================================== >>>>> */
/* = Default settings for Datatables = */
/* ======================================== >>>>> */

$.extend(true, $.fn.dataTable.defaults, {
	"pagingType": "full_numbers",
	"fnInitComplete": function(settings) {
		var selector = settings.oInstance.selector + "_filter";
		var $searchForm = '<div class="form-inline">' +
			'<div class="input-group">' +
			'<span class="input-group-btn">' +
			'<button class="btn btn-primary btn-sm" type="button">' +
			'<i class="fa fa-search"></i>' +
			'</button>' +
			'</span>' +
			'<input type="text" data-table-instance="' + settings.oInstance.selector + '" class="form-control input-sm" placeholder="Search for...">' +
			'</div>' +
			'</div>';
		$(selector).html($searchForm);
		$(selector).find('input').keypress(function(e) {
			if (e.which == 13) $($(selector).find('input').data('table-instance')).DataTable().search($(selector).find('input').val()).draw();
		});
		$(selector).find('button').click(function() {
			$($(selector).find('input').data('table-instance')).DataTable().search($(selector).find('input').val()).draw();
		});
	}
});

/* ======================================== >>>>> */
/* = Tabs = */
/* ======================================== >>>>> */

$(document).ready(function() {
	$('ul.tabs-title').on('click', 'li:not(.active)', function() {
		$(this)
			.addClass('active').siblings().removeClass('active')
			.closest('div.tabs').find('div.tabs-content').removeClass('active').eq($(this).index()).addClass('active');
	});
});

/* ======================================== >>>>> */
/* = General navigation = */
/* ======================================== >>>>> */

$(document).ready(function() {
	$('.box-head').click(function(e) {
		var $btnCollapse = $(this).find('.btn-collapse');
		$btnCollapse.find('i').toggleClass('fa-minus');
		$btnCollapse.find('i').toggleClass('fa-plus');
		$btnCollapse.parent().parent().next().toggle();
	});
});

/* ----- Instance for generateNavigation() ----- */

$(document).ready(function() {
	if ($(document).width() > 768) {
		setTimeout(function() { // TODO: Delete in prodaction
			generateNavigation();
		}, 1000);
	}
});

$(document).resize(function() {
	if ($(document).width() > 768) {
		setTimeout(function() { // TODO: Delete in prodaction
			generateNavigation();
		}, 1000);
	}
});

/* ----- Generator for navigation ----- */

function generateNavigation() {
	var $navigation = $('.header .navigation .general');
	var $profileItem = $navigation.find('.profile');
	var $logo = $('.header .logo');

	function getWidthMenu() {
		var itemsWidth = 10;
		$navigation.children('li').not('.profile').each(function() {
			itemsWidth += $(this).width();
		});
		return itemsWidth;
	}

	// if ((getWidthMenu() + $profileItem.width() + $logo.width()) > $(window).width()) {
	var $dropdownItem = $(
		'<li class="has-child dropdown">' +
		'<a href="#."><i class="fa fa-bars"></i></a>' +
		'<ul class="child"></ul>' +
		'</li>'
	);
	$dropdownItem.insertBefore($profileItem);

	$($navigation.children('li.active').get().reverse()).each(function() {
		$(this).prependTo($navigation);
	});

	$($navigation.children('li').not('.profile, .dropdown, .active, .bars, .no-hide').get().reverse()).each(function() {
		$(this).removeClass('has-child').appendTo($navigation.find('.dropdown > .child'));
		// var $itemsWidth = 0;
		// $($navigation.children('li').not('.profile').get().reverse()).each(function() {
		// 	$itemsWidth += $(this).width();
		// });
		// if ((getWidthMenu() + $profileItem.width() + $logo.width()) < $(window).width()) {
		// 	return false;
		// }
	});
	// }
}

/* ======================================== >>>>> */
/* = Slick carousel = */
/* ======================================== >>>>> */

$(document).ready(function() {

	/* ----- Image carousel ----- */

	$('.image-carousel').slick({
		slidesToShow: 3,
		prevArrow: '<button class="slick-prev"><i class="fa fa-angle-left"></i></button>',
		nextArrow: '<button class="slick-next"><i class="fa fa-angle-right"></i></button>'
	});

	/* ----- Gallery for carousel ----- */

	$('.image-carousel').magnificPopup({
		delegate: 'a:not(".slick-cloned")',
		type: 'image',
		gallery: {
			enabled: true,
			navigateByImgClick: true
		}
	});
});

/* ======================================== >>>>> */
/* = Plugins = */
/* ======================================== >>>>> */

/* ----- DateTimePicker ----- */

$(function() {
	$('.datetime').datetimepicker();
});

/* -----  TinyMCE Editor Init ----- */

$(function() {
	if ($('textarea.editor').length > 0) {
		tinymce.init({ selector: 'textarea.editor' });
	}
});

/* ======================================== >>>>> */
/* = New nabigation = */
/* ======================================== >>>>> */

$(document).ready(function() {
	setTimeout(function() {
		$('.new-subheader .btn').click(function() {
			$('.new-navigation').slideToggle();
		});

		$('.new-navigation li.has-child > a').click(function(e) {
			if ($(this).parent().parent().html() == $('.new-navigation').html()) {
				$('.new-navigation li.has-child').not($(this).parent()).removeClass('opened');
				$(this).parent().toggleClass('opened');
			}
			else {
				$(this).parent().toggleClass('opened');
			}
		});

		$('#sidebar .navigation li.has-child > a').click(function(e) {
			if ($(this).parent().parent().html() == $('.new-navigation').html()) {
				$('.new-navigation li.has-child').not($(this).parent()).removeClass('opened');
				$(this).parent().toggleClass('opened');
			}
			else {
				$(this).parent().toggleClass('opened');
			}
		});
	}, 1000);
});