
/*!
 *	dotdotdot JS 4.1.0
 *
 *	dotdotdot.frebsite.nl
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 *
 *	License: CC-BY-NC-4.0
 *	http://creativecommons.org/licenses/by-nc/4.0/
 */

require(["jquery"], function ($) {
	$(document).ready(function () {
		var tempIndex = 0;
		$('.block_recommenda #myCarousel').on('slide.bs.carousel', function (event) {
			var secondStringData = document.getElementById("secondString");
			var thirdStringData = document.getElementById("thirdString");

			var secondString = secondStringData.textContent;
			var thirdString = thirdStringData.textContent;

			if (event.direction == 'right') {
				if (tempIndex == 0) {
					tempIndex = $('.carousel-item').length;
					var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
					$("#course-count").html(tempString);
				}
				else {
					if (tempIndex == 1) {
						tempIndex = $('.carousel-item').length;
						var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
						$("#course-count").html(tempString);
					}
					else {
						tempIndex--;
						var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
						$("#course-count").html(tempString);
					}
				}
			}
			else {
				if (tempIndex == 0) {
					tempIndex = 2;
					var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
					$("#course-count").html(tempString);
				}
				else {
					if (tempIndex == $('.carousel-item').length) {
						tempIndex = 1;
						var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
						$("#course-count").html(tempString);
					}
					else {
						tempIndex++;
						var tempString = tempIndex + " " + secondString + " " + ($('.carousel-item').length) + " " + thirdString;
						$("#course-count").html(tempString);
					}
				}
			}
		});
	});

});



$(document).ready(function () {
	$(".block_recommenda .coursename-container").dotdotdot({
		height: 70,
		fallbackToLetter: true,
		watch: true,
	});
	$(".block_recommenda .coursename-overlay").dotdotdot({
		height: 70,
		fallbackToLetter: true,
		watch: true,
	});
	$(".block_recommenda .mobile-coursename").dotdotdot({
		height: 70,
		fallbackToLetter: true,
		watch: true,
	});
	$(".block_recommenda .summary-overlay").dotdotdot({
		height: 100,
		fallbackToLetter: true,
		watch: true,
	});
	$(".block_recommenda .carousel-inner .active.carousel-item").each(function (index) {
		if (index != 0) {
			$(this).removeClass('active');
		}
	});
	$(".block_recommenda .courseimage-overlay").css({ 'display': 'none' });
	$(".block_recommenda .courseimage-overlay").css({ 'visibility': 'visible' });
});

require(['jquery', 'theme_boost/carousel'], function ($) {
	$(document).ready(function () {
		$('.block_recommenda #myCarousel').on('slide.bs.carousel', function (e) {
			/*
			CC 2.0 License Iatek LLC 2018
			Attribution required
		*/
			var $e = $(e.relatedTarget);
			var idx = $e.index();
			var itemsPerSlide = 3;
			var totalItems = $('.block_recommenda .carousel-item').length;

			if (idx >= totalItems - (itemsPerSlide - 1)) {
				var it = itemsPerSlide - (totalItems - idx);
				for (var i = 0; i < it; i++) {
					// append slides to end
					if (e.direction == "left") {
						$('.block_recommenda .carousel-item').eq(i).appendTo('.carousel-inner');
					}
					else {
						$('.block_recommenda .carousel-item').eq(0).appendTo('.carousel-inner');
					}
				}
			}
		});

	});
});

$(document).ready(function () {
	$(".block_recommenda .courseimage").hover(function () {

		var imageId = '.block_recommenda #' + $(this).attr('id') + ' > .courseimage-overlay';
		var nameId = '.block_recommenda #' + $(this).attr('id') + ' > .coursename-overlay';
		var summaryId = '.block_recommenda #' + $(this).attr('id') + ' > .summary-overlay';

		var imageOverlay = $(imageId);
		var nameOverlay = $(nameId);
		var summaryOverlay = $(summaryId);

		imageOverlay.css({ 'opacity': 0, 'height': '40px' }).show().animate({ 'opacity': 1, 'height': '250px' }, 300);
		nameOverlay.css({ 'opacity': 0 }).show().animate({ 'opacity': 1 }, 300);
		summaryOverlay.css({ 'opacity': 0 }).show().animate({ 'opacity': 1 }, 600);

	}, function () {
		var imageId = '.block_recommenda #' + $(this).attr('id') + ' > .courseimage-overlay';
		var imageOverlay = $(imageId);

		imageOverlay.fadeOut(200, function () { $(this).hide(); });
	});
});

require(['jquery'], function ($) {
	$(document).ready(function () {
		var divRecommenda = $('.block_recommenda');

		// First execution
		if ($('.carousel-item').length >= 1) {

			if (divRecommenda.width() > 720 && $('.carousel-item').length >= 3) {
				if ($('.carousel-item').length == 3) {
					$('.carousel-control-prev').hide();
					$('.carousel-control-next').hide();
				}
				else {
					$('.carousel-control-prev').show();
					$('.carousel-control-next').show();
				}
				if (!$('.block_recommenda .carousel-item').hasClass('col-md-4')) {
					$('.block_recommenda .carousel-item').addClass('col-md-4');
				}
			}
			else if ((divRecommenda.width() > 529 && divRecommenda.width() <= 720) && $('.carousel-item').length >= 2
				|| (divRecommenda.width() > 529 && $('.carousel-item').length == 2)) {
				if ($('.carousel-item').length == 2) {
					$('.carousel-control-prev').hide();
					$('.carousel-control-next').hide();
				}
				else {
					$('.carousel-control-prev').show();
					$('.carousel-control-next').show();
				}
				if ($('.block_recommenda .carousel-item').hasClass('col-md-4')) {
					$('.block_recommenda .carousel-item').removeClass('col-md-4');
				}
				if (!$('.block_recommenda .carousel-item').hasClass('col-sm-6')) {
					$('.block_recommenda .carousel-item').addClass('col-sm-6');
				}
			}
			else if ((divRecommenda.width() <= 529 && $('.carousel-item').length >= 1) || $('.carousel-item').length == 1) {
				if ($('.carousel-item').length == 1) {
					$('.carousel-control-prev').hide();
					$('.carousel-control-next').hide();
				}
				else {
					$('.carousel-control-prev').show();
					$('.carousel-control-next').show();
				}
				if ($('.block_recommenda .carousel-item').hasClass('col-md-4')) {
					$('.block_recommenda .carousel-item').removeClass('col-md-4');
				}
				if ($('.block_recommenda .carousel-item').hasClass('col-sm-6')) {
					$('.block_recommenda .carousel-item').removeClass('col-sm-6');
				}
			}
		}

		// Watching div resize

		new ResizeSensor(jQuery('.block_recommenda'), function () {
			if ($('.block_recommenda .form-items').width() < 680) {
				if (!$('.block_recommenda .form-items .form-item').hasClass('wd100')) {
					$('.block_recommenda .form-items .form-item').addClass('wd100');
				}
			}
			else {
				if ($('.block_recommenda .form-items .form-item').hasClass('wd100')) {
					$('.block_recommenda .form-items .form-item').removeClass('wd100');
				}
			}
			if ($('.carousel-item').length >= 1) {
				if (divRecommenda.width() > 720 && $('.carousel-item').length >= 3) {
					if ($('.carousel-item').length == 3) {
						$('.carousel-control-prev').hide();
						$('.carousel-control-next').hide();
					}
					else {
						$('.carousel-control-prev').show();
						$('.carousel-control-next').show();
					}
					if (!$('.block_recommenda .carousel-item').hasClass('col-md-4')) {
						$('.block_recommenda .carousel-item').addClass('col-md-4');
					}
				}
				else if ((divRecommenda.width() > 529 && divRecommenda.width() <= 720) && $('.carousel-item').length >= 2
					|| (divRecommenda.width() > 529 && $('.carousel-item').length == 2)) {
					if ($('.carousel-item').length == 2) {
						$('.carousel-control-prev').hide();
						$('.carousel-control-next').hide();
					}
					else {
						$('.carousel-control-prev').show();
						$('.carousel-control-next').show();
					}
					if ($('.block_recommenda .carousel-item').hasClass('col-md-4')) {
						$('.block_recommenda .carousel-item').removeClass('col-md-4');
					}
					if (!$('.block_recommenda .carousel-item').hasClass('col-sm-6')) {
						$('.block_recommenda .carousel-item').addClass('col-sm-6');
					}

				}
				else if ((divRecommenda.width() <= 529 && $('.carousel-item').length >= 1) || $('.carousel-item').length == 1) {
					if ($('.carousel-item').length == 1) {
						$('.carousel-control-prev').hide();
						$('.carousel-control-next').hide();
					}
					else {
						$('.carousel-control-prev').show();
						$('.carousel-control-next').show();
					}
					
					if ($('.block_recommenda .carousel-item').hasClass('col-md-4')) {
						$('.block_recommenda .carousel-item').removeClass('col-md-4');
					}
					if ($('.block_recommenda .carousel-item').hasClass('col-sm-6')) {
						$('.block_recommenda .carousel-item').removeClass('col-sm-6');
					}
				}
			}
		});
	});
});


