<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($_COOKIE['compare_ids'])) {
    $compare_ids = array();
} else {
    $compare_ids = $_COOKIE['compare_ids'];
}

$filter_options = stm_get_single_car_listings();
$compareLink = get_theme_mod("compare_archive");

$empty_cars = 3 - count($compare_ids);
$counter = 0;
?>

<div class="stm_compare_cars_footer_modal" style="display: <?php echo (count($compare_ids) == 0) ? "none;" : "block;";?>">

    <div class="stm-mc-header-wrap">
        <div class="stm-mc-header-icon-wrap ico-speed">
            <i class="icon-speedometr2"></i>
            <div class="stm-compare-badge" <?php if(count($compare_ids) > 0) echo 'style="display: block;"'; ?>><?php echo count($compare_ids); ?></div>
        </div>
        <div class="stm-mc-header-title-wrap">
            <div class="stm-mc-title"><?php esc_html_e('Vehicle Compare', 'stm_vehicles_listing'); ?></div>
        </div>
        <div class="stm-mc-header-icon-wrap arrow">
            <i class="fa fa-chevron-up" aria-hidden="true"></i>
        </div>

    </div>
    <div class="stm-compare-list-wrap">
        <?php if (!empty($compare_ids) or count($compare_ids) != 0): ?>
            <?php $args = array(
                'post_type' => 'listings',
                'post_status' => 'publish',
                'posts_per_page' => 3,

                'post__in' => $compare_ids,
            );
            $compares = new WP_Query($args);
            ?>
            <ul class="stm-mc-items-wrap">
                <?php while ($compares->have_posts()): $compares->the_post(); ?>
                    <?php $carImg = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id())); ?>
                    <li class="stm-mc-item-wrap">
                        <div class="stm-mc-item-img">
                            <img src="<?php echo esc_url($carImg[0])?>" />
                        </div>
                        <div class="stm-mc-item-title">
                            <?php echo get_the_title(); ?>
                        </div>
                        <div class="stm-mc-item-remove">
                            <button class="button add-to-compare-modal" data-id="<?php echo get_the_id(); ?>" data-title="<?php echo get_the_title(); ?>">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                    </li> <!--row-->
                <?php endwhile; ?>
                <li class="stm-mc-item-wrap">
                    <a href="<?php echo esc_url(get_page_link($compareLink));?>" class="stm-compare-btn"><?php esc_html_e('Compare', 'stm_vehicles_listing'); ?></a>
                </li>
            </ul>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
</div> <!--container-->

<script type="text/javascript">
    /*jQuery(document).ready(function ($) {

        stm_equal_cols();

        $('.compare-value-hover').hover(function () {
            var dataValue = $(this).data('value');
            $('.compare-value-hover[data-value = ' + dataValue + ']').addClass('hovered');
        }, function () {
            $('.compare-value-hover').removeClass('hovered');
        })

        $(window).load(function () {
            stm_equal_cols();
        })

        function stm_equal_cols() {
            var colHeight = 0;
            $('.stm_compare_col_top').each(function () {
                var currentColHeight = $(this).outerHeight();

                if (currentColHeight > colHeight) {
                    colHeight = currentColHeight;
                }
            });

            $('.stm_compare_col_top').css({
                'min-height': colHeight + 'px'
            });

            $('.compare-options').css({
                'margin-top': colHeight + 20 + 'px'
            });

        }

    })*/
</script>