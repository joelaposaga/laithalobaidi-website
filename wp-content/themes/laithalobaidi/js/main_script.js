(function($){

	$('#home_testimonial_slider').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		prevArrow: '<button class="slick-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>',
		nextArrow: '<button class="slick-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>'
	});

	/* LAO Tab */

	$('.lao_tab > ul > li > a').on('click', function(e){
		var self = $(this);
		var data_content = self.data('tab-content');
		e.preventDefault();
		if (!self.hasClass('active')) {
			$('.lao_tab > ul > li > a').removeClass('active');
			self.addClass('active');
		}

		if (!$(data_content).hasClass('active')) {
			$('.lao_tab .tab_content > div').removeClass('active');
			$(data_content).addClass('active');
		}
	})

})(jQuery);