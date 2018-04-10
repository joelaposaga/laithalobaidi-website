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
							<option>Car Status</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Car Type</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Brand</option>
							<option>Aston Martin</option>
							<option>Honda</option>
							<option>Hyundai</option>
						</select>
					</div>
					<div class="form-assets left_s select">
						<select>
							<option>Model</option>
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
							<option>Quantity</option>
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