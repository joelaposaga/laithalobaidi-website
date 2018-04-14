<?php  

/* Search Car Shorcode */

function lao_search_shortcode($attr) {

	/*$attributes = shortcode_atts(array(
		'search_title' => 'Find your car'
	), $attr);*/

	$display = <<<HEREDOC

			<div class="car-search">
				<form method="post">
					<div class="form-assets left_s select">
						<select>
							<option>Condition</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Car Type</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Car Make</option>
							<option>Aston Martin</option>
							<option>Honda</option>
							<option>Hyundai</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Car Model</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Year</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Quantity</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Price Range</option>
						</select>
					</div>
					<div class="form-assets left_s submit">
						<button type="submit"><i class="fa fa-search" aria-hidden="true"></i> Search Vehicle</button>
					</div>
				</form>
			</div>

HEREDOC;

return $display;
}

add_shortcode('car_search', 'lao_search_shortcode');
 
/* Get Testimonials */

function lao_get_testimonial($attr) {
	$args = array(
		'post_type' => 'testimonials',
		'post_status' => 'publish',
		'order' => 'DESC',
		'orderby' => 'date',
	);

	$testimonials = new WP_Query($args);

	if ($testimonials->have_posts()) :
		while ($testimonials->have_posts()) :
			$testimonials->the_post();

			$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');

			/*$display_img_url = ($featured_img_url == '' ? $featured_img_url : site_url() . '/wp-content/uploads/2018/04/avatar.png');*/

			$display .= '<div>
							<div class="single_testimonial">
								<div>'. get_the_content() .'</div>
								<div><div><img src="'. esc_url($featured_img_url ? $featured_img_url : site_url() . '/wp-content/uploads/2018/04/avatar.png') .'"></div><span>'. get_the_title() .'</span></div>
							</div>
						</div>';
		endwhile;
	endif;

	return $display;

}

add_shortcode( 'get_testimonials', 'lao_get_testimonial' );

/* Single page car tab info */

function lao_single_page_car_tab_info() {
	ob_start();
	?>

	<div class="single_info lao_tab">
		<ul>
			<li><a href="" class="active" data-tab-content="#tab-1">Specifications</a></li>
			<li><a href="" data-tab-content="#tab-2">Features</a></li>
			<li><a href="" data-tab-content="#tab-3">Overview</a></li>
			<li><a href="" data-tab-content="#tab-4">Contact Us</a></li>
		</ul>
		<div class="tab_content">
			<div id="tab-1" class="active">
				<?php stm_listings_load_template('single-car/car-data'); ?>
			</div>
			<div id="tab-2">
				This is tab 2
			</div>
			<div id="tab-3">
				This is tab 3
			</div>
			<div id="tab-4">
				This is tab 4
			</div>
		</div>
	</div>

	<?php

	return ob_get_clean();
}

add_shortcode('show_tab_info', 'lao_single_page_car_tab_info');