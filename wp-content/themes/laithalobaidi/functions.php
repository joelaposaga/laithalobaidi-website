<?php


function lao_setup() {

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'laith-al-obaidi-featured-image', 2000, 1200, true );
	add_image_size( 'laith-al-obaidi-thumbnail-avatar', 100, 100, true );

	$GLOBALS['content_width'] = 525;

	register_nav_menus( array(
		'main_menu'    => __( 'Main Menu', 'laith-al-obaidi' ),
	) );

	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );

	add_theme_support( 'customize-selective-refresh-widgets' );

	remove_filter('the_content', 'wpautop');

}
add_action( 'after_setup_theme', 'lao_setup' );


function lao_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Left Sidebar', 'laith-al-obaidi' ),
		'id'            => 'left-sidebar-1',
		'description'   => __( 'All left sidebar contents goes here', 'laith-al-obaidi' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Right Sidebar', 'laith-al-obaidi' ),
		'id'            => 'right-sidebar-1',
		'description'   => __( 'All right sidebar contents goes here', 'laith-al-obaidi' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );

	/*register_sidebar( array(
		'name'          => __( 'Footer 1', 'laith-al-obaidi' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'laith-al-obaidi' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'laith-al-obaidi' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'laith-al-obaidi' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );*/
}
add_action( 'widgets_init', 'lao_widgets_init' );


function twentyseventeen_scripts() {

	wp_enqueue_style( 'lao-style', get_stylesheet_uri() );
	wp_enqueue_style( 'lao-main-style', get_template_directory_uri() . '/css/style.css', array(), '1.0.0', 'all' );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/libs/fontawesome/css/font-awesome.min.css', array(), '1.0.0' );
	wp_enqueue_style( 'bootstrap-grid', get_template_directory_uri() . '/libs/bootstrap-grid/bootstrap-grid.min.css', array(), '1.0.0' );

}
add_action( 'wp_enqueue_scripts', 'twentyseventeen_scripts' );


require (get_template_directory() . '/inc/shortcodes.php');