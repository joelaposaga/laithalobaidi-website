<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function detect_plugin_activation( $plugin, $network_activation ) {
	update_option('stm_price_patched', 'updated');
}
add_action( 'activated_plugin', 'detect_plugin_activation', 10, 2 );

/**
 * Get filter configuration
 *
 * @param array $args
 *
 * @return array
 */
function stm_listings_attributes($args = array())
{
    $args = wp_parse_args($args, array(
        'where' => array(),
        'key_by' => ''
    ));

    $result = array();
    $data = array_filter((array)get_option('stm_vehicle_listing_options'));

    foreach ($data as $key => $_data) {
        $passed = true;
        foreach ($args['where'] as $_field => $_val) {
            if (array_key_exists($_field, $_data) && $_data[$_field] != $_val) {
                $passed = false;
                break;
            }
        }

        if ($passed) {
            if ($args['key_by']) {
                $result[$_data[$args['key_by']]] = $_data;
            } else {
                $result[] = $_data;
            }
        }
    }

    return apply_filters('stm_listings_attributes', $result, $args);
}

/**
 * Get single attribute configuration by taxonomy slug
 *
 * @param $taxonomy
 *
 * @return array|mixed
 */
function stm_listings_attribute($taxonomy)
{
    $attributes = stm_listings_attributes(array('key_by' => 'slug'));
    if (array_key_exists($taxonomy, $attributes)) {
        return $attributes[$taxonomy];
    }

    return array();
}

/**
 * Get all terms grouped by taxonomy for the filter
 *
 * @return array
 */
function stm_listings_filter_terms()
{
    static $terms;

    if (isset($terms)) {
        return $terms;
    }

    $filters = stm_listings_attributes(array('where' => array('use_on_car_filter' => true), 'key_by' => 'slug'));

    $numeric = array_keys(stm_listings_attributes(array(
        'where' => array(
            'use_on_car_filter' => true,
            'numeric' => true
        ),
        'key_by' => 'slug'
    )));

    $_terms = get_terms(array(
        'taxonomy' => $numeric,
        'hide_empty' => false,
        'update_term_meta_cache' => false,
    ));

    $taxes = array_diff(array_keys($filters), $numeric);
    $taxes = apply_filters('stm_listings_filter_taxonomies', $taxes);

    $_terms = array_merge($_terms, get_terms(array(
        'taxonomy' => $taxes,
        'hide_empty' => true,
        'update_term_meta_cache' => false,
    )));

    $terms = array();

    foreach ($taxes as $tax) {
        $terms[$tax] = array();
    }

    foreach ($_terms as $_term) {
        $terms[$_term->taxonomy][$_term->slug] = $_term;
    }

    $terms = apply_filters('stm_listings_filter_terms', $terms);

    return $terms;
}

/**
 * Drop-down options grouped by attribute for the filter
 *
 * @return array
 */
function stm_listings_filter_options()
{
    static $options;

    if (isset($options)) {
        return $options;
    }

    $filters = stm_listings_attributes(array('where' => array('use_on_car_filter' => true), 'key_by' => 'slug'));
    $terms = stm_listings_filter_terms();
    $options = array();

    foreach ($terms as $tax => $_terms) {
        $_filter = isset($filters[$tax]) ? $filters[$tax] : array();
        $options[$tax] = _stm_listings_filter_attribute_options($tax, $_terms);

        if (empty($_filter['numeric'])) {
            $_remaining = stm_listings_options_remaining($terms[$tax], stm_listings_query());
            $to_disable = array_filter(array_keys(array_diff_key($options[$tax], (array)$_remaining)));
            foreach ($to_disable as $_key) {
                $options[$tax][$_key]['disabled'] = true;
            }
        }
    }

    $options = apply_filters('stm_listings_filter_options', $options);

    return $options;
}

/**
 * Get list of attribute options filtered by query
 *
 * @param array $terms
 * @param WP_Query $from
 *
 * @return array
 */
function stm_listings_options_remaining($terms, $from = null)
{
    /** @var WP_Query $from */
    $from = is_null($from) ? $GLOBALS['wp_query'] : $from;

    if (empty($terms) || (!count($from->get('meta_query', array())) && !count($from->get('tax_query')))) {
        return array();
    }

    global $wpdb;
    $meta_query = new WP_Meta_Query($from->get('meta_query', array()));
    $tax_query = new WP_Tax_Query($from->get('tax_query', array()));
    $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
    $tax_query_sql = $tax_query->get_sql($wpdb->posts, 'ID');
    $term_ids = wp_list_pluck($terms, 'term_taxonomy_id');
    $post_type = $from->get('post_type');

    // Generate query
    $query = array();
    $query['select'] = "SELECT term_taxonomy.term_taxonomy_id, COUNT( {$wpdb->posts}.ID ) as count";
    $query['from'] = "FROM {$wpdb->posts}";
    $query['join'] = "INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id";
    $query['join'] .= "\nINNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )";
    //$query['join'] .= "\nINNER JOIN {$wpdb->terms} AS terms USING( term_id )";
    $query['join'] .= "\n" . $tax_query_sql['join'] . $meta_query_sql['join'];
    $query['where'] = "WHERE {$wpdb->posts}.post_type IN ( '{$post_type}' ) AND {$wpdb->posts}.post_status = 'publish' ";
    $query['where'] .= "\n" . $tax_query_sql['where'] . $meta_query_sql['where'];
    $query['where'] .= "\nAND term_taxonomy.term_taxonomy_id IN (" . implode(',', array_map('absint', $term_ids)) . ")";
    $query['group_by'] = "GROUP BY term_taxonomy.term_taxonomy_id";

    $query = apply_filters('stm_listings_options_remaining_query', $query);
    $query = join("\n", $query);

    $results = $wpdb->get_results($query);
    $results = array_filter(wp_list_pluck($results, 'count', 'term_taxonomy_id'));

    $terms = wp_list_pluck($terms, 'slug', 'term_taxonomy_id');
    $remaining = array_intersect_key($terms, $results);
    $remaining = array_flip($remaining);

    return $remaining;
}

/**
 * Filter configuration array
 *
 * @return array
 */
function stm_listings_filter()
{
    $query = stm_listings_query();
    $total = $query->found_posts;
    $filters = stm_listings_attributes(array('where' => array('use_on_car_filter' => true), 'key_by' => 'slug'));
    $options = stm_listings_filter_options();
    $terms = stm_listings_filter_terms();

    return apply_filters('stm_listings_filter', compact('options', 'filters', 'total'), $terms);
}

/**
 * Retrieve input data from $_POST, $_GET by path
 *
 * @param $path
 * @param $default
 *
 * @return mixed
 */
function stm_listings_input($path, $default = null)
{

    if (trim($path, '.') == '') {
        return $default;
    }

    foreach (array($_POST, $_GET) as $source) {
        $value = $source;
        foreach (explode('.', $path) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                $value = null;
                break;
            }

            $value = &$value[$key];
        }

        if (!is_null($value)) {
            return $value;
        }
    }

    return $default;
}

/**
 * Current URL with native WP query string parameters ()
 *
 * @return string
 */
function stm_listings_current_url()
{
    global $wp, $wp_rewrite;

    $url = preg_replace("/\/page\/\d+/", '', $wp->request);
    $url = home_url($url . '/');
    if (!$wp_rewrite->permalink_structure) {
        parse_str($wp->query_string, $query_string);

        $leave = array('post_type', 'pagename', 'page_id', 'p');
        $query_string = array_intersect_key($query_string, array_flip($leave));

        $url = trim(add_query_arg($query_string, $url), '&');
        $url = str_replace('&&', '&', $url);
    }

    return $url;
}

function _stm_listings_filter_attribute_options($taxonomy, $_terms)
{

    $attribute = stm_listings_attribute($taxonomy);
    $attribute = wp_parse_args($attribute, array(
        'slug' => $taxonomy,
        'single_name' => '',
        'numeric' => false,
        'slider' => false,
    ));

    $options = array();

    if (!$attribute['numeric']) {


        $options[''] = array(
            'label' => apply_filters('stm_listings_default_tax_name', $attribute['single_name']),
            'selected' => stm_listings_input($attribute['slug']) == null,
            'disabled' => false,
        );

        foreach ($_terms as $_term) {
            $options[$_term->slug] = array(
                'label' => $_term->name,
                'selected' => stm_listings_input($attribute['slug']) == $_term->slug,
                'disabled' => false,
                'count' => $_term->count,
            );
        }
    } else {
        $numbers = array();
        foreach ($_terms as $_term) {
            $numbers[intval($_term->slug)] = $_term->name;
        }
        ksort($numbers);

        if (!empty($attribute['slider'])) {
            foreach ($numbers as $_number => $_label) {
                $options[$_number] = array(
                    'label' => $_label,
                    'selected' => stm_listings_input($attribute['slug']) == $_label,
                    'disabled' => false,
                );
            }
        } else {

            $options[''] = array(
                'label' => sprintf(__('Max %s', 'stm_vehicles_listing'), $attribute['single_name']),
                'selected' => stm_listings_input($attribute['slug']) == null,
                'disabled' => false,
            );

            $_prev = null;
            $_affix = empty($attribute['affix']) ? '' : __($attribute['affix'], 'stm_vehicles_listing');

            foreach ($numbers as $_number => $_label) {

                if ($_prev === null) {
                    $_value = '<' . $_number;
                    $_label = '< ' . $_label . ' ' . $_affix;
                } else {
                    $_value = $_prev . '-' . $_number;
                    $_label = $_prev . '-' . $_label . ' ' . $_affix;
                }

                $options[$_value] = array(
                    'label' => $_label,
                    'selected' => stm_listings_input($attribute['slug']) == $_value,
                    'disabled' => false,
                );

                $_prev = $_number;
            }

            if ($_prev) {
                $_value = '>' . $_prev;
                $options[$_value] = array(
                    'label' => '>' . $_prev . ' ' . $_affix,
                    'selected' => stm_listings_input($attribute['slug']) == $_value,
                    'disabled' => false,
                );
            }
        }
    }

    return $options;
}

if (!function_exists('stm_listings_user_defined_filter_page')) {
    function stm_listings_user_defined_filter_page()
    {
        return apply_filters('stm_listings_inventory_page_id', get_theme_mod('listing_archive', false));
    }
}

function stm_listings_paged_var()
{
    global $wp;

    $paged = null;

    if (isset($wp->query_vars['paged'])) {
        $paged = $wp->query_vars['paged'];
    } elseif (isset($_GET['paged'])) {
        $paged = sanitize_text_field($_GET['paged']);
    }

    return $paged;
}

/**
 * Listings post type identifier
 *
 * @return string
 */
if (!function_exists('stm_listings_post_type')) {
    function stm_listings_post_type()
    {
        return apply_filters('stm_listings_post_type', 'listings');
    }
}

add_action('init', 'stm_listings_init', 1);

function stm_listings_init()
{

    $options = get_option('stm_post_types_options');

	add_action( 'save_post', 'save_metaboxes' );

    $stm_vehicle_options = wp_parse_args($options, array(
        'listings' => array(
            'title' => __('Listings', 'stm_vehicles_listing'),
            'plural_title' => __('Listings', 'stm_vehicles_listing'),
            'rewrite' => 'listings'
        ),
    ));

    register_post_type(stm_listings_post_type(), array(
        'labels' => array(
            'name' => $stm_vehicle_options['listings']['plural_title'],
            'singular_name' => $stm_vehicle_options['listings']['title'],
            'add_new' => __('Add New', 'stm_vehicles_listing'),
            'add_new_item' => __('Add New Item', 'stm_vehicles_listing'),
            'edit_item' => __('Edit Item', 'stm_vehicles_listing'),
            'new_item' => __('New Item', 'stm_vehicles_listing'),
            'all_items' => __('All Items', 'stm_vehicles_listing'),
            'view_item' => __('View Item', 'stm_vehicles_listing'),
            'search_items' => __('Search Items', 'stm_vehicles_listing'),
            'not_found' => __('No items found', 'stm_vehicles_listing'),
            'not_found_in_trash' => __('No items found in Trash', 'stm_vehicles_listing'),
            'parent_item_colon' => '',
            'menu_name' => __($stm_vehicle_options['listings']['plural_title'], 'stm_vehicles_listing'),
        ),
        'menu_icon' => 'dashicons-location-alt',
        'show_in_nav_menus' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'comments', 'excerpt', 'author'),
        'rewrite' => array('slug' => $stm_vehicle_options['listings']['rewrite']),
        'has_archive' => true,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'hierarchical' => false,
        'menu_position' => null,
    ));

    register_post_type('test_drive_request', array(
        'labels' => array(
			'name'               => __( 'Test Drives', 'stm_vehicles_listing' ),
			'singular_name'      => __( 'Test Drives', 'stm_vehicles_listing' ),
			'add_new'            => __( 'Add New', 'stm_vehicles_listing' ),
			'add_new_item'       => __( 'Add New ' . 'Test Drives', 'stm_vehicles_listing' ),
			'edit_item'          => __( 'Edit ' . 'Test Drives', 'stm_vehicles_listing' ),
			'new_item'           => __( 'New ' . 'Test Drives', 'stm_vehicles_listing' ),
			'all_items'          => __( 'All ' . 'Test Drives', 'stm_vehicles_listing' ),
			'view_item'          => __( 'View ' . 'Test Drives', 'stm_vehicles_listing' ),
			'search_items'       => __( 'Search ' . 'Test Drives', 'stm_vehicles_listing' ),
			'not_found'          => __( 'No ' . 'Test Drives' . ' found', 'stm_vehicles_listing' ),
			'not_found_in_trash' => __( 'No ' . 'Test Drives' . '  found in Trash', 'stm_vehicles_listing' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Test Drives', 'stm_vehicles_listing' )
        ),
		'public'             => true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'  	 => 'edit.php?post_type=listings',
		'show_in_nav_menus'  => false,
		'query_var'          => true,
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'          => null,
		'supports'           => array( 'title', 'editor' ),
		'register_meta_box_cb' => 'stm_add_test_drives_metaboxes',
    ));

}

add_filter('get_pagenum_link', 'stm_listings_get_pagenum_link');

function stm_add_test_drives_metaboxes() {
	add_meta_box(
		'test_drive_form',
		__( 'Credentials', 'stm_vehicles_listing' ),
		'display_metaboxes' ,
		'test_drive_request',
		'normal',
		'',
		array(
			'fields' => array(
				'name' => array(
					'label'   => __( 'Name', 'stm_vehicles_listing' ),
					'type'    => 'text'
				),
				'email' => array(
					'label'   => __( 'E-mail', 'stm_vehicles_listing' ),
					'type'    => 'text'
				),
				'phone' => array(
					'label'   => __( 'Phone', 'stm_vehicles_listing' ),
					'type'    => 'text'
				),
				'date' => array(
					'label'   => __( 'Day', 'stm_vehicles_listing' ),
					'type'    => 'text'
				),
			)
		)
	);
}

function display_metaboxes( $post, $metabox ) {

	$fields = $metabox['args']['fields'];

	echo '<input type="hidden" name="stm_custom_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
	echo '<table class="form-table stm">';
	foreach ( $fields as $key => $field ) {
		$meta = get_post_meta( $post->ID, $key, true );
		if( $field['type'] != 'hidden'){
			if( $field['type'] != 'separator'){
				echo '<tr class="stm_admin_'.$key.'"><th><label for="' . $key . '">' . $field['label'] . '</label></th><td>';
			}else{
				echo '<tr><th><h3>' . $field['label'] . '</h3></th><td>';
			}
		}
		switch ( $field['type'] ) {
			case 'text':
				if( empty( $meta ) && ! empty( $field['default'] ) && $post->post_status == 'auto-draft' ){
					$meta = $field['default'];
				}
				echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . $meta . '" />';
				if(isset($field['description'])) {
					echo '<p class="textfield-description">'.$field['description'].'</p>';
				}
				break;
		}
		echo '</td></tr>';
	}
	echo '</table>';

}

function save_metaboxes( $post_id ) {

	if ( ! isset( $_POST['stm_custom_nonce'] ) ) {
		return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return $post_id;
	}
	$metaboxes = array(
		'fields' => array(
			'name' => array(
				'label'   => __( 'Name', 'stm_vehicles_listing' ),
				'type'    => 'text'
			),
			'email' => array(
				'label'   => __( 'E-mail', 'stm_vehicles_listing' ),
				'type'    => 'text'
			),
			'phone' => array(
				'label'   => __( 'Phone', 'stm_vehicles_listing' ),
				'type'    => 'text'
			),
			'date' => array(
				'label'   => __( 'Day', 'stm_vehicles_listing' ),
				'type'    => 'text'
			),
		)
	);

	foreach ( $metaboxes as $stm_field_key => $fields ) {

		foreach ( $fields as $field => $data ) {
			$old = get_post_meta( $post_id, $field, true );
			if ( isset( $_POST[ $field ] ) ) {
				$new = $_POST[ $field ];
				if ( $new && $new != $old ) {
					if($data['type'] == 'listing_select') {
						update_post_meta( $post_id, $field, implode(',', $new) );
					} else {
						update_post_meta( $post_id, $field, $new );
					}
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field, $old );
				}
			} else {
				delete_post_meta( $post_id, $field, $old );
			}
		}


		if($stm_field_key == 'listing_filter') {
			foreach ( $fields as $field => $data ) {

				if($data['type'] == 'listing_select') {
					if ( isset( $_POST[ $field ] ) ) {
						$new = $_POST[ $field ];
						if($new != 'none') {
							wp_set_object_terms( $post_id, $new, $field );
						}
					}
				}
			}
		}

	}
}

function stm_listings_get_pagenum_link($link)
{
    return remove_query_arg('ajax_action', $link);
}

/*Functions*/
function stm_check_motors()
{
    return apply_filters('stm_listing_is_motors_theme', false);
}

require_once 'templates.php';
require_once 'enqueue.php';
require_once 'vehicle_functions.php';

add_action('init', 'stm_listings_include_customizer');

function stm_listings_include_customizer()
{
    if (!stm_check_motors()) {
        require_once 'customizer/customizer.class.php';
    }
}

if (!function_exists('stm_generate_title_from_slugs')) {
	function stm_generate_title_from_slugs($post_id, $show_labels = false)
	{
		$title_from = get_theme_mod('listing_directory_title_frontend', '{make} {serie} {ca-year}');

		$title_return = '';

		if (!empty($title_from)) {
			$title = stm_replace_curly_brackets($title_from);

			$title_counter = 0;

			if (!empty($title)) {
				foreach ($title as $title_part) {
					$term = wp_get_post_terms($post_id, strtolower($title_part), array('orderby' => 'none'));
					if (!empty($term) and !is_wp_error($term)) {
						if (!empty($term[0])) {
							if (!empty($term[0]->name)) {
								$title_counter++;

								if ($title_counter == 1) {
									if ($show_labels) {
										$title_return .= '<div class="labels">';
									}
									$title_return .= $term[0]->name;
								} else {
									$title_return .= ' ' . $term[0]->name;
									if ($show_labels and $title_counter == 2) {
										$title_return .= '</div>';
									}
								}
							} else {
								$number_affix = get_post_meta($post_id, strtolower($title_part), true);
								if (!empty($number_affix)) {
									$title_return .= ' ' . $number_affix . ' ';;
								}
							}
						}
					} else {
						$number_affix = get_post_meta($post_id, strtolower($title_part), true);
						if (!empty($number_affix)) {
							$title_return .= ' ' . $number_affix . ' ';
						}
					}
				}
			}
		}

		if (empty($title_return)) {
			$title_return = get_the_title($post_id);
		}

		return $title_return;
	}
}

if (!function_exists('stm_replace_curly_brackets')) {
	function stm_replace_curly_brackets($string)
	{
		$matches = array();
		preg_match_all('/{(.*?)}/', $string, $matches);

		return $matches[1];
	}
}

function stm_listings_search_inventory()
{
    return apply_filters('stm_listings_default_search_inventory', false);
}

//Ajax request test drive
function stm_ajax_add_test_drive()
{
	$response['errors'] = array();

	if (!filter_var($_POST['name'], FILTER_SANITIZE_STRING)) {
		$response['response'] = esc_html__('Please fill all fields', 'motors');
		$response['errors']['name'] = true;
	}
	if (!is_email($_POST['email'])) {
		$response['response'] = esc_html__('Please enter correct email', 'motors');
		$response['errors']['email'] = true;
	}
	if (!is_numeric($_POST['phone'])) {
		$response['response'] = esc_html__('Please enter correct phone number', 'motors');
		$response['errors']['phone'] = true;
	}
	if (empty($_POST['date'])) {
		$response['response'] = esc_html__('Please fill all fields', 'motors');
		$response['errors']['date'] = true;
	}

	if(!filter_var($_POST['name'], FILTER_SANITIZE_STRING) && !is_email($_POST['email']) && !is_numeric($_POST['phone']) && empty($_POST['date'])) {
		$response['response'] = esc_html__('Please fill all fields', 'motors');
	}


	if (empty($response['errors']) and !empty($_POST['vehicle_id'])) {
		$vehicle_id = intval($_POST['vehicle_id']);
		$test_drive['post_title'] = esc_html__('New request for test drive', 'motors') . ' ' . get_the_title($vehicle_id);
		$test_drive['post_type'] = 'test_drive_request';
		$test_drive['post_status'] = 'draft';
		$test_drive_id = wp_insert_post($test_drive);
		update_post_meta($test_drive_id, 'name', $_POST['name']);
		update_post_meta($test_drive_id, 'email', $_POST['email']);
		update_post_meta($test_drive_id, 'phone', $_POST['phone']);
		update_post_meta($test_drive_id, 'date', $_POST['date']);
		$response['response'] = esc_html__('Your request was sent', 'motors');
		$response['status'] = 'success';

		//Sending Mail to admin
		add_filter('wp_mail_content_type', 'stm_set_html_content_type');

		$to = get_bloginfo('admin_email');
		$subject = esc_html__('Request for a test drive', 'motors') . ' ' . get_the_title($vehicle_id);
		$body = esc_html__('Name - ', 'motors') . $_POST['name'] . '<br/>';
		$body .= esc_html__('Email - ', 'motors') . $_POST['email'] . '<br/>';
		$body .= esc_html__('Phone - ', 'motors') . $_POST['phone'] . '<br/>';
		$body .= esc_html__('Date - ', 'motors') . $_POST['date'] . '<br/>';

		wp_mail($to, $subject, $body);

		/*if (stm_is_listing()) {
			$car_owner = get_post_meta($vehicle_id, 'stm_car_user', true);
			if (!empty($car_owner)) {
				$user_fields = stm_get_user_custom_fields($car_owner);
				if (!empty($user_fields) and !empty($user_fields['email'])) {
					wp_mail($user_fields['email'], $subject, $body);
				}
			}
		}*/

		remove_filter('wp_mail_content_type', 'stm_set_html_content_type');

	} else {
		//$response['response'] = esc_html__('Please fill all fields', 'motors');
		$response['status'] = 'danger';
	}



	$response = json_encode($response);

	echo $response;
	exit;
}

add_action('wp_ajax_stm_ajax_add_test_drive', 'stm_ajax_add_test_drive');
add_action('wp_ajax_nopriv_stm_ajax_add_test_drive', 'stm_ajax_add_test_drive');

function add_footer_template() {
    stm_listings_load_template('modals/get-car-price');
    stm_listings_load_template('modals/test-drive');
    stm_listings_load_template('compare/compare-footer-modal');
}

add_action( 'wp_footer', 'add_footer_template' );
