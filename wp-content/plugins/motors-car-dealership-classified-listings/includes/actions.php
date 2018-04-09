<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('template_redirect', 'stm_listings_template_actions');

function stm_listings_template_actions($template)
{

    if ($action = stm_listings_input('ajax_action')) {
        switch ($action) {
            case 'listings-result':
                stm_listings_ajax_results();
                break;
            case 'listings-binding':
                stm_listings_binding_results();
                break;
            case 'listings-items':
                stm_listings_items();
                break;
        }
    }
}


/**
 * Ajax filter cars
 */
function stm_listings_ajax_results()
{
    $r = stm_listings_filter();

    ob_start();
    stm_listings_load_results();
    $r['html'] = ob_get_clean();
    $r = json_encode($r);

    echo apply_filters('stm_listings_ajax_results', $r);
    exit;
}

/**
 * Ajax filter binding
 */
function stm_listings_binding_results()
{
    $r = stm_listings_filter();

    $r = json_encode($r);

    echo apply_filters('stm_listings_binding_results', $r);
    exit;
}

/**
 * Ajax filter items
 */
function stm_listings_items()
{

    ob_start();
    stm_listings_load_results();
    $r['html'] = ob_get_clean();

    $r = json_encode($r);

    echo apply_filters('stm_listings_items', $r);
    exit;
}

function stm_listings_ajax_save_user_data()
{

    $response = array();

    if (!is_user_logged_in()) {
        die('You are not logged in');
    }

    $got_error_validation = false;
    $error_msg = esc_html__('Settings Saved.', 'stm_vehicles_listing');

    $user_current = wp_get_current_user();
    $user_id = $user_current->ID;
    $user = stm_get_user_custom_fields($user_id);

    /*Get current editing values*/
    $user_mail = stm_listings_input('stm_email', $user['email']);
    $user_mail = sanitize_email($user_mail);
    /*Socials*/
    $socs = array('facebook', 'twitter', 'linkedin', 'youtube');
    $socials = array();
    if (empty($user['socials'])) {
        $user['socials'] = array();
    }
    foreach ($socs as $soc) {
        if (empty($user['socials'][$soc])) {
            $user['socials'][$soc] = '';
        }
        $socials[$soc] = stm_listings_input('stm_user_' . $soc, $user['socials'][$soc]);
    }

    $password_check = false;
    if (!empty($_POST['stm_confirm_password'])) {
        $password_check = wp_check_password($_POST['stm_confirm_password'], $user_current->data->user_pass, $user_id);
    }

    if (!$password_check and !empty($_POST['stm_confirm_password'])) {
        $got_error_validation = true;
        $error_msg = esc_html__('Confirmation password is wrong', 'stm_vehicles_listing');
    }

    $demo = stm_is_site_demo_mode();

    if ($password_check and !$demo) {
        //Editing/adding user filled fields
        /*Image changing*/
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
        if (!empty($_FILES['stm-avatar'])) {
            $file = $_FILES['stm-avatar'];
            if (is_array($file) and !empty($file['name'])) {
                $ext = pathinfo($file['name']);
                $ext = $ext['extension'];
                if (in_array($ext, $allowed)) {

                    $upload_dir = wp_upload_dir();
                    $upload_url = $upload_dir['url'];
                    $upload_path = $upload_dir['path'];


                    /*Upload full image*/
                    if (!function_exists('wp_handle_upload')) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                    }
                    $original_file = wp_handle_upload($file, array('test_form' => false));

                    if (!is_wp_error($original_file)) {
                        $image_user = $original_file['file'];
                        /*Crop image to square from full image*/
                        $image_cropped = image_make_intermediate_size($image_user, 160, 160, true);

                        /*Delete full image*/
                        if (file_exists($image_user)) {
                            unlink($image_user);
                        }

                        if (!$image_cropped) {
                            $got_error_validation = true;
                            $error_msg = esc_html__('Error, please try again', 'motors');

                        } else {

                            /*Get path and url of cropped image*/
                            $user_new_image_url = $upload_url . '/' . $image_cropped['file'];
                            $user_new_image_path = $upload_path . '/' . $image_cropped['file'];

                            /*Delete from site old avatar*/

                            $user_old_avatar = get_the_author_meta('stm_user_avatar_path', $user_id);
                            if (!empty($user_old_avatar) and $user_new_image_path != $user_old_avatar and file_exists($user_old_avatar)) {

                                /*Check if prev avatar exists in another users except current user*/
                                $args = array(
                                    'meta_key' => 'stm_user_avatar_path',
                                    'meta_value' => $user_old_avatar,
                                    'meta_compare' => '=',
                                    'exclude' => array($user_id),
                                );
                                $users_db = get_users($args);
                                if (empty($users_db)) {
                                    unlink($user_old_avatar);
                                }
                            }

                            /*Set new image tmp*/
                            $user['image'] = $user_new_image_url;


                            /*Update user meta path and url image*/
                            update_user_meta($user_id, 'stm_user_avatar', $user_new_image_url);
                            update_user_meta($user_id, 'stm_user_avatar_path', $user_new_image_path);

                            $response = array();
                            $response['new_avatar'] = $user_new_image_url;

                        }

                    }

                } else {
                    $got_error_validation = true;
                    $error_msg = esc_html__('Please load image with right extension (jpg, jpeg, png and gif)', 'stm_vehicles_listing');
                }
            }
        }

        /*Check if delete*/
        if (empty($_FILES['stm-avatar']['name'])) {
            if (!empty($_POST['stm_remove_img']) and $_POST['stm_remove_img'] == 'delete') {
                $user_old_avatar = get_the_author_meta('stm_user_avatar_path', $user_id);
                /*Check if prev avatar exists in another users except current user*/
                $args = array(
                    'meta_key' => 'stm_user_avatar_path',
                    'meta_value' => $user_old_avatar,
                    'meta_compare' => '=',
                    'exclude' => array($user_id),
                );
                $users_db = get_users($args);
                if (empty($users_db)) {
                    unlink($user_old_avatar);
                }
                update_user_meta($user_id, 'stm_user_avatar', '');
                update_user_meta($user_id, 'stm_user_avatar_path', '');

                $response['new_avatar'] = '';
            }
        }

        /*Change email*/
        $new_user_data = array(
            'ID' => $user_id,
            'user_email' => $user_mail
        );

        /*Change email visiblity*/
        if (!empty($_POST['stm_show_mail']) and $_POST['stm_show_mail'] == 'on') {
            update_user_meta($user_id, 'stm_show_email', 'show');
        } else {
            update_user_meta($user_id, 'stm_show_email', '');
        }

        if (!empty($_POST['stm_new_password']) and !empty($_POST['stm_new_password_confirm'])) {
            if ($_POST['stm_new_password_confirm'] == $_POST['stm_new_password']) {
                $new_user_data['user_pass'] = $_POST['stm_new_password'];
            } else {
                $got_error_validation = true;
                $error_msg = esc_html__('New password not saved, because of wrong confirmation.', 'stm_vehicles_listing');
            }
        }

        $user_error = wp_update_user($new_user_data);
        if (is_wp_error($user_error)) {
            $got_error_validation = true;
            $error_msg = $user_error->get_error_message();
        }

        /*Change fields with secondary privilegy*/
        /*POST key => user_meta_key*/
        $changed_info = array(
            'stm_first_name' => 'first_name',
            'stm_last_name' => 'last_name',
            'stm_phone' => 'stm_phone',
            'stm_user_facebook' => 'stm_user_facebook',
            'stm_user_twitter' => 'stm_user_twitter',
            'stm_user_linkedin' => 'stm_user_linkedin',
            'stm_user_youtube' => 'stm_user_youtube',
        );

        foreach ($changed_info as $change_to_key => $change_info) {
            if (!empty($_POST[$change_to_key])) {
                $escaped_value = sanitize_text_field($_POST[$change_to_key]);
                update_user_meta($user_id, $change_info, $escaped_value);
            }
        }

    } else {
        if ($demo) {
            $got_error_validation = true;
            $error_msg = esc_html__('Site is on demo mode', 'stm_vehicles_listing');
        }
    }

    $response['error'] = $got_error_validation;
    $response['error_msg'] = $error_msg;

    $response = json_encode($response);
    echo $response;
    exit;
}

add_action('wp_ajax_stm_listings_ajax_save_user_data', 'stm_listings_ajax_save_user_data');

//Ajax request test drive
function stm_ajax_get_car_price()
{
    $response['errors'] = array();

    if (!filter_var($_POST['name'], FILTER_SANITIZE_STRING)) {
        $response['errors']['name'] = true;
    }
    if (!is_email($_POST['email'])) {
        $response['errors']['email'] = true;
    }
    if (!is_numeric($_POST['phone'])) {
        $response['errors']['phone'] = true;
    }


    if (empty($response['errors']) and !empty($_POST['vehicle_id'])) {
        $response['response'] = esc_html__('Your request was sent', 'motors');
        $response['status'] = 'success';

        //Sending Mail to admin
        add_filter('wp_mail_content_type', 'stm_set_html_content_type');

        $to = get_bloginfo('admin_email');
        $subject = esc_html__('Request car price', 'motors') . ' ' . get_the_title($_POST['vehicle_id']);
        $body = esc_html__('Name - ', 'motors') . $_POST['name'] . '<br/>';
        $body .= esc_html__('Email - ', 'motors') . $_POST['email'] . '<br/>';
        $body .= esc_html__('Phone - ', 'motors') . $_POST['phone'] . '<br/>';

        wp_mail($to, $subject, $body);

        remove_filter('wp_mail_content_type', 'stm_set_html_content_type');
    } else {
        $response['response'] = esc_html__('Please fill all fields', 'motors');
        $response['status'] = 'danger';
    }

    $response = json_encode($response);

    echo $response;
    exit;
}

add_action('wp_ajax_stm_ajax_get_car_price', 'stm_ajax_get_car_price');
add_action('wp_ajax_nopriv_stm_ajax_get_car_price', 'stm_ajax_get_car_price');

function stm_ajax_get_compare_list() {
    if (empty($_COOKIE['compare_ids'])) {
        $compare_ids = array();
    } else {
        $compare_ids = $_COOKIE['compare_ids'];
    }

    $str = "";
    if (!empty($compare_ids) or count($compare_ids) != 0):
        $args = array(
            'post_type' => 'listings',
            'post_status' => 'publish',
            'posts_per_page' => 3,

            'post__in' => $compare_ids,
        );
        $compares = new WP_Query($args);

        $str = $str . '<ul class="stm-mc-items-wrap">';
            while ($compares->have_posts()): $compares->the_post();
                $carImg = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()));
                $str = $str . '<li class="stm-mc-item-wrap">';
                $str = $str . '<div class="stm-mc-item-img">';
                $str = $str . '<img src="' . esc_url($carImg[0]) . '" />';
                $str = $str . '</div>';
                $str = $str . '<div class="stm-mc-item-title">' . get_the_title() . '</div>';
                $str = $str . '<div class="stm-mc-item-remove">';
                $str = $str . '<button class="button add-to-compare-modal" data-id="' . get_the_id() . '" data-title="' . get_the_title() . '">';
                $str = $str . '<i class="fa fa-times" aria-hidden="true"></i>';
                $str = $str . '</button>';
                $str = $str . '</div>';
                $str = $str . '</li>';
            endwhile;
        $str = $str . '<li class="stm-mc-item-wrap">';
        $str = $str . '<a href="' . esc_url(get_page_link(get_theme_mod("compare_archive"))) . '" class="stm-compare-btn">' . esc_html('Compare', 'stm_vehicles_listing') . '</a>';
        $str = $str . '</li>';
        $str = $str . '</ul>';
        wp_reset_postdata();
    endif;
    $response = (strlen($str) > 0) ? $str : "empty";
    wp_send_json($response);
}

add_action('wp_ajax_stm_ajax_get_compare_list', 'stm_ajax_get_compare_list');
add_action('wp_ajax_nopriv_stm_ajax_get_compare_list', 'stm_ajax_get_compare_list');


add_action('init', 'stm_patching_redirect');

function stm_patching_redirect() {
	$patched = get_option('stm_price_patched', '');

	/*If already patched*/
	if(!empty($patched)) {
		return false;
	}

	$patching = false;
	if(isset($_POST['action']) and $_POST['action'] == 'stm_admin_patch_price') {
		$patching = true;
	}

	$listings_created = wp_count_posts(stm_listings_post_type());
	if(!is_wp_error($listings_created)) {
		if(empty($listings_created->publish)) {
			$patched = stm_patch_status('dismiss_patch');
		}
	} else {
		$patched = stm_patch_status('dismiss_patch');
	}

	/*if patch in progress*/
	$current_patching = false;
	if(isset($_GET['page']) and $_GET['page'] == 'patch') {
		$current_patching = true;
	}

	if(empty($patched) and !$current_patching and !$patching) {
		wp_redirect(esc_url_raw(admin_url('edit.php?post_type=listings&page=patch')));
		exit;
	}
}

function stm_patch_status($status)
{
	update_option('stm_price_patched', $status);
	return $status;
}

function stm_admin_patch_price() {
	$r = array();
	$offset = intval($_POST['offset']);

	$args = array(
		'post_type' => stm_listings_post_type(),
		'posts_per_page' => '10',
		'post_status' => 'publish',
		'offset' => $offset,
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => 'stm_genuine_price',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key' => 'price',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key' => 'sale_price',
				'compare' => 'NOT EXISTS'
			)
		)
	);

	$q = new WP_Query($args);
	if($q->have_posts()) {
		while($q->have_posts()) {
			$q->the_post();
			$id = get_the_ID();
			$price = get_post_meta($id, 'price', true);
			$sale_price = get_post_meta($id, 'sale_price', true);

			if(!empty($sale_price)) {
				$price = $sale_price;
			}

			if(!empty($price)) {
				update_post_meta($id, 'stm_genuine_price', $price);
			}
		}
	}

	$new_offset = $offset + 10;

	if($q->found_posts < $new_offset) {
		$new_offset = 'none';
		stm_patch_status('updated');
	}

	$r['offset'] = $new_offset;

	$r = json_encode($r);
	echo $r;

	exit();
}

add_action( 'wp_ajax_stm_admin_patch_price', 'stm_admin_patch_price' );