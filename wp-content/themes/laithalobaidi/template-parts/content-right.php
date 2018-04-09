<div class="lang-switcher">
	<?php

		$args = array(
			'dropdown' => 1
		);
		 
		pll_the_languages($args);
	?>
</div>
<div class="main-menu">
	<?php  
		$args = array(
			'theme_location' => 'main_menu'
		);

		wp_nav_menu( $args );

	?>
</div>
<div class="contact">
	<?php dynamic_sidebar( 'right-sidebar-1' ); ?>
</div>
<div class="joinus">
	<a href=""><i class="fa fa-file-text-o" aria-hidden="true"></i> Join Us</a>
</div>
