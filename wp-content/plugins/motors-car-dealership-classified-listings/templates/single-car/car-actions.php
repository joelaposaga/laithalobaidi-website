<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($_COOKIE['compare_ids'])) {
    $_COOKIE['compare_ids'] = array();
}
$cars_in_compare = $_COOKIE['compare_ids'];

$stock_number = get_post_meta(get_the_id(), 'stock_number', true);
$car_brochure = get_post_meta(get_the_ID(), 'car_brochure', true);

$certified_logo_1 = get_post_meta(get_the_ID(), 'certified_logo_1', true);
$certified_logo_2 = get_post_meta(get_the_ID(), 'certified_logo_2', true);

//Show car actions
$show_stock = get_theme_mod('show_stock', true);
$show_test_drive = get_theme_mod('show_test_drive', true);
$show_compare = get_theme_mod('show_compare', true);
$show_pdf = get_theme_mod('show_pdf', true);
$show_certified_logo_1 = get_theme_mod('show_certified_logo_1', false);
$show_certified_logo_2 = get_theme_mod('show_certified_logo_2', false);
?>

<div class="single-car-actions">
    <ul class="list-unstyled clearfix">

        <!--Stock num-->
        <?php if (!empty($stock_number) and !empty($show_stock) and $show_stock): ?>
            <li>
                <div class="stock-num heading-font"><span><?php echo esc_html__('stock', 'stm_vehicles_listing'); ?>
                        # </span><?php echo esc_attr($stock_number); ?></div>
            </li>
        <?php endif; ?>
		<!--Schedule-->
		<?php if (!empty($show_test_drive) and $show_test_drive): ?>
			<li>
				<a href="#" class="car-action-unit stm-schedule" data-toggle="modal" data-target="#test-drive">
					<i class="stm-icon-steering_wheel"></i>
					<?php esc_html_e('Schedule Test Drive', 'motors'); ?>
				</a>
			</li>
		<?php endif; ?>
        <!--COmpare-->
        <?php if (!empty($show_compare) and $show_compare): ?>
            <li>
                <?php if (in_array(get_the_ID(), $cars_in_compare)): ?>
                    <a
                        href="#"
                        class="car-action-unit add-to-compare active"
                        title="<?php esc_html_e('Remove from compare', 'stm_vehicles_listing'); ?>"
                        data-id="<?php echo esc_attr(get_the_ID()); ?>"
                        data-title="<?php echo esc_attr(get_the_title()); ?>">
                        <?php esc_html_e('Remove from compare', 'stm_vehicles_listing'); ?>
                    </a>
                <?php else: ?>
                    <a
                        href="#"
                        class="car-action-unit add-to-compare"
                        title="<?php esc_html_e('Add to compare', 'stm_vehicles_listing'); ?>"
                        data-id="<?php echo esc_attr(get_the_ID()); ?>"
                        data-title="<?php echo esc_attr(get_the_title()); ?>">
                        <?php esc_html_e('Add to compare', 'stm_vehicles_listing'); ?>
                    </a>
                <?php endif; ?>
                <script type="text/javascript">
                    var stm_label_add = "<?php esc_html_e('Add to compare', 'stm_vehicles_listing'); ?>";
                    var stm_label_remove = "<?php esc_html_e('Remove from compare', 'stm_vehicles_listing'); ?>";
                </script>
            </li>
        <?php endif; ?>

        <!--PDF-->
        <?php if (!empty($show_pdf) and $show_pdf): ?>
            <?php if (!empty($car_brochure)): ?>
                <li>
                    <a
                        href="<?php echo esc_url(wp_get_attachment_url($car_brochure)); ?>"
                        class="car-action-unit stm-brochure"
                        title="<?php esc_html_e('Download brochure', 'stm_vehicles_listing'); ?>"
                        download>
                        <?php esc_html_e('Car brochure', 'stm_vehicles_listing'); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <!--Certified Logo 1-->
        <?php if (!empty($certified_logo_1) and !empty($show_certified_logo_1) and $show_certified_logo_1): ?>
            <?php
            $certified_logo_1 = wp_get_attachment_image_src($certified_logo_1, 'stm-img-255-135');
            if (!empty($certified_logo_1[0])) {
                $certified_logo_1 = $certified_logo_1[0];
            }
            ?>
            <li class="certified-logo-1">
                <img src="<?php echo esc_url($certified_logo_1); ?>"
                     alt="<?php esc_html_e('Logo 1', 'stm_vehicles_listing'); ?>"/>
            </li>
        <?php endif; ?>

        <!--Certified Logo 2-->
        <?php if (!empty($certified_logo_2) and !empty($show_certified_logo_2) and $show_certified_logo_2): ?>
            <?php
            $certified_logo_2 = wp_get_attachment_image_src($certified_logo_2, 'stm-img-255-135');
            if (!empty($certified_logo_2[0])) {
                $certified_logo_2 = $certified_logo_2[0];
            }
            ?>
            <li class="certified-logo-2">
                <img src="<?php echo esc_url($certified_logo_2); ?>"
                     alt="<?php esc_html_e('Logo 2', 'stm_vehicles_listing'); ?>"/>
            </li>
        <?php endif; ?>

    </ul>
</div>