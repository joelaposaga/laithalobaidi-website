<?php  

/* Search Car Shorcode */

function lao_search_shortcode($attr) {

	/*$attributes = shortcode_atts(array(
		'search_title' => 'Find your car'
	), $attr);*/

	$display = <<<HEREDOC

			<div class="car-search">
				<form method="post">
					<div class="form-assets">
						<select>
							<option>Car Status</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Car Type</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Brand</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Model</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Year</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Code</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Quantity</option>
						</select>
					</div>
					<div class="form-assets">
						<select>
							<option>Quantity</option>
						</select>
					</div>
					<div class="form-assets">
						<input type="submit" name="" value="Search Vehicle">
					</div>
				</form>
			</div>

HEREDOC;

return $display;
}

add_shortcode('car_search', 'lao_search_shortcode');