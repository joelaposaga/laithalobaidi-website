<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function stm_listings_enqueue_scripts_styles()
{
    wp_enqueue_style('font-awesome', STM_LISTINGS_URL . '/assets/css/font-awesome.min.css', array());
    wp_enqueue_style('owl.carousel', STM_LISTINGS_URL . '/assets/css/frontend/owl.carousel.css', array());
    wp_enqueue_style('bootstrap-grid', STM_LISTINGS_URL . '/assets/css/frontend/grid.css', array());
    wp_enqueue_style('listings-frontend', STM_LISTINGS_URL . '/assets/css/frontend/frontend_styles.css', array());
    wp_enqueue_style('listings-add-car', STM_LISTINGS_URL . '/assets/css/frontend/add_a_car.css', array());
    wp_enqueue_style('light-gallery', STM_LISTINGS_URL . '/assets/css/frontend/lightgallery.min.css', array());
    wp_enqueue_style('modal-style', STM_LISTINGS_URL . '/assets/css/frontend/modal-style.css', array());
    wp_enqueue_style('modal-bootstrap', STM_LISTINGS_URL . '/assets/css/bootstrap/bootstrap.min.css', array());
    wp_enqueue_style('stm-icon-font', STM_LISTINGS_URL . '/assets/css/frontend/stm-ico-style.css', array());
	wp_enqueue_style( 'datetimepicker', STM_LISTINGS_URL . '/assets/css/jquery.stmdatetimepicker.css', array());

    wp_enqueue_script('jquery_cookie', STM_LISTINGS_URL . '/assets/js/frontend/jquery.cookie.js', array('jquery'), null, true);
    wp_enqueue_script('owl.carousel', STM_LISTINGS_URL . '/assets/js/frontend/owl.carousel.js', array('jquery'), null, true);
    wp_enqueue_script('light-gallery', STM_LISTINGS_URL . '/assets/js/frontend/lightgallery.min.js', array('jquery'), null, true);
    wp_enqueue_script('listings-add-car', STM_LISTINGS_URL . '/assets/js/frontend/add_a_car.js', array('jquery', 'jquery-ui-droppable'), null, true);
    wp_enqueue_script('listings-init', STM_LISTINGS_URL . '/assets/js/frontend/init.js', array('jquery', 'jquery-ui-slider'), null, true);
    wp_enqueue_script('listings-filter', STM_LISTINGS_URL . '/assets/js/frontend/filter.js', array('listings-init'), null, true);
    wp_enqueue_script('modal-bootstrap-js', STM_LISTINGS_URL . '/assets/js/bootstrap/bootstrap.min.js', array('listings-init'), null, true);
	wp_enqueue_script( 'datetimepicker-js', STM_LISTINGS_URL . '/assets/js/jquery.stmdatetimepicker.js', array( 'jquery' ), null, true );
    wp_enqueue_script('compare-modal', STM_LISTINGS_URL . '/assets/js/frontend/compare_modal.js', array('listings-init'), null, true);

}

add_action('wp_enqueue_scripts', 'stm_listings_enqueue_scripts_styles');