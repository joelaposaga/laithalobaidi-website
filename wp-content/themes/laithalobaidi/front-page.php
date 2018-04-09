<?php
/**
 * The front page template file
 */

get_header(); ?>


<?php
// Show the selected front page content.
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		the_content();
	endwhile;
/*else :
	_e('')*/
endif;
?>


<?php get_footer();
