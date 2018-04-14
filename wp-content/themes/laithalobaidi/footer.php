<?php
/**
 * The template for displaying the footer
 */
?>
<div class="footer">
	<?php dynamic_sidebar( 'footer-1' ); ?>
</div>
</div>
<div class="right-sidebar">
	<?php get_template_part( 'template-parts/content', 'right' ) ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
