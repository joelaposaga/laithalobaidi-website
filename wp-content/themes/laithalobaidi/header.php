<?php
/**
 * The header for our theme
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div class="left-sidebar">
		<?php get_template_part( 'template-parts/content', 'left' ); ?>
	</div>
	<div class="main">
	<?php  

		if (is_singular( 'listings' )) {
			echo "hello World";
		}

	?>
